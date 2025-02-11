<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("service/connection_create.php");

$conn = conexao_pdo();

$sql = "SELECT * FROM alunos";
$stm = $conn->prepare($sql);
$stm->execute();
$alunos = $stm->fetchAll(PDO::FETCH_ASSOC);
