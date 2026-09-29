<?php
require_once __DIR__ . '/../service/carteirinhaView.php';
$cardEscape = fn($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<section class="digital-id-section" aria-label="Identificação do aluno">
<?php if (!$carteirinha): ?>
  <div class="card digital-id-empty"><i class="bi bi-person-vcard" aria-hidden="true"></i><h2>Cadastro não encontrado</h2><p>Entre em contato com a secretaria para conferir seus dados.</p></div>
<?php else: $identidade = dadosCarteirinha($carteirinha); ?>
  <div class="digital-id-intro"><span class="eyebrow">SEU ESPAÇO NA RAIA7</span><h2>Sua identificação, sempre com você.</h2><p>Consulte sua turma e leve a carteirinha no celular.</p></div>
  <article class="digital-id" aria-label="Carteirinha de <?= $cardEscape($identidade['nome']) ?>">
    <div class="digital-id-brand"><img src="assets/img/logoR7.png" alt="Academia Aquática Raia7"><div><span>ACADEMIA AQUÁTICA</span><strong>Carteirinha do aluno</strong></div><i class="bi bi-water" aria-hidden="true"></i></div>
    <div class="digital-id-main">
      <div class="digital-id-photo">
        <?php if ($identidade['foto']): ?><img src="<?= $cardEscape($identidade['foto']) ?>" alt="Foto de <?= $cardEscape($identidade['nome']) ?>"><?php else: ?><span class="digital-id-initials" aria-hidden="true"><?= $cardEscape($identidade['iniciais']) ?></span><span class="digital-id-photo-label">Foto não disponível</span><?php endif; ?>
      </div>
      <div class="digital-id-person"><span class="digital-id-label">ALUNO</span><h2><?= $cardEscape($identidade['nome']) ?></h2><span class="digital-id-registration">Matrícula <strong>#<?= $cardEscape($identidade['matricula']) ?></strong></span><div class="digital-id-cpf"><span class="digital-id-label">CPF</span><span><?= $cardEscape($identidade['cpf']) ?></span></div></div>
    </div>
    <dl class="digital-id-schedule"><div><dt><i class="bi bi-clock" aria-hidden="true"></i> Horário da turma</dt><dd><?= $cardEscape($identidade['horario']) ?></dd></div><div><dt><i class="bi bi-water" aria-hidden="true"></i> Piscina</dt><dd><?= $cardEscape($identidade['piscina']) ?></dd></div></dl>
    <div class="digital-id-bottom"><span class="digital-id-tag <?= $identidade['pendente'] ? 'is-pending' : '' ?>"><i class="bi <?= $identidade['pendente'] ? 'bi-hourglass-split' : 'bi-person-vcard' ?>" aria-hidden="true"></i> <?= $identidade['pendente'] ? 'Turma pendente' : 'Identificação do aluno' ?></span><span>Raia7 · AquaManager</span></div>
  </article>
  <?php if ($identidade['pendente']): ?><div class="digital-id-notice" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><p>Sua turma ainda não foi definida. Procure a secretaria para concluir essa etapa. Esta carteirinha não confirma a matrícula em uma turma.</p></div><?php endif; ?>
  <p class="digital-id-tip"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> Use <strong>Exportar PDF</strong> para salvar uma cópia da sua carteirinha.</p>
<?php endif; ?>
</section>
