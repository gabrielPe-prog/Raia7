<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title><?= reportEscape($report['title']) ?></title>
<style>
@page { margin: 102px 38px 58px; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; line-height: 1.55; color: #153c4b; }
.header { position: fixed; top: -77px; left: 0; right: 0; height: 56px; border-bottom: 2px solid #087e8b; }
.header table { width: 100%; }
.header td { border: 0; padding: 0; vertical-align: middle; }
.logo { width: 94px; height: 52px; }
.brand { font-size: 17px; font-weight: bold; }
.eyebrow { color: #087e8b; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; }
.meta { text-align: right; font-size: 8px; color: #647c86; }
.footer { position: fixed; bottom: -32px; left: 0; right: 0; border-top: 1px solid #dfe9ec; padding-top: 9px; font-size: 8px; color: #647c86; }
h1 { font-size: 25px; line-height: 1.2; margin: 7px 0 9px; }
h2 { font-size: 15px; margin: 19px 0 10px; page-break-after: avoid; }
h3 { font-size: 11px; margin: 12px 0 7px; page-break-after: avoid; }
p { margin: 5px 0 10px; }
.period { font-size: 13px; color: #087e8b; margin-bottom: 10px; }
.note { color: #647c86; font-size: 8px; background: #f1f6f7; padding: 10px 13px; border-left: 3px solid #63bebb; }
.summary { width: 100%; margin: 18px 0; border-collapse: collapse; }
.summary td { background: #103d50; color: #fff; padding: 13px 17px; width: 50%; border-right: 2px solid #fff; }
.summary small { color: #b4d8df; font-size: 8px; }
.summary strong { display: block; font-size: 19px; }
.data { width: 100%; border-collapse: collapse; table-layout: fixed; margin: 9px 0 18px; }
.data thead { display: table-header-group; }
.data th { background: #e8f3f4; color: #315d6a; font-size: 8px; padding: 10px 8px; text-align: left; }
.data td { padding: 9px 8px; border-bottom: 1px solid #e5edf0; vertical-align: top; overflow-wrap: break-word; }
.data tr:nth-child(even) td { background: #f8fafb; }
.data tr { page-break-inside: avoid; }
.money { text-align: right; white-space: nowrap; }
.empty { padding: 28px; text-align: center; border: 1px solid #dfe9ec; color: #647c86; margin-top: 20px; }
.student { border-top: 3px solid #087e8b; padding-top: 8px; }
.student + .student { page-break-before: always; }
.student-heading { width: 100%; margin-bottom: 12px; }
.student-heading td { vertical-align: middle; }
.photo { max-width: 76px; max-height: 95px; }
.label { display: block; color: #647c86; font-size: 8px; margin-bottom: 4px; }
.details { width: 100%; border-collapse: collapse; table-layout: fixed; }
.details td { padding: 9px 10px; border-bottom: 1px solid #e5edf0; vertical-align: top; width: 50%; }
.details tr { page-break-inside: avoid; }
.health { padding: 10px; background: #f3f7f8; }
.membership { margin-top: 25px; border: 1px solid #cfdee3; border-radius: 12px; }
.membership-banner { background: #103d50; color: #fff; padding: 16px 22px; border-bottom: 4px solid #66cfca; }
.membership-banner strong { display: block; font-size: 17px; }
.membership-banner span { font-size: 8px; color: #b7dce3; letter-spacing: 1px; }
.membership-body { padding: 24px; }
.membership-name { font-size: 21px; line-height: 1.3; }
.membership-initials { background: #edf5f6; color: #417c89; font-size: 27px; text-align: center; padding: 25px 8px; }
.membership-foot { border-top: 1px solid #dfe9ec; padding: 12px 22px; color: #647c86; font-size: 8px; }
.membership h2 { margin-top: 0; }
</style>
</head>
<body>
<div class="header"><table><tr>
<td style="width:110px"><?php if ($logo): ?><img class="logo" src="<?= $logo ?>" alt="Raia7"><?php endif; ?></td>
<td><span class="brand">AquaManager</span><br><span class="eyebrow">Academia Aquática Raia7</span></td>
<td class="meta">RELATÓRIO<br>Emitido em <?= reportEscape($report['issued']) ?><br>Horário de Recife</td>
</tr></table></div>
<div class="footer">Raia7 · AquaManager · Documento para consulta</div>
<div class="eyebrow">Seu espaço Raia7</div>
<h1><?= reportEscape($report['title']) ?></h1>
<?php if ($report['period'] !== null): ?><div class="period"><?= reportEscape($report['period']) ?></div><?php endif; ?>
<p class="note"><?= reportEscape($report['note']) ?></p>

<?php if ($report['financial']): ?>
    <?php if (!$report['admin']): ?><p><strong>Aluno:</strong> <?= reportEscape($report['students'][0]['nome']) ?> · <strong>CPF:</strong> <?= reportEscape($report['students'][0]['cpf']) ?></p><?php endif; ?>
    <table class="summary"><tr><td><small>PAGAMENTOS NO PERÍODO</small><strong><?= count($report['payments']) ?></strong></td><td><small>VALOR TOTAL REGISTRADO</small><strong><?= reportMoney($report['totalCents']) ?></strong></td></tr></table>
    <?php if (!$report['payments']): ?>
        <div class="empty">Nenhum pagamento encontrado no período selecionado.</div>
    <?php else: ?>
        <table class="data"><thead><tr><th style="width:16%">Data</th><?php if ($report['admin']): ?><th style="width:27%">Aluno</th><?php endif; ?><th>Descrição</th><th style="width:20%" class="money">Valor</th></tr></thead><tbody>
        <?php foreach ($report['payments'] as $payment): ?>
            <tr><td><?= reportEscape(reportDate($payment['data_pagamento'])) ?></td>
            <?php if ($report['admin']): ?><td><?= reportEscape($payment['aluno_nome']) ?><br><span class="label">CPF: <?= reportEscape($payment['aluno_cpf']) ?></span></td><?php endif; ?>
            <td><?= reportEscape($payment['descricao']) ?></td><td class="money"><?= reportMoney((int) round((float) $payment['valor'] * 100)) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
    <?php endif; ?>
<?php elseif ($report['type'] === 'turmas'): ?>
    <?php $studentCount = array_sum(array_map(fn($group) => count($group['students']), $report['groups'])); ?>
    <table class="summary"><tr><td><small>TURMAS</small><strong><?= count(array_filter($report['groups'], fn($group) => $group['id_turma'] !== null)) ?></strong></td><td><small>ALUNOS NO RELATÓRIO</small><strong><?= $studentCount ?></strong></td></tr></table>
    <?php if (!$report['groups']): ?><div class="empty">Nenhuma turma cadastrada.</div><?php endif; ?>
    <?php foreach ($report['groups'] as $group): ?>
        <h2><?= reportEscape($group['horario']) ?> · <?= count($group['students']) ?> aluno(s)</h2>
        <p class="label">Piscina: <?= reportEscape($group['piscina'] ?: 'Não informada') ?></p>
        <table class="data"><thead><tr><th style="width:33%">Nome</th><th>CPF</th><th>Contato</th><th>Nascimento</th></tr></thead><tbody>
        <?php foreach ($group['students'] as $student): ?>
            <tr><td><?= reportEscape($student['nome']) ?></td><td><?= reportEscape($student['cpf']) ?></td><td><?= reportEscape($student['contato']) ?></td><td><?= reportEscape(reportDate($student['data_nascimento'])) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$group['students']): ?><tr><td colspan="4">Nenhum aluno nesta turma.</td></tr><?php endif; ?>
        </tbody></table>
    <?php endforeach; ?>
<?php elseif ($report['type'] === 'carteirinha'): ?>
    <?php $student = $report['students'][0]; $identity = dadosCarteirinha($student); $photo = reportImage((string) ($student['path_foto'] ?? '')); ?>
    <div class="membership">
      <div class="membership-banner"><span>ACADEMIA AQUÁTICA RAIA7</span><strong>Carteirinha do aluno</strong></div>
      <div class="membership-body">
        <table class="student-heading"><tr><td style="width:100px"><?php if ($photo): ?><img class="photo" src="<?= $photo ?>" alt="Foto do aluno"><?php else: ?><div class="membership-initials"><?= reportEscape($identity['iniciais']) ?></div><?php endif; ?></td><td style="padding-left:20px"><span class="eyebrow">Aluno</span><h2 class="membership-name"><?= reportEscape($identity['nome']) ?></h2><span class="label">Matrícula #<?= reportEscape($identity['matricula']) ?></span><span class="label">CPF: <?= reportEscape($identity['cpf']) ?></span></td></tr></table>
        <table class="details"><tr><td><span class="label">Horário da turma</span><?= reportEscape($identity['horario']) ?></td><td><span class="label">Piscina</span><?= reportEscape($identity['piscina']) ?></td></tr></table>
      </div>
      <div class="membership-foot"><?= $identity['pendente'] ? 'TURMA PENDENTE' : 'IDENTIFICAÇÃO DO ALUNO' ?> · Raia7 AquaManager</div>
    </div>
    <?php if ($identity['pendente']): ?><p class="note">Sua turma ainda não foi definida. Este documento não confirma matrícula em uma turma.</p><?php endif; ?>
<?php else: ?>
    <?php if ($report['type'] === 'alunos'): ?><table class="summary"><tr><td><small>ALUNOS CADASTRADOS</small><strong><?= count($report['students']) ?></strong></td><td><small>CONTEÚDO</small><strong>Cadastro completo</strong></td></tr></table><?php endif; ?>
    <?php if (!$report['students']): ?><div class="empty">Nenhum aluno cadastrado.</div><?php endif; ?>
    <?php foreach ($report['students'] as $student): ?>
    <?php $photo = reportImage((string) ($student['path_foto'] ?? '')); ?>
    <div class="student">
        <table class="student-heading"><tr><td><span class="eyebrow">Matrícula #<?= (int) $student['id_aluno'] ?></span><h2><?= reportEscape($student['nome']) ?></h2></td><td style="width:85px;text-align:right"><?php if ($photo): ?><img class="photo" src="<?= $photo ?>" alt="Foto do aluno"><?php endif; ?></td></tr></table>
        <table class="details">
        <?php
        $fields = [
            'CPF' => $student['cpf'], 'Nascimento' => reportDate($student['data_nascimento']),
            'Contato' => $student['contato'], 'Cadastro em' => reportDate($student['created_at']),
            'Turma' => $student['horario_turma'], 'Piscina' => $student['piscina'],
            'Escola' => $student['escola'], 'Série' => $student['serie_escola'],
            'Endereço' => $student['endereco'], 'CEP' => $student['cep'],
        ];
        $column = 0;
        foreach ($fields as $label => $value): ?>
            <?php if ($column % 2 === 0): ?><tr><?php endif; ?>
            <td><span class="label"><?= reportEscape($label) ?></span><?= reportEscape($value !== null && $value !== '' ? $value : 'Não informado') ?></td>
            <?php if ($column % 2 === 1): ?></tr><?php endif; ?>
        <?php $column++; endforeach; ?>
        </table>
        <h3>Observações de saúde</h3>
        <div class="health"><?= nl2br(reportEscape($student['obs_saude'] ?: 'Nenhuma observação informada.')) ?></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
