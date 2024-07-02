<!-- Pagina relativa agli sponsor -->
<?php
  session_start();
  require_once('config/db.php');
  include('check_user_logged.php');
?>



<!doctype html>

<html lang="it">

  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>
    
    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>
  
    <!-- Descrizione iniziale -->
    <div class="container my-5">
      
      <!-- Top sponsor -->
      <div class="row">
        <div class="col-12">
          <span class="fs-2 fw-bold" id="font_diverso">Sponsor</span>
        </div>
      </div>

      <hr/>

      <div class="row gy-3">
        <!-- Geom. Tiziano Tarsi -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto " >
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/geom_tarsi.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Geom. Tiziano Tarsi</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Viale P. Bonòpera, 2 - Senigallia
                </p>
                <!-- Link a sito web 
                <a href="https://www.allservicetrasporti.com/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                -->
              </div>
            </div>
          </div>
        </div>
        
        <!-- Si con te -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/siconte.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Si con te</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Strada Provinciale Sant'Angelo, 159 - Senigallia
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.siconte.it/it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- La collina sul lago -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/lacollina.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">La collina sul lago</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via della Draga, 9 - Montignano
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.lacollinadellago.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Autonoleggio Mariotti -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/mariotti.jpg" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Autonoleggio Mariotti</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via delle Genziane, 18 - Senigallia
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.automariotti.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>

        
      </div>

      
    </div>
    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
    <script>
      $(document).ready(function() {
        var url = window.location.pathname;
        $('.nav-link').each(function() {
          if ($(this).attr('href') === url) {
            $(this).addClass('active');
          }
        });
      });
    </script>


  </body>

</html>