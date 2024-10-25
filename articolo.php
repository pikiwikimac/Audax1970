<?php
  session_start();
  require_once('utilities/q_articoli.php');
  require_once('config/variables.php');

  // Ottieni l'ID dell'articolo
  $id = $_REQUEST['id'];

  // Richiama la funzione per ottenere l'articolo
  $row = getArticoloById($con, $id);

  // Controllo se l'articolo esiste
  if (!$row) {
    die("Articolo non trovato");
  }

  // Gestione dei tag
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
    <title><?php echo $title ?></title>

    
    <meta name="google-site-verification" content="+nxGUDJ4QpAZ5l9Bsjdi102tLVC21AIh5d1Nl23908vVuFHs34=">
    <meta name="robots" content="index, follow">
    
    <!-- Meta tag Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($row['titolo']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr(strip_tags($row['contenuto']), 0, 200)); ?>...">
    <meta property="og:image" content="<?php echo htmlspecialchars($image_url); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta property="og:type" content="article">

    <!-- Altri meta tag per SEO -->
    <meta name="title" content="<?php echo htmlspecialchars(substr(strip_tags($row['titolo']), 0, 150)); ?>...">
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($row['contenuto']), 0, 150)); ?>...">
    <meta name="keywords" content="<?php echo htmlspecialchars(implode(", ", $tags)); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($row['autore']); ?>">

    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!--Mio foglio di stile-->
    <link rel="stylesheet" href="css/style.css">

    <!-- Box icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

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
        <?php include 'elements/navbar.php'; ?>
    </div>
    
    <!-- Descrizione iniziale -->
    <div class="container" style="margin-top:7rem!important">
      <div class="row g-3 ">
        <div class="col-12 align-middle">
          <h2 class="bebas">
            <?php echo htmlspecialchars($row['titolo']); ?>
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

            <?php if($row['intestazione'] !== null ){ ?>
              <div class="float-end">
                <span class="badge bg-secondary " >
                  <?php echo htmlspecialchars($row['intestazione']); ?>
                </span>
              </div>
            <?php } ?>
          </div>

        </div>
      </div>

      <hr/>
      
      <div class="row g-3">
        <div class="col-12 col-lg-8 pe-5">
          <!-- Contenuto articolo -->
          <p class="" >
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
          <?php
            // Verifica se l'array $tags non è vuoto
            if (!empty($tags)) {
                // Filtra i tag per rimuovere quelli vuoti
                $filteredTags = array_filter($tags, function($tag) {
                    return !empty(trim($tag));
                });

                // Verifica se ci sono tag validi dopo il filtro
                if (!empty($filteredTags)) {
                    echo '<div>';
                    // Cicliamo su ciascun tag e lo stampiamo in un badge separato
                    foreach ($filteredTags as $tag) {
                        echo '<span class="badge bg-secondary text-white" style="font-size:12px;font-weight:400">#' . htmlspecialchars(trim($tag)) . '</span> ';
                    }
                    echo '</div>';
                }
            }
            ?>


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
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo htmlspecialchars($row['titolo']); ?>",
        "image": "https://example.com/image/articoli/<?php echo htmlspecialchars($row['immagine_url']); ?>",
        "datePublished": "<?php echo htmlspecialchars($row['data_pubblicazione']); ?>",
        "author": {
          "@type": "Person",
          "name": "<?php echo htmlspecialchars($row['autore']); ?>"
        },
        "publisher": {
          "@type": "Organization",
          "name": "Audax 1970",
          "logo": {
            "@type": "ImageObject",
            "url": "https://example.com/image/loghi/logo.png"
          }
        },
        "articleBody": "<?php echo htmlspecialchars(substr(strip_tags($row['contenuto']), 0, 300)); ?>"
      }
    </script>

  </body>
</html>
