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
    error_log('Checkout: falha ao criar preferência (' . get_class($e) . ').');
    $_SESSION['checkout_error'] = 'Não foi possível abrir o pagamento. Tente novamente mais tarde.';
    header('Location: pagamentosAluno.php', true, 303);
}
