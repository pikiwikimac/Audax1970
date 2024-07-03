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
  $superuser = $_SESSION['superuser'];
  
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  
  // Imposta la variabile $now con la data e l'ora correnti
  $now = date('Y-m-d H:i');

  $sequenza = "SELECT max(id) AS max_id FROM articoli";
  $max_id_result = mysqli_query($con, $sequenza);
  $max_id_row = mysqli_fetch_assoc($max_id_result);
  
  $max_id = $max_id_row['max_id']; // Ottieni il valore massimo dell'id
  
  $new_max = intval($max_id) + 1;
?>

<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include '../elements/head.php'; ?>
  <body>
    <main role="main" class="tpl">
      <?php include '../elements/sidebar.php'; ?>
      <div class="tpl--content">
        <div class="tpl--content--inner">
          <div class="tpl-inner">
            <div class="tpl-inner-content">
              <div class="row pe-3">
                <div class="col-12">            
                  <div class="container-fluid">
                    <!-- Intestazione -->
                    <div class="tpl-header">
                      <div class="tpl-header--title">
                        <h1>Nuovo articolo</h1>
                        <div class="cta-wrapper">  
                          <a type="button" href="articoli.php" class="btn btn-outline-dark float-end">
                            <i class='bx bx-arrow-back'></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- Core della pagina -->
                    <div>
                      <form id="articoloForm" action="../query/action_insert_articolo.php" method="POST" enctype="multipart/form-data">
                        <div class="row gy-4 mb-3">
                          <div class="col-12">
                            <div class="card">
                              <div class="card-body">
                                <h3>Info di base</h3>
                                <div class="row my-3 g-3">
                                  <!-- Titolo -->
                                  <div class="col-12 col-sm-6 col-lg-12">
                                    <label for="titolo" class="form-label">Titolo</label>
                                    <input type="text" class="form-control" id="titolo" name="titolo" required/>
                                  </div>
                                  <!-- Data pubblicazione -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="data_pubblicazione" class="form-label">Data pubblicazione</label>
                                    <input type="datetime-local" class="form-control" id="data_pubblicazione" name="data_pubblicazione" value="<?php echo $now ?>"></input>
                                  </div>
                                  <!-- Squadra -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="autore" class="form-label">Autore</label>
                                    <input type="text" class="form-control" id="autore" name="autore" value="Redazione"></input>
                                  </div>
                                  <!-- Stagione -->
                                  <div class="col-6 col-sm-6 col-lg-2">
                                    <label for="id_stagione" class="form-label">Competizione</label>
                                    <select class="form-select" id="id_stagione" name="id_stagione">
                                      <!-- Opzioni per la squadra ospite -->
                                      <?php
                                        $sql = "SELECT * FROM `stagioni` ORDER BY id_stagione desc";
                                        $stagioni = mysqli_query($con, $sql);
                                        while ($stagione = mysqli_fetch_assoc($stagioni)) {
                                          echo "<option value='{$stagione['id_stagione']}'>{$stagione['descrizione']} {$stagione['anno_inizio']} - {$stagione['anno_fine']}</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <!-- Partita -->
                                  <div class="col-6 col-sm-6 col-lg-3">
                                    <label for="id_partita" class="form-label">Partita</label>
                                    <select class="form-select" id="id_partita" name="id_partita">
                                      <option disabled>-</option>
                                    </select>
                                  </div>
                                  <!-- Tags -->
                                  <div class="col-12 col-sm-6 col-lg-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags"></input>
                                  </div>
                                  <!-- Contenuto -->
                                  <div class="col-12">
                                    <label for="contenuto" class="form-label">Contenuto</label>
                                    <textarea class="form-control" id="contenuto" name="contenuto" rows="15" required></textarea>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Submit -->
                          <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-dark mt-2">Conferma</button>
                          </div>
                        </div>
                      </form>
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
    <script>
      function goBack() {
        history.back();
      }
    </script>
    <script>
      // Funzione per caricare le partite basate su id_stagione
      function loadPartite(id_stagione) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "get_partite.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("id_partita").innerHTML = xhr.responseText;
          }
        };
        xhr.send("id_stagione=" + id_stagione);
      }

      document.getElementById("id_stagione").addEventListener("change", function () {
        var id_stagione = this.value;
        loadPartite(id_stagione);
      });

      // Mostra il toast di successo
      function showSuccessToast() {
        var toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      }
    </script>
    
  </body>
</html>
