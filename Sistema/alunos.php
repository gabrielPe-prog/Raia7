<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

include_once 'service/checkAccess.php';
include_once 'controller/controllerVisualizaAlunos.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Raia7 Alunos</title>
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

  <link href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">

</head>

  <?php include_once 'layout/header.php'; ?>

  <?php include_once 'layout/aside.php'; ?>

  <body>

    <div class="pagetitle">
      <h1>Alunos Matriculados</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
          <li class="breadcrumb-item active">Alunos Matriculados</li>
        </ol>
      </nav>
    </div>

    <main id="main" class="main">
    <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Tabela de ALunos</h5>

                <button type="button" class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#aluno">
                  Adicionar Aluno
                </button>

                <div class="modal fade" id="aluno" tabindex="-1" aria-labelledby="alunoLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="alunoLabel">Novo Aluno</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form class="row g-3" method="POST" action="controller/controllerAdicionaAluno.php">
                          <div class="col-md-6">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome">
                          </div>
                          <div class="col-md-6">
                            <label for="escola" class="form-label">Escola</label>
                            <input type="text" class="form-control" id="escola" name="escola">
                          </div>
                          <div class="col-md-6">
                            <label for="serie_escola" class="form-label">Série</label>
                            <input type="text" class="form-control" id="serie_escola" name="serie_escola">
                          </div>
                          <div class="col-md-6">
                            <label for="contato" class="form-label">Contato</label>
                            <input type="text" class="form-control" id="contato" name="contato" oninput="celularMask(this)">
                          </div>
                          <div class="col-md-9">
                            <label for="endereco" class="form-label">Endereco</label>
                            <input type="text" class="form-control" id="endereco" name="endereco">
                          </div>
                          <div class="col-md-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep">
                          </div>
                          <div class="col-md-4">
                            <label for="data_nascimento" class="form-label">Data Nascimento</label>
                            <input type="text" class="form-control" id="data_nascimento" name="data_nascimento">
                          </div>
                          <div class="col-md-8">
                            <label for="obs_saude" class="form-label">Observação de Saúde</label>
                            <textarea class="form-control"id="obs_saude" rows="1" name="obs_saude"></textarea>
                          </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                          <button type="reset" class="btn btn-warning">Limpar</button>
                          <button type="submit" class="btn btn-success">Adicionar</button>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>
                </div>

                <table class="datatable" id="alunos">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Escola/Série</th>
                      <th>Contato</th>
                      <th>CEP</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($alunos as $aluno) { ?>
                      <tr>
                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['escola']) . '/' . htmlspecialchars($aluno['serie_escola']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['contato']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cep']); ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
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

    <script>
      function celularMask(input) {
    const celular = input.value.replace(/\D+/g, "").slice(0, 11);
    const tamanho = celular.length;

    if (tamanho === 11) {
        input.value = `(${celular.slice(0, 2)}) ${celular.slice(2, 7)}-${celular.slice(7)}`;
    } else if (tamanho >= 7) {
        input.value = `(${celular.slice(0, 2)}) ${celular.slice(2, 7)}-${celular.slice(7, 11)}`;
    } else if (tamanho >= 2) {
        input.value = `(${celular.slice(0, 2)}) ${celular.slice(2)}`;
    } else {
        input.value = `(${celular}`;
    }
  }
    </script>

    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- <script>
    $(document).ready(function() {
    $('#alunos').DataTable({
      "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "Sem Registros",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros de _START_ aa _END_ de um total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros de 0 a 0 de um total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Carregando...",
        "oPaginate": {
          "sFirst": "Primero",
          "sLast": "Último",
          "sNext": "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      }
    });
  });
</script> -->

  </body>
</html>
  