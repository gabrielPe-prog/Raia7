<?php
session_start();
date_default_timezone_set('America/Recife');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../service/studentProfile.php';
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        throw new RuntimeException('Método não permitido.', 405);
    }
    if (($_SESSION['logged_in'] ?? false) !== true) {
        throw new RuntimeException('Sua sessão expirou. Entre novamente.', 401);
    }
    $token = $_POST['csrf_token'] ?? null;
    if (!is_string($token) || empty($_SESSION['profile_csrf']) || !hash_equals($_SESSION['profile_csrf'], $token)) {
        throw new RuntimeException('Atualize a página e tente novamente.', 403);
    }
    $data = validateStudentProfile($_POST);
    ob_start();
    try {
        require_once __DIR__ . '/../service/connection_create.php';
        $db = conexao_pdo();
    } finally {
        ob_end_clean();
    }
    if (!$db instanceof PDO) throw new RuntimeException('Banco indisponível.');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    updateStudentProfile($db, $_SESSION, $data);
    $_SESSION['nome'] = $data['nome'];
    $_SESSION['cpf'] = $data['cpf'];
    $_SESSION['profile_saved'] = true;
    echo json_encode(['success'=>true, 'message'=>'Suas informações foram atualizadas.']);
} catch (Throwable $error) {
    $code = in_array($error->getCode(), [401,403,404,405,409,422], true) ? $error->getCode() : 500;
    http_response_code($code);
    if ($code === 500) error_log('Erro ao atualizar perfil: ' . $error->getMessage());
    echo json_encode(['success'=>false, 'message'=>$code === 500 ? 'Não foi possível salvar. Tente novamente em instantes.' : $error->getMessage()], JSON_UNESCAPED_UNICODE);
}
