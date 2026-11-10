<?php
use MercadoPago\Webhook\WebhookSignatureValidator;
use MercadoPago\Exceptions\InvalidWebhookSignatureException;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST'); http_response_code(405); exit;
}
try {
    require_once __DIR__ . '/controller/controllerPagamento.php';
    $settings = mercadoPagoSettings();
    // PHP converte data.id em data_id. O ID do JSON não é usado.
    $id = $_GET['data_id'] ?? null;
    if (!is_string($id) || !ctype_digit($id)) { http_response_code(400); exit; }
    WebhookSignatureValidator::validate(
        $_SERVER['HTTP_X_SIGNATURE'] ?? null,
        $_SERVER['HTTP_X_REQUEST_ID'] ?? null,
        $id, $settings['secret']
    );
    checkoutService()->sincronizar($id);
    http_response_code(200);
} catch (InvalidWebhookSignatureException $e) {
    http_response_code(401);
} catch (Throwable $e) {
    error_log('Webhook Mercado Pago: falha (' . get_class($e) . ').');
    http_response_code(503);
}
