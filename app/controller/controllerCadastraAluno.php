<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

include_once "../service/connection_create.php";

$cpf = str_replace(['.', '-'], '', $_POST['cpf']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $conn = conexao_pdo();
  $sql = "SELECT COUNT(*) AS count FROM user WHERE cpf = :cpf";
  $stm = $conn->prepare($sql);
  $stm->bindParam(':cpf', $cpf);
  $stm->execute();
  $result = $stm->fetch(PDO::FETCH_ASSOC);

  if ($result['count'] > 0) {
    $_SESSION['user_criado'] = 1;
    header("location: ../formCadastro.php");
    exit();
  }

  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = $_FILES['foto'];

    $tamanhoMaximo = 2 * 1024 * 1024;
    $tiposPermitidos = ['image/jpeg', 'image/png'];

    if ($foto['size'] > $tamanhoMaximo) {
      $_SESSION['erro_upload'] = "tamanho";
      header("location: ../formCadastro.php");
      exit();
    }

    if (!in_array($foto['type'], $tiposPermitidos)) {
      $_SESSION['erro_upload'] = "extensao";
      header("location: ../formCadastro.php");
      exit();
    }

    $pastaAnexos = '../anexo_alunos/' . $cpf;
    if (!file_exists($pastaAnexos)) {
      mkdir($pastaAnexos, 0777, true);
    }

    $nomeImagem = uniqid('foto_') . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
    $caminhoImagem = $pastaAnexos . '/' . $nomeImagem;

    if (move_uploaded_file($foto['tmp_name'], $caminhoImagem)) {
      $path_foto = $caminhoImagem;
    } else {
      $_SESSION['erro_upload'] = "salvar";
      header("location: ../formCadastro.php");
      exit();
    }
  } else {
    $path_foto = null;
  }

  $senha_hash = password_hash($cpf, PASSWORD_DEFAULT);

  $sql = "INSERT INTO alunos(
                nome,
                cpf,
                escola,
                serie_escola,
                contato,
                cep,
                endereco,
                data_nascimento,
                obs_saude,
                path_foto) 
            VALUES (
                :nome,
                :cpf,
                :escola,
                :serie_escola,
                :contato,
                :cep,
                :endereco,
                :data_nascimento,
                :obs_saude,
                :path_foto)";

  $stm = $conn->prepare($sql);
  $stm->bindParam(':nome', $_POST['nome']);
  $stm->bindParam(':cpf', $cpf);
  $stm->bindParam(':escola', $_POST['escola']);
  $stm->bindParam(':serie_escola', $_POST['serie_escola']);
  $stm->bindParam(':contato', $_POST['contato']);
  $stm->bindParam(':cep', $_POST['cep']);
  $stm->bindParam(':endereco', $_POST['endereco']);
  $stm->bindParam(':data_nascimento', $_POST['data_nascimento']);
  $stm->bindParam(':obs_saude', $_POST['obs_saude']);
  $stm->bindParam(':path_foto', $path_foto);

  $retorno = $stm->execute();

  if ($retorno) {
    $select_last = "SELECT * FROM alunos ORDER BY created_at DESC LIMIT 1";
    $select_last_stm = $conn->prepare($select_last);
    $select_last_stm->execute();
    $result = $select_last_stm->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $cria_user = "INSERT INTO user(
                nome,
                senha,
                cpf
            ) VALUES (
                :nome,
                :senha,
                :cpf
            )";

      $cria_user_stm = $conn->prepare($cria_user);
      $cria_user_stm->bindParam(':nome', $result['nome']);
      $cria_user_stm->bindParam(':senha', $senha_hash);
      $cria_user_stm->bindParam(':cpf', $cpf);
      $cria_user_stm->execute();
    }

    if ($cria_user_stm) {
      $_SESSION['logged_in'] = TRUE;
      $_SESSION['nome'] = $result['nome'];
      $_SESSION["nivel"] = "aluno";

      $_SESSION['cadastro_feito'] = 1;
      header("location: ../paginaInicial.php");
      exit();
    } else {
      $_SESSION['erro_cadastro'] = 1;
      header("location: ../formCadastro.php");
      exit();
    }
  } else {
    $_SESSION['erro_cadastro'] = 1;
    header("location: ../formCadastro.php");
    exit();
  }
}
