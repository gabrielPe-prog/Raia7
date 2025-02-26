<header id="header" class="header fixed-top d-flex align-items-center">
<link rel="icon" type="image/png" href="app/assets/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="app/assets/favicon/favicon.svg" />
<link rel="shortcut icon" href="app/assets/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="app/assets/favicon/apple-touch-icon.png" />
<link rel="manifest" href="app/assets/favicon/site.webmanifest" />

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
            <a class="dropdown-item d-flex align-items-center" href="controller/controllerLogout.php">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sair do Sistema</span>
            </a>
          </li>
        </ul>
      </li>

    </ul>
  </nav>

</header>