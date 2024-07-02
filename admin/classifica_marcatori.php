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

  $query = " SELECT * FROM vista_classifica_marcatori_seried_2023_2024 ";

  $marcatori = mysqli_query($con,$query);
  $posizione=1;
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
                          Classifica marcatori 
                        </h1>
                      </div>
                    </div>
                    <!-- END: Intestazione -->
                    
                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row  ">
                        <div class="col-12 table-responsive  ">
                          <table class="table table-sm table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                <th></th>
                                <th>Giocatore</th>
                                <th >Squadra</th>
                                <th >Ruolo</th>
                                <th class="text-end">Gol</th>
                                
                              </tr>
                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($marcatori)) {  ?>
                                
                                
                                <tr class="<?php if ($row['societa'] === 'Audax 1970') { echo 'fw-bold '; } ?>" >

                                  <!-- Posizione in classifica -->
                                  <td class="text-center">
                                    <small>
                                      <?php echo $posizione ?>°
                                    </small>
                                  </td>

                                  <!-- Nome giocatore -->
                                  <td class="text-nowrap" >
                                    <a href="player.php?id=<?php echo $row['id'] ?>" class="text-dark text-decoration-none">
                                      <?php echo $row['nome'] .' '.$row['cognome'] ?>
                                    </a>
                                  </td>

                                  <!-- Società -->
                                  <td class="text-nowrap">
                                    <?php echo $row['societa'] ?> 
                                  </td>

                                  <!-- Ruolo -->
                                  <td>
                                    <?php echo $row['ruolo'] ?>
                                  </td>
                                  
                                  <!-- Totale gol -->
                                  <td class="text-end">
                                    <?php echo $row['gol_fatti'] ?>
                                  </td>
                                </tr>

                              <?php $posizione += 1; } ?>

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