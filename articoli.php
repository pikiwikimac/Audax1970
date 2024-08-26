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
    .card {
      display: flex;
      flex-direction: column;
      height: 100%;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex: 1 1 auto;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
      font-family: 'Bebas Neue';
    }

    .card-text {
      flex: 1;
      font-size: 12px;
      font-weight: 400;
    }

    .card-text-footer {
      text-align: right;
      margin-top: auto;
    }

    .card-img-wrapper {
      height: 255px; /* Altezza fissa per il contenitore dell'immagine */
      overflow: hidden; /* Nasconde le parti dell'immagine che escono dal contenitore */
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
    <div class="container my-4">
      <!-- Portieri -->
      <div class="row gy-3 ">
        <span class="fw-bold fs-5" id="font_diverso">
          Articoli
        </span>
        <hr id="separatore" />
        <?php if(mysqli_num_rows($articoli) > 0){ ?>
          <?php while($articolo = mysqli_fetch_assoc($articoli)) { ?>
            <div class="col-12 col-sm-6 col-lg-3 p-3">
              <a href="articolo.php?id=<?php echo $articolo['id'] ?>" class="text-decoration-none">
                <div class="card h-100">
                  <div class="card-img-wrapper">
                    <img src="image/articoli/<?php echo $articolo['immagine_url'] ?>" class="img-fluid" alt="Articolo immagine">
                  </div>
                  <div class="card-body">
                    <?php if($articolo['intestazione'] !== null ){ ?>
                      <div class="card-img-overlay">
                        <span class="badge bg-secondary bebas">
                          <?php echo $articolo['intestazione'] ?>
                        </span>
                      </div>
                    <?php } ?>
                    <h4 class="card-title"><?php echo $articolo['titolo'] ?></h4>
                    <span class="card-text">
                      <?php 
                        $content = $articolo['contenuto'];
                        if (strlen($content) > 180) {
                            $content = substr($content, 0, 180) . '...';
                            // Wordwrap the content at 180 characters
                            $content = wordwrap($content, 180, "\n", true);
                        }
                        echo nl2br($content);
                      ?>
                    </span>
                    <br/>
                    <div class="card-text-footer">
                      <small class="text-body-secondary">
                        <?php 
                          $data_pubblicazione = $articolo['data_pubblicazione'];
                          $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
                          echo $formatted_date;
                        ?>
                      </small>
                    </div>
                  </div>
                </div>
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
