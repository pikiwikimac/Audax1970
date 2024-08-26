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
  SELECT
    g.*,
    (
    SELECT
        COUNT(*)
    FROM
        ammoniti a
    JOIN partite p ON
        a.id_partita = p.id
    WHERE
        a.id_giocatore = g.id AND p.id_stagione IN(1, 2)
) AS numero_ammonizioni,
(
    SELECT
        COUNT(*)
    FROM
        rossi r
    JOIN partite p ON
        r.id_partita = p.id
    WHERE
        r.id_giocatore = g.id AND p.id_stagione IN(1, 2)
) AS numero_espulsioni,
(
    SELECT
        COUNT(*)
    FROM
        marcatori m
    JOIN partite p ON
        m.id_partita = p.id
    WHERE
        m.id_giocatore = g.id AND p.id_stagione IN(1, 2)
) AS numero_gol,
s.nome_societa,
stag.descrizione as campionato,
stag.girone,
stag.anno_inizio,
stag.anno_fine
FROM
    giocatori g
INNER JOIN societa s ON
    s.id = g.id_squadra
INNER JOIN stagioni stag ON
	s.id_campionato =stag.id_stagione
ORDER BY
    id_squadra,
    ruolo,
    cognome,
    nome ASC";


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
                        <h3>
                          Tutti i giocatori
                        </h3>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          
                    
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row mb-3 ">
                        <div class="col-12 table-responsive">
                          <table class="table table-hover table-striped table-rounded sortable display" id="tabella-giocatori">

                            <thead class="table-dark">

                              <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Squadra</th>
                                <th>Campionato</th>
                                <th>Anno</th>
                                <th>Ruolo</th>
                                <th class="text-center"><i class='bx bx-football align-middle'></i></th>
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
                                  <?php if ($row['image_path']) { ?>
                                    <img src="../image/player/<?php echo $row['image_path'];?>" class="rounded-circle " alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="30" height="30"/>
                                  <?php } else { ?>
                                    <img src="../image/default_user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" width="30" height="30" />
                                  <?php } ?>
                                </td>
                                
                                
                                <!-- Nome e Cognome -->
                                <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row['cognome'] .' '. $row['nome']?>
                                </td>
                                
                                <!-- Squadra -->
                                <td>
                                  <?php echo $row['nome_societa'] ?>
                                </td>

                                <!-- Campionato -->
                                <td>
                                  <?php echo $row['campionato'] . ' - ' .$row['girone'] .' ' .$row['anno_fine'] ?>
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
                                
                                <!-- Numero gol -->
                                <td class="text-center">
                                  <?php echo $row['numero_gol'] ?>
                                </td>

                                <!-- Pulsante Edit -->
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <td class="text-center">
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="edit_player.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bx bx-pencil text-dark ms-2'></i>
                                  </a>
                                  
                                </td> 
                                <!-- Pulsante Delete -->
                                <td class="text-center">
                                  <!-- Delete -->
                                  <a class="text-decoration-none" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                    <i class='bx bx-trash text-danger'></i>
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
          paging: true,
          info: true,
          searching: true, // Abilita il pannello di ricerca
          order: [[1, 'asc']],
          language: {
              url: '//cdn.datatables.net/plug-ins/2.0.1/i18n/it-IT.json',
          },
          columnDefs: [{ // Aggiungi filtri alle colonne desiderate
            targets: [2, 3, 4, 5, 6, 7,8 ], // Indici delle colonne in cui aggiungere i filtri
            searchable: true,
            orderable: true
          }]
        });
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