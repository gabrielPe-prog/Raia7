<?php
session_start();
date_default_timezone_set('America/Recife');

include_once "../service/connection_create.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cpf = $_POST['cpf'];

  // Verifica se o CPF já existe
  $conn = conexao_pdo();
  $sql = "SELECT COUNT(*) AS count FROM user WHERE cpf = :cpf";
  $stm = $conn->prepare($sql);
  $stm->bindParam(':cpf', $cpf);
  $stm->execute();
  $result = $stm->fetch(PDO::FETCH_ASSOC);

  if ($result['count'] > 0) {
    $_SESSION['user_criado'] = true;
    header("location: ../formCadastro.php");
    exit();
  }

  // Processamento do upload da foto
  $path_foto = null;
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = $_FILES['foto'];

    $tamanhoMaximo = 2 * 1024 * 1024; // 2MB
    $tiposPermitidos = ['image/jpeg', 'image/png'];

    if ($foto['size'] > $tamanhoMaximo) {
      $_SESSION['erro_upload'] = "O arquivo é muito grande. O tamanho máximo permitido é 2MB.";
      header("location: ../formCadastro.php");
      exit();
    }

    if (!in_array($foto['type'], $tiposPermitidos)) {
      $_SESSION['erro_upload'] = "Somente arquivos JPEG e PNG são permitidos.";
      header("location: ../formCadastro.php");
      exit();
    }

    if (!getimagesize($foto['tmp_name'])) {
      $_SESSION['erro_upload'] = "O arquivo enviado não é uma imagem válida.";
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
      $_SESSION['erro_upload'] = "Erro ao salvar o arquivo. Tente novamente.";
      header("location: ../formCadastro.php");
      exit();
    }
  }

  // Busca o ID da turma
  $select_turma = "SELECT id_turma FROM turmas WHERE horario = :horario";
  $select_turma_stmt = $conn->prepare($select_turma);
  $select_turma_stmt->bindParam(':horario', $_POST['turma']);
  $select_turma_stmt->execute();
  $result_turma = $select_turma_stmt->fetch(PDO::FETCH_ASSOC);

  if (!$result_turma) {
    $_SESSION['erro_cadastro'] = "Turma não encontrada.";
    header("location: ../formCadastro.php");
    exit();
  }

  // Hash da senha (usando o CPF como senha inicial)
  $senha_hash = password_hash($cpf, PASSWORD_DEFAULT);

  // Inserção do aluno
  $sql = "INSERT INTO alunos(
              nome,
              id_turma,
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
              :id_turma,
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
  $stm->bindParam(':id_turma', $result_turma['id_turma']);
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

  if (!$retorno) {
    error_log("Erro ao inserir aluno: " . print_r($stm->errorInfo(), true));
    $_SESSION['erro_cadastro'] = "Erro ao inserir aluno no banco de dados.";
    header("location: ../formCadastro.php");
    exit();
  }

  // Busca o último aluno inserido
  $select_last = "SELECT * FROM alunos ORDER BY created_at DESC LIMIT 1";
  $select_last_stm = $conn->prepare($select_last);
  $select_last_stm->execute();
  $result = $select_last_stm->fetch(PDO::FETCH_ASSOC);

  if ($result) {
    // Cria o usuário associado
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

    if (!$cria_user_stm->execute()) {
      error_log("Erro ao criar usuário: " . print_r($cria_user_stm->errorInfo(), true));
      $_SESSION['erro_cadastro'] = "Erro ao criar usuário.";
      header("location: ../formCadastro.php");
      exit();
    }

    $_SESSION['logged_in'] = TRUE;
    $_SESSION['nome'] = $result['nome'];
    $_SESSION["nivel"] = "aluno";

    header('Location: ../paginaInicial.php');
    exit();
  } else {
    $_SESSION['erro_cadastro'] = "Erro ao buscar aluno recém-cadastrado.";
    header("location: ../formCadastro.php");
    exit();
  }
}
