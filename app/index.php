<?php
if (!isset($_SESSION)) {
  session_start();
}
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background-image: url("assets/img/bg.png");
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
    }

    #logo_code {
      height: 80px;
      width: 85px;
    }

    #logo_r7 {
      height: 220px;
      width: 370px;
    }
  </style>

</head>

<body>

  <?php

  if (isset($_SESSION['cadastro_feito'])) {
    if ($_SESSION['cadastro_feito'] == 1) {
      echo '<script>
              Swal.fire({
                  icon: "success",
                  title: "Usuário Criado!",
                  text: "Faça o Login e Aproveite o sistema!",
              });
            </script>';
    }
    unset($_SESSION['cadastro_feito']);
  }

  if (isset($_SESSION['erroLogin'])) {
    if ($_SESSION['erroLogin'] == 1) {
      echo '<script>
                  Swal.fire({
                      icon: "error",
                      title: "Senha ou CPF incorretos!",
                      text: "Tente novamente!",
                  });
                </script>';
    }
    unset($_SESSION['erroLogin']);
  }

  ?>

  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-center py-4">
                    <img id="logo_r7" src="assets/img/logoR7.png" alt="">
                  </div>

                  <div class="">
                    <h5 class="card-title text-center pb-0 fs-4">Faça Login no Sistema</h5>
                    <p class="text-center small">Use seu nome de usuário e senha para acessar</p>
                  </div>

                  <form action="controller/controllerLogin.php" method="POST" class="row g-3">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">CPF do Usuário</label>
                      <input type="text" name="cpf" class="form-control" id="cpf">
                    </div>

                    <div class="col-12">
                      <label for="senha" class="form-label">Senha</label>
                      <input type="password" name="senha" class="form-control" id="senha">
                    </div>

                    <div class="col-12">
                      <button class="btn btn-danger w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Não tem Acesso? <a href="formCadastro.php">Realizar Cadastro</a></p>
                    </div>
                  </form>

                  <div class="credits mt-4 text-center">
                    Desenvolvido por <img id="logo_code" src="assets/img/Codewave-PNG-Preto.png">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

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