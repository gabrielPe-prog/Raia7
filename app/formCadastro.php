<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Formulário de Cadastro</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
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
      height: 150px;
      width: 300px;
    }
  </style>

</head>

<body>

  <?php

  if (isset($_SESSION['user_criado'])) {
    if ($_SESSION['user_criado'] == 1) {
      echo '<script>
                  Swal.fire({
                      icon: "warning",
                      title: "Usuário já criado!",
                      text: "Basta acessar o sistema!",
                  });
                </script>';
    }
    unset($_SESSION['user_criado']);
  }

  if (isset($_SESSION['erro_cadastro'])) {
    if ($_SESSION['erro_cadastro'] == 1) {
      echo '<script>
                  Swal.fire({
                      icon: "error",
                      title: "Cadastro não realizado!",
                      text: "entre em contato com o administrador!",
                  });
                </script>';
    }
    unset($_SESSION['erro_cadastro']);
  }

  if (isset($_SESSION['erro_upload'])) {
    switch ($_SESSION['erro_upload']) {
      case 'tamanho':
        echo '<script>
                  Swal.fire({
                      icon: "error",
                      title: "Tamanho inválido!",
                      text: "Diminua o tamanho do arquivo!",
                  });
                </script>';
        break;

      case 'extensao':
        echo '<script>
                  Swal.fire({
                      icon: "error",
                      title: "Extensão inválida!",
                      text: "Somente imagens PNG ou JPGEG são permitidas!",
                  });
                </script>';
        break;

      case 'salvar':
        echo '<script>
                  Swal.fire({
                      icon: "error",
                      title: "Erro ao salvar!",
                      text: "entre em contato com o administrador!",
                  });
                </script>';
        break;
    }
    unset($_SESSION['erro_upload']);
  }

  ?>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-2">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">

                  <div class="d-flex justify-content-center">
                    <img id="logo_r7" src="assets/img/logoR7.png" alt="">
                  </div>
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Realizar Cadastro</h5>
                    <p class="text-center small">Preencha os campos para realizar o cadastro</p>
                  </div>

                  <form class="row g-3" method="POST" action="controller/controllerCadastraAluno.php"
                    onsubmit="validaCadastro(this)" enctype="multipart/form-data">
                    <div class="row mb-4">
                      <div class="col-6">
                        <label for="nome" class="form-label">Nome Completo*</label>
                        <input type="text" class="form-control" id="nome" name="nome">
                      </div>
                      <div class="col-6">
                        <label for="cpf" class="form-label">CPF*</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="11">
                      </div>
                    </div>

                    <div class="row mb-4">
                      <p>*Caso não esteja na escola, deixar o campo em branco</p>
                      <div class="col-8">
                        <label for="escola" class="form-label">Escola</label>
                        <input type="text" class="form-control" id="escola" name="escola">
                      </div>
                      <div class="col-4">
                        <label for="serie_escola" class="form-label">Série na Escola</label>
                        <input type="text" class="form-control" id="serie_escola" name="serie_escola">
                      </div>
                    </div>

                    <div class="row mb-4">
                      <div class="col-6">
                        <label for="contato" class="form-label">Contato*</label>
                        <input type="text" class="form-control" id="contato" name="contato" oninput="celularMask(this)"
                          maxlength="15">
                      </div>
                      <div class="col-6">
                        <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                      </div>
                    </div>

                    <div class="row mb-4">
                      <div class="col-8">
                        <label for="endereco" class="form-label">Endereço*</label>
                        <input type="text" class="form-control" id="endereco" name="endereco">
                      </div>
                      <div class="col-4">
                        <label for="cep" class="form-label">CEP*</label>
                        <input type="text" class="form-control" id="cep" name="cep" oninput="CepMask(this)"
                          maxlength="8">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12 mb-4">
                        <label for="formFileSm" class="form-label">Adicione aqui a uma foto para carteirinha</label>
                        <input class="form-control form-control-sm" id="formFileSm" type="file" name="foto">
                      </div>
                    </div>

                    <div class="row mb-4">
                      <div class="col-12">
                        <label for="obs_saude" class="form-label">Observações Médicas*</label>
                        <input type="text" class="form-control" id="obs_saude" name="obs_saude">
                      </div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Realizar Cadastro</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Você já tem um cadastro? <a href="index.php">Faça Login</a></p>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    function applyMask(input, pattern) {
      const value = input.value.replace(/\D/g, '');
      input.value = pattern(value);
    }

    const masks = {
      cep: (value) => value.replace(/(\d{5})(\d{3})/, '$1-$2'),
      cpf: (value) => value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4'),
      celular: (value) => {
        const celular = value.slice(0, 11);
        if (celular.length === 11) {
          return `(${celular.slice(0, 2)}) ${celular.slice(2, 7)}-${celular.slice(7)}`;
        } else if (celular.length >= 7) {
          return `(${celular.slice(0, 2)}) ${celular.slice(2, 7)}-${celular.slice(7, 11)}`;
        } else if (celular.length >= 2) {
          return `(${celular.slice(0, 2)}) ${celular.slice(2)}`;
        } else {
          return `(${celular}`;
        }
      },
    };

    document.getElementById("cep").addEventListener("input", (e) => applyMask(e.target, masks.cep));
    document.getElementById("cpf").addEventListener("input", (e) => applyMask(e.target, masks.cpf));
    document.getElementById("contato").addEventListener("input", (e) => applyMask(e.target, masks.celular));

    function showAlert(message, icon = 'error') {
      Swal.fire({
        icon: icon,
        title: 'Campos obrigatórios em branco',
        text: message,
        confirmButtonColor: '#3085d6',
      });
    }

    function validateFields() {
      const fields = [{
          id: "nome",
          message: "Campo Nome em Branco"
        },
        {
          id: "cpf",
          message: "Campo CPF em Branco"
        },
        {
          id: "contato",
          message: "Campo Contato em Branco"
        },
        {
          id: "data_nascimento",
          message: "Campo Data de Nascimento em Branco"
        },
        {
          id: "endereco",
          message: "Campo Endereço em Branco"
        },
        {
          id: "cep",
          message: "Campo CEP em Branco"
        },
        {
          id: "obs_saude",
          message: "Campo Observações Médicas em Branco"
        },
      ];

      let isValid = true;

      fields.forEach((field) => {
        const input = document.getElementById(field.id);
        if (!input.value.trim()) {
          showAlert(field.message);
          isValid = false;
        }
      });

      return isValid;
    }

    document.getElementById("cadastroForm").addEventListener("submit", (e) => {
      if (!validateFields()) {
        e.preventDefault();
      }
    });
  </script>

</body>

</html>