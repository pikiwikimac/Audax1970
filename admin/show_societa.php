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

  $id=$_REQUEST['id'];

  #Query che seleziona le info della squadra
  $query = "
  SELECT s.*
  FROM societa s
  WHERE s.id=$id";

  $squadra = mysqli_query($con,$query);
  $info = mysqli_fetch_assoc($squadra);
  $stagione_squadra = $info['id_campionato'];

  $query2 = "
  SELECT g.*
  FROM giocatori g
  INNER JOIN societa s on s.id=g.id_squadra
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa='$id'
  ORDER BY g.ruolo,g.cognome,g.nome";

  $giocatori = mysqli_query($con,$query2);

  $query3 = "
  SELECT soc.nome_societa as casa, soc2.nome_societa as ospite, golCasa,golOspiti,CAST(giornata AS UNSIGNED) AS giornata_,giornata,s.id,s.data,s.played,
  CASE
    WHEN golCasa > golOspiti THEN '1' 
    WHEN golCasa = golOspiti THEN 'X' 
    WHEN golCasa < golOspiti THEN '2' 
    ELSE ''
  END AS risultato 
  FROM `partite` s
  INNER JOIN societa soc on soc.id=s.squadraCasa
  INNER JOIN societa soc2 on soc2.id=s.squadraOspite
  WHERE  s.id_stagione = $stagione_squadra
  AND (squadraCasa=$id or squadraOspite=$id OR squadraCasa=$id or squadraOspite=$id)
  ORDER BY giornata_,casa,ospite";

  $calendario = mysqli_query($con,$query3);

  if ($info['parent_id'] === NULL) {
      // Se parent_id è NULL, eseguiamo la prima query
      $query4 = "
      SELECT *
      FROM societa s
      WHERE parent_id = $id
      OR id = $id
      ";
  } else {
      // Altrimenti, eseguiamo la seconda query
      $parent_id = $info['parent_id'];
      $query4 = "
      SELECT *
      FROM societa s
      WHERE parent_id = $parent_id
      OR id = $parent_id
      ";
  }

  $squadre_correlate = mysqli_query($con, $query4);
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
                          <?php if ($info['logo']) { ?>
                            <img src="../image/loghi/<?php echo $info['logo'];?>" class="img-fluid" width="30px" height="30px"/>
                            &nbsp; 
                          <?php } ?>
                          <?php echo $info['nome_societa'] ?>
                        </h4>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <!-- Info squadra -->
                      <div class="row mb-3">
                        <div class="col-12">
                          <div class="row gy-2">
                            <!-- Elenco società  -->
                            <span class="text-muted">
                              <?php while($row = mysqli_fetch_assoc($squadre_correlate)){?>
                                <a class="text-decoration-none text-white" href="show_societa.php?id=<?php echo $row['id'] ?>">
                                  <span class="badge bg-secondary">
                                    <?php echo $row['tipo'] ?>
                                  </span>
                                </a>
                              <?php } ?>
                            </span>

                            <!-- Sede e città  -->
                            <span class="text-muted">
                              <i class='bi bi-pin-map-fill'></i> &nbsp; <?php echo $info['sede']  ?> - <?php echo $info['citta']  ?>
                            </span>

                            <!-- Giorno e ora  -->
                            <span class="text-muted mt-0">
                              <i class='bi bi-calendar' ></i> &nbsp; <?php echo $info['giorno_settimana']  ?> - <?php echo $info['ora_match']  ?>
                            </span>
                            

                            <!-- Contatto di riferimento -->
                            <?php if($info['contatto_riferimento'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-person-rolodex' ></i> &nbsp; Referente :&nbsp;<?php echo $info['contatto_riferimento'] .' ' .$info['telefono'] ?>
                              </span>
                            <?php } ?>
                            <!-- Email -->
                            <?php if($info['email'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-envelope' ></i> &nbsp; Email :&nbsp;<?php echo $info['email']  ?>
                              </span>
                            <?php } ?>
                            <!-- Instagram -->
                            <?php if($info['instagram'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-instagram'></i> &nbsp; Instagram :&nbsp;<?php echo $info['instagram']  ?>
                              </span>
                            <?php } ?>
                            <!-- Facebook -->
                            <?php if($info['facebook'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-facebook'></i> &nbsp; Facebook :&nbsp;<?php echo $info['facebook']  ?>
                              </span>
                            <?php } ?>
                            <!-- Whatsapp -->
                            <?php if($info['whatsapp'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-whatsapp'></i> &nbsp; Whatsapp :&nbsp;<?php echo $info['whatsapp']  ?>
                              </span>
                            <?php } ?>
                            <!-- Sito web -->
                            <?php if($info['sito_web'] != null){ ?>
                              <span class="text-muted mt-0">
                                <i class='bi bi-link-45deg'></i> &nbsp; Sito web :&nbsp;<a class="text-decoration-none text-dark" href="<?php echo $info['sito_web']?> "><?php echo $info['sito_web']?> </a>
                              </span>
                            <?php } ?>

                            <!-- Presidente -->
                            <?php if($info['presidente'] != null){ ?>
                              <span class="text-muted mt-3">
                                Presidente :&nbsp;<?php echo $info['presidente']  ?>
                              </span>
                            <?php } ?>
                            <!-- VicePresidente -->
                            <?php if($info['vicepresidente'] != null){ ?>
                              <span class="text-muted mt-0">
                                Vicepresidente :&nbsp;<?php echo $info['vicepresidente']  ?>
                              </span>
                            <?php } ?>

                            <!-- Allenatore -->
                            <?php if($info['allenatore'] != null){ ?>
                              <a class="text-decoration-none mt-0 text-muted" href="https://www.google.com/search?q=<?php echo $info['allenatore'] . ' Futsalmarche' ?>" target="_blank">
                                <span class="">
                                Allenatore :&nbsp;<?php echo $info['allenatore']  ?>
                                </span>
                              </a>
                            <?php } ?>

                            <div class="">
                              <!-- Futsalmarche -->
                              <a class="btn btn-sm btn-outline-dark " href="https://www.google.com/search?q=<?php echo urlencode($info['nome_societa']  . ' Futsalmarche'); ?>" target="_blank">
                                <img src="../image/loghi/favicon_fm.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp; Futsalmarche
                              </a>
                              <!-- Tuttocampo  -->
                              <a class="btn btn-sm btn-outline-dark" href="https://www.google.com/search?q=<?php echo urlencode($info['nome_societa']  . ' Tuttocampo'); ?>" target="_blank">
                                <img src="../image/loghi/favicon_tt.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp;Tuttocampo
                              </a>
                              <!-- Aggiungi il nuovo giocatore -->
                              <a href="insert_player.php?id_squadra=<?php echo ($info['id']); ?>" type="button" class="btn btn-sm btn-outline-dark">
                                <i class='bi bi-person-add' ></i>
                              </a>  
                              <!-- Modifica societa -->
                              <a href="edit_societa.php?id=<?php echo $info['id'] ?>" type="button" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-pencil"></i> 
                              </a>
                              <!-- Go back -->
                              <a href="societa.php" type="button" class="btn btn-sm btn-outline-dark ">
                                <i class='bi bi-arrow-left '></i> 
                              </a>
                            </div>
                            
                          </div>
                        </div>
                      </div>

                      <?php if($giocatori->num_rows ===0){ ?>
                        <span class="text-muted">Nessuna giocatore registrato</span>
                        <br/>
                      <?php } ?>

                      <?php if($giocatori->num_rows >0){ ?>
                        <!-- Giocatori -->
                        <div class="row mb-3">
                          <?php if ($giocatori->num_rows > 0 && $calendario->num_rows > 0): ?>
                              <div class="col-12 col-xl-6">
                            <?php elseif ($giocatori->num_rows > 0 && $calendario->num_rows === 0): ?>
                              <div class="col-12 col-xl-12">
                          <?php endif; ?>
                          <table class="table table-sm table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                <th width="3%"></th>
                                <th>Nome</th>
                                <th class="text-center" width="5%">Anno</th>
                                <th class="text-center" width="5%">Ruolo</th>
                                <th class="text-center" width="3%">Piede</th>
                                <th class="text-center" width="3%"></th>
                                <th class="text-center" width="3%"></th>
                                
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($giocatori)){?>
                              <tr class="align-middle">
                                <!-- Immagine -->
                                <td class="text-center">
                                  <?php if ($row['image_path']) { ?>
                                    <img src="../image/player/<?php echo $row['image_path'];?>" class="rounded-circle image-clickable" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="20" height="20"/>
                                  <?php } else { ?>
                                    <img src="../image/default_user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" width="15" height="15" />
                                  <?php } ?>
                                </td>

                                <!-- Nome e Cognome -->
                                <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row['cognome'] .' '. $row['nome']?>
                                </td>

                                <!-- Data di nascita -->
                                <td class="text-center" >
                                  <small>
                                    <?php if($row['data_nascita']==='1970-01-01'){
                                      echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                                    }else{
                                      echo date('Y',strtotime($row['data_nascita']));
                                    } ?>
                                  </small>
                                </td>

                                <!-- Ruolo -->
                                <td class="text-center">
                                  <?php if($row['ruolo']==='Portiere'){
                                      echo '
                                      <span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere">
                                        P'
                                      .'</span>';
                                    }elseif($row['ruolo']==='Centrale'){
                                      echo '
                                      <span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale">
                                        C'
                                      .'</span>';
                                    }elseif($row['ruolo']==='Laterale'){
                                      echo '
                                      <span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale">
                                        L'
                                      .'</span>';
                                    }elseif($row['ruolo']==='Pivot'){
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
                                
                                <!-- Piede -->
                                <td class="text-center">
                                  <small>
                                    <?php echo $row['piede_preferito'] ?>
                                  </small>
                                </td>

                                <!-- Pulsante Edit -->
                                <td class="text-center">
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="edit_player.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bi bi-pencil text-dark ms-2'></i>
                                  </a>
                                </td>
                                
                                <!-- Pulsante Delete  -->
                                <td class="text-center">
                                  <!-- Delete -->
                                  <a class="text-decoration-none" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                    <i class='bi bi-trash text-danger'></i>
                                  </a>
                                </td>
                                
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>           
                      <?php } ?>

                      <?php if($calendario->num_rows >0){ ?>              
                        <!-- Calendario -->
                        <div class="col-12 col-xl-6">
                          <table class="table table-sm table-striped table-hover table-rounded">
                            <thead class="table-dark ">
                              <tr>
                                <th class="text-center" width="10%"></th>
                                <th class="text-center" width="5%"></th>
                                <th class="text-end">Casa</th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class=""> Ospite</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($partita = mysqli_fetch_assoc($calendario)){?>
                              <tr class="align-middle">
                                
                                
                                <!-- Data -->
                                <td class="text-center">
                                  <small class="">
                                    <?php echo $partita['giornata'] ?>° 
                                    &nbsp;
                                    <?php 
                                      setlocale(LC_TIME, 'it_IT.utf8');
                                      $dayOfWeek = strftime('%A', strtotime($partita['data']));
                                      $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                      echo $abbreviatedDay;
                                    ?>
                                  </small>
                                </td>
                                <td class="text-center">
                                  <small class="">
                                    <?php echo date('d/m/y',strtotime( $partita['data'])) ?>
                                  </small>
                                </td>
                                
                                <!-- Squadra casa -->
                                <td class="text-end">
                                  <div class="
                                    <?php 
                                      // Verifica se la squadra ospite è quella dell'utente e assegna 'fw-bold'
                                      if ($partita['casa'] == $info['nome_societa']) {
                                        echo 'fw-bold ';
                                      }

                                      // Verifica il risultato per assegnare il colore
                                      if ($partita['risultato'] === '1' && $partita['casa'] == $info['nome_societa']) {
                                        echo 'text-danger';
                                      } elseif ($partita['risultato'] === 'X') {
                                        echo 'text-primary';
                                      } elseif ($partita['risultato'] === '2' && $partita['casa'] == $info['nome_societa']) {
                                        echo 'text-success';
                                      } else {
                                        echo 'text-dark';
                                      }
                                    ?>
                                  ">
                                    <?php echo $partita['casa'] ?>
                                  </div>
                                </td>
                                <!-- Gol casa -->
                                <td class="text-center">
                                  <?php echo $partita['golCasa'] ?>
                                </td>

                                <!-- Gol ospite -->
                                <td class="text-center">
                                  <?php echo $partita['golOspiti'] ?>
                                </td>
                                <!-- Squadra ospite -->
                                <td class="">
                                  <div class="
                                    <?php 
                                      // Verifica se la squadra ospite è quella dell'utente e assegna 'fw-bold'
                                      if ($partita['ospite'] == $info['nome_societa']) {
                                        echo 'fw-bold ';
                                      }

                                      // Verifica il risultato per assegnare il colore
                                      if ($partita['risultato'] === '1' && $partita['ospite'] == $info['nome_societa']) {
                                        echo 'text-danger';
                                      } elseif ($partita['risultato'] === 'X') {
                                        echo 'text-primary';
                                      } elseif ($partita['risultato'] === '2' && $partita['ospite'] == $info['nome_societa']) {
                                        echo 'text-success';
                                      } else {
                                        echo 'text-dark';
                                      }
                                    ?>
                                  ">
                                    <?php echo $partita['ospite'] ?>
                                  </div>
                                </td>
                                
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <?php } ?>
                      
                      <?php if($calendario->num_rows === 0){ ?>   
                        <span class="text-muted">Nessuna partita registrata</span>
                        <br/>
                      <?php } ?>
                    
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

    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_player.php?id=" + recordId;
        }
      }
    </script>
  </body>
</html> 