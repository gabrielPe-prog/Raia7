<?php

if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Recife');

include_once 'service/checkAccess.php';
include_once 'controller/controllerCarteirinha.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Carteirinha R7</title>
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

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Carteirinha</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
          <li class="breadcrumb-item active">Carteirinha</li>
        </ol>
      </nav>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body mt-4">
            <?php if ($_SESSION['carteirinha']) : ?>

              <div class="text-center mb-4">
                <img src="<?php echo $_SESSION['carteirinha']; ?>" alt="Carteirinha" class="img-fluid" style="max-width: 100%; height: auto;">
              </div>

              <div class="text-center">
                <a href="<?php echo $_SESSION['carteirinha']; ?>" download="carteirinha_<?php echo $_SESSION['cpf']; ?>.png" class="btn btn-primary">
                  <i class="bi bi-download"></i> Baixar Carteirinha
                </a>
              </div>
            <?php else : ?>
              <div class="alert alert-warning mt-4 mb-1" role="alert">
                Nenhuma carteirinha gerada.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <script src="assets/js/main.js"></script>
</body>

</html>