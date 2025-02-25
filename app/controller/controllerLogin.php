<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

require_once("../service/connection_create.php");

$cpf = $_POST['cpf'];
$senha_digitada = $_POST['senha'];

$conn = conexao_pdo();

$sql = "SELECT * FROM user WHERE cpf = :cpf";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":cpf", $cpf);
$stmt->execute();
$acesso_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($acesso_usuario && password_verify($senha_digitada, $acesso_usuario['senha'])) {
  $_SESSION['logged_in'] = TRUE;
  $_SESSION['nome'] = $acesso_usuario["nome"];
  $_SESSION["nivel"] = $acesso_usuario["nivel"];
  $_SESSION["cpf"] = $acesso_usuario["cpf"];
  header('Location: ../paginaInicial.php');
  exit;
} else {
  $_SESSION['erroLogin'] = 1;
  header('Location: ../index.php');
  exit;
}
