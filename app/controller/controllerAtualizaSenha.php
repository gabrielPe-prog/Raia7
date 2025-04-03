<?php
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

require_once __DIR__ . "/../service/connection_create.php";

$conn = conexao_pdo();

if (isset($_POST['tipo']) && $_POST['tipo'] == 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
        $nova_senha = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
        $confirma_senha = isset($_POST['confirma_senha']) ? $_POST['confirma_senha'] : '';

        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (empty($cpf) || empty($nova_senha) || empty($confirma_senha)) {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'Todos os campos são obrigatórios!';
            header('Location: ../index.php');
            exit();
        }

        if ($nova_senha !== $confirma_senha) {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'A nova senha e a confirmação não coincidem!';
            header('Location: ../index.php');
            exit();
        }

        try {

            $stmt = $conn->prepare("SELECT * FROM user WHERE cpf = ?");
            $stmt->execute([$cpf]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                $_SESSION['update_status'] = 'error';
                $_SESSION['update_message'] = 'CPF inválido para este usuário!';
                header('Location: ../index.php');
                exit();
            }

            $user_id = $_SESSION['id_user'];

            $hash_nova_senha = password_hash($nova_senha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE user SET senha = ? WHERE cpf = ?");
            $result = $stmt->execute([$hash_nova_senha, $cpf]);

            if ($result) {
                $_SESSION['update_status'] = 'success';
                $_SESSION['update_message'] = 'Senha atualizada com sucesso!';
            } else {
                $_SESSION['update_status'] = 'error';
                $_SESSION['update_message'] = 'Erro ao atualizar a senha!';
            }
        } catch (PDOException $e) {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'Erro ao processar a solicitação: ' . $e->getMessage();

            error_log('Erro ao atualizar senha: ' . $e->getMessage(), 0);
        }

        header('Location: ../index.php');
        exit();
    }

    header('Location: ../index.php');
    exit();
};

if (isset($_POST['tipo']) && $_POST['tipo'] == 1) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $nova_senha = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
    $confirma_senha = isset($_POST['confirma_senha']) ? $_POST['confirma_senha'] : '';

    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (empty($cpf) || empty($nova_senha) || empty($confirma_senha)) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Todos os campos são obrigatórios!';
        header('Location: ../paginaInicial.php');
        exit();
    }

    if ($nova_senha !== $confirma_senha) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'A nova senha e a confirmação não coincidem!';
        header('Location: ../paginaInicial.php');
        exit();
    }

    try {

        $stmt = $conn->prepare("SELECT * FROM user WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'CPF inválido para este usuário!';
            header('Location: ../paginaInicial.php');
            exit();
        }

        $user_id = $_SESSION['id_user'];

        $hash_nova_senha = password_hash($nova_senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE user SET senha = ? WHERE cpf = ?");
        $result = $stmt->execute([$hash_nova_senha, $cpf]);

        if ($result) {
            $_SESSION['update_status'] = 'success';
            $_SESSION['update_message'] = 'Senha atualizada com sucesso!';
        } else {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'Erro ao atualizar a senha!';
        }
    } catch (PDOException $e) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Erro ao processar a solicitação: ' . $e->getMessage();

        error_log('Erro ao atualizar senha: ' . $e->getMessage(), 0);
    }

    header('Location: ../paginaInicial.php');
    exit();
}

header('Location: ../paginaInicial.php');
exit();
};