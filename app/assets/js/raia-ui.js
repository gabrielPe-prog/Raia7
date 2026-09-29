/* Small, shared accessibility and responsive enhancements. */
document.addEventListener('DOMContentLoaded', () => {
  const page = location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.sidebar-nav a').forEach(link => {
    const active = link.getAttribute('href') === page;
    link.classList.toggle('active', active);
    if (active) link.setAttribute('aria-current', 'page');
  });
  const sidebar = document.getElementById('sidebar');
  const toggle = document.querySelector('.toggle-sidebar-btn');
  if (sidebar && toggle) {
    const backdrop = document.createElement('button');
    backdrop.className = 'sidebar-backdrop';
    backdrop.setAttribute('aria-label', 'Fechar menu');
    backdrop.tabIndex = -1;
    document.body.appendChild(backdrop);
    const mobile = window.matchMedia('(max-width: 1199px)');
    const sync = () => {
      const open = mobile.matches ? document.body.classList.contains('toggle-sidebar') : !document.body.classList.contains('toggle-sidebar');
      toggle.setAttribute('aria-expanded', String(open));
      sidebar.inert = !open;
    };
    const close = () => { document.body.classList.remove('toggle-sidebar'); sync(); toggle.focus(); };
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && mobile.matches && document.body.classList.contains('toggle-sidebar')) close();
    });
    new MutationObserver(sync).observe(document.body, { attributes: true, attributeFilter: ['class'] });
    mobile.addEventListener('change', sync);
    sync();
  }
  document.querySelectorAll('table.table').forEach(table => {
    if (table.closest('.table-responsive, .datatable-container')) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'table-responsive';
    wrapper.tabIndex = 0;
    wrapper.setAttribute('role', 'region');
    wrapper.setAttribute('aria-label', 'Tabela: deslize para ver todas as colunas');
    table.before(wrapper);
    wrapper.appendChild(table);
  });
});
