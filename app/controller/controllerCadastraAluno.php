<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

include_once "../service/connection_create.php";

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$contato = $_POST['contato'];
$data_nascimento = $_POST['data_nascimento'];
$endereco = $_POST['endereco'];
$cep = $_POST['cep'];
$foto = $_FILES['foto'];
$obs_saude = $_POST['obs_saude'];

if (empty($nome) || empty($cpf) || empty($contato) || empty($data_nascimento) || empty($endereco) || empty($cep) || empty($obs_saude) || empty($foto['name'])) {
  echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios!']);
  exit();
}

$cpf = str_replace(['.', '-'], '', $cpf);

$conn = conexao_pdo();
$sql = "SELECT COUNT(*) AS count FROM user WHERE cpf = :cpf";
$stm = $conn->prepare($sql);
$stm->bindParam(':cpf', $cpf);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
  echo json_encode(['status' => 'error', 'message' => 'CPF já cadastrado. Basta acessar o sistema!']);
  exit();
}

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
  $foto = $_FILES['foto'];

  $tamanhoMaximo = 2 * 1024 * 1024;
  $tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];

  if ($foto['size'] > $tamanhoMaximo) {
    echo json_encode(['status' => 'error', 'message' => 'A foto excede o tamanho máximo permitido (2MB).']);
    exit();
  }

  if (!in_array($foto['type'], $tiposPermitidos)) {
    echo json_encode(['status' => 'error', 'message' => 'Somente imagens PNG ou JPEG são permitidas.']);
    exit();
  }

  $pastaAnexos = __DIR__ . '/../anexo_alunos/' . $cpf;
  if (!file_exists($pastaAnexos)) {
    mkdir($pastaAnexos, 0777, true);
  }

  $nomeImagem = uniqid('foto_') . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
  $caminhoAbsolutoImagem = $pastaAnexos . '/' . $nomeImagem;
  $caminhoRelativoImagem = 'anexo_alunos/' . $cpf . '/' . $nomeImagem;

  if (move_uploaded_file($foto['tmp_name'], $caminhoAbsolutoImagem)) {
    $path_foto = $caminhoRelativoImagem;
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a foto. Verifique as permissões do diretório.']);
    exit();
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Erro no upload da foto.']);
  exit();
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
$stm->bindParam(':nome', $nome);
$stm->bindParam(':cpf', $cpf);
$stm->bindParam(':escola', $_POST['escola']);
$stm->bindParam(':serie_escola', $_POST['serie_escola']);
$stm->bindParam(':contato', $contato);
$stm->bindParam(':cep', $cep);
$stm->bindParam(':endereco', $endereco);
$stm->bindParam(':data_nascimento', $data_nascimento);
$stm->bindParam(':obs_saude', $obs_saude);
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
    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);
    exit();
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao criar usuário. Entre em contato com o administrador.']);
    exit();
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar aluno. Entre em contato com o administrador.']);
  exit();
}
