<?php
  session_start();
  require_once('config/db.php');

  $id = $_REQUEST['id'];

  $query_articoli = "
  SELECT a.*, ai.descrizione as intestazione
  FROM articoli a
  LEFT JOIN articoli_intestazioni ai ON ai.id = a.id_intestazione
  WHERE a.id='$id'";
  
  $articoli = mysqli_query($con, $query_articoli);
  $row = mysqli_fetch_assoc($articoli);

  $tags = explode(",", $row['tags']);

  // URL assoluto dell'immagine
  $image_url = "http://$_SERVER[HTTP_HOST]/image/articoli/" . $row['immagine_url'];

  // URL della pagina corrente
  $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<!doctype html>
<html lang="it">
  <!-- Head da utilizzare nelle pagine "base" ovvero utilizzate dall'utente semplice -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- TITLE -->
    <title>Audax 1970</title>

    <meta name="description" content="Serie A2 Nazionale">
    <meta name="google-site-verification" content="+nxGUDJ4QpAZ5l9Bsjdi102tLVC21AIh5d1Nl23908vVuFHs34=">
    <meta name="robots" content="nofollow">
    
    <!-- Meta tag Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($row['titolo']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr(strip_tags($row['contenuto']), 0, 200)); ?>...">
    <meta property="og:image" content="<?php echo htmlspecialchars($image_url); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta property="og:type" content="article">

    <!-- Altri meta tag per SEO -->
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($row['contenuto']), 0, 150)); ?>...">
    <meta name="keywords" content="<?php echo htmlspecialchars(implode(", ", $tags)); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($row['autore']); ?>">

    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!--Mio foglio di stile-->
    <link rel="stylesheet" href="css/style.css">

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <!-- Box icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    

    <!-- Icona title -->
    <link rel="icon" href="image/loghi/logo.png" type ="image/x-icon">

    <!-- Icona per dispositivi Apple -->
    <link rel="apple-touch-icon" sizes="180x180" href="image/loghi/logo.png">
    
    <!-- Icona per dispositivi Android -->
    <link rel="icon" type="image/png" sizes="192x192" href="image/loghi/logo.png">

</head>


<style>
  
</style>


  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
        <?php include 'elements/navbar_red.php'; ?>
    </div>
    
    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel_audax.php'; ?>

    <!-- Descrizione iniziale -->
    <div class="container my-5 px-4">
      <div class="row g-3 margin-mobile">
        <div class="col-12 align-middle">
          <h2 id="font_diverso">
            <?php echo htmlspecialchars($row['titolo']); ?>
            <?php if($row['intestazione'] !== null ){ ?>
              <div class="float-end">
                <span class="badge bg-secondary bebas">
                  <?php echo htmlspecialchars($row['intestazione']); ?>
                </span>
              </div>
            <?php } ?>
          </h2>

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

        </div>
      </div>

      <hr/>
      
      <div class="row g-3">
        <div class="col-12 col-lg-8">
          <!-- Contenuto articolo -->
          <p class="" style="font-size:14px">
            <?php echo nl2br(htmlspecialchars($row['contenuto'])); ?>
          </p>

          <!-- Icone condivisione social --> 
          <div class="">
            <a href="https://www.instagram.com/ss_audax1970_official" class="text-decoration-none text-dark" target="_blank">
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
              <?php echo htmlspecialchars($row['autore']); ?>
            </span>
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
        
        <div class="col-12 col-lg-4">
          <img src="image/articoli/<?php echo htmlspecialchars($row['immagine_url']); ?>" class="w-100 img-fluid rounded" alt="<?php echo htmlspecialchars($row['titolo']); ?>"/>
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
        $('.nav-link').each(function() {
          if ($(this).attr('href') === url) {
            $(this).addClass('active');
          }
        });
      });
    </script>
  </body>
</html>
