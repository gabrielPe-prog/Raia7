<?php
require_once __DIR__ . '/service/reportData.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
try {
    $request = reportRequest($_GET, $_SESSION);
    $session = $_SESSION;
    session_write_close();
    // Connection failures in the legacy helper echo text; buffer it to protect PDF bytes.
    ob_start();
    try {
        require_once __DIR__ . '/service/connection_create.php';
        $db = conexao_pdo();
    } finally {
        ob_end_clean();
    }
    if (!$db instanceof PDO) {
        throw new RuntimeException('Não foi possível conectar ao banco.', 503);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $report = buildReport($db, $request, $session);
    require_once __DIR__ . '/service/reportPdf.php';
    $pdf = renderReportPdf($report);
    $filename = $request['type'] === 'carteirinha'
        ? 'raia7-carteirinha.pdf'
        : sprintf('raia7-%s-%04d-%02d.pdf', $request['type'], $request['year'], $request['month']);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
} catch (Throwable $error) {
    $code = in_array($error->getCode(), [400, 401, 403, 404], true) ? $error->getCode() : 500;
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    if ($code === 500) {
        error_log('Falha na exportação Raia7: ' . $error->getMessage());
    }
    echo json_encode(['error' => $code === 500 ? 'Não foi possível gerar o PDF. Tente novamente em instantes.' : $error->getMessage()], JSON_UNESCAPED_UNICODE);
}
