<?php
// processar_pagamento.php
require_once 'controller/controllerPagamento.php';

// Configura o cabeçalho para retornar JSON
header('Content-Type: application/json');

try {
    // 1. Instancia o controller
    $controller = new PagamentoController();
    
    // 2. Valida e coleta os dados do POST
    $pagamentoId = $_POST['pagamento_id'];
    $valor = $_POST['valor'];
    // ... outros dados necessários
    
    // 3. Prepara os dados do pagador
    $dadosPagador = [
        'nome' => $_POST['aluno_nome'],
        'email' => $_POST['aluno_email'],
        // ... outros dados do formulário
    ];
    
    // 4. Processa o pagamento
    $resultado = $controller->criarPagamento($pagamentoId, $dadosPagador);
    
    // 5. Retorna resposta JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Pagamento realizado com sucesso'
    ]);
    
} catch (Exception $e) {
    // Retorna erro em caso de falha
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>