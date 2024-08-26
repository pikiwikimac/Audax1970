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
  $id_societa=  $_SESSION['id_societa_riferimento'];
  
  $id=$_REQUEST['id'];

  $query =
  "
  SELECT a.*
  FROM allenamenti a
  WHERE a.id='$id'
  ";

  $allenamento = mysqli_query($con,$query);
  $all = mysqli_fetch_assoc($allenamento);


  $query="
  SELECT g.*
  FROM giocatori g
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine is NULL
  ORDER BY g.ruolo,g.cognome,g.nome
  ";
  $giocatori= mysqli_query($con,$query);

  // Ottenere l'elenco dei giocatori registrati per quell'allenamento dal database
  $query = "SELECT id_giocatore FROM partecipazione_allenamenti WHERE id_allenamento = $id";
  $result = mysqli_query($con, $query);

  // Creare un array vuoto per memorizzare gli ID dei giocatori selezionati
  $giocatoriSelezionati = array();

  // Aggiungere gli ID dei giocatori selezionati all'array
  while ($row = mysqli_fetch_assoc($result)) {
    $giocatoriSelezionati[] = $row['id_giocatore'];
  }

  $queryNote="
  SELECT *
  FROM note_allenamenti na
  WHERE na.id_allenamento=$id
  ";

  $note= mysqli_query($con,$queryNote);
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
                          <?php echo $all['tipologia'] ?>
                        </h3>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          
                          <a href="indisponibili_admin.php " class="btn btn-outline-dark float-end" type="button">
                            <i class='bx bxs-ambulance' ></i>
                          </a>
                          
                          <a type="button" href="allenamenti_admin.php" class="btn btn-outline-dark float-end me-2" >
                            <i class='bx bx-left-arrow-alt bx-xs' ></i>
                          </a>
                                           
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row gy-3">
                        <!-- Box: Lista giocatori -->
                        <div class="col-12 col-lg-6">
                          <div class="card">
                            <div class="card-header bg-dark">
                              <span class="text-white">
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="selezionaTutti">
                                </div>
                              </span>
                            </div>

                            <form action="../query/action_presenze_allenamenti.php?id=<?php echo urlencode($id); ?>" method="POST">
                              <div class="card-body">
                                <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                                  <div class="form-check">
                                    <input class="form-check-input" name="presenza[]" type="checkbox" value="<?php echo $row['id']; ?>" <?php if(in_array($row['id'], $giocatoriSelezionati)) echo "checked"; ?>>
                                    <label class="form-check-label" for="flexCheckDefault">
                                      <?php echo $row['nome'] .' ' .$row['cognome']; ?>
                                      
                                      <?php
                                        // Verifica se il giocatore è presente nella tabella indisponibili
                                        $giocatoreId = $row['id'];
                                        $queryVerificaIndisponibilita = "SELECT motivo FROM indisponibili WHERE id_giocatore = $giocatoreId AND a_data >= CURRENT_DATE ";
                                        $resultVerifica = mysqli_query($con, $queryVerificaIndisponibilita);
                                        
                                        if (mysqli_num_rows($resultVerifica) > 0) {
                                            // Il giocatore è indisponibile, otteniamo il motivo
                                            $rowIndisponibilita = mysqli_fetch_assoc($resultVerifica);
                                            $motivo = $rowIndisponibilita['motivo'];
                                            
                                            // In base al motivo, mostriamo l'icona corrispondente
                                            if ($motivo === 'Lavoro') {
                                                echo '<i class="bx bx-briefcase " data-bs-toggle="tooltip" data-bs-title="Lavoro"></i>'; // Icona per Lavoro
                                            } elseif ($motivo === 'Malattia') {
                                                echo '<i class="bx bxs-ambulance text-danger" data-bs-toggle="tooltip" data-bs-title="Malattia"></i>'; // Icona per Malattia
                                            } elseif ($motivo === 'Viaggio') {
                                                echo '<i class="bx bxs-plane-alt" data-bs-toggle="tooltip" data-bs-title="Viaggio"></i >'; // Icona per Viaggio
                                            } else {
                                                // Motivo sconosciuto, puoi gestirlo in modo appropriato
                                            }
                                        }
                                      ?>
                                    </label>
                                    <span class="float-end">
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
                                    </span>
                                  </div>
                                <?php } ?>
                              </div>
                              <button type="submit" class="btn btn-primary mt-2 mb-2 float-end me-2">Conferma</button>
                            </form>
                          </div>
                        </div>

                        <div class="col-12 col-lg-6">
                          <div class="row mb-3">
                            <div class="col-12 col-lg-4">
                              <!-- Luogo allenamento -->
                              <i class='bx bx-calendar' ></i>
                              <?php echo date("d/m/y", strtotime($all['data'])) .' - ' .date("H:i",strtotime($all['orario'])); ?> 
                              <br/>
                              <!-- Luogo allenamento -->
                              <i class='bx bx-map' ></i>
                              <?php echo $all['luogo']  ?>
                              <br/>
                              <!-- Presenti allenamento -->
                              <i class='bx bx-user'></i>
                              <span class="text-nowrap">Presenti : <span class="card-text" id="contatore-selezione">0</span></span>
                              <br/>
                              <!-- Non presenti allenamento -->
                              <i class='bx bx-user-x'></i>
                              <span class="text-nowrap">Assenti : <span class="card-text" id="contatore-non-selezione">0</span></span>
                            </div>
                            <div class="col-12 col-lg-8">
                              <!-- Form upload allegato -->
                              <div class="row mb-3">
                                <div class="col-12">
                                  <?php if (!isset($all['file_path'])) { ?>
                                    <form action="../query/upload_image_allenamento.php?id=<?php echo urlencode($id); ?>" method="POST" enctype="multipart/form-data" class="mt-3">
                                      <div class="row mb-3">
                                        <label for="file_allegato" class="form-label">Allega un file</label>
                                        <input class="form-control" type="file" id="file_allegato" name="file_allegato">
                                      </div>
                                      <button type="submit" class="btn btn-outline-dark mt-2 float-end">
                                        Carica Allegato
                                      </button>
                                    </form>
                                  <?php } ?>
                                </div>
                                <div class="col-12">
                                  <?php if (isset($all['file_path']) && $all['file_path']) { ?>
                                      <div class="mb-3">
                                          <label class="form-label">File allegato:</label>
                                          <a href="<?php echo $all['file_path']; ?>" class="text-decoration-none" target="_blank"><i class="bi bi-paperclip"></i> &nbsp; Visualizza</a>
                                          <br/>
                                          <button type="button" class="btn btn-danger btn-sm" onclick="deleteAllegato('<?php echo $id; ?>')">Elimina allegato</button>
                                      </div>
                                  <?php } ?>
                                </div>
                            </div>

                          </div>
                          
                          <?php if (mysqli_num_rows($note) > 0) { ?>
                            <div class="card my-3">
                              <div class="card-header bg-dark">
                                <h5 class="card-title text-white">Note</h5>
                              </div>
                              <div class="card-body">
                                <?php while($nota = mysqli_fetch_assoc($note)) {  ?>
                                    <table class="table table-sm table-borderless p-0">
                                      <tbody>
                                        <tr>
                                          <td width="98%"><?php echo $nota['descrizione'] ?> </td>
                                          <td class=" text-end" width="2%">
                                            <a class="text-decoration-none text-dark" onclick="confirmDelete('<?php echo $nota['id']; ?>','<?php echo $nota['id_allenamento']; ?>')">
                                              <i class='bx bx-trash' ></i>
                                            </a>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  <?php } ?>
                              </div>
                            </div>
                          <?php } ?>
                          
                          <!-- Form Note allenamento  -->
                          <form action="../query/action_note_allenamenti.php?id=<?php echo urlencode($id); ?>" method="POST" class="mt-0">
                            
                            <label for="note_allenamento">Note allenamento</label>
                            <textarea class="form-control" placeholder="" id="note_allenamento" name="note_allenamento" style="height: 100px"></textarea>

                            <button type="submit" class="btn btn-outline-dark mt-2 float-end">
                              Inserisci
                            </button>
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
      document.addEventListener('DOMContentLoaded', function() {
      const checkboxInputs = document.querySelectorAll('input[name="presenza[]"]');
      const selezionaTuttiCheckbox = document.getElementById('selezionaTutti');
      const contatoreSelezionateElement = document.getElementById('contatore-selezione');
      const contatoreNonSelezionateElement = document.getElementById('contatore-non-selezione');

      let contatoreSelezionate = 0;

      checkboxInputs.forEach(function(checkbox) {
          if (checkbox.checked) {
              contatoreSelezionate++;
          }

          checkbox.addEventListener('change', function() {
              if (checkbox.checked) {
                  contatoreSelezionate++;
              } else {
                  contatoreSelezionate--;
              }
              contatoreSelezionateElement.textContent = contatoreSelezionate;
              contatoreNonSelezionateElement.textContent = checkboxInputs.length - contatoreSelezionate;

              // Se tutte le checkbox sono selezionate, imposta la checkbox "Seleziona tutti" su checked
              selezionaTuttiCheckbox.checked = Array.from(checkboxInputs).every(function(checkbox) {
                  return checkbox.checked;
              });
          });
      });

      // Nuovo listener per l'elemento "Seleziona tutti"
      selezionaTuttiCheckbox.addEventListener('change', function() {
          checkboxInputs.forEach(function(checkbox) {
              checkbox.checked = selezionaTuttiCheckbox.checked;

              if (selezionaTuttiCheckbox.checked) {
                  contatoreSelezionate = checkboxInputs.length;
              } else {
                  contatoreSelezionate = 0;
              }

              contatoreSelezionateElement.textContent = contatoreSelezionate;
              contatoreNonSelezionateElement.textContent = checkboxInputs.length - contatoreSelezionate;
          });
      });

      // Inizializzazione dei contatori
      contatoreSelezionateElement.textContent = contatoreSelezionate;
      contatoreNonSelezionateElement.textContent = checkboxInputs.length - contatoreSelezionate;
  });

    </script>

    <script>
      function confirmDelete(recordId,allenamentoId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_allenamento_note.php?id=" + recordId +"&id_allenamento=" +allenamentoId;
        }
      }
    </script>

    <script>
      function deleteAllegato(allenamentoId) {
        if (confirm("Sei sicuro di voler eliminare questo allegato?")) {
          window.location.href = "../query/delete_allegato_allenamento.php?id=" + allenamentoId;
        }
    }
    </script>






  </body>

</html>