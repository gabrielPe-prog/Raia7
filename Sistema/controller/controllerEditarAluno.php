<?php
if (!isset($_SESSION)){
  session_start();
}

date_default_timezone_set('America/Recife');

require_once "../service/connection_create.php";

$conn = conexao_pdo();

$nome_update = $_POST['nome_update'];
$turma_update = $_POST['turma_update'];
$cpf_update = $_POST['cpf_update'];
$pagamento_update = $_POST['pagamento_update'];
$escola_update = $_POST['escola_update'];
$serie_escola_update = $_POST['serie_escola_update'];
$contato_update =  $_POST['contato_update'];
$endereco_update = $_POST['endereco_update'];
$cep_update = $_POST['cep_update'];
$data_nascimento_update = $_POST['data_nascimento_update'];
$obs_saude_update = $_POST['obs_saude_update'];
$id_aluno = $_POST['id_aluno'];

$sql = "UPDATE alunos SET nome = :nome_update,
                             escola = :escola_update, 
                             turma = :turma_update,
                             cpf = :cpf_update,
                             serie_escola = :serie_escola_update,
                             ultimo_pagamento = :pagamento_update,
                             contato = :contato_update,
                             endereco = :endereco_update,
                             cep = :cep_update,
                             data_nascimento = :data_nascimento_update,
                             obs_saude = :obs_saude_update
                             WHERE id_aluno = :id_aluno";

$stmt = $conn->prepare($sql);

$stmt->bindParam(':nome_update', $nome_update);
$stmt->bindParam(':cpf_update', $cpf_update);
$stmt->bindParam(':turma_update', $turma_update);
$stmt->bindParam(':pagamento_update', $pagamento_update);
$stmt->bindParam(':escola_update', $escola_update);
$stmt->bindParam(':serie_escola_update', $serie_escola_update);
$stmt->bindParam(':contato_update', $contato_update);
$stmt->bindParam(':endereco_update', $endereco_update);
$stmt->bindParam(':cep_update', $cep_update);
$stmt->bindParam(':data_nascimento_update', $data_nascimento_update);
$stmt->bindParam(':obs_saude_update', $obs_saude_update);
$stmt->bindParam(':id_aluno', $id_aluno);

$retorno = $stmt->execute();

if ($retorno) {
    $_SESSION['updateSuccess'] = 1;
    echo "<script>alert('ATUALIZADO COM SUCESSO')</script>";
    header("location: ../alunos.php?success=true");
} else {
    $_SESSION['updateSuccess'] = 0;
    echo "<script>alert('NÃO FOI ATUALIZADO')</script>";
    header("location: ../alunos.php?success=false");
}