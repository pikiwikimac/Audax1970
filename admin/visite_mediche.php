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

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT g.*
  FROM giocatori g
  WHERE id_squadra = 1
  ORDER BY visita_medica asc;
  ";
  
  $giocatori = mysqli_query($con,$query);

  
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
                          Scadenze visite mediche
                        </h1>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row  ">
                        <div class="col-12 table-responsive">
                          <table class="table table-sm table-hover table-striped table-rounded sortable" id="tabella-giocatori">
                            <thead class="table-dark">

                              <tr>
                                <th width="5%"></th>
                                <th>Nome</th>
                                <th>Ruolo</th>
                                <th class="text-center">Data visita</th>
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                              <tr class="align-middle">
                                <!-- Immagine -->
                                <td class="text-center">
                                  <img src="../image/player/<?php echo $row['image_path']; ?>" class="rounded-circle " alt="..." width="30" height="30"/>
                                </td>

                                <!-- Nome e Cognome -->
                                <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row['cognome'] .' '. $row['nome']?>
                                </td>
            
                                <!-- Ruolo -->
                                <td>
                                  <?php echo $row['ruolo'] ?>
                                </td>
                                
                                <!-- Visita medica -->
                                <td class="text-center">
                                <?php
                                  if ($row['visita_medica'] !== '1970-01-01') {
                                      if ($row['visita_medica'] < date("Y-m-d")) {
                                          echo '<span class="text-danger">' . date('d/m/y', strtotime($row['visita_medica'])) . '</span>';
                                      } else {
                                          echo date('d/m/y', strtotime($row['visita_medica']));
                                      }
                                  } else {
                                      echo 'Non definita';
                                  }
                                ?>

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

  </body>
</html>