<!-- Pagina chi siamo per l'utente semplice -->
<?php
  session_start();
  require_once('config/db.php');
  include('check_user_logged.php');

  $id=$_REQUEST['id'];

  $query_articoli = "select a.*,s.descrizione,s.anno_inizio,s.anno_fine,s.girone
  FROM articoli a
  inner join stagioni s on s.id_stagione=a.id_stagione
  where a.id='$id'";
  
  $articoli = mysqli_query($con,$query_articoli);
  $row = mysqli_fetch_assoc($articoli);

  $tags = explode(",", $row['tags']);

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
    <?php include 'elements/carousel_audax.php'; ?>


    <!-- Descrizione iniziale -->
    <div class="container my-5">
      <div class="row g-5 my-3">
        <div class="col-12">
          
          <span class="fs-2 fw-bold" id="font_diverso"><?php echo $row['titolo'] ?></span>
          <?php
            // Ottieni l'URL della pagina corrente
            $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          ?>
          <!-- Data pubblicazione -->
          <div>
            <small class="text-muted ">
              <?php 
                $data_pubblicazione = $row['data_pubblicazione'];
                $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
                echo $formatted_date;
              ?>
            </small>
          </div>
        
          <div>
            <?php
              // Cicliamo su ciascun tag e lo stampiamo in un badge separato
              foreach ($tags as $tag) {
                echo '<span class="badge bg-secondary text-white" style="font-size:12px;font-weight:400">#' . htmlspecialchars(trim($tag)) . '</span> ';
              }
            ?>
          </div>
        </div>
            
          
        <div class="col-12 col-lg-8">
          <!-- Contenuto articolo -->
          <p class="" style="font-size:14px">
            <?php echo nl2br($row['contenuto']); ?>
          </p>

          <!-- Icone condivisione social --> 
          <div class="">
            <a href="https://www.instagram.com/?url=<?php echo urlencode($current_url); ?>" class="text-decoration-none text-dark" target="_blank">
                <i class='bx bxl-instagram bx-sm'></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>" class="text-decoration-none text-dark" target="_blank">
                <i class='bx bxl-facebook-circle bx-sm'></i>
            </a>
            <a href="https://wa.me/?text=<?php echo urlencode($current_url); ?>" class="text-decoration-none text-dark" target="_blank">
                <i class='bx bxl-whatsapp bx-sm'></i>
            </a>
            <a href="https://t.me/share/url?url=<?php echo urlencode($current_url); ?>" class="text-decoration-none text-dark" target="_blank">
                <i class='bx bxl-telegram bx-sm'></i>
            </a>
            <span class="fw-bold float-end">
              <?php echo $row['autore'] ?>
            </span>
          </div>
        </div>
        
        <div class="col-12 col-lg-4">
          <img src="image/articoli/<?php echo $row['immagine_url']; ?>" class="w-100 img-fluid rounded "/>
        </div>

      </div>
      <br/>
      <br/>
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