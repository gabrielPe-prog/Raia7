<?php

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

include_once '../service/conection_create.php';

$info_busca = $_POST['info_busca'];

$info_autorizacao = ltrim($info_busca, '0');
    $conn = conexao_pdo();
    
    $sql = "SELECT * FROM servidores WHERE CPF = :cpf";
    $stm = $conn->prepare($sql);
    $stm->bindParam(':cpf', $info_busca);
    $stm->execute();
    $resultado = $stm->fetch(PDO::FETCH_ASSOC);

    $_SESSION["cpf_servidor"] = $resultado["CPF"];

    if (!$resultado) {
        $_SESSION['servidor_nao_encontrado'] = true;
        header("location: ../formAdesao.php");
        exit();
    } else {
        $_SESSION['mostrar_busca'] = true;
        header("location: ../formAdesao.php");
    }