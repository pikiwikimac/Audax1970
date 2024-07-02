<?php
  session_start();
  // Controlla se l'utente è autenticato
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

?>


<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include '../elements/head.php'; ?>

  <body>
    <main role="main" class="tpl">

      <?php include '../elements/sidebar.php'; ?>

      <!-- Corpo della pagina -->
      <div class="tpl--content">
        <div class="tpl--content--inner">
          <div class="tpl-inner">
            <div class="tpl-inner-content">
              <div class="row pe-3">
                <div class="col-12 ">            
                  <div class="container-fluid">
                    <!-- Intestazione -->
                    <div class="tpl-header">
                      <div class="tpl-header--title">
                        <h1 >
                          Gallery
                        </h1>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a type="button" class="btn btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal" onclick="showCreateFolderModal()">
                            <i class='bx bx-plus '></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">

                      <div class="row gy-3">
                      <?php
                        $directory = '../image/partite'; // Percorso della directory da esaminare

                        // Verifica se il percorso specificato esiste ed è una directory
                        if (is_dir($directory)) {
                            // Ottiene un elenco ordinato dei file e delle cartelle nella directory
                            $entries = scandir($directory);

                            // Rimuove "." e ".." dall'elenco
                            $entries = array_diff($entries, array('.', '..'));

                            // Loop attraverso i file e le cartelle nell'ordine desiderato
                            foreach ($entries as $entry) {
                                // Stampa il nome della cartella
                                echo '<div class="col-6 col-lg-3">
                                          <div class="card">
                                            <div class="card-body">
                                            <a class="text-decoration-none text-dark" href="show_gallery_match.php?folder=' . $entry . '">' . $entry . '</a>
                                            </div>
                                          </div>
                                        </div>';
                            }
                        } else {
                            // Messaggio di errore se il percorso non è una directory
                            echo '<p>Il percorso specificato non è una directory valida.</p>';
                        }
                        ?>


                      </div>
                    </div>
                    <!-- END:Core della pagina -->
                  </div>  
                </div>  
              </div>  
            </div>  
          </div>  
        </div>  
      </div>  

    </main>

   
    <!-- Modal Insert -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Crea Nuova Cartella</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="../query/create_folder.php" method="POST">
            <div class="modal-body">
                <label for="newFolderName">Nome cartella:</label>
                <input type="text" class="form-control" name="newFolderName" id="newFolderName" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="submit" class="btn btn-primary">Crea</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    

    
    
    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>