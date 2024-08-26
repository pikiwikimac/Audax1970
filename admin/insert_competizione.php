<?php 
  session_start();
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
                        <h3>
                          Crea competizione  
                        </h3>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="competizioni.php" class="btn btn-outline-dark float-end">
                            <i class='bx bx-arrow-back'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                      
                      <div class="row gy-4 mb-3 ">
                        
                        <div class="col-12">
                          <form action="../query/action_insert_competizione.php" method="POST" enctype="multipart/form-data">
                            <div class="card">
                              <div class="card-body">
                                
                                <div class="row my-3 g-3">
                                  <!-- Nome stagione -->
                                  <div class="col-12 col-sm-6 col-lg-3  ">
                                    <label for="nome_stagione" class="form-label">Nome stagione</label>
                                    <input typer="text" class="form-control" id="nome_stagione" name="nome_stagione" required ></input>
                                  </div>


                                  <!-- Descrizione -->
                                  <div class="col-12 col-sm-6 col-lg-3 ">
                                    <label for="descrizione" class="form-label">Descrizione</label>
                                    <input type="text" class="form-control" id="descrizione" name="descrizione" ></input>
                                  </div>

                                  <!-- Anno inizio -->
                                  <div class="col-6 col-sm-6 col-lg-1">
                                    <label for="anno_inizio" class="form-label">Anno inizio</label>
                                    <input type="text" class="form-control" id="anno_inizio" name="anno_inizio" value="<?php echo date('Y'); ?>" required>
                                  </div>

                                  <!-- Anno fine -->
                                  <div class="col-6 col-sm-6 col-lg-1">
                                    <label for="anno_fine" class="form-label">Anno fine</label>
                                    <input type="text" class="form-control" id="anno_fine" name="anno_fine" value="<?php echo date('Y') + 1; ?>" required>
                                  </div>


                                  <!-- Girone -->
                                  <div class="col-12 col-sm-6 col-lg-2 ">
                                    <label for="girone" class="form-label">Girone</label>
                                    <input type="text" class="form-control" id="girone" name="girone" ></input>
                                  </div>

                                  <!-- Prima squadra -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="prima_squadra" class="form-label">Squadra</label>
                                    <select class="form-select" id="prima_squadra" name="prima_squadra">
                                      <option value="1">Prima squadra</option>
                                      <option value="2">Settore giovanile</option>
                                    </select>
                                  </div>
                                    
                                </div>
                              </div>
                            </div>
                            <!-- Submit -->
                            <div class="d-flex justify-content-end">
                              <button type="submit" class="btn btn-outline-dark mt-2">Conferma</button>
                            </div>
                          </form>
                        </div>
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


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    

  </body>
</html>