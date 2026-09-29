<?php
require_once __DIR__ . '/../service/reportData.php';
$reportDefinition = reportDefinitions()[$reportType] ?? null;
if (!$reportDefinition || ($reportDefinition['admin'] && (string) ($_SESSION['nivel'] ?? '') !== '1')) {
    return;
}
if ($reportType === 'carteirinha'): ?>
<form id="exportReportForm" action="exportar.php" method="get">
  <input type="hidden" name="tela" value="carteirinha">
  <button type="submit" class="btn btn-primary report-trigger" id="exportReportSubmit"><i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Exportar PDF</button>
  <div id="exportReportStatus" class="report-status" role="status" aria-live="polite"></div>
</form>
<script src="assets/js/report-export.js" defer></script>
<?php return; endif;
$exportNow = new DateTimeImmutable('now', new DateTimeZone('America/Recife'));
$exportMonths = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
?>
<button type="button" class="btn btn-primary report-trigger" data-bs-toggle="modal" data-bs-target="#exportReportModal"><i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Exportar PDF</button>
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h2 class="modal-title" id="exportReportTitle">Exportar PDF</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
      <form id="exportReportForm" action="exportar.php" method="get">
        <div class="modal-body">
          <div class="report-preview"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i><div><strong><?= htmlspecialchars($reportDefinition['title'], ENT_QUOTES, 'UTF-8') ?></strong><span>Relatório completo · PDF A4</span></div></div>
          <p class="report-help" id="exportPeriodHelp"><?= $reportDefinition['financial'] ? 'Serão incluídos todos os pagamentos com data de pagamento no mês e ano selecionados.' : 'Selecione o mês e ano de referência. O PDF contém os dados atuais na emissão, não um histórico do período.' ?></p>
          <input type="hidden" name="tela" value="<?= htmlspecialchars($reportType, ENT_QUOTES, 'UTF-8') ?>">
          <div class="row g-3">
            <div class="col-7"><label for="exportMonth" class="form-label">Mês<?= $reportDefinition['financial'] ? '' : ' de referência' ?></label><select id="exportMonth" name="mes" class="form-select" aria-describedby="exportPeriodHelp" required><?php foreach ($exportMonths as $number => $name): ?><option value="<?= $number ?>" <?= $number === (int) $exportNow->format('n') ? 'selected' : '' ?>><?= $name ?></option><?php endforeach; ?></select></div>
            <div class="col-5"><label for="exportYear" class="form-label">Ano</label><input id="exportYear" name="ano" type="number" class="form-control" min="1900" max="2100" step="1" value="<?= $exportNow->format('Y') ?>" required></div>
          </div>
          <p class="report-help mt-3 mb-0">Inclui todos os registros do relatório, independentemente da paginação ou busca da tela.</p>
          <div id="exportReportStatus" class="report-status" role="status" aria-live="polite"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button><button type="submit" class="btn btn-primary" id="exportReportSubmit"><i class="bi bi-download me-2" aria-hidden="true"></i>Baixar PDF</button></div>
      </form>
    </div>
  </div>
</div>
<script src="assets/js/report-export.js" defer></script>
