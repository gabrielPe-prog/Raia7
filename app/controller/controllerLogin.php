<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

require_once("../service/connection_create.php");

$cpf = $_POST['cpf'];
$senha_digitada = $_POST['senha'];

$conn = conexao_pdo();

$sql = "
    SELECT 
        user.*, 
        alunos.id_turma,
        alunos.path_foto
    FROM 
        user 
    INNER JOIN 
        alunos 
    ON 
        user.cpf = alunos.cpf 
    WHERE 
        user.cpf = :cpf
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":cpf", $cpf);
$stmt->execute();
$acesso_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$horario_turma = "SELECT horario FROM turmas WHERE id_turma = :id_turma";
$stmt_horario = $conn->prepare($horario_turma);
$stmt_horario->bindParam(":id_turma", $acesso_usuario['id_turma']);
$stmt_horario->execute();
$horario_turma = $stmt_horario->fetch(PDO::FETCH_ASSOC);

if ($acesso_usuario && password_verify($senha_digitada, $acesso_usuario['senha'])) {
  $nome = $acesso_usuario['nome'];
  $cpf = $acesso_usuario['cpf'];
  $turma = $horario_turma['horario'];
  $foto = $acesso_usuario['path_foto'];

  $imagemFundo = imagecreatefrompng('../assets/carteirinha/carteirinhaR7.png');

  $corTexto = imagecolorallocate($imagemFundo, 0, 0, 0);

  $caminhoAbsolutoFoto = __DIR__ . '/../' . $foto;
  if (file_exists($caminhoAbsolutoFoto)) {
      $fotoAluno = imagecreatefromjpeg($caminhoAbsolutoFoto);
      $fotoLargura = imagesx($fotoAluno);
      $fotoAltura = imagesy($fotoAluno);

      $novaLargura = 304;
      $novaAltura = 430;
      $fotoRedimensionada = imagecreatetruecolor($novaLargura, $novaAltura);
      imagecopyresampled($fotoRedimensionada, $fotoAluno, 0, 0, 0, 0, $novaLargura, $novaAltura, $fotoLargura, $fotoAltura);

      $posicaoXFoto = 121;
      $posicaoYFoto = 138;
      imagecopy($imagemFundo, $fotoRedimensionada, $posicaoXFoto, $posicaoYFoto, 0, 0, $novaLargura, $novaAltura);
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Foto do aluno não encontrada.']);
      exit();
  }

  $fonte = __DIR__ . '/../assets/carteirinha/trebuc.ttf';

  $posicaoXNome = 740;
  $posicaoYNome = 290;
  imagettftext($imagemFundo, 20, 0, $posicaoXNome, $posicaoYNome, $corTexto, $fonte, $nome);

  $posicaoXCPF = 740;
  $posicaoYCPF = 390;
  imagettftext($imagemFundo, 20, 0, $posicaoXCPF, $posicaoYCPF, $corTexto, $fonte, $cpf);

  $posicaoXTurma = 740;
  $posicaoYTurma = 500;
  imagettftext($imagemFundo, 20, 0, $posicaoXTurma, $posicaoYTurma, $corTexto, $fonte, $turma);

  $caminhoImagem = '../carteirinhasCriadas/carteirinha_' . $cpf . '.png';
  imagepng($imagemFundo, $caminhoImagem);

  imagedestroy($imagemFundo);
  if (isset($fotoRedimensionada)) {
      imagedestroy($fotoRedimensionada);
  }

  $_SESSION['carteirinha'] = $caminhoImagem;

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
?>