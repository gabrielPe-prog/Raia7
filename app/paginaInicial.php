<?php

  if (!isset($_SESSION)){
  session_start();
  }
  date_default_timezone_set('America/Recife');

  include_once 'service/checkAccess.php' ;
  include_once 'controller/controllerInfo.php' ;
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Raia7 AquaManager</title>
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
            <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
            <li class="breadcrumb-item active">Informações Gerais</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body pt-4">
                <div class="row align-items-center">
                  <div class="col-md-12 text-center">
                    <h3 class="card-title pb-0 mb-3 text-primary">Bem-vindo à Academia Aquática R7!</h3>

                    <p class="fs-5"><i class="bi bi-person-badge me-2 text-secondary"></i>Ei, nadador(a)! Seu portal pessoal está pronto para você.</p>

                    <p class="fs-5"><i class="bi bi-info-circle me-2 text-secondary"></i>Aqui você encontra tudo sobre sua vida na piscina: carteirinha digital, informações sobre aulas e controle de mensalidades.</p>

                    <p class="fs-5"><i class="bi bi-emoji-smile me-2 text-secondary"></i>Mergulhe de cabeça na praticidade! Com o Sistema R7, você foca no que importa: nadar e se divertir, enquanto nós cuidamos da burocracia.</p>

                  </div>
                </div>
              </div>
            </div>
          </div>
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