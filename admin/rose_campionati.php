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

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT g.*, 
  (
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p
    ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
    
  ) AS numero_ammonizioni,
  (
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p
    ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
  ) AS numero_espulsioni,
  (
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p
    ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
  ) AS numero_gol,s.nome_societa
  FROM giocatori g
  INNER JOIN societa s on s.id=g.id_squadra
  WHERE g.id_squadra != 1
  ORDER BY id_squadra, ruolo, cognome, nome ASC";


  $giocatori = mysqli_query($con,$query);

  #Query che conta tutti i giocatori
  $query2 = "
  select count(*) as numero_giocatori
  FROM giocatori
  ORDER BY id_squadra";

  $result = mysqli_query($con,$query2);
  $numero_giocatori = mysqli_fetch_assoc($result);



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
                          Tutti i giocatori
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <?php if($_SESSION['superuser'] === 1 ){?>
                          <a href="insert_player.php" type="button" class="btn btn-sm btn-outline-dark float-end">
                            <i class="bi bi-plus"></i>
                          </a>
                          <?php } ?>
                          
                          <a href="rosa_admin.php?id_societa=<?php echo $id_societa ?>" type="button" class="btn btn-sm btn-outline-dark float-end me-2">
                            My team
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      
                      <div class="row mb-3 ">
                        <div class="col-12 table-responsive">
                          <table class="table table-hover table-striped table-rounded sortable" id="tabella-giocatori">

                            <caption><?php echo $numero_giocatori['numero_giocatori'] ?> giocatori totali</caption>

                            <thead class="table-dark">

                              <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Squadra</th>
                                <th>Anno</th>
                                <th>Ruolo</th>
                                <th class="text-center">Maglia</th>
                                <th class="text-center">Piede</th>
                                <th class="text-center"><img src="/image/icon/calcio.svg" alt="Gol">
</th>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <?php } ?>
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                              <tr >
                                <!-- Immagine -->
                                <td class="text-center">
                                  <img src="../image/player/<?php echo $row['image_path']; ?>" class="rounded-circle " alt="..." width="30" height="30"/>
                                </td>
                                
                                
                                <!-- Nome e Cognome -->
                                <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row['cognome'] .' '. $row['nome']?>
                                </td>
                                
                                <!-- Squadra -->
                                <td>
                                  <?php echo $row['nome_societa'] ?>
                                </td>

                                <!-- Data di nascita -->
                                <td>
                                  <?php if($row['data_nascita']==='0000-00-00'){
                                    echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                                  }else{
                                    echo date('d/m/y',strtotime($row['data_nascita']));
                                  } ?>
                                </td>

                                <!-- Ruolo -->
                                <td>
                                  <?php echo $row['ruolo'] ?>
                                </td>

                              
                                <!-- Maglia -->
                                <td class="text-center">
                                  <?php echo $row['maglia'] ?>
                                </td>
                                
                                <!-- Piede -->
                                <td class="text-center">
                                  <?php echo $row['piede_preferito'] ?>
                                </td>
                                
                                <!-- Numero gol -->
                                <td class="text-center">
                                  <?php echo $row['numero_gol'] ?>
                                </td>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <!-- Pulsante Edit -->
                                <td class="text-center">
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="edit_player.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bi bi-pencil text-dark ms-2'></i>
                                  </a>
                                  
                                </td> 
                                <!-- Pulsante Delete -->
                                <td class="text-center">
                                  <!-- Delete -->
                                  <a class="text-decoration-none" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                    <i class='bi bi-trash text-danger'></i>
                                  </a>
                                </td>
                                <?php } ?>
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


    <script>
      $(document).ready(function() {
        $('#tabella-giocatori').DataTable({
          paging:false,
          info:false,
          searching:false
          }
        );
      });
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

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>