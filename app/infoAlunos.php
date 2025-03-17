<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

include_once 'service/checkAccess.php';
include_once 'controller/controllerInfoAluno.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Carteirinha R7</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <link href="assets/css/style.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<?php include_once 'layout/header.php'; ?>

<?php include_once 'layout/aside.php'; ?>

<body>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Informações do Aluno</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
                    <li class="breadcrumb-item active">Informações do Aluno</li>
                </ol>
            </nav>
        </div>

        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <!-- <button type="button" class="btn btn-primary float-end mt-3" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil"></i> Editar
                    </button> -->

                    <h5 class="card-title">Nome</h5>
                    <p class="card-text fs-5 mb-4"><?= $alunos['nome'] ?></p>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">CPF</h5>
                            <p class="card-text"><?= $alunos['cpf'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Escola</h5>
                            <p class="card-text"><?= !empty($alunos['escola']) ? $alunos['escola'] : 'Não Informado' ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">Série</h5>
                            <p class="card-text"><?= !empty($alunos['serie_escola']) ? $alunos['escola'] : 'Não Informado' ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Contato</h5>
                            <p class="card-text"><?= $alunos['contato'] ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">CEP</h5>
                            <p class="card-text"><?= $alunos['cep'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Endereço</h5>
                            <p class="card-text"><?= $alunos['endereco'] ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">Data de Nascimento</h5>
                            <p class="card-text"><?= $alunos['data_nascimento'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Observação sobre Saúde</h5>
                            <p class="card-text"><?= $alunos['obs_saude'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Informações</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="controller/controllerAtualizaDadosAluno.php" method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $alunos['nome'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $alunos['cpf'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="escola" class="form-label">Escola</label>
                                <input type="text" class="form-control" id="escola" name="escola" value="<?= $alunos['escola'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="serie_escola" class="form-label">Série</label>
                                <input type="text" class="form-control" id="serie_escola" name="serie_escola" value="<?= $alunos['serie_escola'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="contato" class="form-label">Contato</label>
                                <input type="text" class="form-control" id="contato" name="contato" value="<?= $alunos['contato'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="<?= $alunos['cep'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $alunos['endereco'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $alunos['data_nascimento'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="obs_saude" class="form-label">Observação sobre Saúde</label>
                                <textarea class="form-control" id="obs_saude" name="obs_saude"><?= $alunos['obs_saude'] ?></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="saveChanges()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveChanges() {
            const formData = new FormData(document.getElementById('editForm'));

            fetch('controller/updateAluno.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao salvar as alterações.'
                });
            });
        }
    </script>

</body>

</html>