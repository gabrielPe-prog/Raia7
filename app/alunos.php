<?php

if (!isset($_SESSION)) {
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <link href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">

</head>

<?php include_once 'layout/header.php'; ?>

<?php include_once 'layout/aside.php'; ?>

<body>
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Alunos Matriculados</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
          <li class="breadcrumb-item active">Alunos Matriculados</li>
        </ol>
      </nav>
    </div>

    <div class="col-lg-12">
      <div class="row">
        <div class="col-md-3">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h4 class="card-title">Total de Alunos Matriculados</h4>

              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-person-circle"></i>
                </div>
                <div class="ps-3 pt-2">
                  <h4><?php echo $_SESSION['totalAlunos']; ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Tabela de Alunos</h5>

                <!-- Button trigger modal -->
                <!-- <button type="button" class="btn btn-primary my-4" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop">
                Adicionar novo Aluno
              </button> -->

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                  tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Adiciona Aluno</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form class="row g-3" method="POST" action="controller/controllerCadastraAluno.php" enctype="multipart/form-data" id="formCadastro">
                          <div class="row my-4">
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
                              <input type="text" class="form-control" id="contato" name="contato"
                                oninput="celularMask(this)" maxlength="15">
                            </div>
                            <div class="col-6">
                              <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                              <input type="text" class="form-control" id="data_nascimento" name="data_nascimento">
                            </div>
                          </div>

                          <div class="row mb-4">
                            <div class="col-8">
                              <label for="endereco" class="form-label">Endereço*</label>
                              <input type="text" class="form-control" id="endereco" name="endereco">
                            </div>
                            <div class="col-4">
                              <label for="cep" class="form-label">CEP*</label>
                              <input type="text" class="form-control" id="cep" name="cep"
                                maxlength="8">
                            </div>
                          </div>
                          <!-- 
                        <div class="row">
                          <div class="col-12 mb-4">
                            <label for="formFileSm" class="form-label">Adicione aqui a uma foto para carteirinha</label>
                            <input class="form-control form-control-sm" id="formFileSm" type="file" name="path_foto">
                          </div>
                        </div> -->

                          <div class="row">
                            <div class="col-12 mb-4">
                              <label for="turma" class="form-label">Selecione uma Turma:</label>
                              <select class="form-select" id="turma" name="turma">
                                <option selected disabled>Escolha uma Turma...</option>
                                <option value="7:30 / 8:30">7:30 / 8:30</option>
                                <option value="9:00 / 10:00">9:00 / 10:00</option>
                                <option value="9:10 / 10:00">9:10 / 10:00</option>
                                <option value="10:00 / 11:00">10:00 / 11:00</option>
                                <option value="12:00 / 13:00">12:00 / 13:00</option>
                                <option value="14:00 / 15:00">14:00 / 15:00</option>
                                <option value="14:10 / 15:00">14:10 / 15:00</option>
                                <option value="15:00 / 16:00">15:00 / 16:00</option>
                                <option value="15:10 / 16:00">15:10 / 16:00</option>
                                <option value="16:00 / 17:00">16:00 / 17:00</option>
                                <option value="17:00 / 18:00">17:00 / 18:00</option>
                                <option value="18:00 / 19:00">18:00 / 19:00</option>
                                <option value="19:00 / 20:00">19:00 / 20:00</option>
                              </select>
                            </div>
                          </div>

                          <div class="row mb-4">
                            <div class="col-12">
                              <label for="obs_saude" class="form-label">Observações Médicas*</label>
                              <input type="text" class="form-control" id="obs_saude" name="obs_saude">
                            </div>
                          </div>

                          <!-- <div class="col-12">
                          <p class="small mb-0">Você já tem um cadastro? <a href="index.php">Faça Login</a></p>
                        </div> -->

                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-success w-100" type="submit">Realizar Cadastro</button>
                        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Fechar</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>

                <?php

                if (isset($_SESSION['update'])) {
                  if ($_SESSION['update'] == 1) {
                    echo '<script>
                            Swal.fire({
                                icon: "success",
                                title: "Sucesso!",
                                text: "A mudança foi realizada com sucesso!",
                            });
                          </script>';
                  } else {
                    echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Erro...",
                                text: "Não foi possível realizar a mudança.",
                            });
                          </script>';
                  }
                  unset($_SESSION['update']);
                }

                ?>

                <table class="datatable" id="alunos">
                  <thead>
                    <tr>
                      <th class="text-center">Nome</th>
                      <th class="text-center">Contato</th>
                      <th class="text-center">CEP</th>
                      <th class="text-center">Informações</th>
                      <th class="text-center">Editar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($alunos as $aluno) { ?>
                      <tr>
                        <td class="text-center"><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($aluno['contato']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($aluno['cep']); ?></td>
                        <td class="text-center">
                          <!-- Button trigger modal -->
                          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#visualizaDados-<?php echo $aluno['id_aluno']; ?>">
                            <i class="bi bi-three-dots"></i>
                          </button>

                          <!-- Modal -->
                          <div class="modal fade" id="visualizaDados-<?php echo $aluno['id_aluno']; ?>" tabindex="-1"
                            aria-labelledby="visualizaDadosLabel-<?php echo $aluno['id_aluno']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5" id="visualizaDadosLabel-<?php echo $aluno['id_aluno']; ?>">
                                    Informações do Aluno</h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <form class="row g-3">
                                    <div class="col-md-12">
                                      <label for="nome" class="form-label">Nome Completo</label>
                                      <input type="text" class="form-control" id="nome_update" name="nome_update"
                                        value="<?php echo $aluno['nome']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="cpf_update" class="form-label">Turma</label>
                                      <input type="text" class="form-control" value="<?php echo $aluno['horario_turma']; ?>"
                                        disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="cpf_update" class="form-label">CPF</label>
                                      <input type="text" class="form-control" maxlength="11"
                                        value="<?php echo $aluno['cpf']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="escola" class="form-label">Escola</label>
                                      <input type="text" class="form-control" id="escola_update" name="escola_update"
                                        value="<?php echo $aluno['escola']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="serie_escola" class="form-label">Série</label>
                                      <input type="text" class="form-control" id="serie_escola_update"
                                        name="serie_escola_update" value="<?php echo $aluno['serie_escola']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="contato" class="form-label">Contato</label>
                                      <input type="text" class="form-control" id="contato_update" name="contato_update"
                                        oninput="celularMask(this)" value="<?php echo $aluno['contato']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="data_nascimento" class="form-label">Data Nascimento</label>
                                      <input type="text" class="form-control" id="data_nascimento_update"
                                        name="data_nascimento_update" value="<?php echo $aluno['data_nascimento']; ?>"
                                        disabled>
                                    </div>
                                    <div class="col-md-9">
                                      <label for="endereco" class="form-label">Endereco</label>
                                      <input type="text" class="form-control" id="endereco_update" name="endereco_update"
                                        value="<?php echo $aluno['endereco']; ?>" disabled>
                                    </div>
                                    <div class="col-md-3">
                                      <label for="cep" class="form-label">CEP</label>
                                      <input type="text" class="form-control" id="cep_update" name="cep_update"
                                        value="<?php echo $aluno['cep']; ?>" disabled>
                                    </div>
                                    <div class="col-md-12">
                                      <label for="obs_saude" class="form-label">Observação de Saúde</label>
                                      <textarea class="form-control" id="obs_saude" rows="1" name="obs_saude_update"
                                        disabled><?php echo $aluno['obs_saude']; ?></textarea>
                                    </div>
                                    <input type="text" name="id_aluno" value="<?php echo $aluno['id_aluno']; ?>" hidden>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="text-center">
                          <!-- Button trigger modal -->
                          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editarDados-<?php echo $aluno['id_aluno']; ?>">
                            <i class="bi bi-pencil-fill"></i>
                          </button>

                          <!-- Modal -->
                          <div class="modal fade" id="editarDados-<?php echo $aluno['id_aluno']; ?>" tabindex="-1"
                            aria-labelledby="editarDadosLabel-<?php echo $aluno['id_aluno']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5" id="editarDadosLabel-<?php echo $aluno['id_aluno']; ?>">
                                    Editar Informações</h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <form class="row g-3" method="POST" action="controller/controllerEditarAluno.php">
                                    <div class="col-md-12">
                                      <label for="nome" class="form-label">Nome Completo</label>
                                      <input type="text" class="form-control" id="nome_update" name="nome_update"
                                        value="<?php echo $aluno['nome']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="cpf_update" class="form-label">CPF</label>
                                      <input type="text" class="form-control" id="cpf_update" name="cpf_update"
                                        maxlength="11" value="<?php echo $aluno['cpf']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="escola" class="form-label">Escola</label>
                                      <input type="text" class="form-control" id="escola_update" name="escola_update"
                                        value="<?php echo $aluno['escola']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="escola" class="form-label">Turma</label>
                                      <input type="number" class="form-control" name="turma_update"
                                        value="<?php echo $aluno['id_turma']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="serie_escola" class="form-label">Série</label>
                                      <input type="text" class="form-control" id="serie_escola_update"
                                        name="serie_escola_update" value="<?php echo $aluno['serie_escola']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="contato" class="form-label">Contato</label>
                                      <input type="text" class="form-control" id="contato_update" name="contato_update"
                                        oninput="celularMask(this)" value="<?php echo $aluno['contato']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label for="data_nascimento" class="form-label">Data Nascimento</label>
                                      <input type="date" class="form-control" id="data_nascimento_update"
                                        name="data_nascimento_update" value="<?php echo $aluno['data_nascimento']; ?>">
                                    </div>
                                    <div class="col-md-9">
                                      <label for="endereco" class="form-label">Endereco</label>
                                      <input type="text" class="form-control" id="endereco_update" name="endereco_update"
                                        value="<?php echo $aluno['endereco']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                      <label for="cep" class="form-label">CEP</label>
                                      <input type="text" class="form-control" id="cep_update" name="cep_update"
                                        value="<?php echo $aluno['cep']; ?>">
                                    </div>
                                    <div class="col-md-12">
                                      <label for="obs_saude" class="form-label">Observação de Saúde</label>
                                      <textarea class="form-control" id="obs_saude" rows="1"
                                        name="obs_saude_update"><?php echo $aluno['obs_saude']; ?></textarea>
                                    </div>
                                    <input type="text" name="id_aluno" value="<?php echo $aluno['id_aluno']; ?>" hidden>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                                      <button type="reset" class="btn btn-warning">Limpar</button>
                                      <button type="submit" class="btn btn-success">Salvar</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
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

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>