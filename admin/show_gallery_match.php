<?php
session_start();

// Controlla se l'utente è autenticato
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$image = isset($_SESSION['image']) ? $_SESSION['image'] : null;
$superuser = $_SESSION['superuser'];

// Verifica se il parametro 'folder' è passato nell'URL
if (isset($_GET['folder'])) {
    $folder = $_GET['folder'];

    // Costruisci il percorso completo della cartella
    $folderPath = '../image/partite/' . $folder;

} else {
    // Il parametro 'folder' non è stato passato nell'URL, reindirizza alla pagina principale
    header('Location: index.php');
    exit;
}

// Gestione dell'upload delle immagini
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photoFile'])) {
    $uploadDir = $folderPath . '/';
    
    foreach ($_FILES['photoFile']['name'] as $key => $value) {
        $uploadedFile = $uploadDir . basename($_FILES['photoFile']['name'][$key]);

        // Verifica se il file è un'immagine
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($_FILES['photoFile']['name'][$key], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            // Sposta il file nella cartella specificata
            move_uploaded_file($_FILES['photoFile']['tmp_name'][$key], $uploadedFile);
        } else {
            echo 'Formato del file non supportato. Sono consentite solo immagini con estensione jpg, jpeg, png o gif.';
        }
    }

    // Ridireziona per evitare il problema del reinvio del modulo
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit;
}
?>

<!doctype html>
<html lang="it">
  <<!-- Head -->
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
                        <?php echo $folder ?>
                      </h1>
                      <!-- Bottoni a destra -->
                      <div class="cta-wrapper">
                        <?php
                        // Mostra il pulsante Aggiungi Foto
                        echo '<form action="" method="post" enctype="multipart/form-data">
                          <label for="photoFileInput" class="btn btn-outline-dark ">
                          Aggiungi Foto
                          </label>
                          <input type="file" id="photoFileInput" name="photoFile[]" accept="image/*" style="display:none;" multiple onchange="showSelectedFiles(this)">
                          <button type="submit" class="btn btn-outline-success ">Carica</button>
                        </form>';
                        ?>
                      </div>
                    </div>
                  </div>
                   <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <!-- Aggiungi questo div sotto l'input file -->
                      <div class="mb-3" id="selectedFiles"></div>
                      
                      
                      <div class="row gy-3">
                          <?php
                          // Verifica se la cartella esiste
                          if (is_dir($folderPath)) {
                              // Ottieni la lista di file nella cartella
                              $files = scandir($folderPath);

                              // Cicla attraverso i file
                              foreach ($files as $file) {
                                  // Ignora le cartelle '.' e '..'
                                  if ($file != "." && $file != "..") {
                                      // Verifica se il file è un'immagine (puoi personalizzare questa condizione)
                                      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                      $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                      
                                      if (in_array($extension, $allowedExtensions)) {
                                          // Mostra l'immagine
                                          echo '<div class="col-12 col-lg-3">
                                                  <div class="card">
                                                      <img src="' . $folderPath . '/' . $file . '" class="card-img-top" alt="Image">
                                                      <div class="card-body">
                                                          <p class="card-text">' . $file . '</p>
                                                      </div>
                                                  </div>
                                                </div>';
                                      }
                                  }
                              }
                          } else {
                              // La cartella specificata non esiste, mostra un messaggio di errore
                              echo '<p>La cartella specificata non esiste.</p>';
                          }
                          ?>
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"
        integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"
        integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ"
        crossorigin="anonymous"></script>

        <!-- All'interno del tag <script> nel tuo codice -->
    <script>
      // Funzione per visualizzare i nomi dei file selezionati
      function showSelectedFiles(input) {
        var selectedFilesDiv = document.getElementById("selectedFiles");
        selectedFilesDiv.innerHTML = "";

        for (var i = 0; i < input.files.length; i++) {
          var fileName = input.files[i].name;
          var fileDiv = document.createElement("div");
          fileDiv.textContent = fileName;
          selectedFilesDiv.appendChild(fileDiv);
        }
      }
    </script>

  </body>

</html>
