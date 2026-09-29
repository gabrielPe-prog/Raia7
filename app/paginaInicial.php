<?php

  if (!isset($_SESSION)){
  session_start();
  }
  date_default_timezone_set('America/Recife');

  include_once 'service/checkAccess.php' ;
  include_once 'controller/controllerInfo.php' ;
  ?>

  <!DOCTYPE html>
  <html lang="pt-BR">

  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Raia7 AquaManager</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/favicon/favicon-96x96.png" rel="icon">
    <link href="assets/favicon/apple-touch-icon.png" rel="apple-touch-icon">

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

    <link href="assets/css/raia-theme.css" rel="stylesheet">
  <script src="assets/js/raia-ui.js" defer></script>
</head>

  <?php include_once 'layout/header.php'; ?>

  <?php include_once 'layout/aside.php'; ?>

  <body>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['update_status']) && isset($_SESSION['update_message'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['update_status']; ?>',
                title: '<?php echo ($_SESSION["update_status"] === "success") ? "Sucesso!" : "Erro!"; ?>',
                text: '<?php echo $_SESSION["update_message"]; ?>',
            });

            <?php

            unset($_SESSION['update_status']);
            unset($_SESSION['update_message']);
            ?>
        <?php endif; ?>
    </script>

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Informações Gerais</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="paginaInicial.php">Início</a></li>
            <li class="breadcrumb-item active">Informações Gerais</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="welcome-panel">
          <div><span class="eyebrow">ACADEMIA AQUÁTICA RAIA7</span>
          <h2>Um novo dia.<br>Mais um mergulho.</h2>
          <p>Bem-vindo ao seu espaço na Raia7. Tudo o que você precisa para seguir em movimento está aqui.</p>
          <a class="btn btn-light" href="<?= $_SESSION['nivel'] == 1 ? 'alunos.php' : 'infoAlunos.php' ?>"> <?= $_SESSION['nivel'] == 1 ? 'Gerenciar alunos' : 'Ver meus dados' ?> <i class="bi bi-arrow-up-right ms-2"></i></a></div>
          <div class="water-lines" aria-hidden="true"></div>
        </div>
        <div class="section-heading"><div><span class="eyebrow">NO SEU RITMO</span><h2>O que vamos fazer hoje?</h2></div><span>Acesso rápido</span></div>
        <div class="quick-grid">
        <?php
          $shortcuts = $_SESSION['nivel'] == 1
            ? [['alunos.php', 'bi-people', 'Alunos', 'Acompanhe matrículas e informações dos seus alunos.'], ['turmas.php', 'bi-calendar-week', 'Turmas', 'Consulte os horários e os alunos de cada turma.'], ['financeiro.php', 'bi-wallet2', 'Financeiro', 'Organize pagamentos e acompanhe as mensalidades.']]
            : [['infoAlunos.php', 'bi-person', 'Meus dados', 'Consulte suas informações e dados de matrícula.'], ['carteirinha.php', 'bi-person-vcard', 'Minha carteirinha', 'Sua identificação de aluno sempre à mão.'], ['infoPagAlunos.php', 'bi-wallet2', 'Mensalidades', 'Acompanhe seu histórico de pagamentos.']];
          foreach ($shortcuts as [$url, $icon, $label, $description]): ?>
          <a class="quick-card" href="<?= $url ?>"><span class="quick-icon"><i class="bi <?= $icon ?>"></i></span><h3><?= $label ?></h3><p><?= $description ?></p><span class="quick-link">Acessar <i class="bi bi-arrow-right"></i></span></a>
        <?php endforeach; ?>
        </div>
      </section>


    </main><!-- End #main -->

    <?php include_once 'layout/footer.php'; ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

  </body>

  </html>