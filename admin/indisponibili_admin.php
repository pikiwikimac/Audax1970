<?php
  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];

  $query = "
  SELECT g.nome, g.cognome, i.*
  FROM indisponibili i
  INNER JOIN giocatori g ON g.id = i.id_giocatore
  WHERE i.a_data >= CURRENT_DATE
  ORDER BY i.da_data, i.a_data;  
  ";
  $result = mysqli_query($con,$query);

  $query = "
  SELECT g.nome, g.cognome, i.*
  FROM indisponibili i
  INNER JOIN giocatori g ON g.id = i.id_giocatore
  WHERE i.a_data < CURRENT_DATE
  ORDER BY i.a_data desc,i.da_data desc ;  
  ";
  $result_old = mysqli_query($con,$query);
  $old_indisponibili_count = mysqli_num_rows($result_old);
?>

<!doctype html>
<html lang="it">


  <body>

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
                      <h1 >
                        Indisponibili
                      </h1>
                      <!-- Bottoni a destra -->
                      <div class="cta-wrapper">
                        <a type="button" class="btn btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
                          <i class='bx bx-plus '></i> 
                        </a>
                      </div>
                    </div>
                  </div>
                  <!-- END:Intestazione -->

                  <!-- Core della pagina -->
                  <div class="container-fluid">
                    <?php if ($result->num_rows >0 ){ ?>
                    <div class="row ">
                      <div class="col-12 table-responsive">
                        <h3>Attuali</h3>
                        <table class="table  table-hover table-striped table-rounded">
                          <thead class="table-dark">

                            <tr>
                              <th width="20%">Giocatore</th>
                              <th width="8%">Da</th>
                              <th width="8%">A</th>
                              <th width="10%">Motivo</th>
                              <th width="50%">Note</th>
                              <th width="2%"></th>
                              <th width="2%"></th>
                            </tr>

                          </thead>

                          <tbody class="">

                            <?php while($row = mysqli_fetch_assoc($result)) {  ?>

                              <tr>
                                <!-- Giocatore -->
                                <td class="text-nowrap">
                                  <?php echo $row['cognome'] . ' ' .$row['nome']   ?>
                                </td>

                                <!-- Da data inizio assenza -->
                                <td>
                                  <?php echo date('d/m/y',strtotime($row['da_data']));?>
                                </td>

                                <!-- A data fine assenza -->
                                <td>
                                  <?php echo date('d/m/y',strtotime($row['a_data'])); ?>
                                </td>

                                <!-- Motivo indisponbilità -->
                                <td>
                                  <?php echo $row['motivo']   ?>
                                </td>

                                <!-- Note -->
                                <td class="text-nowrap">
                                  <?php echo $row['note']   ?>
                                </td>

                                <!-- Edit -->
                                <td>
                                  <a href="#" class="text-decoration-none text-dark"  data-bs-toggle="tooltip" data-bs-title="Modifica"
                                    onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["id_giocatore"]; ?>', '<?php echo $row["motivo"]; ?>', '<?php echo $row["da_data"]; ?>', '<?php echo $row["a_data"]; ?>', '<?php echo $row["note"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                    <i class='bx bx-pencil'></i>
                                  </a>
                                </td>
                                <!-- Delete -->
                                <td>
                                  <a class="text-decoration-none text-dark" data-bs-toggle="tooltip" data-bs-title="Elimina"
                                    onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                    <i class='bx bx-trash'></i>
                                  </a>
                                </td>

                              </tr>

                            <?php } ?>

                          </tbody>
                        </table>
                      </div>
                    </div> 
                    <?php }else{ ?>
                      <span>Nessun indisponibile premi sul bottone</span>
                      <span class="fw-bold">+ add</span>
                      <span> per aggiungere un giocatore alla lista.</span>
                    <?php }?>
                    <div class="row mt-5">
                      <div class="col-12 table-responsive">
                        <h3 id="toggleOldIndisponibili" style="cursor: pointer;" >Vecchie indisponibilità <span class="badge bg-danger float-end" ><?php echo $old_indisponibili_count; ?></span></h3>
                        <table class="table  table-hover table-striped  table-rounded" id="oldIndisponibiliTable" >
                          <thead class="table-dark">

                            <tr>
                            <th width="20%">Giocatore</th>
                              <th width="8%">Da</th>
                              <th width="8%">A</th>
                              <th width="10%">Motivo</th>
                              <th width="50%">Note</th>
                              <th width="2%"></th>
                              <th width="2%"></th>
                            </tr>

                          </thead>

                          <tbody class="">

                            <?php while($row = mysqli_fetch_assoc($result_old)) {  ?>

                              <tr >
                                <!-- Giocatore -->
                                <td class="text-muted text-nowrap">
                                  <?php echo $row['cognome'] . ' ' .$row['nome']   ?>
                                </td>

                                <!-- Da data inizio assenza -->
                                <td class="text-muted">
                                  <?php echo date('d/m/y',strtotime($row['da_data']));?>
                                </td>

                                <!-- A data fine assenza -->
                                <td class="text-muted">
                                  <?php echo date('d/m/y',strtotime($row['a_data'])); ?>
                                </td>

                                <!-- Motivo indisponbilità -->
                                <td class="text-muted">
                                  <?php echo $row['motivo']   ?>
                                </td>

                                <!-- Note -->
                                <td class="text-muted text-nowrap">
                                  <?php echo $row['note']   ?>
                                </td>

                                <td>
                                  <!-- Edit -->
                                  <a href="#" class="text-decoration-none text-dark" data-bs-toggle="tooltip" data-bs-title="Modifica"
                                    onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["id_giocatore"]; ?>', '<?php echo $row["motivo"]; ?>', '<?php echo $row["da_data"]; ?>', '<?php echo $row["a_data"]; ?>', '<?php echo $row["note"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                    <i class='bx bx-pencil'></i>
                                  </a>
                                </td>
                                <td>
                                  <!-- Delete -->
                                  <a class="text-decoration-none text-dark" data-bs-toggle="tooltip" data-bs-title="Elimina"
                                  onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                    <i class='bx bx-trash'></i>
                                  </a>
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


    <!-- Modal Edit-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Modifica indisponibile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per la modifica della partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="editForm" method="post" action="../query/action_edit_indisponibile.php">
              <!-- Inserisci i campi del form per la modifica -->
              <input type="hidden" name="id" id="id" value="">
              
              <div class="row ">
                <div class="col-12 mb-3">
                  <label for="giocatore" class="form-label">Giocatore</label>
                  <select class="form-select" id="giocatore" name="giocatore">
                    <?php
                      // Query per ottenere l'elenco dei giocatori
                      $query_giocatori = "
                      SELECT g.* 
                      FROM giocatori  g
                      INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
                      WHERE ag.id_societa = '$id_societa'
                      AND ag.data_fine is NULL";
                      $result_giocatori = mysqli_query($con, $query_giocatori);
                      while ($row_giocatore = mysqli_fetch_assoc($result_giocatori)) {
                        echo "<option value='" . $row_giocatore['id'] . "'>" . $row_giocatore['nome'] . " " . $row_giocatore['cognome'] . "</option>";
                      }
                    ?>
                  </select>
                </div>
                <!-- Motivo -->
                <div class="col-12 mb-3">
                <label for="motivo" class="form-label">Motivo</label>
                  <select  class="form-select" id="motivo" name="motivo">
                    <option value="Malattia">Malattia</option>
                    <option value="Viaggio">Viaggio</option>
                    <option value="Lavoro">Lavoro</option>
                    <option value="Altro">Altro</option>
                  </select>
                </div>

                <!-- Da data -->
                <div class="col-6 mb-3">
                  <label for="da_data" class="form-label">Da </label>
                  <input type="date" class="form-control" id="da_data" name="da_data"/>
                </div>

                <!-- A data -->
                <div class="col-6 mb-3">
                <label for="a_data" class="form-label">A</label>
                  <input type="date" class="form-control" id="a_data" name="a_data"/>
                </div>

                <!-- Note -->
                <div class="col-12">
                  <label for="note" class="form-label">Note</label>
                  <textarea  class="form-control" id="note" name="note"></textarea>
                </div>
              </div>
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
            <h5 class="modal-title" id="insertModalLabel">Inserisci indisponibile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="insertForm" method="post" action="../query/action_insert_indisponibile.php">
              <div class="row">
                  <div class="col-12 mb-3">
                  <label for="giocatore" class="form-label">Giocatore</label>
                  <select class="form-select" id="giocatore" name="giocatore">
                    <?php
                      // Query per ottenere l'elenco dei giocatori
                      $query_giocatori = "SELECT g.* 
                      FROM giocatori  g
                      INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
                      WHERE ag.id_societa = '$id_societa'
                      AND ag.data_fine is NULL";
                      $result_giocatori = mysqli_query($con, $query_giocatori);
                      while ($row_giocatore = mysqli_fetch_assoc($result_giocatori)) {
                        echo "<option value='" . $row_giocatore['id'] . "'>" . $row_giocatore['nome'] . " " . $row_giocatore['cognome'] . "</option>";
                      }
                    ?>
                  </select>
                </div>
                <!-- Motivo -->
                <div class="col-12 mb-3">
                <label for="motivo" class="form-label">Motivo</label>
                  <select  class="form-select" id="motivo" name="motivo">
                    <option value="Malattia">Malattia</option>
                    <option value="Viaggio">Viaggio</option>
                    <option value="Lavoro">Lavoro</option>
                    <option value="Altro">Altro</option>
                  </select>
                </div>

                <!-- Da data -->
                <div class="col-6 mb-3">
                  <label for="da_data" class="form-label">Da </label>
                  <input type="date" class="form-control" id="da_data" name="da_data"/>
                </div>

                <!-- A data -->
                <div class="col-6 mb-3">
                <label for="a_data" class="form-label">A</label>
                  <input type="date" class="form-control" id="a_data" name="a_data"/>
                </div>

                <!-- Note -->
                <div class="col-12">
                  <label for="note" class="form-label">Note</label>
                  <textarea  class="form-control" id="note" name="note"></textarea>
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

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_indisponibile.php?id=" + recordId;
        }
      }
    </script>

    <script>
      function showEditModal(id, giocatore, motivo, da_data, a_data,note) {
        document.getElementById("id").value = id;
        document.getElementById("giocatore").value = giocatore;
        document.getElementById("motivo").value = motivo;
        document.getElementById("da_data").value = da_data;
        document.getElementById("a_data").value = a_data;
        document.getElementById("note").value = note;
        

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
    <script>
      document.addEventListener("DOMContentLoaded", function() {
      var toggleOldIndisponibili = document.getElementById("toggleOldIndisponibili");
      var oldIndisponibiliTable = document.getElementById("oldIndisponibiliTable");

      toggleOldIndisponibili.addEventListener("click", function() {
        if (oldIndisponibiliTable.style.display === "none") {
          oldIndisponibiliTable.style.display = "";
        } else {
          oldIndisponibiliTable.style.display = "none";
        }
      });
    });

    </script>
    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

  </body>

</html>