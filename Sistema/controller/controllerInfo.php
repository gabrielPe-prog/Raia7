<?php

include_once("service/connection_create.php");

$conn = conexao_pdo(); 

    $sql = "SELECT COUNT(*) AS total_alunos FROM alunos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $totalAlunos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($totalAlunos['total_alunos'])) {
        $_SESSION['totalAlunos'] = $totalAlunos['total_alunos'];
    }