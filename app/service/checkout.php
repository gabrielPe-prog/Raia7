<?php

/** Regras locais com clientes injetados para testes sem cobranças. */
final class CheckoutService
{
    public function __construct(private PDO $db, private array $settings, private $createPreference, private $getPayment)
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function lock(): string
    {
        return $this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
    }

    public static function cents(mixed $value): int
    {
        $value = (string) $value;
        if (!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/D', $value)) {
            throw new InvalidArgumentException('Valor monetário inválido.');
        }
        [$integer, $fraction] = array_pad(explode('.', $value), 2, '');
        return (int) $integer * 100 + (int) str_pad($fraction, 2, '0');
    }

    public function criarPreferencia(int $faturaId, int $alunoId): string
    {
        $this->db->beginTransaction();
        try {
            $fatura = $this->query('SELECT * FROM mp_faturas WHERE id = ? AND aluno_id = ?' . $this->lock(), [$faturaId, $alunoId])->fetch(PDO::FETCH_ASSOC);
            if (!$fatura) throw new DomainException('Fatura não encontrada.');
            if ($fatura['status'] !== 'aberta' || self::cents($fatura['valor']) <= 0) {
                throw new DomainException('Esta fatura não está disponível para pagamento.');
            }
            $checkout = $this->query('SELECT * FROM mp_checkouts WHERE fatura_id = ? ORDER BY criado_em DESC', [$faturaId])->fetch(PDO::FETCH_ASSOC);
            if ($checkout && $checkout['url']) {
                $this->db->commit();
                return $checkout['url'];
            }
            $reference = bin2hex(random_bytes(24));
            $this->query('INSERT INTO mp_checkouts (referencia, fatura_id) VALUES (?, ?)', [$reference, $faturaId]);
            $return = $this->settings['url'] . '/pagamentosAluno.php?retorno=1';
            $preference = ($this->createPreference)([
                'items' => [[
                    'id' => (string) $faturaId, 'title' => $fatura['descricao'],
                    'quantity' => 1, 'currency_id' => 'BRL', 'unit_price' => self::cents($fatura['valor']) / 100,
                ]],
                'external_reference' => $reference,
                'notification_url' => $this->settings['url'] . '/webhook.php',
                'back_urls' => ['success' => $return, 'pending' => $return, 'failure' => $return],
                'auto_return' => 'approved',
            ]);
            $url = $this->settings['live'] ? ($preference->init_point ?? '') : ($preference->sandbox_init_point ?? '');
            if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
                throw new RuntimeException('Checkout sem URL válida.');
            }
            $this->query('UPDATE mp_checkouts SET preference_id = ?, url = ? WHERE referencia = ?', [$preference->id, $url, $reference]);
            $this->db->commit();
            return $url;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    public function sincronizar(string $paymentId): void
    {
        // Somente a API autenticada fornece status, valor e referência confiáveis.
        $payment = ($this->getPayment)($paymentId);
        if ((string) $payment->id !== $paymentId) throw new RuntimeException('ID divergente.');
        $checkout = $this->query('SELECT * FROM mp_checkouts WHERE referencia = ?', [(string) ($payment->external_reference ?? '')])->fetch(PDO::FETCH_ASSOC);
        if (!$checkout) return; // Outra integração da mesma conta.
        $this->db->beginTransaction();
        try {
            $fatura = $this->query('SELECT * FROM mp_faturas WHERE id = ?' . $this->lock(), [$checkout['fatura_id']])->fetch(PDO::FETCH_ASSOC);
            if ((string) $payment->collector_id !== $this->settings['collector']
                || $payment->currency_id !== 'BRL'
                || (bool) $payment->live_mode !== $this->settings['live']
                || self::cents($payment->transaction_amount) !== self::cents($fatura['valor'])) {
                throw new RuntimeException('Pagamento incompatível com a fatura.');
            }
            $updated = (string) ($payment->date_last_updated ?? '');
            if (!$updated || strtotime($updated) === false) throw new RuntimeException('Pagamento sem data de atualização.');
            $old = $this->query('SELECT * FROM mp_transacoes WHERE payment_id = ?', [$paymentId])->fetch(PDO::FETCH_ASSOC);
            if ($old && (int) $old['fatura_id'] !== (int) $fatura['id']) throw new RuntimeException('Vínculo divergente.');
            if ($old && strtotime($old['atualizado_em']) > strtotime($updated)) {
                $this->db->commit();
                return;
            }
            $refund = $payment->transaction_amount_refunded ?? 0;
            self::cents($refund);
            $values = [$fatura['id'], $payment->status, $payment->transaction_amount, $refund,
                $payment->payment_method_id ?? null, $payment->date_approved ?? null, $updated, $paymentId];
            if ($old) {
                $this->query('UPDATE mp_transacoes SET fatura_id=?, status=?, valor=?, estornado=?, metodo=?, aprovado_em=?, atualizado_em=? WHERE payment_id=?', $values);
            } else {
                $this->query('INSERT INTO mp_transacoes (fatura_id,status,valor,estornado,metodo,aprovado_em,atualizado_em,payment_id) VALUES (?,?,?,?,?,?,?,?)', $values);
            }
            $transactions = $this->query('SELECT * FROM mp_transacoes WHERE fatura_id = ?', [$fatura['id']])->fetchAll(PDO::FETCH_ASSOC);
            $approved = 0;
            $review = false;
            foreach ($transactions as $transaction) {
                if ($transaction['status'] === 'approved') {
                    $approved += self::cents($transaction['valor']) - self::cents($transaction['estornado']);
                }
                $review = $review || self::cents($transaction['estornado']) > 0
                    || in_array($transaction['status'], ['refunded', 'charged_back', 'in_mediation'], true);
            }
            $due = self::cents($fatura['valor']);
            // Estornos, contestações e pagamentos duplicados exigem revisão administrativa.
            $status = ($review || $approved > $due) ? 'revisao' : ($approved === $due ? 'paga' : 'aberta');
            $this->query('UPDATE mp_faturas SET status = ? WHERE id = ?', [$status, $fatura['id']]);
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
