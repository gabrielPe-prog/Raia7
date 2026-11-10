document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('classSearch');
  const select = document.getElementById('classSelect');
  if (!search || !select) return;
  const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('pt-BR');
  const groups = [...document.querySelectorAll('[data-group]')].map(element => ({
    element,
    rows: [...element.querySelectorAll('[data-student-name]')].map(row => ({
      row, name: normalize(row.dataset.studentName), cpf: row.dataset.studentCpf.replace(/\D/g, '')
    }))
  }));
  let page = 1;
  const pageSize = 6;
  function filter() {
    const term = normalize(search.value.trim());
    const digits = term.replace(/\D/g, '');
    const cpfSearch = digits.length > 0 && /^[\d.\-\s]+$/.test(term);
    let count = 0;
    let visibleGroups = 0;
    const eligible = [];
    const pinned = [];
    groups.forEach(({element, rows}) => {
      const selected = select.value === 'all' || select.value === element.dataset.group;
      let matches = 0;
      rows.forEach(({row, name, cpf}) => {
        const match = !term || name.includes(term) || (cpfSearch && cpf.includes(digits));
        row.hidden = !match;
        if (match) matches++;
      });
      // A selected empty group remains visible with its own empty state.
      element.hidden = !selected || (Boolean(term) && matches === 0 && select.value === 'all');
      if (!element.hidden) { count += matches; visibleGroups++; (element.dataset.group === 'sem-turma' ? pinned : eligible).push(element); }
      if (term && !element.hidden) element.open = true;
      element.querySelector('[data-group-count]').textContent = term ? `${matches} de ${rows.length}` : String(rows.length);
      element.querySelector('.class-no-match').hidden = !term || matches > 0 || rows.length === 0;
    });
    const pages = Math.max(1, Math.ceil(eligible.length / pageSize));
    page = Math.max(1, Math.min(page, pages));
    eligible.forEach((element, index) => { element.hidden = index < (page - 1) * pageSize || index >= page * pageSize; });
    document.getElementById('classPage').textContent = `Página ${page} de ${pages}`;
    document.getElementById('classPrev').disabled = page <= 1;
    document.getElementById('classNext').disabled = page >= pages;
    const visibleStudents = [...pinned, ...eligible.slice((page - 1) * pageSize, page * pageSize)].reduce((total, element) => total + element.querySelectorAll('[data-student-name]:not([hidden])').length, 0);
    document.getElementById('classResults').textContent = `${visibleStudents} aluno(s) nesta página · ${count} aluno(s) nas turmas filtradas.`;
    document.getElementById('classNoResults').hidden = visibleGroups > 0;
  }
  search.addEventListener('input', () => { page = 1; filter(); });
  select.addEventListener('change', () => { page = 1; filter(); });
  document.getElementById('classReset').addEventListener('click', () => { search.value = ''; select.value = 'all'; page = 1; groups.forEach(({element}) => { element.open = element.dataset.group === 'sem-turma'; }); filter(); search.focus(); });
  document.getElementById('classPrev').addEventListener('click', () => { page--; filter(); });
  document.getElementById('classNext').addEventListener('click', () => { page++; filter(); });
  filter();
  const student = document.getElementById('enrollmentStudent');
  if (!student) return;
  const options = [...student.options].slice(1);
  const updatePrevious = () => { document.getElementById('previousClass').value = student.selectedOptions[0]?.dataset.current || ''; };
  student.addEventListener('change', updatePrevious);
  document.getElementById('enrollmentSearch').addEventListener('input', event => {
    const term = normalize(event.target.value.trim());
    const digits = term.replace(/\D/g, '');
    options.forEach(option => {
      option.hidden = !normalize(option.textContent).includes(term) && !(digits && /^[\d.\-\s]+$/.test(term) && option.textContent.replace(/\D/g, '').includes(digits));
      option.disabled = option.hidden;
    });
    if (student.selectedOptions[0]?.hidden) student.value = '';
    updatePrevious();
  });
  document.getElementById('enrollmentForm').addEventListener('submit', event => {
    updatePrevious();
    const option = student.selectedOptions[0];
    const destination = document.getElementById('enrollmentClass');
    if (option?.dataset.current === destination.value) {
      event.preventDefault();
      window.alert('O aluno já está nesta turma.');
    } else if (option?.dataset.assigned === '1' && !window.confirm(`Transferir ${option.textContent} para ${destination.selectedOptions[0].textContent}?`)) {
      event.preventDefault();
    }
  });
  updatePrevious();
});
