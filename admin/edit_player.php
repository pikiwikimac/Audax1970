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
    FROM giocatori
    WHERE id='$id'
    ";
  $giocatore = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($giocatore);



  $query2 = "
    select nome_societa,id
    FROM societa
    ";
  $squadre = mysqli_query($con,$query2);

  $query_squadra = "
    SELECT id_squadra
    FROM giocatori
    WHERE id = '$id'
    ";
  $result_squadra = mysqli_query($con, $query_squadra);
  $row_squadra = mysqli_fetch_assoc($result_squadra);
  $id_squadra = $row_squadra['id_squadra'];

  $query_societa_collegate = "
    SELECT id, nome_societa, tipo
    FROM societa
    WHERE parent_id = '$id_squadra'
    or id='$id_squadra'
  ";
  $societa_collegate = mysqli_query($con, $query_societa_collegate);



  $query_affiliazioni = "
    SELECT s.id, s.tipo
    FROM affiliazioni_giocatori ag
    INNER JOIN societa s ON s.id = ag.id_societa
    WHERE ag.id_giocatore = '$id'
  ";
  $affiliazioni_player = mysqli_query($con, $query_affiliazioni);
  $affiliated_player = [];
  while ($row_aff = mysqli_fetch_assoc($affiliazioni_player)) {
    $affiliated_player[] = $row_aff['id'];
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
                        <h1>
                          <?php echo $row['nome'].' '.$row['cognome'];?>
                        </h1>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="show_societa.php?id=<?php echo $row['id_squadra'] ?>" class="btn btn-outline-dark float-end" >
                            <i class='bx bx-arrow-back '></i>
                          </a>
                          <button class="btn btn-outline-dark float-end me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                            <i class='bx bx-folder-open' ></i>
                          </button>                          
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row gy-4">
                        
                        <!-- Immagine -->
                        <div class="col-12 col-lg-2">
                          
                          <div class="card mb-3">
                            <?php if (!empty($row['image_path'])) : ?>
                              <img src="../image/player/<?php echo $row['image_path']; ?>"
                              class="img-fluid  rounded " alt="Immagine attuale">
                              <?php endif; ?>
                          </div>
                            
                          <form action="../query/upload_image.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3 p-3">
                              <label for="formFile" class="form-label">Immagine</label>
                              <input class="form-control" type="file" id="formFile" name="playerImage">
                              <input type="hidden"  id="id" name="id" value="<?php echo $id?>" />
                              <input type="submit" value="Carica" class="btn btn-outline-dark float-end mt-2" name="submit">
                            </div>
                          </form>

                        </div>
                        <!-- END: Immagine -->
                          
                        <div class="col-12 col-lg-10">
                          <form action="../query/action_edit_player.php?id=<?php echo $row['id']; ?>" method="POST">

                            <div class="row gy-4 mb-3 ">
                              
                              <div class="col-12 col-lg-8">
                                <div class="card">
                                  <div class="card-body">
                                    <h3>Info di base</h3>
                                    
                                    <div class="row my-3 g-3">
                                      <!-- Nome -->
                                      <div class="col-12 col-sm-6 col-lg-4 ">
                                        <label for="nome" class="form-label">Nome</label>
                                        <input typer="text" class="form-control" id="nome" name="nome" value="<?php echo $row['nome'];?>" required/>
                                      </div>

                                      <!-- Cognome -->
                                      <div class="col-12 col-sm-6 col-lg-4 ">
                                        <label for="cognome" class="form-label">Cognome</label>
                                        <input typer="text" class="form-control " id="cognome" name="cognome" value="<?php echo $row['cognome'];?>" required/>
                                      </div>

                                      <!-- Data di nascita -->
                                      <div class="col-12 col-sm-6 col-lg-4 ">
                                        <label for="data_nascita" class="form-label">Data di nascita</label>
                                        <input type="date" class="form-control" id="data_nascita" name="data_nascita" value="<?php echo $row['data_nascita'];?>"></input>
                                      </div>

                                      <!-- Squadra -->
                                      <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="squadra" class="form-label">Squadra</label>
                                        <select class="form-select" aria-label="Default select example" name="squadra" id="squadra" >
                                          <option disabled selected>-- Seleziona --</option>
                                          <?php
                                            while ($rowX = mysqli_fetch_assoc($squadre))
                                            {
                                              echo '<option value="'.$rowX['id'].'" '.($rowX['id'] === $row['id_squadra'] ? 'selected="selected"' : '').'>'.$rowX['nome_societa'].'</option>';
                                            }

                                          ?>
                                        </select>
                                      </div>


                                      <!-- Ruolo -->
                                      <div class="col-12 col-sm-6 col-lg-4 ">
                                        <label for="ruolo" class="form-label">Ruolo</label>
                                        <select class="form-select" id="ruolo" name="ruolo">
                                          <option <?php if ($row['ruolo']=='Portiere') { ?>selected="selected"<?php } ?> value="Portiere">
                                            Portiere
                                          </option>

                                          <option <?php if ($row['ruolo']=='Centrale') { ?>selected="selected"<?php } ?> value="Centrale">
                                            Centrale
                                          </option>

                                          <option <?php if ($row['ruolo']=='Laterale') { ?>selected="selected"<?php } ?> value="Laterale">
                                            Laterale
                                          </option>

                                          <option <?php if ($row['ruolo']=='Universale') { ?>selected="selected"<?php } ?> value="Universale">
                                            Universale
                                          </option>

                                          <option <?php if ($row['ruolo']=='Pivot') { ?>selected="selected"<?php } ?> value="Pivot">
                                            Pivot
                                          </option>
                                        </select>
                                      </div>

                                      <!-- Codice fiscale -->
                                      <div class="col-12 col-sm-4">
                                        <label for="codice_fiscale" class="form-label">Codice fiscale</label>
                                        <input type="text"  class="form-control" id="codice_fiscale" name="codice_fiscale" value="<?php echo $row['codice_fiscale'];?>"/>
                                      </div>

                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="col-12 col-lg-4">
                                <div class="card">
                                  <div class="card-body">
                                    <h3>Altre info</h3>

                                    <div class="row my-3 g-3">
                                      <!-- Piede -->
                                      <div class="col-6">
                                        <label for="piede_preferito" class="form-label">Piede:</label>
                                        <select class="form-select" aria-label="Default select example" name="piede_preferito" id="piede_preferito" >
                                          <option value="DX" <?php if($row['piede_preferito'] == 'DX') echo 'selected'; ?> >DX</option>
                                          <option value="SX" <?php if($row['piede_preferito'] == 'SX') echo 'selected'; ?>>SX</option>
                                          <option value="Entrambi" <?php if($row['piede_preferito'] == 'Entrambi') echo 'selected'; ?>>Entrambi</option>
                                        </select>
                                      </div>

                                      <!-- Taglia -->
                                      <div class="col-6 ">
                                        <label for="taglia" class="form-label">Taglia</label>
                                        <select class="form-select " id="taglia" name="taglia">
                                          <option value="XS" <?php if ($row['taglia']=='XS') { ?>selected="selected"<?php } ?> >XS</option>
                                          <option value="S" <?php if ($row['taglia']=='S') { ?>selected="selected"<?php } ?> >S</option>
                                          <option value="M" <?php if ($row['taglia']=='M') { ?>selected="selected"<?php } ?> >M</option>
                                          <option value="L" <?php if ($row['taglia']=='L') { ?>selected="selected"<?php } ?> >L</option>
                                          <option value="XL" <?php if ($row['taglia']=='XL') { ?>selected="selected"<?php } ?> >XL</option>
                                          <option value="XXL" <?php if ($row['taglia']=='XXL') { ?>selected="selected"<?php } ?> >XXL</option>
                                        </select>
                                      </div>

                                      <!-- Altezza -->
                                      <div class="col-6 ">
                                        <label for="altezza" class="form-label">Altezza:</label>
                                        <input type="number" class="form-control" id="altezza" name="altezza" value="<?php echo $row['altezza'];?>" />
                                      </div>
                                      
                                      <!-- Peso -->
                                      <div class="col-6 ">
                                        <label for="peso" class="form-label">Peso:</label>
                                        <input type="number" class="form-control" id="peso" name="peso" value="<?php echo $row['peso'];?>"/>
                                      </div>
                                      
                                    </div>

                                  </div>
                                </div>
                              </div>
                              
                            
                              <div class="col-12 col-lg-8">
                                <div class="card">
                                  <div class="card-body">
                                    <h3>Info contrattuali</h3>

                                    <div class="row my-3 g-3">
                                      <!-- Visita medica -->
                                      <div class="col-6 col-md-3 col-lg-2">
                                        <label for="visita_medica" class="form-label">Visita medica:</label>
                                        <input type="date" class="form-control" id="visita_medica" name="visita_medica" value="" />
                                      </div>
                                    
                                      <!-- Tipo contratto -->
                                      <div class="col-6 col-md-3 col-lg-2 ">
                                        <label for="tipo_contratto" class="form-label">Tipo contratto:</label>
                                        <select class="form-select" aria-label="Default select example" name="tipo_contratto" id="tipo_contratto" >
                                          <option value="Proprietari" <?php if ($row['tipo_contratto']=='Proprietari') { ?>selected="selected"<?php } ?> >Propietari</option>
                                          <option value="Prestito" <?php if ($row['tipo_contratto']=='Prestito') { ?>selected="selected"<?php } ?> >Prestito</option>
                                        </select>
                                      </div>

                                      <!-- Matricola tesseramento -->
                                      <div class="col-6 col-md-3 col-lg-2">
                                        <label for="matricola" class="form-label">Matricola</label>
                                        <input type="text" class="form-control"  id="matricola" name="matricola" value="<?php echo $row['matricola'];?>" />
                                      </div>

                                      <!-- Data tesseramento -->
                                      <div class="col-6 col-md-3 col-lg-2">
                                        <label for="data_tesseramento" class="form-label">Data tesseraemento</label>
                                        <input type="date"  class="form-control" id="data_tesseramento" name="data_tesseramento"  value="<?php if ($row['data_tesseramento'] != '1970-01-01') { echo $row['data_tesseramento']; } ?>"/>
                                      </div>
                                      
                                      <!-- Scadenza -->
                                      <div class="col-6 col-md-3 col-lg-2">
                                        <label for="anno_scadenza_tesseramento" class="form-label">Anno scadenza</label>
                                        <input type="text"  class="form-control" id="anno_scadenza_tesseramento" name="anno_scadenza_tesseramento" value="<?php if ($row['anno_scadenza_tesseramento'] != 0) { echo $row['anno_scadenza_tesseramento']; } ?>" />
                                      </div>

                                      <!-- Numero di maglia -->
                                      <div class="col-3 col-md-2 col-lg">
                                        <label for="maglia" class="form-label">N:</label>
                                        <input typer="text" class="form-control" id="maglia" name="maglia" value="<?php echo $row['maglia'];?>"/>
                                      </div>

                                      <!-- Capitano -->
                                      <div class="col-3 col-md-2 col-lg">
                                        <label for="capitano" class="form-label">Capitano:</label>
                                        <select class="form-select" aria-label="Default select example" name="capitano" id="capitano" >
                                          <option <?php if ($row['capitano']=='Giocatore') { ?>selected="selected"<?php } ?> value="Giocatore">
                                            -
                                          </option>

                                          <option <?php if ($row['capitano']=='VC') { ?>selected="selected"<?php } ?> value="VC">
                                            VC
                                          </option>
                                        
                                          <option <?php if ($row['capitano']=='C') { ?>selected="selected"<?php } ?> value="C">
                                            C
                                          </option>
                                        </select>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                                
                              <div class="col-12 col-lg-4">
                                <div class="card">
                                  <div class="card-body">                        
                                    <!-- Affiliazioni giocatore -->
                                    <div class="row g-3">
                                      <div class="col-12 ">
                                        <h3>Affiliazioni</h3>
                                        <div class="p-3">
                                          <?php
                                          while ($row_societa = mysqli_fetch_assoc($societa_collegate)) :
                                            $checked = in_array($row_societa['id'], $affiliated_player) ? 'checked' : '';
                                          ?>
                                            <div class="form-check">
                                              <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                name="affiliazioni[]" 
                                                value="<?php echo $row_societa['id']; ?>" 
                                                id="affiliazione_<?php echo $row_societa['id']; ?>"
                                                <?php echo $checked; ?>
                                              >
                                              <label class="form-check-label" for="affiliazione_<?php echo $row_societa['id']; ?>">
                                                <?php echo $row_societa['tipo']; ?>
                                              </label>
                                            </div>
                                          <?php endwhile; ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                                
                            <!-- Valore HIDDEN -->
                            <input type="hidden"  id="id" name="id" value="<?php echo $row['id'];?>" />
                            
                            <!-- Submit -->
                            <div class="d-flex justify-content-end mt-3">
                              <button type="submit" class="btn btn-outline-dark">Conferma</button>
                            </div>
                          
                          </form>
                          <br/>
                          <br/>
                          
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

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Allegati</h5>
        <button button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
    
      <div class="offcanvas-body">
        <div>
          <form action="../query/upload.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="formFile" class="form-label">Allegati</label>
              <input class="form-control" type="file" id="formFile" name="formFile">
            </div>
            <input type="hidden"  id="id" name="id" value="<?php echo $id?>" />
            <input type="submit" value="Carica" class="btn btn-outline-dark " name="submit">
          </form>
        </div>  
      </div>
    </div>


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
    <script>
      function confirmDelete(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_immagine.php?id=" + recordId;
        }
      }
  </script>
    
  </body>
</html>