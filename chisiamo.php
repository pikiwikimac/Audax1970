<!-- Pagina chi siamo per l'utente semplice -->
<?php
  session_start();
?>
<?php include('check_user_logged.php'); ?>


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

      <div class="row">
        <div class="col-12">
          <span class="fs-2 fw-bold" id="font_diverso">Chi siamo</span>
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
        console.log(url===($(this).attr('href')));
        $('.nav-link').each(function() {
          if ($(this).attr('href') === url) {
            $(this).addClass('active');
          }
        });
      });
    </script>
  </body>
</html>