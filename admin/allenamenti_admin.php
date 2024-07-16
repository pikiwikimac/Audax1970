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

  $query = "SELECT * FROM `allenamenti` WHERE stato='Fissato' ORDER BY data desc";
  $query2 = "SELECT * FROM `allenamenti` WHERE stato!='Fissato' ORDER BY data desc";

  $allenamentiProssimi = mysqli_query($con,$query);
  $allenamentiSvolti = mysqli_query($con,$query2);

  $id_societa=$_REQUEST['$id_societa'];

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
                        <h1 > 
                          Allenamenti
                        </h1>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <?php if($_SESSION['superuser'] === 1 ){ ?>
                          <a type="button" class="btn btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
                            <i class='bx bx-plus '></i>
                          </a>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->
                    
                    <!-- Core della pagina -->
                    <div class="">

                      <!-- Allenamenti fissati -->
                      <div class="row g-5 mb-3 ">
                        <div class="col-12 col-lg-6 table-responsive">
                          <?php if ($allenamentiProssimi->num_rows >0 ){ ?>
                          <table class="table table-sm table-hover table-striped table-rounded">
                            <thead class="table-dark ">

                              <tr>
                                <th class="text-center" width="10%">Giorno</th>
                                <th width="">Tipo</th>
                                <th width="40%">Note</th>
                                <th  class="text-center" width="5%">Stato</th>
                                <th width="1%"></th>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <th width="1%"></th>
                                <th width="1%"></th>
                                <?php } ?>
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($allenamentiProssimi)) {  ?>

                                <tr class="">
                                  <td class="text-center">
                                    <!-- Data -->
                                    <small class="fw-semibold">
                                      <?php echo date('d/m/y',strtotime($row['data']));?>
                                    </small>
                                    <br/>
                                    <!-- Giorno della settimana -->
                                    <small class="text-muted">
                                      <?php 
                                        setlocale(LC_TIME, 'it_IT.utf8');
                                        $dayOfWeek = strftime('%A', strtotime($row['data']));
                                        $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                        echo $abbreviatedDay;
                                      ?>
                                      <!-- Orario -->
                                      <?php echo  date('H:i', strtotime($row['orario'])); ?>
                                    </small>
                                  </td>
                                  
                                  
                                  <!-- Tipologia -->
                                  <td>
                                    <strong>
                                      <?php echo $row['tipologia'] ?>
                                    </strong>
                                    <br/>
                                    <small class="text-muted">
                                      <i class='bx bx-map'></i> &nbsp; <?php echo $row['luogo'] ?>
                                    </small>
                                  </td>

                                  
                                  <!-- Note -->
                                  <td class="align-middle">
                                    <?php echo $row['note'] ?>
                                  </td>
                                  
                                  <!-- Stato -->
                                  <td class="text-center align-middle">
                                    <?php switch($row['stato']){
                                      case 'Fissato':  
                                        echo "<span class='badge bg-secondary bg-opacity-25 border border-2 border-opacity-50 text-secondary border-secondary'>F</span>";
                                        break;
                                      case 'Svolto':  
                                        echo "<span class='badge bg-success bg-opacity-25 border border-2 border-opacity-50 text-success border-success'>S</span>";
                                        break;
                                      case 'Rimandato':  
                                        echo "<span class='badge bg-warning bg-opacity-25 border border-2 border-opacity-50 text-warning border-warning'>R</span>";
                                        break;
                                      case 'Cancellato':
                                        echo "<span class='badge bg-danger bg-opacity-25 border border-2 border-opacity-50 text-danger border-danger'>C</span>";
                                        break;
                                      }?>
                                      
                                  </td>
                                  <td class="align-middle">
                                    <!-- Gestione presenze -->
                                    <a class="text-decoration-none text-dark" data-bs-toggle="tooltip" data-bs-title="Gestione presenti" 
                                      href="edit_presenza_allenamento.php?id=<?php echo $row['id'] ?>">
                                      <i class='bx bx-user'></i>
                                    </a>
                                  </td>

                                  <?php if($_SESSION['superuser'] == 1 ){ ?>

                                    <td class="align-middle">
                                      <!-- Edit -->
                                      <a  class="text-decoration-none text-dark" style="cursor:pointer" 
                                        data-bs-toggle="tooltip" data-bs-title="Modifica"
                                        onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["data"]; ?>', '<?php echo $row["orario"]; ?>', '<?php echo $row["tipologia"]; ?>', '<?php echo $row["stato"]; ?>', '<?php echo $row["luogo"]; ?>', '<?php echo $row["note"]; ?>')" >
                                        <i class='bx bx-pencil'></i>
                                      </a>

                                    </td>
                                  
                                    <td class="align-middle">
                                      <!-- Delete -->
                                      <a class="text-decoration-none text-dark" style="cursor:pointer"
                                        data-bs-toggle="tooltip" data-bs-title="Elimina"  onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                        <i class='bx bx-trash'></i>
                                      </a> 
                                    </td>
                                  <?php } ?>

                                </tr>
                              <?php } ?>

                            </tbody>

                          </table>

                          <!-- Messaggio : Nessun allenamento programmato -->
                          <?php }else{ ?>
                            <h3>Nessun allenamento programmato</h3>
                          <?php } ?>

                        </div>

                        <!-- Allenamenti != Fissati -->
                        <div class="col-12 col-lg-6 table-responsive">
                          <?php if ($allenamentiSvolti->num_rows >0 ){ ?>
                          <table class="table table-sm table-hover table-striped table-rounded">
                            <thead class="table-dark ">

                              <tr>
                                <th class="text-center" width="10%">Giorno</th>
                                <th width="">Tipo</th>
                                <th width="40%">Note</th>
                                <th  class="text-center" width="5%">Stato</th>
                                <th width="1%"></th>
                                <?php if($_SESSION['superuser'] === 1 ){ ?>
                                <th width="1%"></th>
                                <th width="1%"></th>
                                <?php } ?>
                              </tr>

                            </thead>

                            <tbody id="allenamentiSvoltiTbody">

                              <?php while($row = mysqli_fetch_assoc($allenamentiSvolti)) {  ?>

                                <tr class="allenamentoRow">
                                  
                                  <td class="text-center">
                                    <!-- Data -->
                                    <small class="fw-semibold">
                                      <?php echo date('d/m/y',strtotime($row['data']));?>
                                    </small>
                                    <br/>
                                    <small class="text-muted">
                                      <!-- Giorno della settimana -->
                                      <?php 
                                        setlocale(LC_TIME, 'it_IT.utf8');
                                        $dayOfWeek = strftime('%A', strtotime($row['data']));
                                        $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                        echo $abbreviatedDay;
                                      ?>
                                      <!-- Orario -->
                                      <?php echo  date('H:i', strtotime($row['orario'])); ?>
                                    </small>
                                  </td>
                                  
                                  <td>
                                    <!-- Tipologia -->
                                    <div  class="mb-0">
                                      <strong>
                                        <?php echo $row['tipologia'] ?>
                                      </strong>
                                    </div>
                                    <!-- Luogo -->
                                    <div class="mt-0">
                                      <span class="text-muted" style="font-size:12px" >
                                        <i class='bx bx-map'></i> <?php echo $row['luogo'] ?>
                                      </span>
                                    </div>
                                  </td>

                                
                                  <!-- Note -->
                                  <td class="align-middle">
                                    <?php echo $row['note'] ?>
                                  </td>

                                  <!-- Stato -->
                                  <td class="text-center align-middle">
                                    <?php switch($row['stato']){
                                      case 'Fissato':  
                                        echo "<span class='badge bg-secondary bg-opacity-25 border border-2 border-opacity-50 text-secondary border-secondary'>F</span>";
                                        break;
                                      case 'Svolto':  
                                        echo "<span class='badge bg-success bg-opacity-25 border border-2 border-opacity-50 text-success border-success'>S</span>";
                                        break;
                                      case 'Rimandato':  
                                        echo "<span class='badge bg-warning bg-opacity-25 border border-2 border-opacity-50 text-warning border-warning'>R</span>";
                                        break;
                                      case 'Cancellato':
                                        echo "<span class='badge bg-danger bg-opacity-25 border border-2 border-opacity-50 text-danger border-danger'>C</span>";
                                        break;
                                    }?>
                                      
                                  </td>
                                  
                                  
                                  <td class="align-middle">
                                    <!-- Gestione presenze -->
                                    <a class="text-decoration-none text-dark" data-bs-toggle="tooltip" data-bs-title="Gestione presenti"
                                      href="edit_presenza_allenamento.php?id=<?php echo $row['id'] ?>">
                                      <i class='bx bx-user'></i>
                                    </a>
                                  </td>

                                  <?php if($_SESSION['superuser'] == 1 ){ ?>
                                  <td class="align-middle">
                                    <!-- Edit -->
                                    <a  class="text-decoration-none text-dark" style="cursor:pointer" 
                                      data-bs-toggle="tooltip" data-bs-title="Modifica"
                                      onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["data"]; ?>', '<?php echo $row["orario"]; ?>', '<?php echo $row["tipologia"]; ?>', '<?php echo $row["stato"]; ?>', '<?php echo $row["luogo"]; ?>', '<?php echo $row["note"]; ?>')" >
                                      <i class='bx bx-pencil'></i>
                                    </a>

                                  </td>

                                    <td class="align-middle">
                                      <!-- Delete -->
                                      <a class="text-decoration-none text-dark" style="cursor:pointer" 
                                        data-bs-toggle="tooltip" data-bs-title="Elimina"onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                        <i class='bx bx-trash'></i>
                                      </a> 
                                    </td>
                                  <?php } ?>

                                </tr>
                              <?php } ?>

                            </tbody>

                          </table>
                          <!-- Pulsante per mostrare tutti gli allenamenti -->
                          <button id="showMoreButton" data-expanded="false" class="btn btn-outline-dark float-end mt-3">Mostra tutti</button>

                          <!-- Messaggio: Nessun allenamento programmato -->
                          <?php }else{ ?>
                            <h3>Nessun allenamento svolto</h3>
                          <?php } ?>

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
            <h5 class="modal-title" id="editModalLabel">Modifica Allenamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per la modifica della partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="editForm" method="post" action="../query/action_edit_allenamento.php">
              <!-- Inserisci i campi del form per la modifica -->
              <input type="hidden" name="id" id="id" value="">
              
              <div class="row ">
                <!-- Data -->
                <div class="col-6 mb-3">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data" name="data"/>
                </div>

                <!-- Orario -->
                <div class="col-6 mb-3 ">
                  <label for="orario" class="form-label">Orario</label>
                  <input type="time" class="form-control" id="orario" name="orario"/>
                </div>

                <!-- Tipologia -->
                <div class="col-12 mb-3 ">
                  <label for="tipologia" class="form-label">Tipologia</label>
                  <select  class="form-select" id="tipologia" name="tipologia">
                    <option value="Allenamento">Allenamento</option>
                    <option value="Amichevole">Amichevole</option>
                  </select>
                </div>

                <!-- Stato -->
                <div class="col-12 mb-3 ">
                  <label for="stato" class="form-label">Stato</label>
                  <select  class="form-select" id="stato" name="stato">
                    <option value="Fissato">Fissato</option>
                    <option value="Svolto">Svolto</option>
                    <option value="Cancellato">Cancellato</option>
                    <option value="Rimandato">Rimandato</option>
                  </select>
                </div>

                <!-- Squadra casa -->
                <div class="col-12 mb-3 ">
                  <label for="luogo" class="form-label">Luogo</label>
                  <input type="text" class="form-control" id="luogo" name="luogo" />
                </div>

                <!-- Note -->
                <div class="col-12 mb-3 ">
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
            <h5 class="modal-title" id="insertModalLabel">Inserisci Allenamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="insertForm" method="post" action="../query/action_insert_allenamento.php">
              <div class="row">
                <!-- Data -->
                <div class="col-6 mb-3 ">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data" name="data"/>
                </div>

                <!-- orario -->
                <div class="col-6 mb-3">
                  <label for="orario" class="form-label">Orario</label>
                  <input type="time" class="form-control" id="orario" name="orario" value="21:00"/>
                </div>

                <!-- Tipologia -->
                <div class="col-12 mb-3">
                  <label for="tipologia" class="form-label">Tipologia</label>
                  <select  class="form-select" id="tipologia" name="tipologia">
                    <option value="Allenamento">Allenamento</option>
                    <option value="Amichevole">Amichevole</option>
                  </select>
                </div>

                <!-- Stato -->
                <div class="col-12 mb-3">
                  <label for="stato" class="form-label">Stato</label>
                  <select  class="form-select" id="stato" name="stato">
                    <option value="Fissato">Fissato</option>
                    <option value="Svolto">Svolto</option>
                    <option value="Cancellato">Cancellato</option>
                    <option value="Rimandato">Rimandato</option>
                  </select>
                </div>

                <!-- Squadra casa -->
                <div class="col-12 mb-3">
                  <label for="luogo" class="form-label">Luogo</label>
                  <input type="text" class="form-control" id="luogo" name="luogo" value="Palazzetto dello sport"/>
                </div>

                <!-- Note -->
                <div class="col-12 mb-3">
                  <label for="note" class="form-label">Note</label>
                  <textarea  class="form-control" id="note" name="note" rows="3"></textarea>
                </div>
              </div>
              <input type="hidden" value="<?php echo $id_societa ?>" name="id_societa" id="id_societa"/>
              <!-- Aggiungi altri campi per l'inserimento se necessario -->
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
    
    <!-- Cancellazione -->
    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_allenamento.php?id=" + recordId;
        }
      }
    </script>

    <!-- Funzione Edit modal -->
    <script>
      function showEditModal(id, data, orario, tipologia, stato, luogo, note) {
        document.getElementById("id").value = id;
        document.getElementById("data").value = data;
        document.getElementById("orario").value = orario;
        document.getElementById("tipologia").value = tipologia;
        document.getElementById("stato").value = stato;
        document.getElementById("luogo").value = luogo;
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
      var rows = document.querySelectorAll("#allenamentiSvoltiTbody .allenamentoRow");
      var showMoreButton = document.getElementById("showMoreButton");

      function toggleRows() {
        var isExpanded = showMoreButton.getAttribute("data-expanded") === "true";

        rows.forEach((row, index) => {
          if (index >= 10) {
            row.style.display = isExpanded ? "none" : "";
          }
        });

        showMoreButton.textContent = isExpanded ? "Mostra tutti" : "Mostra meno";
        showMoreButton.setAttribute("data-expanded", !isExpanded);
      }

      // Initial state: show only 10 rows
      rows.forEach((row, index) => {
        if (index >= 10) {
          row.style.display = "none";
        }
      });

      showMoreButton.addEventListener("click", toggleRows);
    });

    </script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

  </body>

</html>