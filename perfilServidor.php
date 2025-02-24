<?php 
if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

require_once 'service/conection_create.php';
require_once 'service/checkAccess.php';
require_once 'controller/controllerInfoServidor.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">  
  <title>Sistema de Gestão e Cadastro de Artistas</title>
  <meta content="Sistema de Gestão e Cadastro de Artistas" name="description">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  
  <meta name="author" content="NETTLINK - Soluções em Tecnologia">

  <!-- Favicons -->
 <link rel="shortcut icon" href="templates_painel/assets/img/BrasaoPMC.png" type="image/x-icon">
  <link href="templates_painel/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="templates_painel/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="templates_painel/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="templates_painel/assets/css/style.css" rel="stylesheet"> 

<!-- SCRIPT JS FILE-->
    <script src="templates_painel/assets/js/jquery-3.2.1.min.js"></script>
    <script src="templates_painel/assets/js/HabilitarCampos.js"></script>
    <script src="templates_painel/assets/js/validacao_cadastro.js"></script>
    <script src="templates_painel/assets/js/cep.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</head>

<body>

  <!-- ======= Header ======= -->
  <?php include_once 'view/layout/header.php'; ?>
 <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include_once 'view/layout/aside.php'; ?>
  <!-- End Sidebar--> 

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Informações do(a) Servidor(a)</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="painel.php">Home</a></li>
          <li class="breadcrumb-item">Informações do(a) Servidor(a)</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row"> 
          <div class="col-xl-7">
          <div class="card">
            <div class="card-body pt-3">
                <div class="tab-pane fade show active profile-overview" id="profile-pessoais">
                  <div class="row">
                  <?php

                    echo '<img src="assets/carteirinha/geradas/' . $_SESSION["cpf_servidor"] . '.jpg">';

                  ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
 
<!-- ======= Footer ======= -->
<?php include_once 'view/layout/footer.php'; ?>
  <!-- End Footer --> 

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="templates_painel/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="templates_painel/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="templates_painel/assets/vendor/chart.js/chart.umd.js"></script>
  <script src="templates_painel/assets/vendor/echarts/echarts.min.js"></script>
  <script src="templates_painel/assets/vendor/quill/quill.min.js"></script>
  <script src="templates_painel/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="templates_painel/assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="templates_painel/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="templates_painel/assets/js/main.js"></script>

</body>

</html>