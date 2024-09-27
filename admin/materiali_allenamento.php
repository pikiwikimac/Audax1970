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
  FROM materiale_allenamento m
  ORDER BY nome_materiale asc;
  ";
  $materiali = mysqli_query($con,$query);
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
                          Materiali Allenamento
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
                                <th width="36%">Nome</th>
                                <th class="text-center" width="20%">Costo unitario</th>
                                <th class="text-center" width="20%">Quantità</th>
                                <th class="text-center" width="20%">Costo totale</th>
                                <th width="2%"></th>
                                <th width="2%"></th>
                                
                              </tr>

                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($materiali)) {  ?>
                              <tr >
                                
                                <!-- Nome -->
                                <td class="text-nowrap">
                                  <?php echo $row['nome_materiale'] ?>
                                </td>

                                <!-- Costo unitario -->
                                <td class="text-center">
                                <?php echo $row['costo'].' €' ?>
                                </td>
                                
                                <!-- Quantità -->
                                <td  class="text-center">
                                  <?php echo $row['quantita'] ?>
                                </td>
                                <?php
                                  // Recupera i valori dal row
                                  $costo_unitario = isset($row['costo']) ? $row['costo'] : 0;
                                  $quantita = isset($row['quantita']) ? $row['quantita'] : 0;

                                  // Inizializza il costo totale
                                  $costo_totale = 0;

                                  // Verifica che entrambi i valori siano non nulli e maggiori di 0
                                  if ($costo_unitario > 0 && $quantita > 0) {
                                      // Calcola il costo totale
                                      $costo_totale = $costo_unitario * $quantita;
                                  }

                                  // Mostra i risultati
                                ?>
                                <!-- Costo totale -->
                                <td class="text-center">
                                  <?php echo htmlspecialchars($costo_totale) .' €'; ?>
                                </td>

                                <!-- Bottone edit-->
                                <td width="2%">
                                  <a class="text-decoration-none text-dark" onclick="showEditModal('<?php echo $row["id"]; ?>', '<?php echo $row["nome_materiale"]; ?>', '<?php echo $row["costo"]; ?>', '<?php echo $row["quantita"]; ?>')" >
                                    <i class='bi bi-pencil align-middle '></i>
                                  </a>
                                </td>

                                <!-- Bottone delete -->
                                <td width="2%">
                                  <a class="text-decoration-none text-dark" onclick="deleteMaterial('<?php echo $row["id"]; ?>')">
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

    
    <!-- Modal Insert -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Nuovo materiale allenamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form id="insertForm" method="post" action="../query/action_insert_materiali_allenamenti.php">
              
              <div class="row g-3">
                <!-- Nome materiale -->
                <div class="col-12">
                  <label for="nome" class="form-label">Nome materiale</label>
                  <input type="text" class="form-control" id="nome_materiale" name="nome_materiale"/>
                  <input type="hidden" class="form-control" value="1" id="id_stagione" name="id_stagione"/>
                </div>

                <div class="col-6">
                  <label for="costo_unitario" class="form-label">Costo unitario</label>
                  <input type="number" class="form-control" id="costo_unitario" name="costo_unitario"/>
                </div>
                <div class="col-6">
                  <label for="quantita" class="form-label">Quantità</label>
                  <input type="number" class="form-control" id="quantita" name="quantita"/>
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

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Modifica materiale allenamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editForm" method="post" action="../query/action_edit_materiale_allenamento.php">
              <input type="hidden" id="edit_id_materiale" name="id_materiale" />
              <div class="row g-3">
                <!-- Nome materiale -->
                <div class="col-12">
                  <label for="edit_nome_materiale" class="form-label">Nome materiale</label>
                  <input type="text" class="form-control" id="edit_nome_materiale" name="nome_materiale" />
                </div>
                <div class="col-6">
                  <label for="edit_costo_unitario" class="form-label">Costo unitario</label>
                  <input type="number" class="form-control" id="edit_costo_unitario" name="costo_unitario" />
                </div>
                <div class="col-6">
                  <label for="edit_quantita" class="form-label">Quantità</label>
                  <input type="number" class="form-control" id="edit_quantita" name="quantita" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitEditForm()">Salva modifiche</button>
          </div>
        </div>
      </div>
    </div>

    
    

    
    
    <script>
      function submitInsertForm() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("insertForm").submit();
      }

      function showEditModal(id, nome, costo, quantita) {
        // Precompila i campi del form con i valori correnti
        document.getElementById('edit_id_materiale').value = id;
        document.getElementById('edit_nome_materiale').value = nome;
        document.getElementById('edit_costo_unitario').value = costo;
        document.getElementById('edit_quantita').value = quantita;

        // Mostra il modal di modifica
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
      }

      function submitEditForm() {
        document.getElementById('editForm').submit();
      }

      function deleteMaterial(idMaterial) {
          // Conferma con l'utente prima di procedere con l'eliminazione
          if (confirm("Sei sicuro di voler eliminare questo materiale?")) {
              // Invia una richiesta AJAX al server per eliminare il materiale
              var xhr = new XMLHttpRequest();
              xhr.open('POST', '../query/delete_material_allenamento.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
              xhr.onreadystatechange = function() {
                  if (xhr.readyState === 4 && xhr.status === 200) {
                      // Gestisci la risposta dal server, se necessario
                      console.log(xhr.responseText);
                      // Ricarica la pagina o aggiorna la tabella HTML, se necessario
                      location.reload(); // Ricarica la pagina dopo l'eliminazione
                  }
              };
              xhr.send('idMaterial=' + idMaterial);
          }
      }

    </script>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>