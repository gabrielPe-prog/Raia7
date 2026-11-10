<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../service/connection_create.php';

$autenticado = !empty($_SESSION['logged_in']) && !empty($_SESSION['id_user']);
$destino = $autenticado ? '../paginaInicial.php' : '../index.php';

function finalizarAtualizacaoSenha($status, $mensagem, $destino)
{
    $_SESSION['update_status'] = $status;
    $_SESSION['update_message'] = $mensagem;
    header('Location: ' . $destino, true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || !isset($_SESSION['senha_csrf_token']) ||
    !hash_equals($_SESSION['senha_csrf_token'], $token)) {
    finalizarAtualizacaoSenha('error', 'Solicitação inválida. Recarregue a página e tente novamente.', $destino);
}

foreach (['cpf', 'senha_atual', 'nova_senha', 'confirma_senha'] as $campo) {
    if (!isset($_POST[$campo]) || !is_string($_POST[$campo]) || $_POST[$campo] === '') {
        finalizarAtualizacaoSenha('error', 'Todos os campos são obrigatórios!', $destino);
    }
}

$cpf = preg_replace('/[.\-\s]/', '', $_POST['cpf']);
$senhaAtual = $_POST['senha_atual'];
$novaSenha = $_POST['nova_senha'];
if (!preg_match('/^[0-9]{11}$/D', $cpf)) {
    finalizarAtualizacaoSenha('error', 'Informe um CPF com 11 dígitos.', $destino);
}
if ($novaSenha !== $_POST['confirma_senha']) {
    finalizarAtualizacaoSenha('error', 'A nova senha e a confirmação não coincidem!', $destino);
}
if (strlen($novaSenha) < 8 || strlen($novaSenha) > 72 || strpos($novaSenha, "\0") !== false || trim($novaSenha) === '') {
    finalizarAtualizacaoSenha('error', 'A nova senha deve ter entre 8 e 72 bytes e não pode conter apenas espaços.', $destino);
}

try {
    $conn = conexao_pdo();
    if (!$conn instanceof PDO) {
        throw new RuntimeException('Conexão indisponível.');
    }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT id_user, senha FROM user WHERE cpf = ?';
    $parametros = [$cpf];
    if ($autenticado) {
        $sql .= ' AND id_user = ?';
        $parametros[] = $_SESSION['id_user'];
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($parametros);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($senhaAtual, $usuario['senha'])) {
        finalizarAtualizacaoSenha('error', 'CPF ou senha atual inválidos.', $destino);
    }
    if (password_verify($novaSenha, $usuario['senha'])) {
        finalizarAtualizacaoSenha('error', 'A nova senha deve ser diferente da senha atual.', $destino);
    }

    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    if ($hash === false) {
        throw new RuntimeException('Não foi possível gerar o hash da senha.');
    }
    // A condição com o hash anterior impede sobrescrever uma alteração concorrente.
    $stmt = $conn->prepare('UPDATE user SET senha = ? WHERE id_user = ? AND senha = ?');
    $stmt->execute([$hash, $usuario['id_user'], $usuario['senha']]);
    if ($stmt->rowCount() !== 1) {
        finalizarAtualizacaoSenha('error', 'A senha foi alterada em outra solicitação. Tente novamente.', $destino);
    }
    unset($_SESSION['senha_csrf_token']);
    session_regenerate_id(true);
    finalizarAtualizacaoSenha('success', 'Senha atualizada com sucesso!', $destino);
} catch (Throwable $e) {
    error_log('Erro ao atualizar senha: ' . $e->getMessage());
    finalizarAtualizacaoSenha('error', 'Não foi possível atualizar a senha. Tente novamente mais tarde.', $destino);
}
