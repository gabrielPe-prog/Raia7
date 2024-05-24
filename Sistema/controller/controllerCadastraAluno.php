<?php
if (!isset($_SESSION)){
  session_start();
}
date_default_timezone_set('America/Recife');

include_once "../service/connection_create.php";

$cpf = $_POST['cpf'];

function cpfExists($cpf) {
  $conn = conexao_pdo();
  $sql = "SELECT COUNT(*) AS count FROM alunos WHERE cpf = :cpf LIMIT 1";
  $stm = $conn->prepare($sql);
  $stm->bindParam(':cpf', $cpf);
  $stm->execute();
  $result = $stm->fetch(PDO::FETCH_ASSOC);
  return $result['count'] > 0;
}

if (cpfExists($cpf)) {
  echo "<script>
              Swal.fire({
                title: 'Falha ao Cadastrar',
                text: 'CPF já utilizado para cadastro',
                icon: 'error',
                confirmButtonText: 'Fechar'
              })
        </script>";
  header("location: ../formCadastro.php");
  exit();
}

    $nome = $_POST['nome'];
    $escola = $_POST['escola'];
    $serie_escola = $_POST['serie_escola'];
    $contato =  $_POST['contato'];
    $cep = $_POST['cep'];
    $endereco = $_POST['endereco'];
    $data_nascimento = $_POST['data_nascimento'];
    $obs_saude = $_POST['obs_saude'];

$diretorio_foto = trim($cpf);
$diretorio_foto = str_replace(array('.','-','/'), "", $diretorio_foto);

$foto_name = $_FILES['path_foto']['name'];
$foto_tmp = $_FILES['path_foto']['tmp_name'];
$foto_size = $_FILES['path_foto']['size'];
$foto_type = $_FILES['path_foto']['type'];

if(empty($foto_name)) { 
  $path_foto = NULL;
} else {
  $path_foto = '../anexoFoto/'.$diretorio_foto.'/'.$foto_name;
}

$local_foto = '../anexoFoto/'.$diretorio_foto.'/'.$foto_name;

    $conn = conexao_pdo(); 
    
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
    $stm->bindParam(':escola', $escola);
    $stm->bindParam(':serie_escola', $serie_escola);
    $stm->bindParam(':contato', $contato);
    $stm->bindParam(':cep', $cep);
    $stm->bindParam(':endereco', $endereco);
    $stm->bindParam(':data_nascimento', $data_nascimento);
    $stm->bindParam(':obs_saude', $obs_saude);
    $stm->bindParam(':path_foto', $path_foto);

    
    $retorno = $stm->execute();

if($retorno) {

  if ($foto_type !== "image/jpeg") {
    echo "<script>
              Swal.fire({
                title: 'Falha ao Cadastrar',
                text: 'Foto na extenção errada',
                icon: 'error',
                confirmButtonText: 'Fechar'
              })
          </script>";
    header("location: ../formCadastro.php");
    exit();
  }

  if($foto_name !== '') {

    if (!file_exists("../anexoFoto/$diretorio_foto")){
      mkdir ("../anexoFoto/$diretorio_foto");
      chmod ("../anexoFoto/$diretorio_foto", 0775); 
    }
    move_uploaded_file($foto_tmp, $local_foto);
  }
  echo "<script>alert('Tudo Certo');</script>";
header("location: ../index.php");
}

else{
    header("location: ../index.php");
}