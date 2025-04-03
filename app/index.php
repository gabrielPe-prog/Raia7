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
      background-color: #f8f9fa;
    }
    
    @media (max-width: 768px) {
      body {
        background-image: url("assets/img/logo_mobile.png");
        background-size: cover;
      }
    }

    #logo_code {
      height: 80px;
      width: 85px;
    }

    #logo_r7 {
      height: 220px;
      width: 370px;
    }

    @media (max-width: 768px) {
      #logo_r7 {
        height: 150px;
        width: 250px;
      }

      #logo_code {
        height: 60px;
        width: 65px;
      }

      .section.register {
        padding: 20px;
      }

      .card {
        margin: 10px;
      }
    }
    
    .action-btn {
      padding: 8px 15px;
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.3s;
      display: inline-block;
      text-align: center;
      margin: 5px 0;
    }
    
    .btn-cadastro {
      background-color: #4154f1;
      color: white;
      border: 1px solid #4154f1;
    }
    
    .btn-cadastro:hover {
      background-color: #364af3;
      color: white;
    }
    
    .btn-senha {
      background-color: #4154f1;
      color: white;
      border: 1px solid #4154f1;
    }
    
    .btn-senha:hover {
      background-color: #364af3;
      color: white;
    }
    
    .action-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
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
  
  if (isset($_SESSION['update_status']) && isset($_SESSION['update_message'])) {
    $status = $_SESSION['update_status'];
    $message = $_SESSION['update_message'];
    
    echo "<script>
            Swal.fire({
                icon: '$status',
                title: '" . ($status == 'success' ? 'Sucesso!' : 'Erro!') . "',
                text: '$message'
            });
          </script>";
    
    unset($_SESSION['update_status']);
    unset($_SESSION['update_message']);
  }

  ?>

  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8 d-flex flex-column align-items-center justify-content-center">
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
                      <input type="text" name="cpf" class="form-control form-control-sm" id="cpf">
                    </div>

                    <div class="col-12">
                      <label for="senha" class="form-label">Senha</label>
                      <input type="password" name="senha" class="form-control form-control-sm" id="senha">
                    </div>

                    <div class="col-12">
                      <button class="btn btn-danger w-100 btn-sm" type="submit">Login</button>
                    </div>
                    
                    <div class="col-12 action-buttons">
                      <a href="formCadastro.php" class="action-btn btn-cadastro">
                        <i class="bi bi-person-plus"></i> Cadastrar
                      </a>
                      <a href="#" data-bs-toggle="modal" data-bs-target="#alterarSenhaModal" class="action-btn btn-senha">
                        <i class="bi bi-key"></i> Alterar Senha
                      </a>
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

  <!-- Modal para Alterar Senha -->
  <div class="modal fade" id="alterarSenhaModal" tabindex="-1" aria-labelledby="alterarSenhaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alterarSenhaModalLabel">Alterar Senha</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAlterarSenha" action="controller/controllerAtualizaSenha.php" method="POST">
          <input type="text" value="0" hidden name="tipo">
          <div class="mb-3">
            <label for="cpf" class="form-label">CPF para validação</label>
            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF" required onkeyup="CpfMask(this)">
            <div class="form-text">Digite seu CPF para confirmar sua identidade.</div>
          </div>
          <div class="mb-3">
            <label for="nova_senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
          </div>
          <div class="mb-3">
            <label for="confirma_senha" class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
            <div id="senhaHelp" class="form-text text-danger"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success" id="btnAlterarSenha">Alterar Senha</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

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
  
  <script>
    // Formatação de CPF
    document.getElementById('cpf').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 11) value = value.slice(0, 11);
      
      if (value.length > 9) {
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
      } else if (value.length > 6) {
        value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
      } else if (value.length > 3) {
        value = value.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');
      }
      
      e.target.value = value;
    });
    
    document.getElementById('cpf_modal').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 11) value = value.slice(0, 11);
      
      if (value.length > 9) {
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
      } else if (value.length > 6) {
        value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
      } else if (value.length > 3) {
        value = value.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');
      }
      
      e.target.value = value;
    });
  </script>

</body>

</html>