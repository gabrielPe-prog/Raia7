<?php
if (!isset($_SESSION)){
  session_start();
}

require_once __DIR__ . "/../service/connection_create.php";

date_default_timezone_set('America/Recife');

$conn = conexao_pdo();

$sql = "SELECT 
            alunos.*, 
            turmas.horario 
        FROM 
            alunos 
        JOIN 
            turmas 
        ON 
            alunos.id_turma = turmas.id_turma 
        WHERE 
            alunos.cpf = :cpf;";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":cpf", $_SESSION['cpf'] );
$stmt->execute();
$carteirinha = $stmt->fetch(PDO::FETCH_ASSOC);