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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


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

  <main>
    <div class="container px-3 py-3">

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
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.querySelector('form').addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const response = await fetch('controller/controllerCadastraAluno.php', {
          method: 'POST',
          body: formData,
        });

        const result = await response.json();

        if (result.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: result.message,
          }).then(() => {
            window.location.href = '../index.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: result.message,
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Ocorreu um erro ao processar a requisição.',
        });
      }
    });
  </script>

  <body>

</html>