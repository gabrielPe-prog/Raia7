<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("service/connection_create.php");

$conn = conexao_pdo();

$listaAlunos = "SELECT id_aluno, nome FROM alunos";
$stm = $conn->prepare($listaAlunos);
$stm->execute();
$alunos = $stm->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT 
    p.*,
    a.id_aluno,
    a.nome AS aluno_nome
FROM pagamentos p
JOIN alunos a ON p.aluno_id = a.id_aluno;";
$stm = $conn->prepare($sql);
$stm->execute();
$financeiro = $stm->fetchAll(PDO::FETCH_ASSOC);

$pagAlunos = "SELECT p.* FROM pagamentos p WHERE p.aluno_id = :id_aluno";
$stm = $conn->prepare($pagAlunos);
$stm->bindParam(":id_aluno", $_SESSION["id_aluno"]);
$stm->execute();
$pagAluno = $stm->fetchAll(PDO::FETCH_ASSOC);