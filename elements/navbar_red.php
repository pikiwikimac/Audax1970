<style>
  /* CSS for dropdown-submenu */
.dropdown-submenu {
  position: relative;
}

.dropdown-submenu .dropdown-menu {
  top: 0;
  left: 100%;
}

.dropdown-menu{
  background: #BC0524;
  border:none;
}

.dropdown-item{
  color:white;
}

.dropdown-item:hover{
  color: #fedc00;
  background: #BC0524;
}

</style>

<nav class="navbar navbar-expand-lg fixed-top align-middle" style="background: #BC0524; border-bottom:10px solid #fedc00">
  <div class="container">
    <!-- Logo + Nome squadra -->
    <a class="navbar-brand text-light" href="../index.php" >
      <!-- Logo -->
      <img width="40" height="40" src="../image/loghi/logo.png" class="logo rounded mb-2" alt="Audax Logo" decoding="async" loading="lazy" >
      &nbsp; &nbsp; 
      <!-- Nome squadra -->
      <span class="fs-5 ">
        Audax 1970
      </span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!--  -->
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/index.php') echo 'active'; ?>" aria-current="page" href="index.php" id="home" name="home">
            Home
          </a>
        </li>
        <!--  -->
        <li class="nav-item dropdown me-3 ">
          <a class="nav-link dropdown-toggle  text-light"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Prima squadra
          </a>
          <ul class="dropdown-menu" >
            <li><a class="dropdown-item" href="squadra.php?id_squadra=1" id="rosa" name="rosa">Rosa</a></li>
            <li><a class="dropdown-item" href="calendario.php?id_squadra=1&id_stagione=1" id="calendario" name="calendario">Calendario</a></li>
            <li><a class="dropdown-item" href="classifica.php?view=A2_2024_2025" id="classifica" name="classifica">Classifica</a></li>
          </ul>
        </li>
        <!--  -->
        <li class="nav-item dropdown me-3 ">
          <a class="nav-link dropdown-toggle  text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Settore giovanile</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#">Under 19</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="squadra.php?id_squadra=3">Rosa</a></li>
                <li><a class="dropdown-item" href="calendario.php?id_squadra=3&id_stagione=6">Calendario</a></li>
                <li><a class="dropdown-item" href="classifica.php?view=U19_2024_2025&squadra=U19">Classifica</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#">Under 17</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="squadra.php?id_squadra=4">Rosa</a></li>
                <li><a class="dropdown-item" href="calendario.php?id_squadra=4&id_stagione=7">Calendario</a></li>
                <li><a class="dropdown-item" href="classifica.php?view=U17_2024_2025&squadra=U17">Classifica</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#">Under 15</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="squadra.php?id_squadra=6">Rosa</a></li>
                <li><a class="dropdown-item" href="calendario.php?id_squadra=6&id_stagione=8">Calendario</a></li>
                <li><a class="dropdown-item" href="classifica.php?view=U15_2024_2025&squadra=U15">Classifica</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <!--
        <li class="nav-item me-3 ">
          <a class="nav-link  text-light <?php if ($_SERVER['PHP_SELF'] === '/gallery.php') echo 'active'; ?>" href="gallery.php" id="gallery" name="gallery">Gallery</a>
        </li>
          -->
        <!--  -->
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/organigramma.php') echo 'active'; ?>" aria-current="page" href="organigramma.php" id="organigramma" name="organigramma">
            Organigramma
          </a>
        </li>
        <!--  -->
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/chisiamo.php') echo 'active'; ?>" href="chisiamo.php" id="chi-siamo" name="chi-siamo">Chi siamo</a>
        </li>
        <!--  -->
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/sponsor.php') echo 'active'; ?>" href="sponsor.php" id="sponsor" name="sponsor">Sponsor</a>
        </li>
        <!--  -->
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/articoli.php') echo 'active'; ?>" href="articoli.php" id="articoli" name="articoli">Articoli</a>
        </li>
        <!-- 
        <li class="nav-item me-3 ">
          <a class="nav-link text-light <?php if ($_SERVER['PHP_SELF'] === '/shop.php') echo 'active'; ?>" href="shop.php" id="shop" name="shop">Shop</a>
        </li>
         -->
      </ul>
      <div class="d-flex">
        <a class="text-decoration-none me-2 text-light" href="https://www.facebook.com/ssaudax1970">
          <i class='bx bxl-facebook-circle bx-tada-hover bx-sm'></i>
        </a>
        <a class="text-decoration-none me-2 text-light" href="https://www.instagram.com/ss_audax1970_official/">
          <i class='bx bxl-instagram-alt bx-tada-hover bx-sm'></i>
        </a>
        <a class="text-decoration-none me-2 text-light" href="mailto:audax1970@gmail.com">
          <i class='bx bxl-gmail bx-tada-hover bx-sm'></i>
        </a>
        <a class="text-decoration-none me-2 text-light" href="login/login.php">
          <i class='bx bx-log-in bx-tada-hover bx-sm'></i>
        </a>
      </div>
    </div>
  </div>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var dropdowns = document.querySelectorAll('.dropdown-submenu');

    dropdowns.forEach(function (dropdown) {
      dropdown.addEventListener('mouseover', function (e) {
        var subMenu = this.querySelector('.dropdown-menu');
        subMenu.classList.add('show');
      });

      dropdown.addEventListener('mouseout', function (e) {
        var subMenu = this.querySelector('.dropdown-menu');
        subMenu.classList.remove('show');
      });
    });
  });
</script>
