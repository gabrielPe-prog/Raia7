<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$id = $argv[1] ?? '';
if (!ctype_digit($id)) { fwrite(STDERR, "Uso: php reconciliar_pagamento.php ID_DO_PAGAMENTO\n"); exit(1); }
try {
    require_once __DIR__ . '/controller/controllerPagamento.php';
    checkoutService()->sincronizar($id);
    echo "Consulta concluída. Pagamentos vinculados foram sincronizados.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Falha na reconciliação (" . get_class($e) . "). Confira configuração, banco e ID.\n");
    exit(1);
}
