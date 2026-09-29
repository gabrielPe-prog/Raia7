<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

if (($_SESSION['logged_in'] ?? false) !== true) {
    header('Location: index.php');
    exit;
}
$_SESSION['profile_csrf'] ??= bin2hex(random_bytes(32));
include_once 'controller/controllerInfoAluno.php';
if (!$alunos) {
    http_response_code(404);
    exit('Cadastro não encontrado. Entre em contato com a secretaria.');
}
$profileEscape = fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Meus dados · Raia7</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="assets/favicon/favicon-96x96.png" rel="icon">
    <link href="assets/favicon/apple-touch-icon.png" rel="apple-touch-icon">

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
  <link href="assets/css/raia-theme.css" rel="stylesheet">
  <script src="assets/js/raia-ui.js" defer></script>
</head>

<body>
<?php include_once 'layout/header.php'; ?>

<?php include_once 'layout/aside.php'; ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Informações do Aluno</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="paginaInicial.php">Início</a></li>
                    <li class="breadcrumb-item active">Informações do Aluno</li>
                </ol>
            </nav>
      <?php $reportType = 'infoAlunos'; include __DIR__ . '/layout/export.php'; ?>
        </div>

        <?php if (!empty($_SESSION['profile_saved'])): unset($_SESSION['profile_saved']); ?>
        <div class="alert alert-success" role="status">Suas informações foram atualizadas com sucesso.</div>
        <?php endif; ?>
        <div class="container mt-4">
            <div class="card student-profile">
                <div class="card-body">
                    <button type="button" class="btn btn-primary float-end mb-3" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil me-2" aria-hidden="true"></i>Editar meus dados
                    </button>

                    <h5 class="card-title">Nome</h5>
                    <p class="card-text fs-5 mb-4"><?= $profileEscape($alunos['nome']) ?></p>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">CPF</h5>
                            <p class="card-text"><?= $profileEscape($alunos['cpf']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Escola</h5>
                            <p class="card-text"><?= $profileEscape($alunos['escola'] ?: 'Não informado') ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">Série</h5>
                            <p class="card-text"><?= $profileEscape($alunos['serie_escola'] ?: 'Não informado') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Contato</h5>
                            <p class="card-text"><?= $profileEscape($alunos['contato']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">CEP</h5>
                            <p class="card-text"><?= $profileEscape($alunos['cep']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Endereço</h5>
                            <p class="card-text"><?= $profileEscape($alunos['endereco']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">Data de Nascimento</h5>
                            <p class="card-text"><?= date('d-m-Y', strtotime($alunos['data_nascimento'])) ?></p>

                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Observação sobre Saúde</h5>
                            <p class="card-text"><?= $profileEscape($alunos['obs_saude']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Informações</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="form-text">Atualize seus dados pessoais. O CPF informado será usado no próximo login. Para alterar a turma, procure a secretaria.</p>
                        <form id="editForm" action="controller/controllerAtualizaDadosAluno.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $profileEscape($_SESSION['profile_csrf']) ?>">
                            <div class="mb-3">
                                <label for="profile_nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="profile_nome" name="nome" maxlength="255" required value="<?= $profileEscape($alunos['nome']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="profile_cpf" name="cpf" maxlength="14" inputmode="numeric" placeholder="000.000.000-00" required value="<?= $profileEscape($alunos['cpf']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_escola" class="form-label">Escola</label>
                                <input type="text" class="form-control" id="profile_escola" name="escola" maxlength="50" value="<?= $profileEscape($alunos['escola']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_serie_escola" class="form-label">Série</label>
                                <input type="text" class="form-control" id="profile_serie_escola" name="serie_escola" maxlength="15" value="<?= $profileEscape($alunos['serie_escola']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_contato" class="form-label">Contato</label>
                                <input type="text" class="form-control" id="profile_contato" name="contato" maxlength="15" inputmode="tel" autocomplete="tel-national" placeholder="(81) 99999-9999" required value="<?= $profileEscape($alunos['contato']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="profile_cep" name="cep" maxlength="9" inputmode="numeric" autocomplete="postal-code" placeholder="00000-000" required value="<?= $profileEscape($alunos['cep']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="profile_endereco" name="endereco" maxlength="255" required value="<?= $profileEscape($alunos['endereco']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="profile_data_nascimento" name="data_nascimento" required min="1900-01-01" max="<?= date('Y-m-d') ?>" value="<?= $profileEscape($alunos['data_nascimento']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_obs_saude" class="form-label">Observação sobre Saúde</label>
                                <textarea class="form-control" id="profile_obs_saude" name="obs_saude" maxlength="1000"><?= $profileEscape($alunos['obs_saude']) ?></textarea>
                            </div>
                        <div id="profileStatus" role="status" aria-live="polite"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" form="editForm" id="profileSave" class="btn btn-primary">Salvar alterações</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/student-profile.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>