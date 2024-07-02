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

  
  $id=  $_REQUEST['id'];

  $query =
    "
    SELECT *
    FROM articoli
    WHERE id='$id'
    ";
  $articolo = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($articolo);

  $selected_partita = $row['id_partita'];  // Ottieni l'ID della partita selezionata
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
                        <h1>
                          Modifica articolo  
                        </h1>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="articoli.php" class="btn btn-outline-dark float-end">
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
                                    <label for="id_stagione" class="form-label">Competizione</label>
                                    <select class="form-select" id="id_stagione" name="id_stagione">
                                      <!-- Opzioni per la squadra ospite -->
                                      <?php
                                        $sql = "SELECT * FROM `stagioni` ORDER BY id_stagione desc";
                                        $stagioni = mysqli_query($con, $sql);
                                        while ($stagione = mysqli_fetch_assoc($stagioni)) {
                                          $selected = ($stagione['id_stagione'] == $row['id_stagione']) ? "selected" : "";
                                          echo "<option value='{$stagione['id_stagione']}' $selected>{$stagione['descrizione']} {$stagione['anno_inizio']} - {$stagione['anno_fine']}</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>

                                  <!-- Partita -->
                                  <div class="col-6 col-sm-6 col-lg-3 ">
                                    <label for="id_partita" class="form-label">Partita</label>
                                    <select class="form-select" id="id_partita" name="id_partita">
                                       
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


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
    <script>
      function goBack() {
        history.back();
      }
    </script>

    <script>
      function loadPartite(id_stagione) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "get_partite.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
          if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("id_partita").innerHTML = xhr.responseText;
          }
        };
        xhr.send("id_stagione=" + id_stagione + "&selected_partita=<?php echo $selected_partita; ?>");
      }

      document.getElementById("id_stagione").addEventListener("change", function () {
        var id_stagione = this.value;
        loadPartite(id_stagione);
      });

      // Carica le partite per la stagione corrente al caricamento della pagina
      window.onload = function() {
        loadPartite(document.getElementById("id_stagione").value);
      };


      // Mostra il toast di successo
      function showSuccessToast() {
        var toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      }
    </script>
    

  </body>
</html>