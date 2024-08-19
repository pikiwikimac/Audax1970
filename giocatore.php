<?php

  session_start();
  require_once('config/db.php');
  include('check_user_logged.php');


  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  $id=$_REQUEST['id'];

  $query = "
  SELECT g.*, s.nome_societa,
  (
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p
    ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione = 1
    
  ) AS numero_ammonizioni,
  (
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p
    ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione = 1
  ) AS numero_espulsioni,
  (
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p
    ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione = 1
  ) AS numero_gol,
  (
    SELECT count(*) as convocazioni
    FROM convocazioni c
    INNER JOIN partite p on p.id=c.id_partita
    WHERE c.id_giocatore=g.id
    AND p.id_stagione = 1
  ) as convocazioni
  
  FROM giocatori g
  INNER JOIN societa s on s.id=id_squadra 
  WHERE g.id=$id 
  ORDER BY ruolo,cognome,nome asc";
  $giocatore = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($giocatore);
  
  
  $query_coppa = "
  SELECT g.*, s.nome_societa,
  (
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p
    ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione = 2
    
  ) AS numero_ammonizioni,
  (
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p
    ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione = 2
  ) AS numero_espulsioni,
  (
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p
    ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione = 2
  ) AS numero_gol,
  (
    SELECT count(*) as convocazioni
    FROM convocazioni c
    INNER JOIN partite p on p.id=c.id_partita
    WHERE c.id_giocatore=g.id
    AND p.id_stagione = 2
  ) as convocazioni
  
  FROM giocatori g
  INNER JOIN societa s on s.id=id_squadra 
  WHERE g.id=$id 
  ORDER BY ruolo,cognome,nome asc";
  $giocatore_coppa = mysqli_query($con,$query_coppa);
  $row_coppa = mysqli_fetch_assoc($giocatore_coppa);

?>



<!doctype html>

<html lang="it">

  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>

    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>
    <main class="d-flex flex-nowrap">
      
      <!-- Descrizione iniziale -->
      <div class="container my-5">
        <h1 id="font-diverso">
          <?php echo $row['nome']. ' ' . $row['cognome'] ?>          
        </h1>


        <hr/>
        <!-- Visualizzazione a card -->
        <div class="row  ">


          <div class="col-12 col-lg-4  ">
            <div class="row gy-3">
              <div class="col-12">
                <img src="image/player/<?php echo $row['image_path']; ?>" class="rounded img-fluid " alt="..." width="500" height="500"/>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-8 ps-lg-5 ">
            <div class="row gy-2 mt-3">
              <!-- Ruolo -->
              <div class="col-6 col-md-3">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['ruolo']  ?>" value="<?php echo $row['ruolo']  ?>">
                  <label> Ruolo:</label>
                </div>
              </div>

              <!-- Piede -->
              <div class="col-6 col-md-3">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['piede_preferito']  ?>" value="<?php echo $row['piede_preferito']  ?>">
                  <label>Piede:</label>
                </div>
              </div>
              
              <!-- Numero maglia -->
              <div class="col-6 col-md-3">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['maglia']  ?>" value="<?php echo $row['maglia']  ?>">
                  <label>Maglia:</label>
                </div>
              </div>
              
              <!-- Data nascita -->
              <div class="col-6 col-md-3">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" 
                  placeholder="<?php if($row['data_nascita']==='0000-00-00'){
                        echo '-';
                      }else{
                        echo date('d/m/y',strtotime($row['data_nascita']));
                      } ?>" 
                  value="<?php if($row['data_nascita']==='0000-00-00'){
                        echo '-';
                      }else{
                        echo date('d/m/y',strtotime($row['data_nascita']));
                      } ?>">
                  <label for="floatingPlaintextInput">Data nascita:</label>
                </div>
              </div>
              
              <div class="col-12 table-responsive mt-5">
                <h4>Serie D</h4>
                <!-- Tabella: risultati della stagione -->
                <table class="table table-sm table-hover table-striped table-rounded">
                  <thead class="table-dark">
                    <tr>
                      <th class="text-center"><i class='bx bxs-t-shirt align-middle'></i></th>
                      <th class="text-center"><i class='bx bx-football align-middle'></i></th>
                      <th class="text-center"><i class='bx bxs-card align-middle' style='color:#ffb900'  ></i></th>
                      <th class="text-center"><i class='bx bxs-card align-middle' style='color:#FF0000'  ></i></th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Convocazioni -->
                    <td class="text-center">
                      <?php if($row['convocazioni']==='0'){
                        echo '-';
                      }else{
                        echo $row['convocazioni'] ;
                      } ?>
                    </td>

                    <!-- Numero gol -->
                    <td class="text-center">
                      <?php if($row['numero_gol']==='0'){
                        echo '-';
                      }else{
                        echo $row['numero_gol'] ;
                      } ?>
                      
                    </td>

                    <!-- Numero ammonizioni -->
                    <td class="text-center">
                      <?php if($row['numero_ammonizioni']==='0'){
                        echo '-';
                      }else{
                        echo $row['numero_ammonizioni'] ;
                      } ?>
                      
                    </td>

                    <!-- Numero espulsioni -->
                    <td class="text-center">
                      <?php if($row['numero_espulsioni']==='0'){
                        echo '-';
                      }else{
                        echo $row['numero_espulsioni'] ;
                      } ?>
                    </td>
                  </tbody>
                </table>
              </div> 
              
              <div class="col-12 table-responsive mt-5">
                <h4>Coppa marche</h4>
                <!-- Tabella: risultati della coppa marche -->
                <table class="table table-sm table-hover table-striped table-rounded">
                  <thead class="table-dark">
                    <tr>
                      <th class="text-center">
                        <i class='bx bxs-t-shirt align-middle'></i>
                      </th>
                      <th class="text-center">
                        <i class='bx bx-football align-middle'></i>
                      </th>
                      <th class="text-center">
                        <i class='bx bxs-card align-middle' style='color:#ffb900'  ></i>
                      </th>
                      <th class="text-center">
                        <i class='bx bxs-card align-middle' style='color:#FF0000'  ></i>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Convocazioni -->
                    <td class="text-center">
                      <?php if($row_coppa['convocazioni']==='0'){
                        echo '-';
                      }else{
                        echo $row_coppa['convocazioni'] ;
                      } ?>
                      
                    </td>

                    <!-- Numero gol -->
                    <td class="text-center">
                      <?php if($row_coppa['numero_gol']==='0'){
                        echo '-';
                      }else{
                        echo $row_coppa['numero_gol'] ;
                      } ?>
                      
                    </td>

                    <!-- Numero ammonizioni -->
                    <td class="text-center">
                      <?php if($row_coppa['numero_ammonizioni']==='0'){
                        echo '-';
                      }else{
                        echo $row_coppa['numero_ammonizioni'] ;
                      } ?>
                      
                    </td>

                    <!-- Numero espulsioni -->
                    <td class="text-center">
                      <?php if($row_coppa['numero_espulsioni']==='0'){
                        echo '-';
                      }else{
                        echo $row_coppa['numero_espulsioni'] ;
                      } ?>
                    </td>
                  </tbody>
                </table>
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