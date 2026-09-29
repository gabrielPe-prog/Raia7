<?php
$escapeTurma = fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$gruposTurmas = $turmas;
$gruposTurmas['sem-turma'] = ['horario' => 'Sem turma definida', 'piscina' => '', 'alunos' => $alunosSemTurma];
?>
<section class="section class-directory" aria-label="Organização das turmas">
  <div class="class-summary">
    <div><i class="bi bi-calendar-week" aria-hidden="true"></i><span>Turmas cadastradas<strong><?= count($turmas) ?></strong></span></div>
    <div><i class="bi bi-people" aria-hidden="true"></i><span>Total de alunos<strong><?= $totalAlunosTurmas ?></strong></span></div>
    <div class="class-summary-pending"><i class="bi bi-person-exclamation" aria-hidden="true"></i><span>Sem turma definida<strong><?= count($alunosSemTurma) ?></strong></span></div>
  </div>
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
    <article class="card class-group <?= $pending ? 'class-group-pending' : '' ?>" data-group="<?= $escapeTurma($id) ?>">
      <header class="class-group-header"><div><span class="eyebrow"><?= $pending ? 'ORGANIZAÇÃO PENDENTE' : 'HORÁRIO DA TURMA' ?></span><h2><?= $escapeTurma($grupo['horario']) ?></h2><p><?= $pending ? 'Alunos que ainda precisam de uma turma.' : 'Piscina: ' . $escapeTurma($grupo['piscina'] ?: 'Não informada') ?></p></div><span class="class-count"><span data-group-count><?= count($grupo['alunos']) ?></span> aluno(s)</span></header>
      <?php if ($pending && $grupo['alunos']): ?><p class="class-pending-help">Para definir uma turma, edite o cadastro na <a href="alunos.php">tela de alunos <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>.</p><?php endif; ?>
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
    </article>
  <?php endforeach; ?>
  </div>
  <div id="classNoResults" class="class-empty card" hidden><i class="bi bi-search" aria-hidden="true"></i><h2>Nenhum aluno encontrado</h2><p>Tente outro nome, CPF ou selecione outra turma.</p></div>
</section>
