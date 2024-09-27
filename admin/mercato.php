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

  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }


  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT m.*
  FROM mercato m
  ORDER BY realizzazione DESC,cognome,nome asc;
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
                          Mercato
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a type="button" class="btn btn-sm btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
                            <i class="bi bi-plus"></i>
                          </a>
                        </div>
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
                                <th width="35%">Nome</th>
                                <th width="20%" class="text-center">%</th>
                                <th width="41%">Note</th>
                                <th width="2%"></th>
                                <th width="2%"></th>
                                
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                              <tr >
                                
                                <!-- Nome e Cognome -->
                                <td width="35%" class="text-nowrap">
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
                                    &nbsp; 
                                    <?php echo $row['cognome'] .' '. $row['nome']?>
                                </td>

                                <!-- Realizzazione -->
                                <td class="text-center" width="20%">
                                  <div class="progress me-5">
                                    <div class="progress-bar" role="progressbar" style="width:<?php echo intval($row['realizzazione']);?>%"
                                    aria-valuenow="<?php echo intval($row['realizzazione']); ?>"
                                    aria-valuemin="0" aria-valuemax="100"><?php echo $row['realizzazione'] ?> %</div>
                                  </div>
                                </td>
                                
                                <!-- Note -->
                                <td  width="41%">
                                  <?php echo $row['note'] ?>
                                </td>
                                <!-- Bottone edit-->
                                <td width="2%">
                                  <a class="text-decoration-none text-dark" onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["nome"]; ?>', '<?php echo $row["cognome"]; ?>', '<?php echo $row["realizzazione"]; ?>', '<?php echo $row["note"]; ?>', '<?php echo $row["ruolo"]; ?>')" >
                                    <i class='bi bi-pencil align-middle '></i>
                                  </a>
                                </td>
                                <!-- Bottone delete -->
                                <td width="2%">
                                  <a class="text-decoration-none text-dark" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                  <i class='bi bi-trash-alt align-middle '></i>
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
            <h5 class="modal-title" id="editModalLabel">Modifica giocatore per il mercato</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form id="editForm" method="post" action="../query/action_edit_mercato.php">
              <!-- Inserisci i campi del form per la modifica -->
              <input type="hidden" name="id" id="id" value="">
              
              <div class="row ">
                <!-- Nome -->
                <div class="col-6 mb-3">
                  <label for="nome" class="form-label">Nome</label>
                  <input type="text" class="form-control" id="nome" name="nome"/>
                </div>

                <!-- Cognome -->
                <div class="col-6 mb-3 ">
                  <label for="cognome" class="form-label">Cognome</label>
                  <input type="text" class="form-control" id="cognome" name="cognome"/>
                </div>

                <!-- Realizzazione -->
                <div class="col-6 mb-3 ">
                  <label for="realizzazione" class="form-label">Realizzazione</label>
                  <input type="number" class="form-control" id="realizzazione" name="realizzazione"/>
                </div>

                <!-- Ruolo -->
                <div class="col-6 mb-3 ">
                  <label for="ruolo" class="form-label">Ruolo</label>
                  <select class="form-select" id="ruolo" name="ruolo">
                      <option value="Portiere">Portiere</option>
                      <option value="Centrale">Centrale</option>
                      <option value="Laterale">Laterale</option>
                      <option value="Universale">Universale</option>
                      <option value="Pivot">Pivot</option>
                  </select>
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
            <h5 class="modal-title" id="insertModalLabel">Inserisci giocatore per il mercato</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form id="insertForm" method="post" action="../query/action_insert_mercato.php">
              
              <div class="row ">
                <!-- Nome -->
                <div class="col-6 mb-3">
                  <label for="nome" class="form-label">Nome</label>
                  <input type="text" class="form-control" id="nome" name="nome"/>
                </div>

                <!-- Cognome -->
                <div class="col-6 mb-3 ">
                  <label for="cognome" class="form-label">Cognome</label>
                  <input type="text" class="form-control" id="cognome" name="cognome"/>
                </div>

                <!-- Realizzazione -->
                <div class="col-6 mb-3 ">
                  <label for="realizzazione" class="form-label">Realizzazione</label>
                  <input type="number" class="form-control" id="realizzazione" name="realizzazione"/>
                </div>
                
                <!-- Ruolo -->
                <div class="col-6 mb-3 ">
                  <label for="ruolo" class="form-label">Ruolo</label>
                  <select class="form-select" id="ruolo" name="ruolo">
                      <option value="Portiere">Portiere</option>
                      <option value="Centrale">Centrale</option>
                      <option value="Laterale">Laterale</option>
                      <option value="Universale">Universale</option>
                      <option value="Pivot">Pivot</option>
                  </select>
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
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitInsertForm()">Salva</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/delete_mercato.php?id=" + recordId;
        }
      }
    </script>
    <!-- Funzione Edit modal -->
    <script>
      function showEditModal(id, nome, cognome, realizzazione, ruolo, note) {
        
        document.getElementById("id").value = id;
        document.getElementById("nome").value = nome;
        document.getElementById("cognome").value = cognome;
        document.getElementById("realizzazione").value = realizzazione;
        document.getElementById("ruolo").value = ruolo;
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
    
    
    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>