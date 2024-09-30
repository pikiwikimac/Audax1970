<?php
  session_start();
  require_once('config/db.php');
  include('check_user_logged.php');

  // INIZIO QUERY
  $query = "
  SELECT a.*, ai.descrizione as intestazione
  FROM articoli a
  LEFT JOIN articoli_intestazioni ai ON ai.id = a.id_intestazione
  ORDER BY a.data_pubblicazione desc";
  $articoli = mysqli_query($con, $query);
?>

<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

<style>

.card-title {
    font-size: 1.25rem; /* Regola la dimensione del font come preferisci */
    line-height: 1.5; /* Regola l'altezza della linea per migliorare la leggibilità */
    overflow: hidden; /* Nasconde il testo in eccesso */
    text-overflow: ellipsis; /* Mostra i puntini di sospensione per il testo troncato */
    white-space: nowrap; /* Impedisce il ritorno a capo */
    display: block;
    width: 100%; /* Assicura che l'elemento occupi tutto lo spazio disponibile */
    font-family: 'Bebas Neue';
  }

  .card-title-container {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Limita il numero di righe */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>
        
    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>

    <!-- Descrizione iniziale -->
    <div class="container my-5 px-4">
      <!-- Articoli -->
      <h1 class="bebas">
        Articoli
      </h1>
      <hr id="separatore" />
      
      <div class="row g-4">
        
        <?php if(mysqli_num_rows($articoli) > 0){ ?>
          <?php while($articolo = mysqli_fetch_assoc($articoli)) { ?>
            <div class="col-12 col-sm-6 col-lg-3 p-3">
              <a href="articolo.php?id=<?php echo $articolo['id'] ?>" class="text-decoration-none">
                <div class="card mb-2">
                  <?php if($articolo['immagine_url']){ ?>
                    <img src="image/articoli/<?php echo $articolo['immagine_url'] ?>" class="img-fluid card-img" alt="..." style="max-height:280px" >
                  <?php }else{ ?>
                    <img src="image/lnd_a2.webp" class="img-fluid card-img" alt="..." style="max-height:280px">
                  <?php } ?>
                </div>
                  

                <!-- Intestazione -->
                <?php if($articolo['intestazione'] !== null ){ ?>
                  <span class="badge bg-secondary">
                    <?php echo $articolo['intestazione'] ?>
                  </span>
                <?php } ?>

                <br/>
                    
                <div class="card-title-container">
                  <h4 class="card-title text-dark mt-2">
                    <?php echo $articolo['titolo'] ?>
                  </h4>
                </div>
                                     
               
                  
                <!-- Contenuto dell'articolo -->
                <p class="text-muted" style="font-size:12px;">
                  <?php 
                      $content = $articolo['contenuto'];
                      
                      // Rimuovi spazi bianchi in eccesso e ritorni a capo
                      $content = preg_replace('/\s+/', ' ', trim($content));
                      
                      // Se il contenuto è più lungo di 132 caratteri, troncalo e aggiungi "..."
                      if (strlen($content) > 132) {
                          $content = substr($content, 0, 132) . '...';
                      }
                      
                      // Mostra il contenuto con i ritorni a capo convertiti in <br>
                      echo nl2br(htmlspecialchars($content));
                  ?>
                </p>
                
                <!-- Data pubblicazione -->
                <span class="text-muted float-end" style="font-size:12px">
                  <?php
                  $data_pubblicazione = $articolo['data_pubblicazione'];
                  $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
                  echo $formatted_date;
                  ?>
                </span>
                
                
                      

              </a>
            </div>
          <?php } ?>
        <?php } else { ?>
          <span class="text-muted">Nessun articolo ancora presente</span>
        <?php } ?>     
      </div>
    </div>

    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
  </body>
</html>
