<?php
session_start();
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST'); http_response_code(405); exit;
}
if (($_SESSION['logged_in'] ?? false) !== true || empty($_SESSION['id_aluno'])) {
    http_response_code(401); exit('Entre novamente no sistema.');
}
if (!is_string($_POST['csrf'] ?? null) || empty($_SESSION['checkout_csrf'])
    || !hash_equals($_SESSION['checkout_csrf'], $_POST['csrf'])) {
    http_response_code(403); exit('Solicitação inválida. Atualize a página.');
}
$id = filter_var($_POST['fatura_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { http_response_code(400); exit('Fatura inválida.'); }
try {
    require_once __DIR__ . '/controller/controllerPagamento.php';
    $url = checkoutService()->criarPreferencia($id, (int) $_SESSION['id_aluno']);
    header('Location: ' . $url, true, 303);
} catch (DomainException $e) {
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: pagamentosAluno.php', true, 303);
} catch (Throwable $e) {
    $diagnostico = 'Checkout: falha ao criar preferência (' . get_class($e) . ')';
    if ($e instanceof \MercadoPago\Exceptions\MPApiException) {
        $diagnostico .= ', HTTP ' . $e->getStatusCode();
    } elseif ($e instanceof PDOException) {
        $diagnostico .= ', SQLSTATE ' . preg_replace('/[^A-Za-z0-9]/', '', (string) $e->getCode());
    } elseif ($e instanceof RuntimeException && in_array($e->getMessage(), [
        'A variável MERCADO_PAGO_ACCESS_TOKEN não foi configurada.',
        'Configuração incompleta. Consulte docs/checkout-pro.md.',
        'O arquivo de credenciais deve ficar fora da raiz pública.',
        'Configuração privada inválida.',
        'Checkout sem URL válida.',
    ], true)) {
        $diagnostico .= ': ' . $e->getMessage();
    }
    error_log($diagnostico . '.');
    $_SESSION['checkout_error'] = 'Não foi possível abrir o pagamento. Tente novamente mais tarde.';
    header('Location: pagamentosAluno.php', true, 303);
}
