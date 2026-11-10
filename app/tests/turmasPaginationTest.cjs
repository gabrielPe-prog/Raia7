const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
function element(extra = {}) {
  return {value: '', hidden: false, disabled: false, textContent: '', listeners: {}, addEventListener(type, fn) { this.listeners[type] = fn; }, focus() {}, ...extra};
}
const ids = Object.fromEntries(['classSearch', 'classSelect', 'classReset', 'classPrev', 'classNext', 'classPage', 'classResults', 'classNoResults'].map(id => [id, element()]));
ids.classSelect.value = 'all';
const groups = Array.from({length: 14}, (_, index) => {
  const row = element({dataset: {studentName: index === 13 ? 'João' : 'Maria', studentCpf: String(index)}});
  const count = element();
  const empty = element();
  return element({dataset: {group: String(index)}, querySelectorAll(selector) { return selector.includes(':not') && row.hidden ? [] : [row]; }, querySelector(selector) { return selector === '[data-group-count]' ? count : empty; }});
});
const document = {
  getElementById: id => ids[id] || null,
  querySelectorAll: () => groups,
  addEventListener: (_, fn) => fn(),
};
vm.runInNewContext(fs.readFileSync(require('node:path').join(__dirname, '../assets/js/turmas.js'), 'utf8'), {document});
const visible = () => groups.filter(group => !group.hidden).map(group => group.dataset.group);
assert.deepEqual(visible(), ['0', '1', '2', '3', '4', '5']);
assert.equal(ids.classPrev.disabled, true);
ids.classNext.listeners.click();
assert.deepEqual(visible(), ['6', '7', '8', '9', '10', '11']);
ids.classNext.listeners.click();
assert.deepEqual(visible(), ['12', '13']);
assert.equal(ids.classNext.disabled, true);
ids.classPrev.listeners.click();
assert.equal(visible()[0], '6');
ids.classSearch.value = 'joao';
ids.classSearch.listeners.input();
assert.deepEqual(visible(), ['13']);
assert.equal(ids.classPage.textContent, 'Página 1 de 1');
ids.classSearch.value = 'inexistente';
ids.classSearch.listeners.input();
assert.equal(visible().length, 0);
assert.equal(ids.classNoResults.hidden, false);
ids.classReset.listeners.click();
assert.equal(visible().length, 6);
ids.classSelect.value = '13';
ids.classSelect.listeners.change();
assert.deepEqual(visible(), ['13']);
console.log('OK: próxima, anterior, limites, busca, filtros e limpeza; paginação independente do formulário de matrícula.');

// O cartão sem turma permanece visível sem consumir uma vaga na paginação.
groups.unshift(element({dataset: {group: 'sem-turma'}, querySelectorAll() { return []; }, querySelector() { return element(); }}));
ids.classSelect.value = 'all';
vm.runInNewContext(fs.readFileSync(require('node:path').join(__dirname, '../assets/js/turmas.js'), 'utf8'), {document});
assert.deepEqual(visible(), ['sem-turma', '0', '1', '2', '3', '4', '5']);
ids.classNext.listeners.click();
assert.deepEqual(visible(), ['sem-turma', '6', '7', '8', '9', '10', '11']);
ids.classNext.listeners.click();
assert.deepEqual(visible(), ['sem-turma', '12', '13']);
assert.equal(ids.classPage.textContent, 'Página 3 de 3');
console.log('OK: cartão sem turma permanece no topo em todas as páginas.');
