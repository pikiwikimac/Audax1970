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
  $superuser = $_SESSION['superuser'];
  
  $id_societa = $_REQUEST['id_societa'];
  $id_societa_squadra_admin = $_SESSION['id_societa_riferimento'];

  $stagione = 1;
  $coppa_marche = 2;

  # Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  WITH ammonizioni AS (
  SELECT a.id_giocatore, p.id_stagione, COUNT(*) AS count
  FROM ammoniti a
  JOIN partite p ON a.id_partita = p.id
  WHERE p.id_stagione IN ('$stagione', '$coppa_marche')
  GROUP BY a.id_giocatore, p.id_stagione
),
espulsioni AS (
  SELECT r.id_giocatore, p.id_stagione, COUNT(*) AS count
  FROM rossi r
  JOIN partite p ON r.id_partita = p.id
  WHERE p.id_stagione IN ('$stagione', '$coppa_marche')
  GROUP BY r.id_giocatore, p.id_stagione
),
gol AS (
  SELECT m.id_giocatore, p.id_stagione, COUNT(*) AS count
  FROM marcatori m
  JOIN partite p ON m.id_partita = p.id
  WHERE p.id_stagione IN ('$stagione', '$coppa_marche')
  GROUP BY m.id_giocatore, p.id_stagione
),
allenamenti AS (
  SELECT pa.id_giocatore, COUNT(*) AS count
  FROM partecipazione_allenamenti pa
  JOIN allenamenti a ON pa.id_allenamento = a.id
  WHERE a.id_societa = '$id_societa'
  GROUP BY pa.id_giocatore
),
convocazioni AS (
  SELECT c.id_giocatore, p.id_stagione, COUNT(*) AS count
  FROM convocazioni c
  JOIN partite p ON p.id = c.id_partita
  WHERE p.id_stagione IN ('$stagione', '$coppa_marche')
  GROUP BY c.id_giocatore, p.id_stagione
)
SELECT g.*,
  COALESCE(ammonizioni_tot.count, 0) AS numero_ammonizioni,
  COALESCE(ammonizioni_coppa.count, 0) AS numero_ammonizioni_coppa,
  COALESCE(ammonizioni_campionato.count, 0) AS numero_ammonizioni_campionato,
  COALESCE(espulsioni_tot.count, 0) AS numero_espulsioni,
  COALESCE(espulsioni_coppa.count, 0) AS numero_espulsioni_coppa,
  COALESCE(espulsioni_campionato.count, 0) AS numero_espulsioni_campionato,
  COALESCE(gol_tot.count, 0) AS numero_gol,
  COALESCE(gol_coppa.count, 0) AS numero_gol_coppa,
  COALESCE(gol_campionato.count, 0) AS numero_gol_campionato,
  COALESCE(allenamenti.count, 0) AS numero_allenamenti,
  COALESCE(convocazioni_tot.count, 0) AS convocazioni,
  COALESCE(convocazioni_coppa.count, 0) AS convocazioni_coppa,
  COALESCE(convocazioni_campionato.count, 0) AS convocazioni_campionato
