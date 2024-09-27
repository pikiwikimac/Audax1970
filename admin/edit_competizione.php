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
  
  $id_stagione=  $_REQUEST['id_stagione'];

  $query =
    "
    SELECT *
    FROM stagioni
    WHERE id_stagione='$id_stagione'
    ";
  $competizione = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($competizione);

  $query=
  "
  SELECT COUNT(*) as partite
  FROM partite
  WHERE id_stagione='$id_stagione';
  ";

  $count=mysqli_query($con,$query);
  $count_partite=mysqli_fetch_assoc($count);


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
                          Modifica competizione  
                        </h4>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="competizioni.php" class="btn btn-sm btn-outline-dark float-end">
                            <i class='bi bi-arrow-left'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <span class="">
                      
                      <div class="row gy-4 mb-3 ">
                        <?php if($count_partite['partite'] > 0){ ?>
                          <span>  
                            Totale partite registrate in questa competizione:
                            <strong>
                              <?php echo $count_partite['partite'] ?>
                            </strong>
                          </span>
                        <?}else{ ?>
                          <span class="text-muted"> 
                            Nessuna partita registrata in questa competizione
                          </span>
                        <?php } ?>
                        
                        <div class="col-12">
                          <form action="../query/action_edit_competizione.php" method="POST" enctype="multipart/form-data">
                            <div class="card">
                              <div class="card-body">
                                
                                <div class="row mb-3 g-3">
                                  <!-- Nome stagione -->
                                  <div class="col-12 col-sm-6 col-lg-3  ">
                                    <label for="nome_stagione" class="form-label">Nome stagione</label>
                                    <input typer="text" class="form-control" id="nome_stagione" name="nome_stagione" required value="<?php echo $row['nome_stagione'] ?>"></input>
                                  </div>


                                  <!-- Descrizione -->
                                  <div class="col-12 col-sm-6 col-lg-3  ">
                                    <label for="descrizione" class="form-label">Descrizione</label>
                                    <input type="text" class="form-control" id="descrizione" name="descrizione" value="<?php echo $row['descrizione'] ?>"></input>
                                  </div>

                                  <!-- Anno inizio -->
                                  <div class="col-6 col-sm-6 col-lg-1">
                                    <label for="anno_inizio" class="form-label">Anno inizio</label>
                                    <input type="text" class="form-control" id="anno_inizio" name="anno_inizio" value="<?php echo $row['anno_inizio'] ?>"></input>
                                  </div>

                                  <!-- Anno fine -->
                                  <div class="col-6 col-sm-6 col-lg-1">
                                    <label for="anno_fine" class="form-label">Anno fine</label>
                                    <input type="text" class="form-control" id="anno_fine" name="anno_fine" value="<?php echo $row['anno_fine'] ?>"></input>
                                  </div>

                                  <!-- Girone -->
                                  <div class="col-12 col-sm-6 col-lg-2 ">
                                    <label for="girone" class="form-label">Girone</label>
                                    <input type="text" class="form-control" id="girone" name="girone" value="<?php echo $row['girone'] ?>"></input>
                                  </div>

                                  <!-- Prima squadra -->
                                  <div class="col-12 col-sm-6 col-lg-2">
                                    <label for="prima_squadra" class="form-label">Squadra</label>
                                    <select class="form-select" id="prima_squadra" name="prima_squadra">
                                      <option value="1" <?php if ($row['prima_squadra']==1) { ?>selected="selected"<?php } ?>>Prima squadra</option>
                                      <option value="2" <?php if ($row['prima_squadra']==2) { ?>selected="selected"<?php } ?>>Settore giovanile</option>
                                    </select>
                                  </div>
                                    
                                  <input type="hidden"  id="id_stagione" name="id_stagione" value="<?php echo $row['id_stagione'] ?>"></input>
                                </div>
                              </div>
                            </div>
                            <!-- Submit -->
                            <div class="d-flex justify-content-end">
                              <button type="submit" class="btn btn-sm btn-outline-dark mt-3">Conferma</button>
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
    
  </body>
</html>