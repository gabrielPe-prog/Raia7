<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('America/Recife');

include_once 'service/checkAccess.php';
include_once 'controller/controllerInfo.php';
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

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Pagamentos</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="paginaInicial.php">Home</a></li>
                    <li class="breadcrumb-item active">Pagamentos</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <div class="card mt-5">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagamentos as $pagamento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pagamento['descricao']) ?></td>
                                        <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($pagamento['data_vencimento'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $pagamento['status'] === 'pago' ? 'success' : ($pagamento['status'] === 'atrasado' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($pagamento['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pagamento['status'] !== 'pago'): ?>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPagamento"
                                                    onclick="carregarFormularioPagamento(<?= $pagamento['id'] ?>, <?= $pagamento['valor'] ?>, '<?= $pagamento['descricao'] ?>')">
                                                    Pagar
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de Pagamento -->
            <div class="modal fade" id="modalPagamento" tabindex="-1" aria-labelledby="modalPagamentoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPagamentoLabel">Realizar Pagamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="formulario-pagamento">
                                <!-- O formulário será carregado aqui via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            </div>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializa o Mercado Pago
        const mp = new MercadoPago('SUA_PUBLIC_KEY', {
            locale: 'pt-BR'
        });

        function carregarFormularioPagamento(pagamentoId, valor, descricao) {
            // Limpa o formulário anterior
            document.getElementById('formulario-pagamento').innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        `;

            // Cria um formulário de cartão de crédito
            mp.checkout({
                preference: {
                    id: '' // Deixe vazio para pagamento direto
                },
                autoOpen: false,
                render: {
                    container: '#formulario-pagamento',
                    label: 'Pagar',
                    type: 'credit_card'
                },
                theme: {
                    elementsColor: '#007bff',
                    headerColor: '#007bff'
                },
                customTexts: {
                    creditCardForm: {
                        cardNumber: {
                            label: 'Número do cartão',
                            placeholder: '1234 5678 9012 3456'
                        },
                        cardholderName: {
                            label: 'Nome no cartão',
                            placeholder: 'Nome como está no cartão'
                        },
                        expirationDate: {
                            label: 'Data de expiração',
                            placeholder: 'MM/AAAA'
                        },
                        securityCode: {
                            label: 'Código de segurança',
                            placeholder: 'CVV'
                        },
                        installments: {
                            label: 'Parcelas',
                            placeholder: 'Quantidade de parcelas'
                        },
                        cardholderIdentification: {
                            label: 'CPF do titular',
                            placeholder: '000.000.000-00'
                        }
                    }
                },
                callbacks: {
                    onFormMounted: function() {
                        // Adiciona campos adicionais
                        const formContainer = document.getElementById('formulario-pagamento');

                        // Adiciona campos ocultos com os dados do pagamento
                        const hiddenFields = document.createElement('div');
                        hiddenFields.innerHTML = `
                        <input type="hidden" name="pagamento_id" value="${pagamentoId}">
                        <input type="hidden" name="valor" value="${valor}">
                        <input type="hidden" name="descricao" value="${descricao}">
                    `;
                        formContainer.appendChild(hiddenFields);

                        // Adiciona botão de submit personalizado
                        const submitBtn = document.createElement('button');
                        submitBtn.className = 'btn btn-primary w-100 mt-3';
                        submitBtn.textContent = 'Pagar R$ ' + valor.toFixed(2).replace('.', ',');
                        submitBtn.type = 'button';
                        submitBtn.onclick = function() {
                            processarPagamento();
                        };
                        formContainer.appendChild(submitBtn);
                    }
                }
            });
        }

        function processarPagamento() {
            const form = document.querySelector('#formulario-pagamento form');
            const formData = new FormData(form);

            // Adiciona dados adicionais
            formData.append('pagamento_id', document.querySelector('input[name="pagamento_id"]').value);
            formData.append('valor', document.querySelector('input[name="valor"]').value);
            formData.append('descricao', document.querySelector('input[name="descricao"]').value);

            // Adiciona dados do aluno (você pode obter esses dados da sessão ou de outro lugar)
            formData.append('aluno_nome', 'Nome do Aluno'); // Substitua pelos dados reais
            formData.append('aluno_email', 'aluno@email.com'); // Substitua pelos dados reais
            formData.append('aluno_cpf', '000.000.000-00'); // Substitua pelos dados reais

            // Envia os dados para o servidor
            fetch('processar_pagamento.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Pagamento realizado com sucesso!');
                        window.location.reload();
                    } else {
                        alert('Erro no pagamento: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocorreu um erro ao processar o pagamento');
                });
        }
    </script>

    <?php include_once 'layout/footer.php'; ?>

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