FROM giocatori g
INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id
LEFT JOIN (
  SELECT id_giocatore, SUM(count) AS count
  FROM ammonizioni
  GROUP BY id_giocatore
) ammonizioni_tot ON g.id = ammonizioni_tot.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM ammonizioni
  WHERE id_stagione = '$coppa_marche'
) ammonizioni_coppa ON g.id = ammonizioni_coppa.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM ammonizioni
  WHERE id_stagione = '$stagione'
) ammonizioni_campionato ON g.id = ammonizioni_campionato.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, SUM(count) AS count
  FROM espulsioni
  GROUP BY id_giocatore
) espulsioni_tot ON g.id = espulsioni_tot.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM espulsioni
  WHERE id_stagione = '$coppa_marche'
) espulsioni_coppa ON g.id = espulsioni_coppa.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM espulsioni
  WHERE id_stagione = '$stagione'
) espulsioni_campionato ON g.id = espulsioni_campionato.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, SUM(count) AS count
  FROM gol
  GROUP BY id_giocatore
) gol_tot ON g.id = gol_tot.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM gol
  WHERE id_stagione = '$coppa_marche'
) gol_coppa ON g.id = gol_coppa.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM gol
  WHERE id_stagione = '$stagione'
) gol_campionato ON g.id = gol_campionato.id_giocatore
LEFT JOIN allenamenti ON g.id = allenamenti.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, SUM(count) AS count
  FROM convocazioni
  GROUP BY id_giocatore
) convocazioni_tot ON g.id = convocazioni_tot.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM convocazioni
  WHERE id_stagione = '$coppa_marche'
) convocazioni_coppa ON g.id = convocazioni_coppa.id_giocatore
LEFT JOIN (
  SELECT id_giocatore, count AS count
  FROM convocazioni
  WHERE id_stagione = '$stagione'
) convocazioni_campionato ON g.id = convocazioni_campionato.id_giocatore
WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine IS NULL
ORDER BY ruolo, cognome, nome ASC;

  ";

  $giocatori = mysqli_query($con, $query);
  
  # Query che conta tutti i giocatori
  $query2 = "
  SELECT count(*) as numero_giocatori
  FROM giocatori g
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine is NULL";

  $result = mysqli_query($con,$query2);
  $numero_giocatori = mysqli_fetch_assoc($result);

  $query3 = "
  SELECT COUNT(*) as tot_allenamenti
  FROM allenamenti a
  WHERE a.stato='Svolto'
  AND a.id_societa = '$id_societa'
  ";
  
  $allenamenti = mysqli_query($con,$query3);
  $tot_allenamenti_svolti = mysqli_fetch_assoc($allenamenti);

  // Eseguire una query iniziale per ottenere il parent_id della società con id_societa
  $checkParentQuery = "SELECT parent_id FROM societa WHERE id = '$id_societa'";
  $checkParentResult = mysqli_query($con, $checkParentQuery);
  $row = mysqli_fetch_assoc($checkParentResult);

  // Controllare il valore di parent_id
  if ($row['parent_id'] !== null) {
      // Se parent_id non è null, selezionare tutte le squadre con lo stesso parent_id inclusa la squadra con id = parent_id
      $parent_id = $row['parent_id'];
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.parent_id = '$id_societa_squadra_admin'
      OR s.id = '$id_societa_squadra_admin'
      ";
  } else {
      // Se parent_id è null, selezionare la società con id_societa e tutte le sue società figlie
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.id = '$id_societa_squadra_admin'
      OR s.parent_id = '$id_societa_squadra_admin'
      ";
  }

  // Eseguire la query e ottenere i risultati
  $societa_collegate = mysqli_query($con, $query4);
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
                          Rosa
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <?php if($_SESSION['superuser'] === 1 ){ ?>
                          <a href="insert_player.php" type="button" class="btn btn-sm btn-outline-dark">
                            <i class='bx bx-plus'></i> 
                          </a>
                          <?php } ?>
                          
                          <button onclick="window.location.href='rose_campionati.php'" type="button" class="btn btn-sm btn-outline-dark me-2">
                            Rose campionati
                          </button>

                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row mb-3">
                        <div class="col-12">
                          <?php while($row = mysqli_fetch_assoc($societa_collegate)) { ?>
                            <a class="text-decoration-none text-white" href="rosa_admin.php?id_societa=<?php echo $row['id'] ?>">
                              <span class="badge bg-secondary" style="font-size:10px;padding:6px">
                                <?php echo $row['tipo'] ?>
                              </span>  
                            </a>
                          <?php } ?>    
                        </div>
                        <div class="col-12 table-responsive">
                          <table class="table table-sm table-hover table-striped table-rounded sortable mt-3" id="tabella-giocatori">

                            <caption><?php echo $numero_giocatori['numero_giocatori'] ?> giocatori totali</caption>

                            <thead class="table-dark">
                              <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Anno</th>
                                <th>Ruolo</th>
                                <th class="text-center">CAP</th>
                                <th class="text-center">Taglia</th>
                                <th class="text-center">Maglia</th>
                                <th class="text-center">Piede</th>
                                <th class="text-center">Allenamenti</th>
                                <th class="text-center"><i class='bx bxs-t-shirt align-middle'></i></th>
                                <th class="text-center"><i class='bx bx-football align-middle'></i></th>
                                <th class="text-center"><i class='bx bxs-card align-middle' style='color:#ffb900'></i></th>
                                <th class="text-center"><i class='bx bxs-card align-middle' style='color:#FF0000'></i></th>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <th class="text-center"></th>
                                <?php } ?>
                              </tr>
                            </thead>

                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($giocatori)) { ?>
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
                                <td class="fw-semibold">
                                  <?php echo $row['nome'] . " " . $row['cognome']; ?>
                                </td>

                                <!-- Data di nascita -->
                                <td>
                                  <?php if($row['data_nascita']==='0000-00-00'){
                                    echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                                  }else{
                                    echo date('d/m/Y',strtotime($row['data_nascita']));
                                  } ?>
                                </td>
                                
                                <!-- Ruolo -->
                                <td>
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

                                <td class="text-center"> <?php
                                    if ($row['capitano'] == 'C') {
                                        echo 'C';
                                    } elseif ($row['capitano'] == 'VC') {
                                        echo 'VC';
                                    }
                                  ?>
                                  </td>
                                <td class="text-center"><?php echo $row['taglia']; ?></td>
                                <td class="text-center"><?php echo $row['maglia']; ?></td>
                                <td class="text-center"><?php echo $row['piede_preferito']; ?></td>
                                <td class="text-center"><?php echo $row['numero_allenamenti']; ?></td>
                                <td class="text-center"><?php echo $row['convocazioni']; ?></td>
                                <td class="text-center"><?php echo $row['numero_gol']; ?></td>
                                <td class="text-center"><?php echo $row['numero_ammonizioni']; ?></td>
                                <td class="text-center"><?php echo $row['numero_espulsioni']; ?></td>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <td class="text-center">
                                  <a class="text-decoration-none text-dark" href="edit_player.php?id=<?php echo $row['id'] ?>">
                                    <i class='bx bx-pencil'></i>
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



    


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

    <script>
      $(document).ready(function() {
        // Gestisci il click sull'immagine
        $('.image-clickable').click(function() {
          var imagePath = $(this).attr('src');
          var playerName = $(this).data('player-name'); // Ottieni il nome del giocatore
          $('#modalImage').attr('src', imagePath); // Imposta l'immagine nel modal
          $('#editModalLabel').html(playerName); // Imposta il nome del giocatore nel modal-header
          $('#imageModal').modal('show'); // Apri il modal
        });
      });
    </script>

    <script>
      $(document).ready(function() {
        $('#tabella-giocatori').DataTable({
          paging:false,
          info:false,
          searching:false,
          columnDefs: [
            { orderable: false, targets: [0,4,5] }
            ]
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


  </body>
</html> 