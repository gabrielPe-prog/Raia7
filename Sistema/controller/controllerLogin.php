<?php
if (!isset($_SESSION)){
  session_start();
}
date_default_timezone_set('America/Recife');

require_once("../service/conection_create.php");

$email_usuario = $_POST['email_usuario'];
$senha_hash = $_POST['senha_acesso'];
$senha_acesso = sha1(md5($senha_hash));

$conn = conexao_pdo(); 

    $sql = "SELECT * FROM usuarios WHERE email_usuario = :email_usuario AND senha_acesso = :senha_acesso";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email_usuario", $email_usuario);
    $stmt->bindParam(":senha_acesso", $senha_acesso);
    $stmt->execute();
    $acesso_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($acesso_usuario) {

      $_SESSION['logged_in'] = TRUE;
      $_SESSION['nome_usuario'] = $acesso_usuario["nome_usuario"];

  header('Location: ../painelInicial.php');
  exit;

} else {
  $_SESSION['erroLogin'] = 0;
  header('Location: ../paginaInicial.php');
  exit;
}