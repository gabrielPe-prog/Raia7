<?php

use MercadoPago\MercadoPagoConfig;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Arquivo privado fora da raiz pública, inclusive quando app/ está em subpastas.
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: null;
if (empty($_SERVER['DOCUMENT_ROOT'])) $documentRoot = null;
$privateConfigPath = getenv('MERCADO_PAGO_CONFIG_FILE')
    ?: ($documentRoot ? dirname($documentRoot) : dirname(__DIR__, 2)) . '/mercadopago.local.php';
if ($documentRoot && is_file($privateConfigPath)) {
    $resolvedConfig = realpath($privateConfigPath);
    if ($resolvedConfig && str_starts_with($resolvedConfig, rtrim($documentRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
        throw new RuntimeException('O arquivo de credenciais deve ficar fora da raiz pública.');
    }
}
$privateConfig = is_file($privateConfigPath) ? require $privateConfigPath : [];
if (!is_array($privateConfig)) throw new RuntimeException('Configuração privada inválida.');

function mercadoPagoEnv(string $key): string
{
    global $privateConfig;
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : (string) ($privateConfig[$key] ?? '');
}

$accessToken = mercadoPagoEnv('MERCADO_PAGO_ACCESS_TOKEN');

if (!$accessToken) {
    throw new RuntimeException(
        'A variável MERCADO_PAGO_ACCESS_TOKEN não foi configurada.'
    );
}

MercadoPagoConfig::setAccessToken($accessToken);

function mercadoPagoSettings(): array
{
    $url = rtrim((string) mercadoPagoEnv('APP_URL'), '/');
    $collector = (string) mercadoPagoEnv('MERCADO_PAGO_COLLECTOR_ID');
    $secret = (string) mercadoPagoEnv('MERCADO_PAGO_WEBHOOK_SECRET');
    $live = mercadoPagoEnv('MERCADO_PAGO_LIVE_MODE');
    if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https'
        || parse_url($url, PHP_URL_QUERY) || parse_url($url, PHP_URL_FRAGMENT)
        || !ctype_digit($collector) || $secret === '' || !in_array($live, ['true', 'false'], true)) {
        throw new RuntimeException('Configuração incompleta. Consulte docs/checkout-pro.md.');
    }
    return ['url' => $url, 'collector' => $collector, 'secret' => $secret, 'live' => $live === 'true'];
}
