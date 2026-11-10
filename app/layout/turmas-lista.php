<?php
$escapeTurma = fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$gruposTurmas = ['sem-turma' => ['horario' => 'Sem turma definida', 'piscina' => '', 'alunos' => $alunosSemTurma]] + $turmas;
?>
<section class="section class-directory" aria-label="Organização das turmas">
  <div class="class-summary">
    <div><i class="bi bi-calendar-week" aria-hidden="true"></i><span>Turmas cadastradas<strong><?= count($turmas) ?></strong></span></div>
    <div><i class="bi bi-people" aria-hidden="true"></i><span>Total de alunos<strong><?= $totalAlunosTurmas ?></strong></span></div>
    <div class="class-summary-pending"><i class="bi bi-person-exclamation" aria-hidden="true"></i><span>Sem turma definida<strong><?= count($alunosSemTurma) ?></strong></span></div>
  </div>
  <?php if (isset($_SESSION['turmas_feedback'])): [$tipoFeedback, $mensagemFeedback] = $_SESSION['turmas_feedback']; unset($_SESSION['turmas_feedback']); ?>
    <div class="alert alert-<?= $escapeTurma($tipoFeedback) ?>" role="status"><?= $escapeTurma($mensagemFeedback) ?></div>
  <?php endif; ?>
  <div class="card"><div class="card-body pt-3">
    <h2 class="h5">Adicionar aluno a uma turma</h2>
    <p class="text-muted">Selecione o aluno e a turma de destino. Alunos com turma serão transferidos.</p>
    <form method="post" action="turmas.php" id="enrollmentForm" class="row g-3">
      <input type="hidden" name="csrf_token" value="<?= $escapeTurma($_SESSION['turmas_csrf']) ?>">
      <input type="hidden" name="turma_anterior" id="previousClass" value="">
      <div class="col-md-4"><label for="enrollmentSearch" class="form-label">Buscar aluno para adicionar</label><input id="enrollmentSearch" type="search" class="form-control" placeholder="Nome ou CPF"></div>
      <div class="col-md-4"><label for="enrollmentStudent" class="form-label">Aluno</label><select id="enrollmentStudent" name="id_aluno" class="form-select" required><option value="">Selecione um aluno</option>
      <?php foreach ($gruposTurmas as $grupoId => $grupo): foreach ($grupo['alunos'] as $aluno): ?>
        <option value="<?= (int) $aluno['id_aluno'] ?>" data-current="<?= $escapeTurma($aluno['id_turma']) ?>" data-assigned="<?= $grupoId === 'sem-turma' ? '0' : '1' ?>"><?= $escapeTurma($aluno['nome'] . ' · ' . $aluno['cpf'] . ' · ' . $grupo['horario']) ?></option>
      <?php endforeach; endforeach; ?></select></div>
      <div class="col-md-4"><label for="enrollmentClass" class="form-label">Turma de destino</label><select id="enrollmentClass" name="id_turma" class="form-select" required><option value="">Selecione uma turma</option><?php foreach ($turmas as $id => $grupo): ?><option value="<?= (int) $id ?>"><?= $escapeTurma($grupo['horario'] . ' · ' . $grupo['piscina']) ?></option><?php endforeach; ?></select></div>
      <div class="col-12"><button class="btn btn-primary" type="submit">Salvar turma do aluno</button></div>
    </form>
  </div></div>
  <div class="card class-filter"><div class="card-body">
    <div class="class-filter-fields">
      <div><label for="classSearch" class="form-label">Buscar aluno</label><input type="search" id="classSearch" class="form-control" placeholder="Digite o nome ou CPF" autocomplete="off"></div>
      <div><label for="classSelect" class="form-label">Horário / turma</label><select id="classSelect" class="form-select"><option value="all">Todas as turmas</option><?php foreach ($gruposTurmas as $id => $grupo): ?><option value="<?= $escapeTurma($id) ?>"><?= $escapeTurma($grupo['horario']) ?><?= $grupo['piscina'] ? ' · ' . $escapeTurma($grupo['piscina']) : '' ?></option><?php endforeach; ?></select></div>
      <button type="button" class="btn btn-light" id="classReset">Limpar filtros</button>
    </div>
    <p class="class-results" id="classResults" role="status" aria-live="polite"><?= $totalAlunosTurmas ?> aluno(s) · Alunos em ordem alfabética em cada turma.</p>
  </div></div>
  <div class="class-grid">
  <?php foreach ($gruposTurmas as $id => $grupo): $pending = $id === 'sem-turma'; ?>
    <details class="card class-group <?= $pending ? 'class-group-pending' : '' ?>" data-group="<?= $escapeTurma($id) ?>" <?= $pending ? 'open' : '' ?>>
      <summary class="class-group-header"><div><span class="eyebrow"><?= $pending ? 'ORGANIZAÇÃO PENDENTE' : 'HORÁRIO DA TURMA' ?></span><h2><?= $escapeTurma($grupo['horario']) ?></h2><p><?= $pending ? 'Alunos que ainda precisam de uma turma.' : 'Piscina: ' . $escapeTurma($grupo['piscina'] ?: 'Não informada') ?></p></div><span class="class-count"><span data-group-count><?= count($grupo['alunos']) ?></span> aluno(s)</span><span class="class-expand">Ver alunos / recolher</span></summary>
      <?php if ($grupo['alunos']): ?>
      <div class="table-responsive" tabindex="0" role="region" aria-label="Alunos: <?= $escapeTurma($grupo['horario']) ?>">
        <table class="table class-students"><thead><tr><th scope="col">Aluno</th><th scope="col">CPF</th><th scope="col">Contato</th><th scope="col">Nascimento</th></tr></thead><tbody>
        <?php foreach ($grupo['alunos'] as $aluno): ?>
          <tr data-student-name="<?= $escapeTurma($aluno['nome']) ?>" data-student-cpf="<?= $escapeTurma($aluno['cpf']) ?>">
            <td><div class="class-student-name"><span class="class-avatar" aria-hidden="true"><?= $escapeTurma(mb_strtoupper(mb_substr(trim($aluno['nome']), 0, 1))) ?></span><span><strong><?= $escapeTurma($aluno['nome']) ?></strong><small>Matrícula #<?= (int) $aluno['id_aluno'] ?></small></span></div></td>
            <td><?= $escapeTurma($aluno['cpf'] ?: 'Não informado') ?></td><td><?= $escapeTurma($aluno['contato'] ?: 'Não informado') ?></td>
            <td><?php $nascimento = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $aluno['data_nascimento']); echo $nascimento ? $nascimento->format('d/m/Y') : 'Não informado'; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table>
      </div>
      <?php else: ?><div class="class-empty"><i class="bi <?= $pending ? 'bi-check-circle' : 'bi-people' ?>" aria-hidden="true"></i><p><?= $pending ? 'Todos os alunos já possuem uma turma.' : 'Esta turma ainda não possui alunos.' ?></p></div><?php endif; ?>
      <p class="class-no-match" hidden>Nenhum aluno corresponde à busca nesta turma.</p>
    </details>
  <?php endforeach; ?>
  </div>
  <nav class="class-pagination" aria-label="Páginas de turmas"><button type="button" class="btn btn-outline-primary" id="classPrev">Anterior</button><span id="classPage" role="status" aria-live="polite"></span><button type="button" class="btn btn-outline-primary" id="classNext">Próxima</button></nav>
  <div id="classNoResults" class="class-empty card" hidden><i class="bi bi-search" aria-hidden="true"></i><h2>Nenhum aluno encontrado</h2><p>Tente outro nome, CPF ou selecione outra turma.</p></div>
</section>
