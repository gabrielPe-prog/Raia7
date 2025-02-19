<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("service/connection_create.php");

$conn = conexao_pdo();

$sql = "SELECT 
    f.*, 
    a.id_aluno,
    a.nome AS aluno_nome, 
    p.nome AS pacote_nome,
    p.sobre AS pacote_sobre
FROM financeiro f
JOIN alunos a ON f.id_aluno = a.id_aluno
JOIN pacotes p ON f.id_pacote = p.id;";
$stm = $conn->prepare($sql);
$stm->execute();
$financeiro = $stm->fetchAll(PDO::FETCH_ASSOC);

$sql2 = "SELECT id, nome FROM pacotes";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute();
$pacotes = $stmt2->fetchAll(PDO::FETCH_ASSOC);