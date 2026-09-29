document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editForm');
  if (!form) return;
  const phoneMask = value => {
    const digits = value.replace(/\D/g, '').slice(0, 11);
    if (!digits) return '';
    if (digits.length <= 2) return `(${digits}`;
    const local = digits.slice(2);
    const split = local.length > 8 ? 5 : 4;
    return `(${digits.slice(0, 2)}) ${local.slice(0, split)}${local.length > split ? '-' + local.slice(split) : ''}`;
  };
  const cpfMask = value => value.replace(/\D/g, '').slice(0, 11).replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3}\.\d{3})(\d)/, '$1.$2').replace(/^(\d{3}\.\d{3}\.\d{3})(\d)/, '$1-$2');
  const cepMask = value => value.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2');
  const applyMask = (input, format) => {
    if (!input) return;
    input.value = format(input.value);
    input.addEventListener('beforeinput', event => {
      // Backspace beside a separator should also remove the preceding digit.
      const caret = input.selectionStart;
      if (event.inputType === 'deleteContentBackward' && caret === input.selectionEnd && caret > 0) {
        let start = caret - 1;
        while (start > 0 && /\D/.test(input.value[start])) start--;
        input.setSelectionRange(start, caret);
      }
    });
    input.addEventListener('input', () => {
      const digitsBeforeCaret = input.value.slice(0, input.selectionStart).replace(/\D/g, '').length;
      input.value = format(input.value);
      let caret = 0;
      let digits = 0;
      while (caret < input.value.length && digits < digitsBeforeCaret) {
        if (/\d/.test(input.value[caret])) digits++;
        caret++;
      }
      input.setSelectionRange(caret, caret);
    });
  };
  applyMask(document.getElementById('profile_cpf'), cpfMask);
  applyMask(document.getElementById('profile_contato'), phoneMask);
  applyMask(document.getElementById('profile_cep'), cepMask);
  const button = document.getElementById('profileSave');
  const status = document.getElementById('profileStatus');
  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (button.disabled || !form.reportValidity()) return;
    button.disabled = true;
    button.textContent = 'Salvando…';
    status.className = 'mt-3 text-muted';
    status.textContent = 'Salvando suas informações…';
    form.setAttribute('aria-busy', 'true');
    try {
      const response = await fetch(form.action, { method:'POST', body:new FormData(form), credentials:'same-origin' });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Não foi possível salvar.');
      location.reload();
    } catch (error) {
      status.className = 'mt-3 text-danger';
      status.textContent = error instanceof SyntaxError ? 'Resposta inesperada. Tente novamente.' : error.message;
      button.disabled = false;
      button.textContent = 'Salvar alterações';
      form.removeAttribute('aria-busy');
    }
  });
});
