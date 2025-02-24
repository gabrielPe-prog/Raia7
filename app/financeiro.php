<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once 'service/checkAccess.php';
include_once 'controller/controllerFinanceiro.php';
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

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">

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
            <h1>Financeiro</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
                    <li class="breadcrumb-item active">Financeiro</li>
                </ol>
            </nav>
        </div>


        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tabela de Alunos</h5>

                            <table class="datatable" id="alunos">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nome</th>
                                        <th class="text-center">Pacote</th>
                                        <th class="text-center">Ultimo Pagamento</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Editar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($financeiro as $f) {

                                        $dataAtual = new DateTime();
                                        $dataFinal = new DateTime($f['pagamento']);
                                        
                                        $diferenca = $dataAtual->diff($dataFinal);
                                        
                                        $diasRestantes = $diferenca->days;
                                        
                                        switch (true) {
                                            case ($diasRestantes == 0):
                                                $status = "<span class='badge text-bg-warning'>Dia do Pagamento</span>";
                                                break;
                                            case ($diasRestantes < 0):
                                                $status = "<span class='badge text-bg-success'>Pagamento Atrasado</span>";
                                                break;
                                            case ($diasRestantes > 0):
                                                $status = "<span class='badge text-bg-success'>Aguardando Pagamento</span>";
                                                break;
                                            default:
                                                $status = "Erro ao calcular a data.";
                                                break;
                                        }
                                        
                                        var_dump($diasRestantes, $status);
                                        

                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo htmlspecialchars($f['aluno_nome']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($f['pacote_nome']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars(date('d-m-Y', strtotime($f['pagamento']))); ?></td>
                                            <td class="text-center"><?php echo $status; ?></td>
                                            <td class="text-center">
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editarDados-<?php echo $f['id']; ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="editarDados-<?php echo $f['id']; ?>" tabindex="-1"
                                                    aria-labelledby="editarDadosLabel-<?php echo $f['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="editarDadosLabel-<?php echo $f['id']; ?>">
                                                                    Editar Informações</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="row g-3" method="POST" action="controller/controllerAtualizaPacote.php">

                                                                    <label for="pacote-<?php echo $f['id']; ?>">Pacote:</label>
                                                                    <select class="form-select" id="pacote-<?php echo $f['id']; ?>" name="id_pacote" required>
                                                                        <?php
                                                                        foreach ($pacotes as $pacote) {
                                                                            $selected = ($pacote['id'] == $f['id_pacote']) ? 'selected' : '';
                                                                            echo "<option value='{$pacote['id']}' $selected>{$pacote['nome']}</option>";
                                                                        }
                                                                        ?>
                                                                    </select>

                                                                    <label for="pagamento-<?php echo $f['id']; ?>">Data do Pagamento:</label>
                                                                    <input type="date" class="form-control" id="pagamento-<?php echo $f['id']; ?>" name="pagamento"
                                                                        value="<?php echo htmlspecialchars($f['pagamento']); ?>" required>

                                                                    <input type="hidden" name="id_financeiro" value="<?php echo $f['id']; ?>">
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
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

    <script>
        function CpfMask(input) {
            const value = input.value.replace(/\D/g, '');
            input.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }


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

</body>

</html>