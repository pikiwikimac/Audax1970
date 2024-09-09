<?php
  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  // Verifica se l'utente è un superuser (superuser = 1)
  //if ($_SESSION['superuser'] !== 1) {
    // L'utente non è autorizzato ad accedere a questa pagina
  //  header('Location: error_page/access_denied.php');
  //  exit;
  //}
  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT s.*
  FROM societa s
  WHERE id = 1";

  $squadra = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($squadra);

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
                        <h4>
                          <?php echo $row['nome_societa']?>
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a href="edit_societa.php?id=<?php echo $row['id']; ?>" type="button" class="btn btn-sm btn-outline-dark float-end ">
                            <i class='bx bx-pencil'></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row mb-3">
                        <div class="col-12 col-lg-4">
                          <div class="row">
                            <div class="col-12">
                              <img src="../image/loghi/<?php echo $row['logo']; ?>" class="rounded img-fluid " alt="..." width="500" height="500"/>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-lg-8 ps-md-5 ">
                          <div class="row gy-2 mt-3">
                            <!-- Sede -->
                            <div class="col-12 col-md-12">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['sede']  ?>" value="<?php echo $row['sede']  ?>">
                                <label style="margin-top:-10px">Sede:</label>
                              </div>
                            </div>

                            <!-- Città -->
                            <div class="col-4 ">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['citta']  ?>" value="<?php echo $row['citta']  ?>">
                                <label style="margin-top:-10px" >Città:</label>
                              </div>
                            </div>
                            
                            <!-- Giorno settimana -->
                            <div class="col-4 ">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['giorno_settimana']  ?>" value="<?php echo $row['giorno_settimana']  ?>">
                                <label style="margin-top:-10px">Giorno settimana:</label>
                              </div>
                            </div>
                            
                            <!-- Orario match -->
                            <div class="col-4 ">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['ora_match']  ?> " value="<?php echo $row['ora_match']  ?>"> 
                                <label style="margin-top:-10px">Orario match:</label>
                              </div>
                            </div>
                            <!-- Presidente -->
                            <div class="col-4 ">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['presidente']  ?> " value="<?php echo $row['presidente']  ?>"> 
                                <label style="margin-top:-10px">Presidente:</label>
                              </div>
                            </div>
                            <!-- VicePresidente -->
                            <div class="col-4 ">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['vicepresidente']  ?> " value="<?php echo $row['vicepresidente']  ?>"> 
                                <label style="margin-top:-10px">Vice Presidente:</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
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

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
  </body>
</html> 