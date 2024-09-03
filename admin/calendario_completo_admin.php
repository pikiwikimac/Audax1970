<?php
  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  
  require_once('../config/db.php');

  if (!$con) {
    die('Errore di connessione: ' . mysqli_connect_error());
  }

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  $stagione_id = $_REQUEST['id_stagione'];
  $societa_id = $_REQUEST['id_societa'];

  $query = "
  SELECT soc.nome_societa as casa, soc2.nome_societa as ospite, s.golCasa,s.golOspiti,s.giornata,s.id,s.data,s.played,soc.ora_match
  FROM `partite` s
  INNER JOIN societa soc on soc.id=s.squadraCasa
  INNER JOIN societa soc2 on soc2.id=s.squadraOspite
  WHERE s.id_stagione=2
  ORDER BY data,ora_match
  ";

  $coppa_marche = mysqli_query($con,$query);  
  
  $query_giornate = "SELECT DISTINCT CAST(giornata AS UNSIGNED) AS giornata_numero  FROM `partite` p WHERE p.id_stagione = '$stagione_id' ORDER BY giornata_numero";
  $lista_giornate = mysqli_query($con, $query_giornate);

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
                        Calendario completo
                      <h4>

                      <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <?php if($_SESSION['superuser'] == 1 ){ ?>
                          <a type="button" class="btn btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
                            <i class='bx bx-plus '></i>
                          </a>
                          <?php } ?>
                          <button onclick="window.location.href='calendario_admin.php?id_stagione=<?php echo $stagione_id ?>&id_societa=<?php echo $id_societa ?>'"  class="btn btn-outline-dark float-end me-2" >
                            My team
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
         
                      <div class="row g-3 mb-3">
                        <?php 
                          while ($giornata = mysqli_fetch_assoc($lista_giornate)) {
                              $giornata_numero = $giornata['giornata_numero'];
                              $query_camp = "SELECT 
                                          soc.nome_societa as casa, 
                                          soc2.nome_societa as ospite, 
                                          golCasa, 
                                          golOspiti, 
                                          CAST(giornata AS UNSIGNED) AS giornata_numero,
                                          s.id, 
                                          s.data, 
                                          s.played,
                                          soc.ora_match
                                      FROM `partite` s
                                      LEFT JOIN societa soc on soc.id=s.squadraCasa
                                      LEFT JOIN societa soc2 on soc2.id=s.squadraOspite
                                      WHERE s.id_stagione = 1
                                      AND giornata = '$giornata_numero'
                                      ORDER BY giornata_numero,s.data,soc.ora_match, casa, ospite";
                              $campionato = mysqli_query($con, $query_camp);
                          ?>
                        <div class="col-12 col-xl-6 table-responsive"> 
                          <table class="table table-sm table-hover table-striped table-rounded caption-top ">
                            <caption class="fs-5 text-dark fw-bold"><?php echo $giornata_numero ?> ° GIORNATA </caption>
                                    
                            <tbody class="">
                              <?php while($row = mysqli_fetch_assoc($campionato)) {  ?>
                                <tr class="<?php echo $rowClass; ?> align-middle">
                                  
                                  <!-- Data -->
                                  <td class="text-center">
                                    <small class="">
                                      <?php echo date('d/m/y',strtotime( $row['data'])) ?>
                                    </small>
                                    &nbsp;
                                    <small class="">
                                      <?php 
                                        setlocale(LC_TIME, 'it_IT.utf8');
                                        $dayOfWeek = strftime('%A', strtotime($row['data']));
                                        $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                        echo $abbreviatedDay;
                                      ?>
                                    </small>
                                  </td>
                                  
                                  <!-- Squadra casa -->
                                  <td class="text-end">
                                    <div class="<?= $row['casa'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">
                                      <?php echo strlen($row['casa']) > 15 ? substr($row['casa'], 0, 15) . '...' : $row['casa']; ?>
                                    </div>
                                  </td>
                                  <!-- Gol casa -->
                                  <td class="text-center">
                                    <?php echo $row['golCasa'] ?>
                                  </td>

                                  <!-- Gol ospite -->
                                  <td class="text-center">
                                    <?php echo $row['golOspiti'] ?>
                                  </td>
                                  <!-- Squadra ospite -->
                                  <td class="">
                                    <div class="<?= $row['ospite'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">
                                      <?php echo strlen($row['ospite']) > 15 ? substr($row['ospite'], 0, 15) . '...' : $row['ospite']; ?>
                                    </div>
                                  </td>
                                  
                                  <?php if($_SESSION['superuser'] == 1 ){ ?>
                                  <!-- Bottoni modifica stato match -->
                                  <td class="text-center d-print-none">
                                    <?php
                                      if ($row['played'] == 1) {
                                          // Se played è uguale a 1, aggiungi la classe text-success
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=1&page=calendario_completo_admin"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="Played">
                                                  <i class="bx bx-check-double text-danger"></i>
                                                </a>';
                                      } else {
                                          // Altrimenti, lascia la classe vuota
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=0&page=calendario_completo_admin"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="NOT Played">
                                                  <i class="bx bx-check"></i>
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
                                      <i class='bx bx-football'   ></i>
                                    </a>
                                  </td>
                                  


                                  <td class="text-center d-print-none">
                                    <!-- Aggiungi il link per aprire il modal -->
                                    <a href="#" class="text-decoration-none text-dark" 
                                    onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["casa"]; ?>', '<?php echo $row["ospite"]; ?>', '<?php echo $row["golCasa"]; ?>', '<?php echo $row["golOspiti"]; ?>', '<?php echo $row["data"]; ?>', '<?php echo $row["giornata_numero"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                      <i class='bx bx-pencil '></i>
                                    </a>
                                  </td>

                                  <td class="text-center d-print-none">
                                    <a class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Elimina"
                                      onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                      <i class='bx bx-trash text-danger' ></i>
                                    </a>
                                  </td>

                                  <?php } ?>
                                  

                                </tr>
                              <?php } ?>
                            </tbody>

                          </table>
                        </div>
                        <?php } ?>
                      </div>

                      <?php if($coppa_marche->num_rows > 0) {  ?>
                      <div class="row mb-3">
                        <div class="col-12 table-responsive">
                          <span class="fs-5">Coppa marche</span>
                          <table class="table  table-hover table-striped table-rounded">
                            <thead class="table-dark">

                              <tr>
                                <th class="text-center" width="2%"></th>
                                <th class="text-center" width="2%"></th>
                                <th class="text-center" width="2%"></th>
                                <th class="text-center">Casa</th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"> Ospite</th>
                                <?php if($_SESSION['superuser'] == 1 ){ ?>
                                <th class="text-center" style="width:2%"></th>
                                <th class="text-center" style="width:2%"></th>
                                <th class="text-center" style="width:2%"></th>
                                <th class="text-center" style="width:2%"></th>
                                <th class="text-center" style="width:2%"></th>
                                <?php } ?>
                                
                              </tr>

                            </thead>

                            <tbody class="">
                              <?php while($row = mysqli_fetch_assoc($coppa_marche)) {  ?>
                                <tr class="<?php echo $rowClass; ?>">
                                  <!-- Numero giornata -->
                                  <td class="text-center">
                                    <small class="">
                                      <?php echo $row['giornata'] ?>° 
                                    </small>
                                  </td>
                                  <!-- Cas o fuori casa -->
                                  <td class="text-center">
                                    <small class="">
                                      <?php if($row['casa']=='Audax 1970' || $row['ospite']=='Audax 1970'){ ?>
                                        <?php if($row['casa']!='Audax 1970'){?><i class='bx bxs-plane-alt'></i> <?php } ?>
                                        <?php if($row['casa']=='Audax 1970'){?><i class='bx bxs-home'></i> <?php } ?>
                                      <?php } ?>
                                    </small>
                                  </td>
                                  <!-- Numero giornata -->
                                  <td class="text-center">
                                    <small class="">
                                      <?php echo date('d/m/y',strtotime( $row['data'])) ?>
                                    </small>
                                  </td>


                                  <!-- Squadra casa -->
                                  <td class="text-center">
                                    <div class="<?= $row['casa'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">   <?php echo $row['casa'] ?></div>
                                  </td>
                                  <!-- Gol casa -->
                                  <td class="text-center">
                                    <?php echo $row['golCasa'] ?>
                                  </td>

                                  <!-- Gol ospite -->
                                  <td class="text-center">
                                    <?php echo $row['golOspiti'] ?>
                                  </td>
                                  <!-- Squadra ospite -->
                                  <td class="text-center">
                                    <div class="<?= $row['ospite'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">   <?php echo $row['ospite'] ?></div>
                                  </td>

                                  <?php if($_SESSION['superuser'] == 1 ){ ?>
                                  <!-- Bottoni modifica stato match -->
                                  <td class="text-center">
                                    <?php
                                      if ($row['played'] == 1) {
                                          // Se played è uguale a 1, aggiungi la classe text-success
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=1"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="Played">
                                                  <i class="bx bx-check-double text-danger"></i>
                                                </a>';
                                      } else {
                                          // Altrimenti, lascia la classe vuota
                                          echo '<a href="../query/action_played.php?id=' . $row["id"] . '&played=0"
                                                  class="text-decoration-none text-dark"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-title="NOT Played">
                                                  <i class="bx bx-check"></i>
                                                </a>';
                                      }

                                    ?>
                                  </td>

                                  <!-- Bottoni modifica -->
                                  <td class="text-center">
                                  <a href="edit_risultato_massivo.php?id=<?php echo $row["id"]; ?>"
                                      class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Marcatori">
                                      <i class='bx bx-football'   ></i>
                                    </a>
                                  </td>
                                  
                                  <!-- Bottoni  -->
                                  <td class="text-center ">
                                    <a href="edit_presenza_convocazione.php?id=<?php echo $row["id"]; ?>"
                                      class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Convocazioni">
                                      <i class='bx bx-list-ol'></i>
                                    </a>
                                  </td>

                                  <td class="text-center">
                                    <!-- Aggiungi il link per aprire il modal -->
                                    <a href="#" class="text-decoration-none text-dark" 
                                    onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["casa"]; ?>', '<?php echo $row["ospite"]; ?>', '<?php echo $row["golCasa"]; ?>', '<?php echo $row["golOspiti"]; ?>', '<?php echo $row["data"]; ?>', '<?php echo $row["giornata"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                      <i class='bx bx-pencil '></i>
                                    </a>
                                  </td>

                                  <td class="text-center">
                                    <a class="text-decoration-none text-dark"
                                      data-bs-toggle="tooltip"
                                      data-bs-title="Elimina"
                                      onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                      <i class='bx bx-trash text-danger' ></i>
                                    </a>
                                  </td>

                                  <?php } ?>


                                </tr>
                              <?php } ?>
                            </tbody>

                          </table>
                        </div>
                      </div>
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
              <!-- Aggiungi altri campi per la modifica se necessario -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-primary" onclick="submitEditForm()">Salva</button>
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
                    $query_squadre = "SELECT * FROM societa ORDER BY id_campionato asc,nome_societa";
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
                    $sql="SELECT * FROM `stagioni` ORDER BY anno_fine DESC, anno_inizio DESC, priorita DESC, descrizione ASC, girone ASC ";
                    $stagioni=mysqli_query($con,$sql);
                    while ($stagione = mysqli_fetch_assoc($stagioni)) {
                      echo "<option value='{$stagione['id_stagione']}'>{$stagione['descrizione']} {$stagione['anno_inizio']} - {$stagione['anno_fine']}  </option>";
                    }
                  ?>
                  </select>
                </div>
              </div>
              
              
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-primary" onclick="submitInsertForm()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function showEditModal(id, squadraCasa, squadraOspite, golCasa, golOspiti, data, giornata) {
        document.getElementById("id").value = id;
        document.getElementById("golCasa").value = golCasa
        document.getElementById("golOspiti").value = golOspiti
        document.getElementById("data").value = data;
        document.getElementById("giornata").value = giornata;
        document.getElementById("squadraCasaEdit").value = squadraCasa;
        document.getElementById("squadraOspiteEdit").value = squadraOspite;


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