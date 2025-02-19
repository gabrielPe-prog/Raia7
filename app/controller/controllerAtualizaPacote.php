<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("../service/connection_create.php");

$conn = conexao_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_financeiro = $_POST['id_financeiro'];
    $id_pacote = $_POST['id_pacote'];
    $pagamento = $_POST['pagamento'];

    if (empty($id_financeiro) || empty($id_pacote) || empty($pagamento)) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Preencha todos os campos!';
        header('Location: ../financeiro.php');
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE financeiro SET id_pacote = ?, pagamento = ? WHERE id = ?");
        $stmt->execute([$id_pacote, $pagamento, $id_financeiro]);

        $_SESSION['update_status'] = 'success';
        $_SESSION['update_message'] = 'Dados atualizados com sucesso!';
    } catch (PDOException $e) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Erro ao atualizar os dados!';
    }
    
    header('Location: ../financeiro.php');
    exit();
}

header("Location: ../financeiro.php");
exit();
