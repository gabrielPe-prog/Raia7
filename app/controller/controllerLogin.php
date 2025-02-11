<?php
if (!isset($_SESSION)){
  session_start();
}
date_default_timezone_set('America/Recife');

require_once("../service/connection_create.php");

$cpf = $_POST['cpf'];
$senha_hash = $_POST['senha'];
$senha = sha1(md5($senha_hash));

$conn = conexao_pdo(); 

    $sql = "SELECT * FROM user WHERE cpf = :cpf AND senha = :senha";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cpf", $cpf);
    $stmt->bindParam(":senha", $senha);
    $stmt->execute();
    $acesso_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($acesso_usuario) {

      $_SESSION['logged_in'] = TRUE;
      $_SESSION['nome'] = $acesso_usuario["nome"];
      $_SESSION["nivel"] = $acesso_usuario["nivel"];

  header('Location: ../paginaInicial.php');
  exit;

} else {
  $_SESSION['erroLogin'] = 0;
  header('Location: ../index.php');
  exit;
}