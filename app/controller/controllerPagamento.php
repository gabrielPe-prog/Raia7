<?php
// PagamentoController.php
require_once 'vendor/autoload.php';
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

require_once dirname(__DIR__, 1) . '/service/connection_create.php';

class PagamentoController {
    private $mpClient;
    
    public function __construct() {
        MercadoPagoConfig::setAccessToken("SEU_ACCESS_TOKEN");
        $this->mpClient = new PaymentClient();
    }
    
    // Lista pagamentos do aluno
    public function listarPagamentos($alunoId) {
        // Conectar ao banco de dados
        $db = conexao_pdo();
        
        $stmt = $db->prepare("SELECT * FROM pagamentos WHERE aluno_id = :aluno_id");
        $stmt->bindParam(':aluno_id', $alunoId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cria pagamento no Mercado Pago
    public function criarPagamento($pagamentoId, $dadosPagador) {
        // Buscar informações do pagamento no banco
        $db = conexao_pdo();
        $stmt = $db->prepare("SELECT * FROM pagamentos WHERE id = :id");
        $stmt->bindParam(':id', $pagamentoId);
        $stmt->execute();
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pagamento) {
            throw new Exception("Pagamento não encontrado");
        }
        
        // Criar request para o Mercado Pago
        $request = [
            "transaction_amount" => (float)$pagamento['valor'],
            "description" => $pagamento['descricao'],
            "payment_method_id" => $dadosPagador['payment_method_id'],
            "payer" => [
                "email" => $dadosPagador['email'],
                "first_name" => explode(' ', $dadosPagador['nome'])[0],
                "last_name" => explode(' ', $dadosPagador['nome'])[1] ?? '',
                "identification" => [
                    "type" => "CPF",
                    "number" => $dadosPagador['cpf']
                ]
            ]
        ];
        
        // Adicionar dados específicos do método de pagamento
        if ($dadosPagador['payment_method_id'] === 'bolbradesco') {
            $request['payer']['address'] = [
                "zip_code" => $dadosPagador['cep'],
                "street_name" => $dadosPagador['rua'],
                "street_number" => $dadosPagador['numero'],
                "neighborhood" => $dadosPagador['bairro'],
                "city" => $dadosPagador['cidade'],
                "federal_unit" => $dadosPagador['estado']
            ];
        } elseif ($dadosPagador['payment_method_id'] === 'pix') {
            // Nada adicional necessário para Pix
        } elseif ($dadosPagador['payment_method_id'] === 'credit_card') {
            $request['token'] = $dadosPagador['token'];
            $request['installments'] = $dadosPagador['installments'];
            $request['issuer_id'] = $dadosPagador['issuer_id'];
        }
        
        // Criar pagamento no MP
        $payment = $this->mpClient->create($request);
        
        // Salvar transação no banco
        $this->salvarTransacao($pagamentoId, $payment);
        
        return $payment;
    }
    
    // Webhook para receber atualizações de pagamento
    public function webhook() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];
            $payment = $this->mpClient->get($paymentId);
            
            // Atualizar status no banco de dados
            $this->atualizarStatusPagamento($payment);
        }
        
        http_response_code(200);
    }
    
    private function salvarTransacao($pagamentoId, $payment) {
        $db = conexao_pdo();
        
        $stmt = $db->prepare("
            INSERT INTO transacoes (
                pagamento_id, 
                transaction_id, 
                status, 
                valor, 
                data_pagamento, 
                metodo_pagamento, 
                dados_json
            ) VALUES (
                :pagamento_id, 
                :transaction_id, 
                :status, 
                :valor, 
                :data_pagamento, 
                :metodo_pagamento, 
                :dados_json
            )
        ");
        
        $stmt->execute([
            ':pagamento_id' => $pagamentoId,
            ':transaction_id' => $payment->id,
            ':status' => $payment->status,
            ':valor' => $payment->transaction_amount,
            ':data_pagamento' => date('Y-m-d H:i:s', strtotime($payment->date_created)),
            ':metodo_pagamento' => $payment->payment_method_id,
            ':dados_json' => json_encode($payment)
        ]);
    }
    
    private function atualizarStatusPagamento($payment) {
        $db = conexao_pdo();
        
        // Encontrar o pagamento relacionado
        $stmt = $db->prepare("
            SELECT p.id 
            FROM pagamentos p
            JOIN transacoes t ON p.id = t.pagamento_id
            WHERE t.transaction_id = :transaction_id
        ");
        $stmt->execute([':transaction_id' => $payment->id]);
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pagamento) {
            $status = $this->mapStatus($payment->status);
            
            $update = $db->prepare("UPDATE pagamentos SET status = :status WHERE id = :id");
            $update->execute([':status' => $status, ':id' => $pagamento['id']]);
            
            // Atualizar também a transação
            $updateTrans = $db->prepare("
                UPDATE transacoes 
                SET status = :status 
                WHERE transaction_id = :transaction_id
            ");
            $updateTrans->execute([
                ':status' => $payment->status,
                ':transaction_id' => $payment->id
            ]);
        }
    }
    
    private function mapStatus($mpStatus) {
        switch ($mpStatus) {
            case 'approved':
                return 'pago';
            case 'pending':
            case 'in_process':
                return 'pendente';
            case 'rejected':
            case 'cancelled':
            case 'refunded':
            case 'charged_back':
                return 'pendente'; // ou outro status adequado
            default:
                return 'pendente';
        }
    }
}
?>