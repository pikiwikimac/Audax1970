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
    <div class="container my-5 px-4">
      
      <!-- Top sponsor -->
      <div class="row">
        <div class="col-12">
          <h1 id="font_diverso">Sponsor</h1>
        </div>
      </div>

      <hr/>

      <div class="row gy-3">
        
        <!-- Fiorini -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/fiorini.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Fiorini</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via Giambattista Fiorini,25 - 60019 Senigallia (AN)
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.fiorini.biz" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Francesconi costruzioni -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/francesconi.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Francesconi costruzioni</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via del Buzzo, 4, 61037 Mondolfo PU
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.francesconicostruzioni.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
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
                <img src="image/sponsor/mariotti.png" alt="" width="300" height="300" class="img-fluid p-2 ">
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

        <!-- TMS -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/tms.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">T.M.S. Impianti S.r.l</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Viale G. Leopardi, 225, 60019 Senigallia AN
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.termoidraulicatms.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Arkingegni -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/arkingegneri.jpg" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Arkingegni</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via Umberto I, 29, 60018 Montemarciano AN
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.arkingegnisrl.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Verde mare -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/verdemare.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Verde mare</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Str. delle Vigne in Frazione Scapezzano, 273, 60019 Senigallia AN
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.verdemarecountryhouse.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Vetreria Misa -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/vetreria_misa.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Vetreria Misa</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via Veronese, 36, 60019 Senigallia AN
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://www.vetreriamisa.it/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Laprogema -->
        <div class="col-12 text-center col-sm-6 ">
          <div class="flip-card mx-auto ">
            <div class="flip-card-inner">
              <!-- Fronte : Immagine -->
              <div class="flip-card-front">
                <img src="image/sponsor/laprogema.png" alt="" width="300" height="300" class="img-fluid p-2 ">
              </div>
              <!-- Retro -->
              <div class="flip-card-back">
                <!-- Nome azienda -->
                <p class="title">Laprogema S.r.l</p>
                <!-- Sede azienda -->
                <p class="location">
                  <i class='bx bxs-map'></i> 
                  Via dell'Industria, 60012 Brugnetto AN
                </p>
                <!-- Link a sito web -->
                <p class="sito_web">
                  <a href="https://laprogemasrl.com/" class="text-decoration-none text-dark "><i class='bx bx-globe align-middle'></i> Sito web</a>
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