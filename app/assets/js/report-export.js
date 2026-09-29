document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('exportReportForm');
  if (!form) return;
  const submit = document.getElementById('exportReportSubmit');
  const status = document.getElementById('exportReportStatus');
  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (submit.disabled || !form.reportValidity()) return;
    const originalLabel = submit.innerHTML;
    submit.disabled = true;
    form.setAttribute('aria-busy', 'true');
    submit.textContent = 'Gerando PDF…';
    status.className = 'report-status';
    status.textContent = 'Preparando todas as informações. Aguarde o download.';
    const params = new URLSearchParams(new FormData(form));
    try {
      const response = await fetch(`${form.action}?${params}`, { credentials: 'same-origin', cache: 'no-store' });
      if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        throw new Error(data.error || 'Não foi possível exportar. Tente novamente.');
      }
      if (!response.headers.get('Content-Type')?.includes('application/pdf')) {
        throw new Error('O servidor não retornou um PDF válido. Entre novamente e tente exportar.');
      }
      const blob = await response.blob();
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = params.get('tela') === 'carteirinha'
        ? 'raia7-carteirinha.pdf'
        : `raia7-${params.get('tela')}-${params.get('ano')}-${params.get('mes').padStart(2, '0')}.pdf`;
      document.body.appendChild(link);
      link.click();
      link.remove();
      setTimeout(() => URL.revokeObjectURL(url), 10000);
      status.className = 'report-status text-success';
      status.textContent = 'PDF gerado! Confira os downloads do seu navegador.';
    } catch (error) {
      status.className = 'report-status text-danger';
      status.textContent = error.message || 'Falha de conexão. Tente novamente.';
    } finally {
      submit.disabled = false;
      submit.innerHTML = originalLabel;
      form.removeAttribute('aria-busy');
    }
  });
});
