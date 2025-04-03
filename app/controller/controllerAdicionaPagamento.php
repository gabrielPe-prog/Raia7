<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("../service/connection_create.php");

$conn = conexao_pdo();

// var_dump($_POST);
// die();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = isset($_POST['id_aluno']) ? $_POST['id_aluno'] : null;
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;
    $valor = isset($_POST['valor']) ? $_POST['valor'] : null;
    $data_pagamento = isset($_POST['data_pagamento']) ? $_POST['data_pagamento'] : null;

    if (empty($aluno_id) || empty($descricao) || empty($valor) || empty($data_pagamento)) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Todos os campos são obrigatórios!';
        header('Location: ../financeiro.php');
        exit();
    }

    $valor = str_replace(',', '.', $valor);
    
    try {
        $stmt = $conn->prepare("INSERT INTO pagamentos (aluno_id, descricao, valor, data_pagamento) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$aluno_id, $descricao, $valor, $data_pagamento]);

        if ($result) {
            $_SESSION['update_status'] = 'success';
            $_SESSION['update_message'] = 'Pagamento registrado com sucesso!';
        } else {
            $_SESSION['update_status'] = 'error';
            $_SESSION['update_message'] = 'Erro ao registrar o pagamento!';
        }
    } catch (PDOException $e) {
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_message'] = 'Erro ao registrar o pagamento: ' . $e->getMessage();
        
        error_log('Erro ao adicionar pagamento: ' . $e->getMessage(), 0);
    }
    
    header('Location: ../financeiro.php');
    exit();
}
header('Location: ../financeiro.php');
exit();