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

  $query = "
  SELECT * FROM vista_classifica_seried_2023_2024
  ";

  $classifica = mysqli_query($con,$query);
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
                          Classifica
                        </h1>
                      </div>
                    </div>
                    <!-- END:Intestazione -->
                    
                    <!-- Core della pagina -->
                    <div class="">
              
                      <div class="row  ">
                        <div class="col-12 table-responsive  ">
                          <table class="table table-striped table-hover table-rounded">
                            <thead class="table-dark ">
                              <tr>
                                <th></th>
                                <th>Squadra</th>
                                <th class="text-center">G</th>
                                <th class="text-center">V</th>
                                <th class="text-center">P</th>
                                <th class="text-center">S</th>
                                <th class="text-center">GF</th>
                                <th class="text-center">GS</th>
                                <th class="text-center">+/-</th>
                                <th class="text-center">Punti</th>
                              </tr>
                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($classifica)) {  ?>
                                
                                <?php
                                // Add different CSS classes based on the position of the row in the table
                                $rowClass = '';
                                if ($posizione ==1) {
                                  $rowClass = 'bg-success'; // Green background for top 4 rows
                                } elseif ($posizione >= 2 && $posizione <=5) {
                                  $rowClass = 'bg-primary'; // Red background for last 4 rows
                                }elseif ($posizione >= mysqli_num_rows($classifica)) {
                                  $rowClass = 'bg-danger'; // Red background for last 4 rows
                                }  else {
                                  $rowClass = ''; // Yellow background for the rest
                                }
                                ?>

                                <tr class="bg-opacity-25 <?php echo $rowClass; ?>">
                                  <!-- Posizione in classifica -->
                                  <td class="text-center fw-bold ">
                                    <?php echo $posizione ?>°
                                  </td>

                                  <!-- Nome squadra -->
                                  <td class="fw-bold text-nowrap " style="cursor:pointer" onclick="window.location='show_societa.php?id=<?php echo $row['id']; ?>';">
                                    <?php echo $row['societa'] ?>
                                  </td>

                                  <!-- Partite giocate -->
                                  <td class="text-center">
                                    <?php echo $row['played'] ?>
                                  </td>

                                  <!-- Partite vinte -->
                                  <td class="text-center">
                                    <?php echo $row['vinte'] ?>
                                  </td>

                                  <!-- Partite pareggiate -->
                                  <td class="text-center">
                                    <?php echo $row['pareggi'] ?>
                                  </td>
                                  <!-- Partite perse -->
                                  <td class="text-center">
                                    <?php echo $row['perse'] ?>
                                  </td>
                                  <!-- Gol Fatti -->
                                  <td class="text-center">
                                    <?php echo $row['golFatti'] ?>
                                  </td>
                                  <!-- Gol Subiti -->
                                  <td class="text-center">
                                    <?php echo $row['golSubiti'] ?>
                                  </td>
                                  <!-- Gol differenza -->
                                  <td class="text-center">
                                    <?php echo $row['goal_diff'] ?>
                                  </td>
                                  <!-- Punti totali -->
                                  <td class="fw-bold text-center">
                                    <?php echo $row['risultato'] ?>
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