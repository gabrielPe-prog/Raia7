<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

require_once("../service/connection_create.php");

$cpf = $_POST['cpf'];
$senha_digitada = $_POST['senha'];

$conn = conexao_pdo();

// Consulta para buscar dados do usuário
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

// Consulta para buscar o horário da turma
$horario_turma = "SELECT horario FROM turmas WHERE id_turma = :id_turma";
$stmt_horario = $conn->prepare($horario_turma);
$stmt_horario->bindParam(":id_turma", $acesso_usuario['id_turma']);
$stmt_horario->execute();
$horario_turma = $stmt_horario->fetch(PDO::FETCH_ASSOC);

if ($acesso_usuario && password_verify($senha_digitada, $acesso_usuario['senha'])) {
  // Dados do usuário
  $nome = $acesso_usuario['nome'];
  $cpf = $acesso_usuario['cpf'];
  $turma = $horario_turma['horario'];
  $foto = $acesso_usuario['path_foto']; // Caminho da foto no servidor

  // Carregar a imagem de fundo da carteirinha
  $imagemFundo = imagecreatefrompng('../assets/carteirinha/carteirinhaR7.png'); // Caminho da imagem de fundo

  // Definir a cor do texto
  $corTexto = imagecolorallocate($imagemFundo, 0, 0, 0); // Preto

  // Carregar a foto do aluno (se existir)
  if (file_exists($foto)) {
      $fotoAluno = imagecreatefromjpeg($foto);
      $fotoLargura = imagesx($fotoAluno);
      $fotoAltura = imagesy($fotoAluno);

      // Redimensionar a foto (opcional)
      $novaLargura = 304; // Largura desejada
      $novaAltura = 430;  // Altura desejada
      $fotoRedimensionada = imagecreatetruecolor($novaLargura, $novaAltura);
      imagecopyresampled($fotoRedimensionada, $fotoAluno, 0, 0, 0, 0, $novaLargura, $novaAltura, $fotoLargura, $fotoAltura);

      // Posicionar a foto na imagem de fundo
      $posicaoXFoto = 121;  // Posição X da foto
      $posicaoYFoto = 138; // Posição Y da foto
      imagecopy($imagemFundo, $fotoRedimensionada, $posicaoXFoto, $posicaoYFoto, 0, 0, $novaLargura, $novaAltura);
  }

  // Carregar a fonte TrueType (TTF)
  $fonte = __DIR__ . '/../assets/carteirinha/trebuc.ttf'; // Caminho para a fonte

  // Adicionar o nome do aluno
  $posicaoXNome = 740; // Posição X do nome
  $posicaoYNome = 290; // Posição Y do nome
  imagettftext($imagemFundo, 20, 0, $posicaoXNome, $posicaoYNome, $corTexto, $fonte, $nome);

  // Adicionar o CPF do aluno
  $posicaoXCPF = 740; // Posição X do CPF
  $posicaoYCPF = 390; // Posição Y do CPF
  imagettftext($imagemFundo, 20, 0, $posicaoXCPF, $posicaoYCPF, $corTexto, $fonte, $cpf);

  // Adicionar a turma do aluno
  $posicaoXTurma = 740; // Posição X da turma
  $posicaoYTurma = 500; // Posição Y da turma
  imagettftext($imagemFundo, 20, 0, $posicaoXTurma, $posicaoYTurma, $corTexto, $fonte, $turma);

  // Salvar a imagem gerada
  $caminhoImagem = '../carteirinhasCriadas/carteirinha_' . $cpf . '.png';
  imagepng($imagemFundo, $caminhoImagem);

  // Liberar memória
  imagedestroy($imagemFundo);
  if (isset($fotoRedimensionada)) {
      imagedestroy($fotoRedimensionada);
  }

  // Armazenar o caminho da carteirinha na sessão
  $_SESSION['carteirinha'] = $caminhoImagem;

  // Definir variáveis de sessão
  $_SESSION['logged_in'] = TRUE;
  $_SESSION['nome'] = $acesso_usuario["nome"];
  $_SESSION["nivel"] = $acesso_usuario["nivel"];
  $_SESSION["cpf"] = $acesso_usuario["cpf"];

  // Redirecionar para a página inicial
  header('Location: ../paginaInicial.php');
  exit;
} else {
  $_SESSION['erroLogin'] = 1;
  header('Location: ../index.php');
  exit;
}
?>