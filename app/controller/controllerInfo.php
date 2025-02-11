<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("service/connection_create.php");

$conn = conexao_pdo();

$sql = "SELECT COUNT(*) AS total_alunos FROM alunos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$totalAlunos = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($totalAlunos['total_alunos'])) {
    $_SESSION['totalAlunos'] = $totalAlunos['total_alunos'];
}

$sql2 = "SELECT 
        t.id_turma AS turma_id,
        t.horario,
        a.id_aluno AS aluno_id,
        a.nome,
        a.cpf,
        a.data_nascimento,
        a.contato
        FROM turmas t
        LEFT JOIN alunos a ON t.id_turma = a.id_turma
        ORDER BY t.id_turma, a.nome;
";

$stmt2 = $conn->prepare($sql2);
$stmt2->execute();
$dados = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$turmas = [];
foreach ($dados as $item) {
    $turmaId = $item['turma_id'];
    if (!isset($turmas[$turmaId])) {
        $turmas[$turmaId] = [
            'horario' => $item['horario'],
            'alunos' => []
        ];
    }
    if ($item['aluno_id']) {
        $turmas[$turmaId]['alunos'][] = [
            'aluno_id' => $item['aluno_id'],
            'nome' => $item['nome'],
            'cpf' => $item['cpf'],
            'data_nascimento' => $item['data_nascimento'],
            'contato' => $item['contato']

        ];
    }
}
