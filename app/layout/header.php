<header id="header" class="header fixed-top d-flex align-items-center">
  <link rel="shortcut icon" href="/assets/favicon/favicon.ico" />
  <link rel="icon" type="image/png" href="/assets/favicon/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/assets/favicon/favicon.svg" />
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png" />
  <link rel="manifest" href="/assets/favicon/site.webmanifest" />

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

  <div class="d-flex align-items-center justify-content-between">
    <a href="paginaInicial.php" class="logo d-flex align-items-center">
      <img src="assets/img/logoR7.png" alt="">
      <span class="d-none d-lg-block">Academia Aquática Raia7</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-person-badge-fill"></i>
          <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['nome']; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li>
            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#alterarSenhaModal">
              <i class="bi bi-key"></i>
              <span>Alterar Senha</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="controller/controllerLogout.php">
              <i class="bi bi-door-open"></i>
              <span>Sair do Sistema</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>


</header>

<div class="modal fade" id="alterarSenhaModal" tabindex="-1" aria-labelledby="alterarSenhaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alterarSenhaModalLabel">Alterar Senha</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAlterarSenha" action="controller/controllerAtualizaSenha.php" method="POST">
          <input type="text" value="1" hidden name="tipo">
          <div class="mb-3">
            <label for="cpf" class="form-label">CPF para validação</label>
            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF" required onkeyup="CpfMask(this)">
            <div class="form-text">Digite seu CPF para confirmar sua identidade.</div>
          </div>
          <div class="mb-3">
            <label for="nova_senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
          </div>
          <div class="mb-3">
            <label for="confirma_senha" class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
            <div id="senhaHelp" class="form-text text-danger"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success" id="btnAlterarSenha">Alterar Senha</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

  function CpfMask(input) {
    const value = input.value.replace(/\D/g, '');
    input.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
  }

  document.addEventListener('DOMContentLoaded', function() {
    const formAlterarSenha = document.getElementById('formAlterarSenha');
    const novaSenha = document.getElementById('nova_senha');
    const confirmaSenha = document.getElementById('confirma_senha');
    const senhaHelp = document.getElementById('senhaHelp');
    const btnAlterarSenha = document.getElementById('btnAlterarSenha');

    function validarSenhas() {
      if (novaSenha.value !== confirmaSenha.value) {
        senhaHelp.textContent = 'As senhas não coincidem!';
        btnAlterarSenha.disabled = true;
        return false;
      } else {
        senhaHelp.textContent = '';
        btnAlterarSenha.disabled = false;
        return true;
      }
    }

    novaSenha.addEventListener('input', validarSenhas);
    confirmaSenha.addEventListener('input', validarSenhas);

    formAlterarSenha.addEventListener('submit', function(e) {
      if (!validarSenhas()) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'As senhas não coincidem!'
        });
      }
    });
  });
</script>