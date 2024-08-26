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
  
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  
  $id=$_REQUEST['id'];

  $query =
    "
    SELECT *
    FROM articoli
    WHERE id='$id'
    ";
  $articolo = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($articolo);


  $sql = "SELECT * FROM `articoli_intestazioni` ORDER BY id ";
  $result = mysqli_query($con, $sql);

  // Salva i risultati della query in un array
  $intestazioni = [];
  while ($row2 = mysqli_fetch_assoc($result)) {
    $intestazioni[] = $row2;
  }
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
                          Modifica articolo  
                        </h3>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <button class="btn btn-outline-dark float-end" data-bs-toggle="offcanvas"   href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                            <i class='bx bx-plus'></i>
                          </button>	
                          <a type="button" href="articoli.php" class="btn btn-outline-dark float-end me-2">
                            <i class='bx bx-arrow-back'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                      
                      <div class="row gy-4 mb-3 ">
                        <!-- Inserimento immagine articolo -->
                        <div class="col-12 col-lg-2">
                          <div class="card">
                            <?php if (!empty($row['immagine_url'])) : ?>
                              <img src="../image/articoli/<?php echo $row['immagine_url']; ?>" class="img-fluid rounded" alt="Immagine attuale">
                            <?php else : ?>
                              <img src="../image/default.jpeg" class="img-fluid rounded" alt="Immagine di default grigia">
                            <?php endif; ?>
                          </div>

                          <form action="../query/upload_image_articolo.php" method="post" enctype="multipart/form-data" class="mt-3">
                            
                            <label for="formFile" class="form-label">Immagine dell'articolo</label>
                            <input class="form-control form-control-md" type="file" id="formFile" name="playerImage">
                            <input type="hidden"  id="id" name="id" value="<?php echo $id?>" />
                            <input type="submit" value="Carica" class="btn btn-outline-dark float-end mt-3" name="submit">
                            
                          </form>
                        </div>

                        <div class="col-12 col-lg-10">
                          <form action="../query/action_edit_articolo.php" method="POST" enctype="multipart/form-data">
                            <div class="card">
                              <div class="card-body">
                                <h3>Info di base</h3>
                                
                                <div class="row my-3 g-3">
                                  <!-- Titolo -->
                                  <div class="col-12 col-sm-6 col-lg-12">
                                    <label for="titolo" class="form-label">Titolo</label>
                                    <input typer="text" class="form-control" id="titolo" name="titolo" required value="<?php echo $row['titolo'] ?>"></input>
                                  </div>


                                  <!-- Data pubblicazione -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="data_pubblicazione" class="form-label">Data pubblicazione</label>
                                    <input type="datetime" class="form-control" id="data_pubblicazione" name="data_pubblicazione" value="<?php echo $row['data_pubblicazione'] ?>"></input>
                                  </div>

                                  <!-- Squadra -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="autore" class="form-label">Autore</label>
                                    <input type="text" class="form-control" id="autore" name="autore" value="<?php echo $row['autore'] ?>"></input>
                                  </div>

                                  <!-- Stagione -->
                                  <div class="col-6 col-sm-6 col-lg-2 ">
                                    <label for="intestazione" class="form-label">Intestazione</label>
                                    <select class="form-select" id="intestazione" name="intestazione">
                                      <!-- Opzioni per la squadra ospite -->
                                      <?php
                                        $sql = "SELECT * FROM `articoli_intestazioni` ORDER BY id ";
                                        $intestazioni2 = mysqli_query($con, $sql);
                                        while ($intestazione = mysqli_fetch_assoc($intestazioni2)) {
                                          $selected = ($intestazione['id'] == $row['id_intestazione']) ? "selected" : "";
                                          echo "<option value='{$intestazione['id']}' $selected>{$intestazione['descrizione']} </option>";
                                        }
                                      ?>
                                    </select>
                                  </div>

                                  

                                  <!-- Tags -->
                                  <div class="col-12 col-sm-6 col-lg-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo $row['tags'] ?>"></input>
                                  </div>
                                    
                                  <!-- Contenuto -->
                                  <div class="col-12">
                                    <label for="contenuto" class="form-label">Contenuto</label>
                                    <textarea class="form-control" id="contenuto" name="contenuto" rows="15" required><?php echo $row['contenuto']; ?></textarea>
                                  </div>
                                  
                                  <input type="hidden"  id="id" name="id" value="<?php echo $row['id'] ?>"></input>
                                </div>
                              </div>
                            </div>
                            <!-- Submit -->
                            <div class="d-flex justify-content-end">
                              <button type="submit" class="btn btn-outline-dark mt-2">Conferma</button>
                            </div>
                          </form>
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
            <h5 class="modal-title" id="insertModalLabel">Nuova intestazione</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            
            <form id="insertForm" method="post" action="../query/action_insert_intestazione_articolo.php">
              
              <div class="row ">
                <!-- Nome materiale -->
                <div class="col-12 mb-3">
                  <label for="intestazione" class="form-label">Descrizione</label>
                  <input type="text" class="form-control" id="intestazione" name="intestazione"/>
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
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Lista intestazioni</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        

        <table class="table table-sm table-striped " >
          <thead>
            <tr>
              <th>Descrizione</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($int as $intestazioni) { ?>
            <tr>
              <td><?php echo $int; ?></td>
              <!-- Aggiungi il listener per l'evento click all'icona del cestino -->
              <td style="width:20px"><i class='bx bx-trash' onclick="deleteIntestazione('<?php echo $int['id']; ?>')"></i></td>

            </tr>
          <?php } ?>  
          </tbody>
        </table>

        <a type="button" class="btn btn-sm btn-outline-dark float-end"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal">
          <i class='bx bx-plus '></i>
        </a> 

      </div>
    </div>



    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
    <script>
      function goBack() {
        history.back();
      }
    </script>

    <script>

      // Mostra il toast di successo
      function showSuccessToast() {
        var toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      }
    </script>

<script>
    function deleteIntestazione(intestazioneID) {
    // Conferma con l'utente prima di procedere con l'eliminazione
    if (confirm("Sei sicuro di voler eliminare questa intestazione per gli articoli?")) {
        // Invia una richiesta AJAX al server per eliminare il materiale
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../query/delete_intestazione.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Gestisci la risposta dal server, se necessario
                    console.log(xhr.responseText);
                    // Ricarica la pagina o aggiorna la tabella HTML, se necessario
                    location.reload(); // Ricarica la pagina dopo l'eliminazione
                } else {
                    console.error('Errore durante l\'eliminazione: ' + xhr.responseText);
                }
            }
        };
        xhr.send('intestazioneID=' + encodeURIComponent(intestazioneID));
    }
}

  </script>

    

  </body>
</html>