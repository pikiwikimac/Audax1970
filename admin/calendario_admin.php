<?php
session_start();

// Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

// Controlla la connessione al database
if (!$con) {
    die('Errore di connessione: ' . mysqli_connect_error());
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$image = isset($_SESSION['image']) ? $_SESSION['image'] : null;
$superuser = isset($_SESSION['superuser']) ? $_SESSION['superuser'] : 0;

// Validazione input
$stagione_id = isset($_REQUEST['id_stagione']) && is_numeric($_REQUEST['id_stagione']) ? intval($_REQUEST['id_stagione']) : null;
$societa_id = isset($_REQUEST['id_societa']) && is_numeric($_REQUEST['id_societa']) ? intval($_REQUEST['id_societa']) : null;

if (!$stagione_id || !$societa_id) {
    die('Errore: ID stagione o ID società non valido.');
}

// Query che seleziona le info della squadra (usando prepared statement)
$query = "SELECT s.* FROM societa s WHERE s.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $societa_id);
$stmt->execute();
$squadra = $stmt->get_result();
$info_squadra = $squadra->fetch_assoc();

// Query che seleziona le partite (usando prepared statement)
$sql = "
SELECT
  soc.nome_societa AS casa,                  
  soc2.nome_societa AS ospite,
  soc.id AS id_casa,                  
  soc2.id AS id_ospite,               
  s.golCasa,                                 
  s.golOspiti,                               
  CAST(s.giornata AS UNSIGNED) AS giornata_,
  s.giornata,                                
  s.id,                                      
  s.data,                                    
  s.played,                                  
  CASE
    WHEN s.orario_modificato IS NOT NULL THEN s.orario_modificato
    ELSE soc.ora_match
  END AS orario_partita,                     
  CASE
    WHEN s.data_modificata IS NOT NULL THEN s.data_modificata
    ELSE s.data
  END AS giornata_partita,                   
  CASE
    WHEN s.golCasa > s.golOspiti THEN '1'
    WHEN s.golCasa = s.golOspiti THEN 'X'
    WHEN s.golCasa < s.golOspiti THEN '2'
    ELSE ''
  END AS risultato                           
FROM
  partite s                                
INNER JOIN societa soc ON soc.id = s.squadraCasa   
INNER JOIN societa soc2 ON soc2.id = s.squadraOspite 
WHERE
  s.id_stagione = ?                          
  AND (s.squadraCasa = ? OR s.squadraOspite = ?)
ORDER BY
  giornata_, casa, ospite;
";
$stmt = $con->prepare($sql);
$stmt->bind_param("iii", $stagione_id, $societa_id, $societa_id);
$stmt->execute();
$campionato = $stmt->get_result();

// Query per ottenere le informazioni sulla stagione
$query1 = "SELECT * FROM stagioni s WHERE s.id_stagione = ?";
$stmt = $con->prepare($query1);
$stmt->bind_param("i", $stagione_id);
$stmt->execute();
$info_stagione = $stmt->get_result();
$info = $info_stagione->fetch_assoc();

// Gestione delle squadre correlate
if ($info_squadra['parent_id'] === NULL) {
    // Se parent_id è NULL, seleziona le squadre con lo stesso parent_id o ID società
    $query4 = "
    SELECT * FROM societa s
    WHERE parent_id = ? OR id = ?";
    $stmt = $con->prepare($query4);
    $stmt->bind_param("ii", $societa_id, $societa_id);
} else {
    // Altrimenti, seleziona le squadre con lo stesso parent_id
    $parent_id = $info_squadra['parent_id'];
    $query4 = "
    SELECT * FROM societa s
    WHERE parent_id = ? OR id = ?";
    $stmt = $con->prepare($query4);
    $stmt->bind_param("ii", $parent_id, $parent_id);
}

$stmt->execute();
$squadre_correlate = $stmt->get_result();

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
                          Calendario
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <?php if($_SESSION['superuser'] == 1 ){ ?>
                          <a type="button" class="btn btn-sm btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
                            <i class="bi bi-plus"></i>
                          </a>
                          <?php } ?>
                          <button onclick="window.location.href='calendario_completo_admin.php?id_stagione=<?php echo $stagione_id ?>'"  class="btn btn-sm btn-outline-dark float-end me-2"  >
                            Girone
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row g-3 mb-3">
                        <div class="col-12">
                          <span class="fs-6 mb-2 ">
                            <?php echo $info['descrizione'] ?>
                          </span>

                          <div class="float-end mb-2">
                            
                            <?php while($row = mysqli_fetch_assoc($squadre_correlate)){?>
                              <a class="text-decoration-none text-white" href="calendario_admin.php?id_stagione=<?php echo $row['id_campionato']?>&id_societa=<?php echo $row['id'] ?>">
                                <span class="badge bg-secondary">
                                  <?php echo $row['tipo'] ?>
                                </span>
                              </a>
                            <?php } ?>
                            
                            <a href="calendario_admin.php?id_stagione=3&id_societa=1">
                              <span class="badge bg-secondary">
                                Amichevoli
                              </span>
                            </a>
                            
                          </div>
                        </div>

                        <div class="col-12 table-responsive">
                          
                          
                          <table class="table  table-hover table-striped table-rounded caption-top">
                            <!-- <caption>Giornate di riposo: 8° e 21° </caption> -->
                            <thead class="table-dark">

                              <tr>
                                <th class="text-center"></th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Orario</th>
                                <th class="text-end">Casa</th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class=""> Ospite</th>
                                <?php if($_SESSION['superuser'] == 1 ){ ?>
                                <th class="text-center" style="width:1%"></th>
                                <th class="text-center" style="width:1%"></th>
                                <th class="text-center" style="width:1%"></th>
                                <th class="text-center" style="width:1%"></th>
                                <th class="text-center" style="width:1%"></th>
                                <?php } ?>
                                
                                
                              </tr>

                            </thead>

                            <tbody class="">
                              <?php while($row = mysqli_fetch_assoc($campionato)) {  ?>
                                <tr class=" align-middle">
                                  <!-- Numero giornata -->
                                  <td class="text-center">
                                    <small class="text-center">
                                      &nbsp;<?php echo $row['giornata'] ?>° 
                                    </small>
                                    <br/>
                                    <small class="text-center">
                                      <?php 
                                        if (in_array($row['id_casa'], ['1', '3', '4', '6'])) { 
                                            echo "<i class='bi bi-house-door-fill'></i>";
                                        } else { 
                                            echo "<i class='bi bi-airplane-fill'></i>";
                                        }
                                      ?>
                                    </small>


                                  </td>
                                  
                                  <!-- Data -->
                                  <td class="text-center">
                                    <small class="">
                                      <?php echo date('d/m/y',strtotime( $row['data'])) ?>
                                    </small>
                                    <br/>
                                    <small class="">
                                      <?php 
                                        setlocale(LC_TIME, 'it_IT.utf8');
                                        $dayOfWeek = strftime('%A', strtotime($row['giornata_partita']));
                                        $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                        echo $abbreviatedDay;
                                      ?>
                                    </small>
                                  </td>
                                  
                                  <td class="text-center">
                                    <small class="text-center">
                                      <?php echo date('H:i', strtotime($row['orario_partita'])) ?>
                                    </small>
                                  </td>
                                  
                                  <!-- Squadra casa -->
                                  <td class="text-end text-nowrap">
                                    <a href="show_societa.php?id=<?php echo $row['id_casa'] ?>" class="text-decoration-none">
                                      <div class="
                                        <?php 
                                          if (in_array($row['id_casa'], ['1', '3', '4', '6'])) { 
                                              echo 'fw-bold'; 
                                          } else { 
                                              echo 'text-dark'; 
                                          }
                                          
                                          if ($row['risultato'] === '1') {
                                              echo ' text-success'; // Aggiunto uno spazio prima della classe per evitare concatenazione
                                          } elseif ($row['risultato'] === 'X') {
                                              echo ' text-primary'; // Aggiunto uno spazio prima della classe per evitare concatenazione
                                          } elseif ($row['risultato'] === '2') {
                                              echo ' text-danger'; // Aggiunto uno spazio prima della classe per evitare concatenazione
                                          } else {
                                              echo ' text-dark'; // Aggiunto uno spazio prima della classe per evitare concatenazione
                                          }
                                        ?>">

                                          <?php echo $row['casa'] ?>
                                      </div>
                                    </a>
                                </td>

                                  <!-- Gol casa -->
                                  <td class="text-center">
                                    <?php echo $row['golCasa'] ?>
                                  </td>

                                  <!-- Gol ospite -->
                                  <td class="text-center ">
                                    <?php echo $row['golOspiti'] ?>
                                  </td>
                                  <!-- Squadra ospite -->
                                  <td class="text-nowrap">
                                    <a href="show_societa.php?id=<?php echo $row['id_ospite'] ?>" class="text-decoration-none">
                                      <div class="
                                        <?php 
                                          if (in_array($row['id_ospite'], ['1', '3', '4', '6'])) { 
                                            echo 'fw-bold'; 
                                          } else { 
                                            echo 'text-dark'; 
                                          }
                                            
                                          if ($row['risultato'] === '2') {
                                            echo ' text-success'; 
                                          } elseif ($row['risultato'] === 'X') {
                                            echo ' text-primary';
                                          } elseif ($row['risultato'] === '2') {
                                            echo ' text-danger'; 
                                          } else {
                                            echo ' text-dark'; 
                                          }
                                        ?>">

                                        <?php echo $row['ospite'] ?>
                                      </div>
                                      
                                    </a>
                                  </td>

                                  
                                  <?php if($_SESSION['superuser'] == 1 ){ ?>
                                  <!-- Bottoni modifica stato match -->
                                  <td class="text-center d-print-none">
                                    <?php
                                      if ($row['played'] == 1) {
                                          // Se played è uguale a 1, aggiungi la classe text-success
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=1&page=calendario_admin"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="Played">
                                                  <i class="bi bi-check-all text-danger"></i>
                                                </a>';
                                      } else {
                                          // Altrimenti, lascia la classe vuota
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=0&page=calendario_admin"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="NOT Played">
                                                  <i class="bi bi-check"></i>
                                                </a>';
                                      }

                                    ?>
                                  </td>
                                  
                                  
                                  <!-- Bottoni modifica -->
                                  <td class="text-center d-print-none">
                                  <a href="edit_risultato_massivo.php?id=<?php echo $row["id"]; ?>"
                                      class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Marcatori">
                                      <img src="/image/icon/calcio.svg" alt="Gol">

                                    </a>
                                  </td>
                                  
                                  <!-- Bottoni  -->
                                  <td class="text-center  d-print-none">
                                    <a href="edit_presenza_convocazione.php?id=<?php echo $row["id"]; ?>"
                                      class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Convocazioni">
                                      <i class='bi bi-list-ol'></i>
                                    </a>
                                  </td>

                                  <td class="text-center d-print-none">
                                    <!-- Aggiungi il link per aprire il modal -->
                                    <a href="#" class="text-decoration-none text-dark" 
                                    onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["casa"]; ?>', '<?php echo $row["ospite"]; ?>', '<?php echo $row["golCasa"]; ?>', '<?php echo $row["golOspiti"]; ?>', '<?php echo $row["data"]; ?>', '<?php echo $row["giornata"]; ?>', '<?php echo $row["id_stagione"]; ?>', '<?php echo $row["id_societa"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                      <i class="bi bi-pencil"></i>
                                    </a>
                                  </td>

                                  <td class="text-center d-print-none">
                                    <a class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Elimina"
                                      onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                      <i class='bi bi-trash text-danger' ></i>
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

    <!-- Modal Edit-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Modifica Partita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per la modifica della partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="editForm" method="post" action="../query/action_edit_calendario.php">
              <!-- Inserisci i campi del form per la modifica -->
              <input type="hidden" name="id" id="id" value="">
              
              
              

              <div class="row mb-3">
                <div class="col-9 ">
                  <label for="squadraCasaEdit" class="form-label">Squadra Casa</label>
                  <input readonly type="text"  class="form-control"id="squadraCasaEdit" name="squadraCasaEdit" />
                </div>
                <div class="col-3 ">
                  <label for="golCasa" class="form-label">Gol Casa</label>
                  <input type="text" class="form-control" name="golCasa" id="golCasa">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-9">
                  <label for="squadraOspiteEdit" class="form-label">Squadra Ospite</label>
                  <input readonly type="text" class="form-control" id="squadraOspiteEdit" name="squadraOspiteEdit" />
                </div>
              
                <div class="col-3">
                  <label for="golOspiti" class="form-label">Gol Ospiti</label>
                  <input type="text" class="form-control" name="golOspiti" id="golOspiti">
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-6">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" name="data" id="data">
                </div>

                <div class="col-6">
                  <label for="giornata" class="form-label">Giornata</label>
                  <input type="text" class="form-control" name="giornata" id="giornata">
                </div>
              </div>
            
              <input type="hidden" name="id_stagione" id="id_stagione" value="<?php echo $id_stagione ?>">
              <input type="hidden" name="id_societa" id="id_societa" value="<?php echo $id_societa ?>">
            
            </form>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitEditForm()">Salva</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Insert -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Inserisci Partita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="insertForm" method="post" action="../query/action_insert_calendario.php">
              <!-- Inserisci i campi del form per l'inserimento -->
              <div class="mb-3">
                <label for="squadraCasa" class="form-label">Squadra Casa</label>
                <select class="form-select" name="squadraCasa" id="squadraCasa">
                  <!-- Opzioni per la squadra casa -->
                  <?php
                    $query_squadre = "SELECT * FROM societa ORDER BY id_campionato ASC,nome_societa";
                    $result_squadre = mysqli_query($con, $query_squadre);
                    while ($row_squadre = mysqli_fetch_assoc($result_squadre)) {
                      echo "<option value='{$row_squadre['id']}'>{$row_squadre['nome_societa']}</option>";
                    }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="squadraOspite" class="form-label">Squadra Ospite</label>
                <select class="form-select" name="squadraOspite" id="squadraOspite">
                  <!-- Opzioni per la squadra ospite -->
                  <?php
                    mysqli_data_seek($result_squadre, 0); // Riporta il puntatore del result al primo record
                    while ($row_squadre = mysqli_fetch_assoc($result_squadre)) {
                      echo "<option value='{$row_squadre['id']}'>{$row_squadre['nome_societa']}</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" name="data" id="data">
                </div>
              
                <div class="col-6">
                  <label for="giornata" class="form-label">Giornata</label>
                  <input type="text" class="form-control" name="giornata" id="giornata">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-12">
                  <label for="data" class="form-label">Stagione</label>
                  <select class="form-select" name="stagione" id="stagione">
                    <!-- Opzioni per la squadra ospite -->
                    <?php
                      $sql = "SELECT * FROM `stagioni` ORDER BY anno_fine DESC, anno_inizio DESC, priorita DESC, descrizione ASC, girone ASC";
                      $stagioni = mysqli_query($con, $sql);
                      while ($stagione = mysqli_fetch_assoc($stagioni)) {
                        $selected = ($stagione['id_stagione'] == $id_stagione) ? 'selected' : '';
                        echo "<option value='{$stagione['id_stagione']}' $selected>{$stagione['descrizione']} {$stagione['anno_inizio']} - {$stagione['anno_fine']}</option>";
                      }
                    ?>
                  </select>

                </div>
              </div>
              <input type="hidden" name="id_stagione" id="id_stagione" value="<?php echo $id_stagione ?>">
              <input type="hidden" name="id_societa" id="id_societa" value="<?php echo $id_societa ?>">
              
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitInsertForm()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function showEditModal(id, squadraCasa, squadraOspite, golCasa, golOspiti, data, giornata,id_stagione,id_societa) {
        document.getElementById("id").value = id;
        document.getElementById("golCasa").value = golCasa;
        document.getElementById("golOspiti").value = golOspiti;
        document.getElementById("data").value = data;
        document.getElementById("giornata").value = giornata;
        document.getElementById("squadraCasaEdit").value = squadraCasa;
        document.getElementById("squadraOspiteEdit").value = squadraOspite;
        document.getElementById("id_stagione").value = id_stagione;
        document.getElementById("id_societa").value = id_societa;


        // Seleziona le squadre nella select
        var selectSquadraCasa = document.getElementById("squadraCasa");
        var selectSquadraOspite = document.getElementById("squadraOspite");
        for (var i = 0; i < selectSquadraCasa.options.length; i++) {
          if (selectSquadraCasa.options[i].text === squadraCasa) {
            selectSquadraCasa.options[i].selected = true;
          }
          if (selectSquadraOspite.options[i].text === squadraOspite) {
            selectSquadraOspite.options[i].selected = true;
          }
        }

        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
      }

      function submitEditForm() {
        // Effettua la richiesta di modifica al server tramite il form
        document.getElementById("editForm").submit();
      }

      function submitInsertForm() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("insertForm").submit();
      }
    </script>


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>


    <!-- Conferma eliminazione -->
    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
        // Effettua la richiesta di eliminazione al server
        window.location.href = "../query/action_delete_calendario.php?id=" + recordId;
        }
      }
    </script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>


  </body>

</html>