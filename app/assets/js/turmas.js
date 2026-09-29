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
  function filter() {
    const term = normalize(search.value.trim());
    const digits = term.replace(/\D/g, '');
    const cpfSearch = digits.length > 0 && /^[\d.\-\s]+$/.test(term);
    let count = 0;
    let visibleGroups = 0;
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
      if (!element.hidden) { count += matches; visibleGroups++; }
      element.querySelector('[data-group-count]').textContent = term ? `${matches} de ${rows.length}` : String(rows.length);
      element.querySelector('.class-no-match').hidden = !term || matches > 0 || rows.length === 0;
    });
    document.getElementById('classResults').textContent = `${count} aluno(s) exibido(s) · Alunos em ordem alfabética em cada turma.`;
    document.getElementById('classNoResults').hidden = visibleGroups > 0;
  }
  search.addEventListener('input', filter);
  select.addEventListener('change', filter);
  document.getElementById('classReset').addEventListener('click', () => { search.value = ''; select.value = 'all'; filter(); search.focus(); });
  filter();
});
