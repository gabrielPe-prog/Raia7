<?php
if (!isset($adminFaturas)) { http_response_code(404); exit; }
session_start();
date_default_timezone_set('America/Recife');
header('Cache-Control: no-store');
if (($_SESSION['logged_in'] ?? false) !== true) {
    header('Location: index.php'); exit;
}
if (($adminFaturas && (string) ($_SESSION['nivel'] ?? '') !== '1')
    || (!$adminFaturas && empty($_SESSION['id_aluno']))) {
    http_response_code(403); exit('Acesso não permitido.');
}
require_once __DIR__ . '/connection_create.php';
require_once __DIR__ . '/checkout.php';
$_SESSION['checkout_csrf'] ??= bin2hex(random_bytes(32));
$_SESSION['fatura_csrf'] ??= bin2hex(random_bytes(32));
$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);
$rows = $history = $students = [];
function faturaEscape($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
try {
    $db = conexao_pdo();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$adminFaturas || !is_string($_POST['csrf'] ?? null)
            || !hash_equals($_SESSION['fatura_csrf'], $_POST['csrf'])) {
            http_response_code(403); exit('Solicitação inválida. Atualize a página.');
        }
        $aluno = filter_var($_POST['aluno_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $description = is_string($_POST['descricao'] ?? null) ? trim($_POST['descricao']) : '';
        $value = is_string($_POST['valor'] ?? null) ? str_replace(',', '.', $_POST['valor']) : '';
        $date = is_string($_POST['vencimento'] ?? null) ? $_POST['vencimento'] : '';
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$aluno || $description === '' || mb_strlen($description) > 255
            || !$parsed || $parsed->format('Y-m-d') !== $date || CheckoutService::cents($value) <= 0) {
            throw new InvalidArgumentException('Confira o aluno, a descrição, o valor e o vencimento.');
        }
        $stmt = $db->prepare('SELECT id_aluno FROM alunos WHERE id_aluno = ?');
        $stmt->execute([$aluno]);
        if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Aluno não encontrado.');
        $stmt = $db->prepare('INSERT INTO mp_faturas (aluno_id,descricao,valor,vencimento) VALUES (?,?,?,?)');
        $stmt->execute([$aluno, $description, $value, $date]);
        $_SESSION['fatura_csrf'] = bin2hex(random_bytes(32));
        header('Location: faturas.php?criada=1', true, 303); exit;
    }
    $where = $adminFaturas ? '' : ' WHERE f.aluno_id = ?';
    $params = $adminFaturas ? [] : [$_SESSION['id_aluno']];
    $stmt = $db->prepare('SELECT f.*, a.nome AS aluno_nome FROM mp_faturas f JOIN alunos a ON a.id_aluno=f.aluno_id' . $where . ' ORDER BY f.vencimento DESC, f.id DESC');
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $db->prepare('SELECT t.*, f.descricao, a.nome AS aluno_nome FROM mp_transacoes t JOIN mp_faturas f ON f.id=t.fatura_id JOIN alunos a ON a.id_aluno=f.aluno_id' . $where . ' ORDER BY t.atualizado_em DESC');
    $stmt->execute($params);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($adminFaturas) $students = $db->query('SELECT id_aluno, nome FROM alunos ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
} catch (InvalidArgumentException $e) {
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: faturas.php', true, 303); exit;
} catch (Throwable $e) {
    error_log('Faturas: falha de consulta (' . get_class($e) . ').');
    $error = 'Não foi possível carregar as faturas. Entre em contato com a administração.';
}
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Faturas | Raia7</title>
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"><link href="assets/css/raia-theme.css" rel="stylesheet">
<script src="assets/js/raia-ui.js" defer></script><?php require dirname(__DIR__) . '/layout/favicon.php'; ?>
</head><body>
<?php require dirname(__DIR__) . '/layout/header.php'; require dirname(__DIR__) . '/layout/aside.php'; ?>
<main id="main" class="main">
<h1><?= $adminFaturas ? 'Faturas dos alunos' : 'Minhas faturas' ?></h1>
<p><a href="<?= $adminFaturas ? 'financeiro.php' : 'infoPagAlunos.php' ?>">Consultar registros anteriores de pagamentos</a></p>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= faturaEscape($error) ?></div><?php endif; ?>
<?php if (isset($_GET['retorno'])): ?><div class="alert alert-info">A situação abaixo reflete a confirmação recebida pelo sistema. Se acabou de pagar, aguarde e <a href="pagamentosAluno.php">atualize a página</a>.</div><?php endif; ?>
<?php if ($adminFaturas && isset($_GET['criada'])): ?><div class="alert alert-success">Fatura criada.</div><?php endif; ?>
<?php if ($adminFaturas): ?>
<div class="card"><div class="card-body"><h2 class="card-title">Nova fatura</h2>
<form method="post" action="faturas.php" class="row g-3">
<input type="hidden" name="csrf" value="<?= faturaEscape($_SESSION['fatura_csrf']) ?>">
<div class="col-md-4"><label class="form-label" for="aluno">Aluno</label><select id="aluno" name="aluno_id" class="form-select" required>
<?php foreach ($students as $student): ?><option value="<?= (int) $student['id_aluno'] ?>"><?= faturaEscape($student['nome']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label" for="descricao">Descrição</label><input id="descricao" name="descricao" maxlength="255" class="form-control" required></div>
<div class="col-md-2"><label class="form-label" for="valor">Valor (R$)</label><input id="valor" name="valor" type="number" min="0.01" max="9999999999.99" step="0.01" class="form-control" required></div>
<div class="col-md-2"><label class="form-label" for="vencimento">Vencimento</label><input id="vencimento" name="vencimento" type="date" class="form-control" required></div>
<div><button class="btn btn-primary">Criar fatura</button></div></form></div></div>
<?php endif; ?>
<div class="card"><div class="card-body"><h2 class="card-title">Faturas</h2><div class="table-responsive"><table class="table">
<thead><tr><?php if ($adminFaturas): ?><th>Aluno</th><?php endif; ?><th>Descrição</th><th>Valor</th><th>Vencimento</th><th>Situação</th><th>Ação</th></tr></thead><tbody>
<?php foreach ($rows as $row):
$status = $row['status'] === 'aberta' && $row['vencimento'] < date('Y-m-d') ? 'Vencida' : ['aberta'=>'Em aberto','paga'=>'Paga','revisao'=>'Em revisão'][$row['status']] ?? $row['status']; ?>
<tr><?php if ($adminFaturas): ?><td><?= faturaEscape($row['aluno_nome']) ?></td><?php endif; ?>
<td><?= faturaEscape($row['descricao']) ?></td><td>R$ <?= number_format((float) $row['valor'],2,',','.') ?></td><td><?= faturaEscape(date('d/m/Y',strtotime($row['vencimento']))) ?></td><td><?= faturaEscape($status) ?></td><td>
<?php if (!$adminFaturas && $row['status'] === 'aberta'): ?><form method="post" action="processar_pagamentos.php">
<input type="hidden" name="csrf" value="<?= faturaEscape($_SESSION['checkout_csrf']) ?>"><input type="hidden" name="fatura_id" value="<?= (int) $row['id'] ?>"><button class="btn btn-primary btn-sm">Pagar no Mercado Pago</button></form><?php endif; ?></td></tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="6">Nenhuma fatura encontrada.</td></tr><?php endif; ?>
</tbody></table></div></div></div>
<div class="card"><div class="card-body"><h2 class="card-title">Histórico do Mercado Pago</h2><div class="table-responsive"><table class="table"><thead><tr><?php if ($adminFaturas): ?><th>Aluno</th><?php endif; ?><th>Descrição</th><th>Valor</th><th>Estornado</th><th>Situação</th><th>Aprovação</th></tr></thead><tbody>
<?php foreach ($history as $item): ?><tr><?php if ($adminFaturas): ?><td><?= faturaEscape($item['aluno_nome']) ?></td><?php endif; ?><td><?= faturaEscape($item['descricao']) ?></td><td>R$ <?= number_format((float)$item['valor'],2,',','.') ?></td><td>R$ <?= number_format((float)$item['estornado'],2,',','.') ?></td><td><?= faturaEscape(['approved'=>'Aprovado','pending'=>'Pendente','in_process'=>'Em análise','rejected'=>'Recusado','cancelled'=>'Cancelado','refunded'=>'Estornado','charged_back'=>'Contestado','in_mediation'=>'Em mediação'][$item['status']] ?? $item['status']) ?></td><td><?= $item['aprovado_em'] ? faturaEscape((new DateTimeImmutable($item['aprovado_em']))->setTimezone(new DateTimeZone('America/Recife'))->format('d/m/Y H:i')) : '—' ?></td></tr><?php endforeach; ?>
<?php if (!$history): ?><tr><td colspan="6">Nenhuma transação recebida.</td></tr><?php endif; ?>
</tbody></table></div></div></div></main>
<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script><script src="assets/js/main.js"></script>
</body></html>
