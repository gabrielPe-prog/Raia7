<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("service/connection_create.php");

$conn = conexao_pdo();

$sql = "SELECT 
    a.*,
    t.horario AS horario_turma
FROM 
    alunos a
LEFT JOIN 
    turmas t ON a.id_turma = t.id_turma
WHERE cpf = :cpf;";
$stm = $conn->prepare($sql);
$stm->bindParam(':cpf' , $_SESSION['cpf']);
$stm->execute();
$alunos = $stm->fetch(PDO::FETCH_ASSOC);
