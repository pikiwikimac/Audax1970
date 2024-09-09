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


  $query = "select * 
  FROM societa 
  WHERE tipo='Prima squadra' 
  ORDER BY nome_societa";
  $squadre = mysqli_query($con,$query);


  $sql="
  SELECT *
  FROM stagioni s
  ";
  $stagioni = mysqli_query($con,$sql);

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
                          Nuova societa 
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a type="button" href="societa.php" class="btn btn-sm btn-outline-dark float-end" >
                            <i class='bx bx-arrow-back '></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <form action="../query/action_insert_societa.php" method="POST" enctype="multipart/form-data">
                      <div class="col-12">

                        <div class="card">
                          <div class="card-body">
                            
                            <div class="row g-4 mb-4 p-3">
                              <div class="col-12">
                                <div class="row g-4">
                                  
                                  <!-- Nome societa -->
                                  <div class="col-12 col-lg-3">
                                    <label for="sede" class="form-label">Società</label>
                                    <input type="text" class="form-control form-control-sm" id="nome_societa" name="nome_societa"  required/>
                                  </div>

                                  <!-- Tipo -->
                                  <div class="col-12 col-lg-2">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select class="form-select form-select-sm" id="tipo" name="tipo" onchange="toggleParentSelect()">
                                      <option value="Prima squadra">Prima squadra</option>
                                      <option value="Seconda squadra">Seconda squadra</option>
                                      <option value="Under 21">Under 21</option>
                                      <option value="Under 19">Under 19</option>
                                      <option value="Under 17">Under 17</option>
                                      <option value="Under 15">Under 15</option>
                                      <option value="Altro">Altro</option>
                                    </select>
                                  </div>

                                  
                                  
                                  <!-- Campo da gioco -->
                                  <div class="col-12 col-lg-5">
                                    <label for="sede" class="form-label">Campo da gioco</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bxs-map-pin' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sede" name="sede" />
                                    </div>
                                  </div>

                                  <!-- Città -->
                                  <div class="col-12 col-lg-2">
                                    <label for="citta" class="form-label">Città</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-map' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="citta" name="citta"/>
                                    </div>
                                  </div>

                                  <!-- Giorno match -->
                                  <div class="col-6 col-lg-2">
                                    <label for="giorno_settimana" class="form-label">Giorno match</label>
                                    <select class="form-select form-select-sm" id="giorno_settimana" name="giorno_settimana" >
                                      <option value="Lunedi">Lunedì</option>
                                      <option value="Martedi">Martedì</option>
                                      <option value="Mercoledi">Mercoledì</option>
                                      <option value="Giovedi">Giovedì</option>
                                      <option value="Venerdi">Venerdì</option>
                                      <option value="Sabato">Sabato</option>
                                      <option value="Domenica">Domenica</option>
                                    </select>
                                  </div>

                                  

                                  <!-- Ora match -->
                                  <div class="col-6 col-lg-2">
                                    <label for="ora_match" class="form-label">Ora match</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-time-five' ></i>
                                      </span>
                                      <input type="time" class="form-control form-control-sm" id="ora_match" name="ora_match"  />
                                    </div>
                                  </div>

                                  <!-- Presidente -->
                                  <div class="col-6 col-lg-2">
                                    <label for="citta" class="form-label">Presidente</label>
                                    <input type="text" class="form-control form-control-sm" id="presidente" name="presidente"   />
                                  </div>

                                  <!-- Vicepresidente -->
                                  <div class="col-6 col-lg-2">
                                    <label for="vicepresidente" class="form-label">Vicepresidente</label>
                                    <input type="text" class="form-control form-control-sm" id="vicepresidente" name="vicepresidente" ></input>
                                  </div>
                                  
                                  <!-- Allenatore -->
                                  <div class="col-6 col-lg-2">
                                    <label for="allenatore" class="form-label">Allenatore</label>
                                    <input type="text" class="form-control form-control-sm" id="allenatore" name="allenatore" ></input>
                                  </div>

                                  <!-- Contatto -->
                                  <div class="col-12 col-lg-2">
                                    <label for="contatto_riferimento" class="form-label">Contatto</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bxs-user-circle'></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="contatto_riferimento" name="contatto_riferimento"  />
                                    </div>
                                  </div>

                                  <!-- Sede legale -->
                                  <div class="col-12 col-lg-4">
                                    <label for="sede_legale" class="form-label">Sede legale</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-building-house' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sede_legale" name="sede_legale" ></input>
                                    </div>
                                  </div>

                                  <!-- Campionato attuale -->
                                  <div class="col-12 col-lg-4">
                                    <label for="campionato" class="form-label">Campionato attuale</label>
                                    <select class="form-select form-select-sm" id="campionato" name="campionato" >
                                      <?php while($stagione = mysqli_fetch_assoc($stagioni)){ ?>
                                        <option value="<?php echo $stagione['id_stagione']?>"><?php echo $stagione['descrizione'] .' Girone ' . $stagione['girone'] .' - ' . $stagione['anno_inizio'].'/' .$stagione['anno_fine']   ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                  
                                  <!-- Email -->
                                  <div class="col-12 col-lg-4">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class="bx bx-at"></i>
                                      </span>
                                      <input type="email" class="form-control form-control-sm" id="email" name="email" ></input>
                                    </div>
                                  </div>div>

                                  

                                  <!-- Telefono -->
                                  <div class="col-12 col-lg-2">
                                    <label for="telefono" class="form-label">Telefono</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-phone' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" ></input>
                                    </div>
                                  </div>


                                  <!-- Whatsapp -->
                                  <div class="col-6 col-lg-2">
                                    <label for="whatsapp" class="form-label">Whatsapp</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bxl-whatsapp' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="whatsapp" name="whatsapp" ></input>
                                    </div>
                                  </div>

                                  <!-- Instagram -->
                                  <div class="col-6 col-lg-2">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bxl-instagram' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="instagram" name="instagram"></input>
                                    </div>
                                  </div>

                                  <!-- Sito web -->
                                  <div class="col-12 col-lg-4">
                                    <label for="sito_web" class="form-label">Sito web</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-link' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sito_web" name="sito_web" ></input>
                                    </div>
                                  </div>


                                  <!-- Squadra principale -->
                                  <div class="col-6 col-lg-2" id="parentSelectContainer" style="display:none;">
                                    <label for="parent_id" class="form-label">Squadra principale</label>
                                    <select class="form-select form-select-sm" id="parent_id" name="parent_id" >
                                      <option value="0" selected> - Seleziona prima squadra - </option>
                                      <?php while ($squadra = mysqli_fetch_assoc($squadre)) { ?>
                                        <option value="<?php echo $squadra['id']; ?>"><?php echo $squadra['nome_societa']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>


                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-end mt-3">
                          <button type="submit" class="btn btn-sm btn-outline-dark mt-2">Conferma</button>
                        </div>
                      </div>

                    </form>
                    <!-- END:Core della pagina -->
                    <br/>
                    <br/>
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
      // Mostra il toast di successo
      function showSuccessToast() {
        var toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
      }
    </script>

    <script>
      function toggleParentSelect() {
        var tipo = document.getElementById("tipo").value;
        var parentSelectContainer = document.getElementById("parentSelectContainer");

        if (tipo === "Prima squadra") {
          parentSelectContainer.style.display = "none";
        } else {
          parentSelectContainer.style.display = "block";
        }
      }
    </script>


  </body>
</html>