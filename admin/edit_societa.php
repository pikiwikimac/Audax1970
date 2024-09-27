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
    FROM societa
    WHERE id='$id'
    ";
  $societa = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($societa);

  $query2 = "select * 
  FROM societa 
  WHERE tipo='Prima squadra' 
  ORDER BY nome_societa";
  $squadre = mysqli_query($con,$query2);

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
                          <?php echo $row['nome_societa'] ?> 
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a type="button" href="societa.php" class="btn btn-sm btn-outline-dark float-end" >
                            <i class='bi bi-arrow-left '></i>
                          </a>
                        </div>
                      </div>
                    

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row">
                        <div class="col-12 col-lg-2 ">
                          
                          
                          <div class="card">
                            <?php if (!empty($row['logo'])) : ?>
                              <img src="../image/loghi/<?php echo $row['logo']; ?>" class="img-fluid rounded" alt="Immagine attuale">
                            <?php else : ?>
                              <img src="../image/default.jpeg" class="img-fluid rounded" alt="Immagine di default grigia">
                            <?php endif; ?>
                          </div>

                          <form action="../query/upload_image_team.php" method="post" enctype="multipart/form-data" class="mt-3">
                            
                            <label for="formFile" class="form-label">Logo</label>
                            <input class="form-control form-control-sm form-control form-control-sm-md" type="file" id="formFile" name="playerImage">
                            <input type="hidden"  id="id" name="id" value="<?php echo $id?>" />
                            <input type="submit" value="Carica" class="btn btn-sm btn-outline-dark float-end mt-3" name="submit">
                            
                          </form>
                          
                        </div>

                        <div class="col-12 col-lg-10 ps-md-4 ">
                          <form action="../query/action_edit_societa.php?id=<?php echo $row['id'] ?>" method="POST">
                            
                            <div class="card ">
                              <div class="card-body">

                                <div class="row g-4 mb-4 p-3">
                                  <!-- Nome societa -->
                                  <div class="col-12 col-lg-3">
                                    <label for="sede" class="form-label">Società</label>
                                    <input type="text" class="form-control form-control-sm" id="nome_societa" name="nome_societa" value="<?php echo $row['nome_societa'];?>" required/>
                                  </div>

                                  <!-- Tipo -->
                                  <div class="col-12 col-lg-2">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select class="form-select form-select-sm" id="tipo" name="tipo" onchange="toggleParentSelect()">
                                      <option value="Prima squadra" <?php if ($row['tipo']==='Prima squadra') { ?>selected="selected"<?php } ?>>Prima squadra</option>
                                      <option value="Seconda squadra" <?php if ($row['tipo']==='Seconda squadra') { ?>selected="selected"<?php } ?>>Seconda squadra</option>
                                      <option value="Under 21" <?php if ($row['tipo']==='Under 21') { ?>selected="selected"<?php } ?>>Under 21</option>
                                      <option value="Under 19" <?php if ($row['tipo']==='Under 19') { ?>selected="selected"<?php } ?>>Under 19</option>
                                      <option value="Under 17" <?php if ($row['tipo']==='Under 17') { ?>selected="selected"<?php } ?>>Under 17</option>
                                      <option value="Under 15" <?php if ($row['tipo']==='Under 15') { ?>selected="selected"<?php } ?>>Under 15</option>
                                    </select>
                                  </div>
                                  
                                  <!-- Campo da gioco -->
                                  <div class="col-12 col-lg-5">
                                    <label for="sede" class="form-label">Campo da gioco</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-pin-map-fill' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sede" name="sede" value="<?php echo $row['sede'];?>" />
                                    </div>
                                  </div>
                                  
                                  <!-- Città -->
                                  <div class="col-12 col-lg-2">
                                    <label for="citta" class="form-label">Città</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-geo-map' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="citta" name="citta" value="<?php echo $row['citta'];?>"  />
                                    </div>
                                  </div>

                                  <!-- Giorno match -->
                                  <div class="col-6 col-lg-2">
                                    <label for="giorno_settimana" class="form-label">Giorno match</label>
                                    <select class="form-select form-select-sm" id="giorno_settimana" name="giorno_settimana" >
                                      <option value="Lunedi" <?php if ($row['giorno_settimana']==='Lunedi') { ?>selected="selected"<?php } ?>>Lunedì</option>
                                      <option value="Martedi" <?php if ($row['giorno_settimana']==='Martedi') { ?>selected="selected"<?php } ?>>Martedì</option>
                                      <option value="Mercoledi" <?php if ($row['giorno_settimana']==='Mercoledi') { ?>selected="selected"<?php } ?>>Mercoledì</option>
                                      <option value="Giovedi" <?php if ($row['giorno_settimana']==='Giovedi') { ?>selected="selected"<?php } ?>>Giovedì</option>
                                      <option value="Venerdi" <?php if ($row['giorno_settimana']==='Venerdi') { ?>selected="selected"<?php } ?>>Venerdì</option>
                                      <option value="Sabato" <?php if ($row['giorno_settimana']==='Sabato') { ?>selected="selected"<?php } ?>>Sabato</option>
                                      <option value="Domenica" <?php if ($row['giorno_settimana']==='Domenica') { ?>selected="selected"<?php } ?>>Domenica</option>
                                    </select>
                                  </div>

                                
                                  <!-- Ora match -->
                                  <div class="col-6 col-lg-2">
                                    <label for="ora_match" class="form-label">Ora match</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-clock' ></i>
                                      </span>
                                      <input type="time" class="form-control form-control-sm" id="ora_match" name="ora_match" value="<?php echo $row['ora_match'];?>" />
                                    </div>
                                  </div>

                                  <!-- Presidente -->
                                  <div class="col-12 col-lg-2">
                                    <label for="citta" class="form-label">Presidente</label>
                                    <input type="text" class="form-control form-control-sm" id="presidente" name="presidente" value="<?php echo $row['presidente'];?>"  />
                                  </div>

                                  <!-- Vicepresidente -->
                                  <div class="col-12 col-lg-2">
                                    <label for="vicepresidente" class="form-label">Vicepresidente</label>
                                    <input type="text" class="form-control form-control-sm" id="vicepresidente" name="vicepresidente" value="<?php echo $row['vicepresidente'];?>"></input>
                                  </div>

                                  <!-- Allenatore -->
                                  <div class="col-12 col-lg-2">
                                    <label for="allenatore" class="form-label">Allenatore</label>
                                    <input type="text" class="form-control form-control-sm" id="allenatore" name="allenatore" value="<?php echo $row['allenatore'];?>"></input>
                                  </div>

                                  <!-- Contatto -->
                                  <div class="col-12 col-lg-2">
                                    <label for="contatto_riferimento" class="form-label">Contatto</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-person-circle'></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="contatto_riferimento" name="contatto_riferimento" value="<?php echo $row['contatto_riferimento'];?>"  />
                                    </div>
                                  </div>

                                  <!-- Sede legale -->
                                  <div class="col-12 col-lg-4">
                                    <label for="sede_legale" class="form-label">Sede legale</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-buildings' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sede_legale" name="sede_legale" value="<?php echo $row['sede_legale'];?>"></input>
                                    </div>
                                  </div>


                                  <!-- Campionato attuale -->
                                  <div class="col-12 col-lg-4">
                                    <label for="campionato" class="form-label">Campionato attuale</label>
                                    <select class="form-select form-select-sm" id="campionato" name="campionato" >
                                      <?php while($stagione = mysqli_fetch_assoc($stagioni)){ ?>
                                        <option value="<?php echo $stagione['id_stagione'] ?>" <?php if ($stagione['id_stagione']===$row['id_campionato']) { ?>selected="selected"<?php } ?>>
                                          <?php echo $stagione['descrizione'] .' Girone ' . $stagione['girone'] .' - ' . $stagione['anno_inizio'].'/' .$stagione['anno_fine']   ?>
                                        </option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                  
                                  <!-- Email -->
                                  <div class="col-12 col-lg-4">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class="bi bi-at"></i>
                                      </span>
                                      <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo $row['email'];?>"></input>
                                    </div>
                                  </div>
                                  

                                  <!-- Telefono -->
                                  <div class="col-12 col-lg-2">
                                    <label for="telefono" class="form-label">Telefono</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-telephone' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="<?php echo $row['telefono'];?>"></input>
                                      
                                    </div>
                                  </div>

                                  

                                  <!-- Whatsapp -->
                                  <div class="col-12 col-lg-2">
                                    <label for="whatsapp" class="form-label">Whatsapp</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-whatsapp' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="whatsapp" name="whatsapp" value="<?php echo $row['whatsapp'];?>"></input>
                                    </div>
                                  </div>

                                  <!-- Instagram -->
                                  <div class="col-12 col-lg-2">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-instagram' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="instagram" name="instagram" value="<?php echo $row['instagram'];?>"></input>
                                    </div>
                                  </div>

                                  <!-- Facebook -->
                                  <div class="col-12 col-lg-2">
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-facebook' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="facebook" name="facebook" value="<?php echo $row['facebook'];?>"></input>
                                    </div>
                                  </div>

                                  <!-- Sito web -->
                                  <div class="col-12 col-lg-4">
                                    <label for="sito_web" class="form-label">Sito web</label>
                                    <div class="input-group">
                                      <span class="input-group-text" id="basic-addon1">
                                        <i class='bi bi-link-45deg' ></i>
                                      </span>
                                      <input type="text" class="form-control form-control-sm" id="sito_web" name="sito_web" value="<?php echo $row['sito_web'];?>"></input>
                                    </div>
                                  </div>
                                  <!-- Squadra principale -->
                                  <div class="col-12 col-lg-2 " id="parentSelectContainer" >
                                    <label for="parent_id" class="form-label">Squadra principale</label>
                                    <select class="form-select form-select-sm" id="parent_id" name="parent_id" >
                                      <?php while ($squadra = mysqli_fetch_assoc($squadre)) { ?>
                                        <option value="<?php echo $squadra['id']; ?>" <?php if ($squadra['id']=== $row['id']) { ?>selected="selected"<?php } ?>><?php echo $squadra['nome_societa']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                          
                            <!-- Valore HIDDEN -->
                            <input type="hidden"  id="id" name="id" value="<?php echo $row['id'];?>" />
                            
                            <!-- Submit -->
                            <div class="d-flex justify-content-end my-3">
                              <button type="submit" class="btn btn-sm btn-outline-dark">Conferma</button>
                            </div>
                                    
                          </form>
                        </div>
                      </div>
                    </div>
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