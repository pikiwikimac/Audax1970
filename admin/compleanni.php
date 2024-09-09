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
  $id_societa=  $_SESSION['id_societa_riferimento'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT g.*, 
    CASE
        WHEN DATE_FORMAT(g.data_nascita, '%m-%d') >= DATE_FORMAT(NOW(), '%m-%d') THEN DATEDIFF(DATE_FORMAT(NOW(), CONCAT(YEAR(NOW()), '-', MONTH(g.data_nascita), '-', DAY(g.data_nascita))), NOW())
        ELSE DATEDIFF(DATE_FORMAT(NOW(), CONCAT(YEAR(NOW())+1, '-', MONTH(g.data_nascita), '-', DAY(g.data_nascita))), NOW())
    END AS giorni_mancanti_compleanno
  FROM giocatori g
  WHERE g.id_squadra = '$id_societa'
  ORDER BY giorni_mancanti_compleanno,g.data_nascita, g.ruolo, g.cognome, g.nome ASC;

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
                        <h4>
                          Compleanni
                        </h4>
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
                                <th class="text-center">Data</th>
                                <th class="text-center">Giorni</th>
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
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
                                  <?php echo $row['cognome'] . '  ' .$row['nome']   ?>
                                </td>
            
                                <!-- Ruolo -->
                                <td>
                                  <?php echo $row['ruolo'] ?>
                                </td>
                                
                                <!-- Data di nascita -->
                                <td class="text-center">
                                  <?php if($row['data_nascita']==='0000-00-00'){
                                    echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                                  }else{
                                    echo date('d/m/y',strtotime($row['data_nascita']));
                                  } ?>
                                </td>
                                
                                <td class="text-center">
                                  <?php echo $row['giorni_mancanti_compleanno'] .' giorni mancanti'?>
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
      </main>



    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>