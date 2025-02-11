<?php

if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

include_once("../service/connection_create.php");
include_once 'controller/controllerInfo.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Raia7 Turmas</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <link href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

  <style>
    .nav-tabs .nav-link:hover {
      background-color: rgb(223, 10, 10);
      color: white;
      border-color: rgb(223, 10, 10);
      transition: all 0.3s ease;
    }
  </style>

</head>

<?php include_once 'layout/header.php'; ?>

<?php include_once 'layout/aside.php'; ?>

<body>
  <div class="pagetitle">
    <h1>Turmas</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
        <li class="breadcrumb-item active">Turmas</li>
      </ol>
    </nav>
  </div>

  <main id="main" class="main">
    <section class="section">
      <div class="card">
        <div class="row">
          <div class="col-md-12">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs" id="turmasTab" role="tablist">
              <?php foreach ($turmas as $turmaId => $turma): ?>
                <li class="nav-item" role="presentation">
                  <a class="nav-link <?= $turmaId === array_key_first($turmas) ? 'active' : '' ?>"
                    id="tab-<?= $turmaId ?>"
                    data-toggle="tab"
                    href="#content-<?= $turmaId ?>"
                    role="tab"
                    aria-controls="content-<?= $turmaId ?>"
                    aria-selected="<?= $turmaId === array_key_first($turmas) ? 'true' : 'false' ?>">
                    <?= htmlspecialchars($turma['horario']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>

            <!-- Conteúdo das Tabs -->
            <div class="tab-content" id="turmasTabContent">
              <?php foreach ($turmas as $turmaId => $turma): ?>
                <div class="tab-pane fade <?= $turmaId === array_key_first($turmas) ? 'show active' : '' ?>"
                  id="content-<?= $turmaId ?>"
                  role="tabpanel"
                  aria-labelledby="tab-<?= $turmaId ?>">
                  <div class="card-body">
                    <table class="table mt-5">
                      <thead>
                        <tr>
                          <th>Nome</th>
                          <th>CPF</th>
                          <th>Contato</th>
                          <th>Data de Nascimento</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($turma['alunos'])): ?>
                          <?php foreach ($turma['alunos'] as $aluno): ?>
                            <tr>
                              <td><?= htmlspecialchars($aluno['nome']) ?></td>
                              <td><?= htmlspecialchars($aluno['cpf']) ?></td>
                              <td><?= htmlspecialchars($aluno['contato']) ?></td>
                              <td><?= htmlspecialchars($aluno['data_nascimento']) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4">Nenhum aluno encontrado nesta turma.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include_once 'layout/footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
</body>

</html>