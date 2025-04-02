<aside id="sidebar" class="sidebar">

  <?php
  if ($_SESSION['nivel'] == 1): ?>

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="paginaInicial.php">
          <i class="bi bi-bar-chart-fill"></i>
          <span>Informações Gerais</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="alunos.php">
          <i class="bi bi-person-arms-up"></i>
          <span>Alunos</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="turmas.php">
          <i class="bi bi-person-vcard-fill"></i>
          <span>Turmas</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="financeiro.php">
          <i class="bi bi-cash-coin"></i>
          <span>Financeiro</span>
        </a>
      </li>
    </ul>

  <?php else: ?>

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="paginaInicial.php">
          <i class="bi bi-bar-chart-fill"></i>
          <span>Informações Gerais</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="infoAlunos.php">
        <i class="bi bi-card-list"></i>
          <span>Informações do Aluno</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="carteirinha.php">
          <i class="bi bi-person-vcard-fill"></i>
          <span>Carteirinha</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="infoPagAlunos.php">
          <i class="bi bi-cash-coin"></i>
          <span>Informações Financeiras</span>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link " href="pagamentosAluno.php">
          <i class="bi bi-cash-coin"></i>
          <span>Pagamentos</span>
        </a>
      </li> -->
    </ul>

  <?php endif; ?>
</aside>