<?php

  session_start();
  require_once('config/db.php');
  include('check_user_logged.php');


  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  $id=$_REQUEST['id'];

  
  
  $query = 
  "
  SELECT 
    p.*,
    soc.nome_societa as casa,
    soc2.nome_societa as ospite,
    stag.descrizione,
    stag.girone,
    soc.sede,
    soc.citta,
    soc.giorno_settimana,
    soc.ora_match,
    soc.logo as logoCasa,
    soc2.logo as logoOspiti,
    CASE
        WHEN p.orario_modificato IS NOT NULL THEN p.orario_modificato
        ELSE soc.ora_match
    END AS orario_partita,
    CASE
        WHEN p.data_modificata IS NOT NULL THEN p.data_modificata
        ELSE p.data
    END AS giornata_partita
  FROM partite p
  INNER JOIN societa soc ON soc.id = p.squadraCasa
  INNER JOIN societa soc2 ON soc2.id = p.squadraOspite
  INNER JOIN stagioni stag ON stag.id_stagione = p.id_stagione

  WHERE p.id= '$id'
  ";

  $partita = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($partita);


?>



<!doctype html>

<html lang="it">

  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>

    
    <main class="d-flex flex-nowrap">
      
      <!-- Descrizione iniziale -->
      <div class="container my-5 px-4">
        <h1 id="font-diverso">
          <span class="fw-bold">Giornata <?php echo $row['giornata'] .' ° ' ?></span>
          <span class="badge bg-primary float-end"><?php echo $row['descrizione'] ?> - <?php echo $row['girone'] ?> 
          </span>
        </h1>

        <hr/>
        <!-- Visualizzazione a card -->
        <div class="row  ">
          <div class="col-12">
            <!-- Giorno -->
            <span>
              <i class='bx bx-calendar'></i>&nbsp; <strong>Data:</strong> <?php echo date('d/m/y', strtotime($row['data'])) ?>
            </span>
            <br/>
            <!-- Orario -->
            <span>
              <i class='bx bx-time' ></i>&nbsp; <strong>Orario:</strong> <?php echo date('H:i', strtotime($row['orario_partita']))  ?>
            </span>
            <br/>
            <!-- Luogo -->
            <span>
              <i class='bx bx-map' ></i>&nbsp; <strong>Luogo:</strong> <?php echo $row['sede'] .' - ' .$row['citta'] ?>
            </span>
            <br/>
          </div>
        </div>

        <div class="row  mt-5 gy-3">
          <div class="col-12 col-lg-6">
            <h4 id="font-diverso" class="fw-bold">
              <img src="image/loghi/<?php echo $row['logoCasa']; ?>" class="rounded-circle " alt="..." width="30" height="30"/>
              <?php echo $row['casa'] ?>
              <span class="float-end">
                <?php echo $row['golCasa'] ?>
              </span>
            </h4>

            <hr/>
            
            <?php
              if ($row['squadraCasa'] === '1') {
                  $query = "
                      SELECT c.*, g.cognome, g.nome, g.ruolo,g.id
                      FROM convocazioni c
                      INNER JOIN giocatori g ON g.id = c.id_giocatore
                      WHERE id_partita='$id'
                      ORDER BY ruolo, cognome, nome
                  ";
              } else {
                  $query = "
                    SELECT g.*
                    FROM giocatori g
                    INNER JOIN societa s ON s.id = g.id_squadra
                    WHERE s.id = " . $row['squadraCasa'] . "
                    ORDER BY g.ruolo, g.cognome, g.nome;
                  ";
              }

              $convocati = mysqli_query($con, $query);
            ?>
            <?php $contatore=1 ?>
            
              <table class="table table-sm ">
                <tbody>
                  <?php while($casa_convocati = mysqli_fetch_assoc($convocati)){?>
                    <tr onclick="window.location='giocatore.php?id=<?php echo $casa_convocati['id']; ?>';">
                      <!-- Contatore -->
                      <td width="5%">
                        <?php echo $contatore;  ?>
                      </td>
                      <!-- Ruolo -->
                      <td width="8%">
                      <?php if($casa_convocati['ruolo']==='Portiere'){
                        echo '
                        <span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere">
                            P'
                        .'</span>';
                        }elseif($casa_convocati['ruolo']==='Centrale'){
                        echo '
                        <span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale">
                            C'
                        .'</span>';
                        }elseif($casa_convocati['ruolo']==='Laterale'){
                        echo '
                        <span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale">
                            L'
                        .'</span>';
                        }elseif($casa_convocati['ruolo']==='Pivot'){
                        echo '
                        <span class="badge bg-danger text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Pivot">
                            P'
                        .'</span>';
                        }else{
                        echo '
                        <span class="badge bg-info text-light" style="width:30px;"  data-bs-toggle="tooltip" data-bs-title="Universale ">
                            U'
                        .'</span>';
                        } ?>
                      </td>
                      <!-- GIocatore -->
                      <td>
                        <?php echo $casa_convocati['cognome']  . ' ' .$casa_convocati['nome'] ?>
                      </td>
                      <td class="text-end">
                        <?php
                          $id_giocatore = $casa_convocati['id']; // Assumi che ci sia un campo id_giocatore
                          
                          $query_gol = "SELECT COUNT(*) AS gol_fatti FROM marcatori WHERE id_partita = '$id' AND id_giocatore = '$id_giocatore'";
                          $result_gol = mysqli_query($con, $query_gol);
                          $row_gol = mysqli_fetch_assoc($result_gol);
                          
                          $query_gialli = "SELECT COUNT(*) AS gialli FROM ammoniti a WHERE a.id_partita = '$id' AND a.id_giocatore = '$id_giocatore'";
                          $result_gialli = mysqli_query($con, $query_gialli);
                          $row_gialli = mysqli_fetch_assoc($result_gialli);

                          $query_rossi = "SELECT COUNT(*) AS rossi FROM rossi a WHERE a.id_partita = '$id' AND a.id_giocatore = '$id_giocatore'";
                          $result_rossi = mysqli_query($con, $query_rossi);
                          $row_rossi = mysqli_fetch_assoc($result_rossi);

                          

                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($i = 0; $i < $row_gol['gol_fatti']; $i++) {
                              echo '<i class="bx bx-football"></i>';
                          }
                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($j = 0; $j < $row_gialli['gialli']; $j++) {
                            echo '<i class="bx bxs-card" style="color:#ffb900"></i>';
                          }

                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($k = 0; $k < $row_rossi['rossi']; $k++) {
                            echo '<i class="bx bxs-card" style="color:#ff0000"></i>';
                          }
                        ?>

                      </td>

                    </tr>
                    <?php $contatore+=1; } ?>
                </tbody>
              </table>
            
          </div>
          <div class="col-12 col-lg-6 ">
            <h4 id="font-diverso" class="fw-bold">
              <img src="image/loghi/<?php echo $row['logoOspiti']; ?>" class="rounded-circle " alt="..." width="30" height="30"/>
              <?php echo $row['ospite'] ?> 
              <span class="float-end">
                <?php echo $row['golOspiti'] ?>
              </span> 
            </h4>
            <hr/>

            <?php
              if ($row['squadraOspite'] === '1') {
                  $query2 = "
                      SELECT c.*, g.cognome, g.nome, g.ruolo,g.id
                      FROM convocazioni c
                      INNER JOIN giocatori g ON g.id = c.id_giocatore
                      WHERE id_partita='$id'
                      ORDER BY ruolo, cognome, nome
                  ";
              } else {
                  $query2 = "
                    SELECT g.*
                    FROM giocatori g
                    INNER JOIN societa s ON s.id = g.id_squadra
                    WHERE s.id = " . $row['squadraOspite'] . "
                    ORDER BY g.ruolo, g.cognome, g.nome;
                  ";
              }

              $convocati_ospiti = mysqli_query($con, $query2);
            ?>
            <?php $contatore_ospiti=1 ?>
            
              <table class="table table-sm ">
                <tbody>
                  <?php while($ospiti_convocati = mysqli_fetch_assoc($convocati_ospiti)){?>
                    <tr onclick="window.location='giocatore.php?id=<?php echo $ospiti_convocati['id']; ?>';">
                      <!-- Contatore -->
                      <td width="5%">
                        <?php echo $contatore_ospiti;  ?>
                      </td>
                      <!-- Ruolo -->
                      <td width="8%">
                      <?php if($ospiti_convocati['ruolo']==='Portiere'){
                        echo '
                        <span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere">
                            P'
                        .'</span>';
                        }elseif($casa_ospiti_convocaticonvocati['ruolo']==='Centrale'){
                        echo '
                        <span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale">
                            C'
                        .'</span>';
                        }elseif($ospiti_convocati['ruolo']==='Laterale'){
                        echo '
                        <span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale">
                            L'
                        .'</span>';
                        }elseif($ospiti_convocati['ruolo']==='Pivot'){
                        echo '
                        <span class="badge bg-danger text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Pivot">
                            P'
                        .'</span>';
                        }else{
                        echo '
                        <span class="badge bg-info text-light" style="width:30px;"  data-bs-toggle="tooltip" data-bs-title="Universale ">
                            U'
                        .'</span>';
                        } ?>
                      </td>
                      <!-- GIocatore -->
                      <td>
                        <?php echo $ospiti_convocati['cognome'] . ' ' .$ospiti_convocati['nome'] ?>
                      </td>
                      <td class="text-end">
                        <?php
                          $id_giocatore = $ospiti_convocati['id']; // Assumi che ci sia un campo id_giocatore
                          
                          $query_gol = "SELECT COUNT(*) AS gol_fatti FROM marcatori WHERE id_partita = '$id' AND id_giocatore = '$id_giocatore'";
                          $result_gol = mysqli_query($con, $query_gol);
                          $row_gol = mysqli_fetch_assoc($result_gol);
                          
                          $query_gialli = "SELECT COUNT(*) AS gialli FROM ammoniti a WHERE a.id_partita = '$id' AND a.id_giocatore = '$id_giocatore'";
                          $result_gialli = mysqli_query($con, $query_gialli);
                          $row_gialli = mysqli_fetch_assoc($result_gialli);

                          $query_rossi = "SELECT COUNT(*) AS rossi FROM rossi a WHERE a.id_partita = '$id' AND a.id_giocatore = '$id_giocatore'";
                          $result_rossi = mysqli_query($con, $query_rossi);
                          $row_rossi = mysqli_fetch_assoc($result_rossi);

                          

                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($i = 0; $i < $row_gol['gol_fatti']; $i++) {
                              echo '<i class="bx bx-football"></i>';
                          }
                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($j = 0; $j < $row_gialli['gialli']; $j++) {
                            echo '<i class="bx bxs-card" style="color:#ffb900"></i>';
                          }

                          // Stampa l'icona bx-football tante volte quanto il valore di $row_gol['gol_fatti']
                          for ($k = 0; $k < $row_rossi['rossi']; $k++) {
                            echo '<i class="bx bxs-card" style="color:#ff0000"></i>';
                          }
                          
                        ?>

                      </td>

                    </tr>
                    <?php $contatore_ospiti+=1; } ?>
                </tbody>
              </table>

          </div>
        </div>
        
      </div>

    </main>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
  </body>


</html>