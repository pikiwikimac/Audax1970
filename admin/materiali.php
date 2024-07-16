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
if ($superuser === 0) {
  header('Location: ../error_page/access_denied.php');
  exit;
}

// Query per ottenere la lista dei materiali
$queryMateriali = "SELECT nome_materiale FROM materiali WHERE id_stagione = 1";
$resultMateriali = mysqli_query($con, $queryMateriali);

$materiali_array = [];
while ($mat = mysqli_fetch_assoc($resultMateriali)) {
    $materiali_array[] = $mat['nome_materiale'];
}

// Genera la parte dinamica della query con backtick per i nomi dei campi
$materiali_query_parts = array_map(function ($mat) {
    return "MAX(IF(m.nome_materiale = '$mat', 1, NULL)) AS `possiede_" . str_replace(" ", "_", $mat) . "`";
}, $materiali_array);

$queryGiocatori = "
    SELECT g.*, 
        " . implode(", ", $materiali_query_parts) . "
    FROM giocatori g
    LEFT JOIN (
        SELECT id_giocatore, nome_materiale
        FROM giocatori_materiali gm
        JOIN materiali m ON gm.id_materiale = m.id
        WHERE m.id_stagione = 1
    ) m ON g.id = m.id_giocatore
    WHERE g.id_squadra = 1
    GROUP BY g.id
    ORDER BY g.ruolo, g.cognome, g.nome ASC
";

$resultGiocatori = mysqli_query($con, $queryGiocatori);

if (!$resultGiocatori) {
    die("Errore nella query: " . mysqli_error($con));
}

// Processa i risultati
// ...
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
                          Materiale
                        </h1>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <button class="btn btn-outline-dark float-end" data-bs-toggle="offcanvas"   href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                            Gestione
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">

                      <div class="row mb-3">
                        <div class="col-12 table-responsive">
                          <table class="table table-hover table-striped table-rounded sortable" id="tabella-giocatori">

                            

                            <thead class="table-dark">

                            <tr>
                              <th>Nome</th>
                              <th>Taglia</th>
                              <?php foreach ($materiali_array as $mat) { ?>
                                <th><?php echo $mat; ?></th>
                              <?php } ?>
                            </tr>


                            </thead>

                            <tbody>

                            <?php while ($row = mysqli_fetch_assoc($resultGiocatori)) {  ?>
                                <tr class="align-middle">
                                    <!-- Nome e Cognome -->
                                    <td onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" class="text-nowrap">
                                      <?php echo $row['cognome'] . ' ' . $row['nome'] ?>
                                    </td>
                                    <td>
                                      <?php echo $row['taglia'] ?>
                                    </td>
                                    <?php foreach ($materiali_array as $mat) { ?>
                                      <td>
                                        <input class="form-check-input" type="checkbox" value="" id="<?php echo $mat . '-' . $row['id'] ?>" data-id="<?php echo $row['id'] ?>" <?php echo ($row['possiede_' . $mat] == 1) ? 'checked' : ''; ?>>
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
      function submitInsertForm() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("insertForm").submit();
      }
    </script>

    <!-- Modal Insert -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Nuovo materiale</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form id="insertForm" method="post" action="../query/action_insert_materiali.php">
              
              <div class="row ">
                <!-- Nome materiale -->
                <div class="col-12 mb-3">
                  <label for="nome" class="form-label">Nome materiale</label>
                  <input type="text" class="form-control" id="nome_materiale" name="nome_materiale"/>
                  <input type="hidden" class="form-control" value="1" id="id_stagione" name="id_stagione"/>
                </div>
              </div>
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-primary" onclick="submitInsertForm()">Salva</button>
          </div>
        </div>
      </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Lista materiali</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        

        <table class="table table-sm table-striped " >
          <thead>
            <tr>
              <th>Nome</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($materiali_array as $mat) { ?>
            <tr>
              <td><?php echo $mat; ?></td>
              <!-- Aggiungi il listener per l'evento click all'icona del cestino -->
              <td style="width:20px"><i class='bx bx-trash' onclick="deleteMaterial('<?php echo $mat; ?>')"></i></td>

            </tr>
          <?php } ?>  
          </tbody>
        </table>

        <a type="button" class="btn btn-sm btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
          <i class='bx bx-plus '></i>
        </a> 

      </div>
    </div>

    

    <script>
      // Aggiungi un listener per l'evento change alle checkbox
      document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
          checkbox.addEventListener('change', function() {
              // Ottieni l'ID del giocatore e il nome del materiale dalla checkbox
              var playerId = checkbox.getAttribute('data-id');
              var materialName = checkbox.getAttribute('id').split('-')[0];
              var checked = checkbox.checked ? 1 : 0;

              // Invia una richiesta AJAX al server per aggiornare il database
              var xhr = new XMLHttpRequest();
              xhr.open('POST', '../query/update_player_material.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
              xhr.onreadystatechange = function() {
                  if (xhr.readyState === 4 && xhr.status === 200) {
                      // Gestisci la risposta dal server, se necessario
                      console.log(xhr.responseText);
                  }
              };
              xhr.send('playerId=' + playerId + '&materialName=' + materialName + '&checked=' + checked);
          });
      });
  </script>

  <script>
    function deleteMaterial(materialName) {
        // Conferma con l'utente prima di procedere con l'eliminazione
        if (confirm("Sei sicuro di voler eliminare questo materiale?")) {
            // Invia una richiesta AJAX al server per eliminare il materiale
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../query/delete_material.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Gestisci la risposta dal server, se necessario
                    console.log(xhr.responseText);
                    // Ricarica la pagina o aggiorna la tabella HTML, se necessario
                    location.reload(); // Ricarica la pagina dopo l'eliminazione
                }
            };
            xhr.send('materialName=' + materialName);
        }
    }
  </script>



    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

    

  </body>
</html> 