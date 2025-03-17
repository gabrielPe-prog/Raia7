<?php
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once("../service/connection_create.php");

$conn = conexao_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $escola = $_POST['escola'];
    $serie_escola = $_POST['serie_escola'];
    $contato = $_POST['contato'];
    $cep = $_POST['cep'];
    $endereco = $_POST['endereco'];
    $data_nascimento = $_POST['data_nascimento'];
    $obs_saude = $_POST['obs_saude'];

    $sql = "UPDATE alunos SET nome = ?, cpf = ?, escola = ?, serie_escola = ?, contato = ?, cep = ?, endereco = ?, data_nascimento = ?, obs_saude = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $cpf, $escola, $serie_escola, $contato, $cep, $endereco, $data_nascimento, $obs_saude, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Aluno atualizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar aluno.']);
    }
}