<?php
require_once dirname(__DIR__) . '/config/mercadopago.php';
require_once dirname(__DIR__) . '/service/connection_create.php';
require_once dirname(__DIR__) . '/service/checkout.php';

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;

function checkoutService(): CheckoutService
{
    return new CheckoutService(conexao_pdo(), mercadoPagoSettings(),
        static fn(array $request) => (new PreferenceClient())->create($request),
        static fn(string $id) => (new PaymentClient())->get($id));
}
