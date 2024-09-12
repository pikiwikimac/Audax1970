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
  ) as convocazioni,stag.descrizione as competizione, stag.girone,stag.id_stagione
  FROM giocatori g
  INNER JOIN societa s on s.id=id_squadra
  INNER JOIN stagioni stag on stag.id_stagione=s.id_campionato
  WHERE g.id=$id 
  ORDER BY ruolo,cognome,nome asc;";
  $giocatore = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($giocatore);

  $query="
  SELECT s.nome_societa, s.tipo, s.id
  FROM societa s
  INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
  WHERE ag.id_giocatore = $id
  ";
  $squadre_giocatore = mysqli_query($con,$query);
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
      <div class="container" style="margin-top:3rem!important">
        <h1 id="font_diverso">
          <?php echo $row['nome']. ' ' . $row['cognome'] ?>
          <?php if($row['maglia']===null){ ?>
            <div class="float-end">
              <span class="badge bg-dark text-light" >
                # <?php echo $row['maglia']  ?>
              </span>
            </div>
          <?php } ?>
        </h1>

        <hr/>
        <!-- Visualizzazione a card -->
        <div class="row  ">

          <div class="col-12 col-lg-4  ">
            
            <?php if ($row['image_path']) { ?>
              <img src="image/player/<?php echo $row['image_path'];?>" class="img-fluid rounded" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
            <?php } else { ?>
              <img src="image/default_user.jpg" class="img-fluid rounded" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" />
            <?php } ?>
             
          </div>

          <div class="col-12 col-lg-8 ps-lg-3 ">
            <div class="row gy-2 ">
              
              <!-- Squadra -->
              <div class="col-6 col-md-12">
                <label class="fw-bold text-muted">Squadra:</label>
                <a class="text-decoration-none text-dark" href="show_societa.php?id=<?php echo $row['id_squadra'] ?>">
                  <?php echo $row['nome_societa'] ?>                     
                </a>
              </div>
              <!-- Squadre attive -->
              <?php if(mysqli_num_rows($squadre_giocatore )>1)  { ?>
              <div class="col-6 col-md-12">
                <label class="fw-bold text-muted">Squadre attive:</label>
                <?php while($squadra = mysqli_fetch_assoc($squadre_giocatore)) {  ?>
                  <span class="badge bg-secondary">
                    <?php echo $squadra['tipo'] ?> 
                  </span> 
                <?php } ?>
              </div>
              <?php } ?>
              <!-- Ruolo  -->
              <div class="col-6 col-md-12">
                <label class="fw-bold text-muted">Ruolo:</label>
                <span>
                  <?php echo $row['ruolo'] ?> 
                </span>
              </div>
              <!-- Piede  -->
              <div class="col-6 col-md-12">
                <label class="fw-bold text-muted">Piede:</label>
                <span>
                  <?php echo $row['piede_preferito'] ?> 
                </span>
              </div>
              
              <!-- Data di nascita -->
              <div class="col-6 col-md-12">
                <label class="fw-bold text-muted">Data di nascita:</label>
                <span>
                <?php if($row['data_nascita']==='1970-01-01'){
                  echo '-';
                }else{
                  echo date('d/m/y',strtotime($row['data_nascita']));
                } ?>
                </span>
              </div>

              <div class="col-12">
                <!-- Futsalmarche -->
                <a class="btn btn-sm btn-outline-dark " href="https://www.google.com/search?q=<?php echo urlencode($row['nome'] . ' ' . $row['cognome'] . ' Futsalmarche'); ?>" target="_blank">
                  <img src="image/loghi/favicon_fm.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp; Futsalmarche
                </a>
                <!-- Tuttocampo -->
                <a class="btn btn-sm btn-outline-dark " href="https://www.google.com/search?q=<?php echo urlencode($row['nome'] . ' ' . $row['cognome'] . ' Tuttocampo'); ?>" target="_blank">
                  <img src="image/loghi/favicon_tt.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp;Tuttocampo
                </a>
              </div>
              
              
              <div class="col-12 table-responsive mt-5">
                <h4><?php echo $row['competizione']  ?></h4>
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