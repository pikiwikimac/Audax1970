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
  WHERE  s.id_stagione = 1
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
                    <!-- Core della pagina -->
                    <div class="">
                      <!-- Info squadra -->
                      <div class="row mb-3">
                        <div class="col-12 col-md-2">
                          <?php if ($info['logo']) { ?>
                            <img src="../image/loghi/<?php echo $info['logo'];?>" class="img-thumbnail"  />
                          <?php } else { ?>
                            <img src="../image/default_societa.png" class="img-thumbnail"  />
                          <?php } ?>
                        </div>
                        <div class="col-12 col-md-10">
                          <div class="row gy-2">

                            <!-- Nome società  -->
                            <span class="fs-2 fw-bold">
                              <?php echo $info['nome_societa'] ?>
                            </span>

                            <!-- Elenco società  -->
                            <span class="fs-4 text-muted">
                              <?php while($row = mysqli_fetch_assoc($squadre_correlate)){?>
                                <a class="text-decoration-none text-white" href="show_societa.php?id=<?php echo $row['id'] ?>">
                                  <span class="badge bg-secondary">
                                    <?php echo $row['tipo'] ?>
                                  </span>
                                </a>
                              <?php } ?>
                            </span>
                            
                            <!-- Sede e città  -->
                            <span class="fs-4 text-muted">
                              <i class='bx bxs-map-pin'></i> &nbsp; <?php echo $info['sede']  ?> - <?php echo $info['citta']  ?>
                            </span>

                            <!-- Giorno e ora  -->
                            <span class="fs-5 text-muted mt-0">
                              <i class='bx bx-calendar' ></i> &nbsp; <?php echo $info['giorno_settimana']  ?> - <?php echo $info['ora_match']  ?>
                            </span>
                            

                            <!-- Contatto di riferimento -->
                            <?php if($info['contatto_riferimento'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                <i class='bx bxs-contact' ></i> &nbsp; Referente :&nbsp;<?php echo $info['contatto_riferimento'] .' ' .$info['telefono'] ?>
                              </span>
                            <?php } ?>
                            <!-- Email -->
                            <?php if($info['email'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                <i class='bx bx-envelope' ></i> &nbsp; Email :&nbsp;<?php echo $info['email']  ?>
                              </span>
                            <?php } ?>
                            <!-- Instagram -->
                            <?php if($info['instagram'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                <i class='bx bxl-instagram'></i> &nbsp; Instagram :&nbsp;<?php echo $info['instagram']  ?>
                              </span>
                            <?php } ?>
                            <!-- Whatsapp -->
                            <?php if($info['whatsapp'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                <i class='bx bxl-whatsapp'></i> &nbsp; Whatsapp :&nbsp;<?php echo $info['whatsapp']  ?>
                              </span>
                            <?php } ?>
                            <!-- Sito web -->
                            <?php if($info['sito_web'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                <i class='bx bx-link'></i> &nbsp; Sito web :&nbsp;<a class="text-decoration-none text-dark" href="<?php echo $info['sito_web']?> "><?php echo $info['sito_web']?> </a>
                              </span>
                            <?php } ?>

                            <!-- Presidente -->
                            <?php if($info['presidente'] != null){ ?>
                              <span class="fs-5 text-muted mt-3">
                                Presidente :&nbsp;<?php echo $info['presidente']  ?>
                              </span>
                            <?php } ?>
                            <!-- VicePresidente -->
                            <?php if($info['vicepresidente'] != null){ ?>
                              <span class="fs-5 text-muted mt-0">
                                Vicepresidente :&nbsp;<?php echo $info['vicepresidente']  ?>
                              </span>
                            <?php } ?>
                            
                            <div>
                              <!-- Aggiungi il nuovo bottone -->
                              <a href="insert_player.php?id_squadra=<?php echo ($info['id']); ?>" type="button" class="btn btn-outline-dark  me-2">
                                <i class='bx bx-user-plus' ></i>
                              </a>  
                            
                            
                              <a href="edit_societa.php?id=<?php echo $info['id'] ?>" type="button" class="btn btn-outline-dark  me-2">
                                <i class='bx bx-pencil '></i> 
                              </a>
                              
                              <a href="societa.php" type="button" class="btn btn-outline-dark ">
                                <i class='bx bx-arrow-back '></i> 
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
                                      <img src="../image/player/<?php echo $row['image_path'];?>" class="rounded-circle image-clickable" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="30" height="30"/>
                                    <?php } else { ?>
                                      <img src="../image/default_user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" width="30" height="30" />
                                    <?php } ?>
                                  </td>

                                  <!-- Nome e Cognome -->
                                  <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                    <?php echo $row['cognome'] .' '. $row['nome']?>
                                  </td>

                                  <!-- Data di nascita -->
                                  <td class="text-center" >
                                    <?php if($row['data_nascita']==='1970-01-01'){
                                      echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                                    }else{
                                      echo date('Y',strtotime($row['data_nascita']));
                                    } ?>
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
                                    <?php echo $row['piede_preferito'] ?>
                                  </td>

                                  <!-- Pulsante Edit -->
                                  <td class="text-center">
                                    <!-- Edit -->
                                    <a class="text-decoration-none" href="edit_player.php?id=<?php echo $row["id"]; ?>" >
                                      <i class='bx bx-pencil text-dark ms-2'></i>
                                    </a>
                                  </td>
                                  
                                  <!-- Pulsante Delete  -->
                                  <td class="text-center">
                                    <!-- Delete -->
                                    <a class="text-decoration-none" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                      <i class='bx bx-trash text-danger'></i>
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
                                  <br/>
                                  <small class="">
                                    <?php echo date('d/m/y',strtotime( $partita['data'])) ?>
                                  </small>
                                </td>
                                
                                <!-- Squadra casa -->
                                <td class="text-end">
                                  <div class="<?php if (($partita['casa'] == $info['nome_societa'] ) && $partita['risultato'] === '1') {
                                      echo 'text-success fw-bold';
                                    } elseif (($partita['casa'] == $info['nome_societa'] ) && $partita['risultato'] === 'X') {
                                      echo 'text-primary fw-bold';
                                    } elseif (($partita['casa'] == $info['nome_societa'] ) && $partita['risultato'] === '2') {
                                      echo 'text-danger fw-bold';
                                    } else {
                                      echo 'text-dark';
                                    }
                                    ?>
                                  ">
                                  <?php echo $partita['casa'] ?></div>
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
                                <div class="<?php if (($partita['ospite'] == $info['nome_societa'] ) && $partita['risultato'] === '1') {
                                      echo 'text-danger fw-bold';
                                    } elseif (($partita['ospite'] == $info['nome_societa'] ) && $partita['risultato'] === 'X') {
                                      echo 'text-primary fw-bold';
                                    } elseif (($partita['ospite'] == $info['nome_societa'] ) && $partita['risultato'] === '2') {
                                      echo 'text-success fw-bold';
                                    } else {
                                      echo 'text-dark';
                                    }
                                    ?>
                                  ">
                                    <?php echo $partita['ospite'] ?></div>
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