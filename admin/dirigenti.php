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
    SELECT d.*
    FROM dirigenti d
    ORDER BY CAST(d.ordinamento AS UNSIGNED) ASC, nome;
    ";
  $result = mysqli_query($con,$query);

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
                          Dirigenti
                        </h3>
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
                    <div class="">
                      <div class="row">
                        <div class="col-12 table-responsive">
                          <table class="table table-sm table-hover table-striped table-rounded">
                            <thead class="table-dark">
                              <tr>

                                <th width="5%"></th>
                                <th width="23%">Nome</th>
                                <th width="20%">Data</th>
                                <th width="20%">Ruolo</th>
                                <th width="5%">Ordinamento</th>
                                <th width="25%">Documento</th>
                                <th width="1%"></th>
                                <th width="1%"></th>

                              </tr>
                            </thead>

                            <tbody class="">

                              <?php while($row = mysqli_fetch_assoc($result)) {  ?>

                                <tr class="align-middle">
                                  <td class="text-center">
                                    <?php if ($row['image_path']) { ?>
                                      <img src="../image/staff/<?php echo $row['image_path']; ?>" class="rounded-circle" alt="Immagine Dirigente" width="30" height="30" />
                                    <?php } else { ?>
                                      <img src="../image/default_user.jpg" class="rounded-circle" alt="Immagine di default" width="30" height="30" />
                                    <?php } ?>
                                  </td>
                                  <!-- Dirigente -->
                                  <td class="text-nowrap">
                                    <?php echo $row['nome']   ?>
                                  </td>

                                  <!-- Data nascita -->
                                  <td>
                                    <?php if($row['data_nascita']=== NULL){
                                      echo '-';
                                    }else{
                                      echo date('d/m/y',strtotime($row['data_nascita']));
                                    } ?>
                                  </td>

                                  <!-- Ruolo all'intenrno della società -->

                                  <td>
                                    <?php echo $row['ruolo']   ?>
                                  </td>
                                  
                                  <!-- Ruolo all'intenrno della società -->
                                  <td>
                                    <?php echo $row['ordinamento']   ?>
                                  </td>

                                  <!-- Numnero documento  -->
                                  <td>
                                    <?php echo $row['documento']   ?>
                                  </td>

                                  <td>
                                    <!-- Edit -->
                                    <a href="#" class="text-decoration-none text-dark" 
                                      onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["nome"]; ?>', '<?php echo $row["data_nascita"]; ?>', '<?php echo $row["ruolo"]; ?>','<?php echo $row["ordinamento"]; ?>', '<?php echo $row["documento"]; ?>')" data-bs-toggle="tooltip" data-bs-title="Modifica">
                                      <i class='bx bx-pencil'></i>
                                    </a>
                                  </td>
                                  <td>
                                    <!-- Delete -->
                                    <a class="text-decoration-none text-dark" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
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
      </div>

    </main>

    <!-- Modal Edit-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Modifica Dirigente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per la modifica della partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="editForm" method="post" action="../query/action_edit_dirigente.php">
              <!-- Inserisci i campi del form per la modifica -->
              <input type="hidden" name="id" id="id" value="">
              
              <div class="row ">
                <!-- Nome -->
                <div class="col-12 mb-3">
                  <label for="nome" class="form-label">Nome</label>
                  <input typer="text" class="form-control" id="nome" name="nome" value="" required/>
                </div> 

                <!-- Ruolo -->
                <div class="col-12 col-lg-8 mb-3">
                  <label for="ruolo" class="form-label">Ruolo</label>
                  <input typer="text" class="form-control" id="ruolo" name="ruolo" value="" required/>
                </div>
                
                <!-- Ordinamento -->
                <div class="col-12 col-lg-4 mb-3">
                  <label for="ordinamento" class="form-label">Ordinamento</label>
                  <input typer="text" class="form-control" id="ordinamento" name="ordinamento" value="" />
                </div>

                <!-- Data di nascita -->
                <div class="col-6 mb-3">
                  <label for="data_nascita" class="form-label">Data di nascita</label>
                  <input type="date" class="form-control" id="data_nascita" name="data_nascita" value="" />
                </div>

                <!-- Documento -->
                <div class="col-6 mb-3">
                  <label for="documento" class="form-label">Documento</label>
                  <input type="text" class="form-control" id="documento" name="documento" value="" />
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
            <h5 class="modal-title" id="insertModalLabel">Inserisci Dirigente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="insertForm" method="post" action="../query/action_insert_dirigente.php">
              <div class="row">
                <!-- Nome -->
                <div class="col-12 mb-3">
                  <label for="nome" class="form-label">Nome</label>
                  <input typer="text" class="form-control" id="nome" name="nome" value="" required/>
                </div> 

                <!-- Ruolo -->
                <div class="col-12 mb-3">
                  <label for="ruolo" class="form-label">Ruolo</label>
                  <input typer="text" class="form-control" id="ruolo" name="ruolo" value="" required/>
                </div>

                <!-- Data di nascita -->
                <div class="col-6 mb-3">
                  <label for="data_nascita" class="form-label">Data di nascita</label>
                  <input type="date" class="form-control" id="data_nascita" name="data_nascita" value="" />
                </div>

                <!-- Documento -->
                <div class="col-6 mb-3">
                  <label for="documento" class="form-label">Documento</label>
                  <input type="text" class="form-control" id="documento" name="documento" value="" />
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
          window.location.href = "../query/action_delete_dirigente.php?id=" + recordId;
        }
      }
    </script>


    <script>
      function showEditModal(id, nome, data_nascita, ruolo, ordinamento, documento) {
        document.getElementById("id").value = id;
        document.getElementById("nome").value = nome;
        document.getElementById("data_nascita").value = data_nascita;
        document.getElementById("ruolo").value = ruolo;
        document.getElementById("ordinamento").value = ordinamento;
        document.getElementById("documento").value = documento;
        

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

  </body>

</html>