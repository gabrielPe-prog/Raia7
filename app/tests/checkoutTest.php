<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/service/checkout.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';
use MercadoPago\Webhook\WebhookSignatureValidator;
use MercadoPago\Exceptions\InvalidWebhookSignatureException;

$count = 0;
function check(bool $condition, string $message): void {
    global $count;
    if (!$condition) throw new RuntimeException($message);
    $count++;
}
function fails(callable $action, string $class = Throwable::class): void {
    try { $action(); } catch (Throwable $e) { check($e instanceof $class, 'Exceção inesperada: ' . get_class($e)); return; }
    throw new RuntimeException('A operação deveria falhar.');
}
if (getenv('CHECKOUT_TEST_MYSQL') === '1') {
    ob_start();
    require_once dirname(__DIR__) . '/service/connection_create.php';
    $db = conexao_pdo();
    ob_end_clean();
    if (!$db) throw new RuntimeException('Banco de testes indisponível.');
    // Tabelas temporárias ocultam as reais apenas nesta conexão e somem ao sair.
    $create = 'CREATE TEMPORARY TABLE ';
} else {
    $db = new PDO('sqlite::memory:');
    $create = 'CREATE TABLE ';
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec($create . 'mp_faturas (id INTEGER PRIMARY KEY, aluno_id INTEGER, descricao TEXT, valor DECIMAL(12,2), vencimento VARCHAR(10), status VARCHAR(30))');
$db->exec($create . 'mp_checkouts (referencia VARCHAR(64) PRIMARY KEY, fatura_id INTEGER, preference_id TEXT, url TEXT, criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
$db->exec($create . 'mp_transacoes (payment_id VARCHAR(64) PRIMARY KEY, fatura_id INTEGER, status VARCHAR(40), valor DECIMAL(12,2), estornado DECIMAL(12,2), metodo TEXT, aprovado_em TEXT, atualizado_em TEXT)');
$db->exec("INSERT INTO mp_faturas VALUES (1,10,'Mensalidade',120.50,'2026-10-10','aberta'),(2,20,'Outra',50,'2026-10-10','aberta')");
$settings = ['url'=>'https://raia.example/app','collector'=>'123','live'=>false];
$calls = 0;
$request = [];
$payment = null;
$service = new CheckoutService($db, $settings,
    function ($input) use (&$calls, &$request) {
        $calls++; $request = $input;
        return (object) ['id'=>'pref-1','sandbox_init_point'=>'https://sandbox.mercadopago.com.br/test','init_point'=>'https://www.mercadopago.com.br/live'];
    }, function ($id) use (&$payment) { return $payment; });

fails(fn() => $service->criarPreferencia(1, 20), DomainException::class);
check($calls === 0, 'Acesso indevido não pode criar preferência.');
$url = $service->criarPreferencia(1, 10);
check($url === 'https://sandbox.mercadopago.com.br/test', 'Deve escolher o checkout de teste.');
check($request['items'][0]['unit_price'] === 120.5, 'Valor deve vir do banco.');
check($request['notification_url'] === 'https://raia.example/app/webhook.php', 'URL deve manter subdiretório.');
check($service->criarPreferencia(1, 10) === $url && $calls === 1, 'Reutilização de preferência.');
$reference = $request['external_reference'];
$payment = (object) ['id'=>100,'external_reference'=>$reference,'collector_id'=>123,'currency_id'=>'BRL','live_mode'=>false,
    'transaction_amount'=>120.50,'transaction_amount_refunded'=>0,'status'=>'pending','payment_method_id'=>'pix',
    'date_approved'=>null,'date_last_updated'=>'2026-10-01T12:00:00Z'];
$status = fn() => $db->query('SELECT status FROM mp_faturas WHERE id=1')->fetchColumn();
$service->sincronizar('100');
check($status() === 'aberta', 'Pagamento pendente não quita.');
$payment->status = 'approved'; $payment->date_approved = '2026-10-01T12:01:00Z'; $payment->date_last_updated = '2026-10-01T12:01:00Z';
$service->sincronizar('100'); $service->sincronizar('100');
check($status() === 'paga', 'Aprovação quita.');
check((int) $db->query('SELECT COUNT(*) FROM mp_transacoes')->fetchColumn() === 1, 'Webhook repetido não duplica.');
fails(fn() => $service->criarPreferencia(1, 10), DomainException::class);
$payment->status = 'pending'; $payment->date_last_updated = '2026-10-01T12:00:00Z';
$service->sincronizar('100');
check($status() === 'paga', 'Evento antigo não reabre fatura.');
$payment->id = 101; $payment->status = 'rejected'; $payment->date_last_updated = '2026-10-01T12:02:00Z';
$service->sincronizar('101');
check($status() === 'paga', 'Outra tentativa recusada não reabre fatura.');
foreach (['collector_id'=>999, 'currency_id'=>'USD','live_mode'=>true,'transaction_amount'=>1] as $field=>$bad) {
    $original = $payment->$field; $payment->$field = $bad;
    fails(fn() => $service->sincronizar('101'));
    check($status() === 'paga', 'Dados divergentes não alteram fatura.');
    $payment->$field = $original;
}
$payment->status = 'approved';
$service->sincronizar('101');
check($status() === 'revisao', 'Duas aprovações exigem revisão.');
$payment->id = 100; $payment->transaction_amount_refunded = 10;
$service->sincronizar('100');
check($status() === 'revisao', 'Estorno parcial exige revisão.');
$payment->status = 'charged_back'; $payment->transaction_amount_refunded = 0;
$service->sincronizar('100');
check($status() === 'revisao', 'Contestação exige revisão.');
$payment->external_reference = 'outra-integracao';
$service->sincronizar('100');
check($status() === 'revisao', 'Referência desconhecida não altera fatura.');

$failureService = new CheckoutService($db, $settings, function () { throw new RuntimeException('Falha simulada'); }, fn()=>null);
fails(fn() => $failureService->criarPreferencia(2,20));
check((int)$db->query('SELECT COUNT(*) FROM mp_checkouts WHERE fatura_id=2')->fetchColumn() === 0, 'Falha deve reverter checkout.');
check(CheckoutService::cents('0.01') === 1 && CheckoutService::cents('120.5') === 12050, 'Conversão monetária.');
fails(fn() => CheckoutService::cents('-1'));
fails(fn() => CheckoutService::cents('1.001'));
$ts = '1742505638683';
$hash = hash_hmac('sha256', "id:100;request-id:req-1;ts:$ts;", 'secret-test');
WebhookSignatureValidator::validate("ts=$ts,v1=$hash", 'req-1', '100', 'secret-test');
check(true, 'Assinatura válida.');
fails(fn() => WebhookSignatureValidator::validate("ts=$ts,v1=$hash", 'req-1', '101', 'secret-test'), InvalidWebhookSignatureException::class);
fails(fn() => WebhookSignatureValidator::validate(null, 'req-1', '100', 'secret-test'), InvalidWebhookSignatureException::class);
echo "$count verificações concluídas, sem chamadas à API.\n";
