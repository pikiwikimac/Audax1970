<?php
  session_start();
  require_once('../config/db.php');
  
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  $id= $_REQUEST['id'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  select * 
  from stagioni s
  where s.anno_inizio = 2023
  and s.anno_fine = 2024
  and prima_squadra != 1";

  $squadre_settore_giovanile = mysqli_query($con,$query);


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
                          Settore giovanile
                        </h3>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a href="show_societa.php?id=<?php echo $id ?>" type="button" class="btn btn-outline-dark float-end">
                            Prima squadra
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->
                    
                    <!-- Core della pagina -->
                    <div class="">
           
                      <div class="row mb-3">
                        <div class="col-12">
                          <table class="table table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                
                                <th width="68%">Nome stagione</th>
                                <th width="20%">Annata</th>
                                <th width="10%">Girone</th>
                                <th width="2%"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($squadre_settore_giovanile)){?>
                              <tr class="align-middle">
                                <td>
                                  <?php echo $row['descrizione'] ?>
                                </td>
                                <td>
                                  <a href="show_societa.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark">
                                    <?php echo $row['anno_inizio'] .'-' .$row['anno_fine'] ?>
                                  </a>
                                </td>
                                <td><?php echo $row['girone'] ?></td>
                                
                                <!-- Pulsante Edit -->
                                <td class="text-center">
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="" >
                                    GO
                                  </a>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
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

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
  </body>
</html> 