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

  
  $query = "select nome_societa,id 
  FROM societa 
  ORDER BY nome_societa";
  $squadra = mysqli_query($con,$query);

  $nome=$_REQUEST['nome'];
  $cognome=$_REQUEST['cognome'];
  $team=$_REQUEST['squadra'];
  
  $query2 = "select nome_societa 
  FROM societa 
  WHERE id='$team'
  ORDER BY nome_societa";
  $squadra_player_inserito = mysqli_query($con,$query2);
  $row = mysqli_fetch_assoc($squadra_player_inserito);
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
                          Nuovo giocatore  
                        </h4>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <?php
                            if (isset($_GET['id_squadra'])) {
                              $redirectUrl = 'show_societa.php?id=' . $_GET['id_squadra'];
                            } else {
                              $redirectUrl = 'rosa_admin.php?id_societa=' . $id_societa;
                            }
                          ?>

                          <a type="button" href="<?php echo $redirectUrl; ?>" class="btn btn-sm btn-outline-dark float-end">
                            <i class='bi bi-arrow-left'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                      <?php if($_REQUEST['msg']==='success'){?>
                        <div class="alert alert-secondary" role="alert">
                          Inserito nuovo giocatore: <?php echo $nome .' '. $cognome .' nella squadra ' .$row['nome_societa']?>
                        </div>
                      <?php } ?>
                     

                      <form action="../query/action_insert_player.php" method="POST" enctype="multipart/form-data">
                        <div class="row gy-4 mb-3 ">
                          <div class="col-12 col-lg-8">
                            <div class="card">
                              <div class="card-body">
                                <h4>Info di base</h4>
                                
                                <div class="row my-3 g-3">
                                  <!-- Nome -->
                                  <div class="col-12 col-sm-6 col-lg-4 ">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input typer="text" class="form-control" id="nome" name="nome" required/>
                                  </div>

                                  <!-- Cognome -->
                                  <div class="col-12 col-sm-6 col-lg-4 ">
                                    <label for="cognome" class="form-label">Cognome</label>
                                    <input typer="text" class="form-control " id="cognome" name="cognome" required/>
                                  </div>

                                  <!-- Data di nascita -->
                                  <div class="col-12 col-sm-6 col-lg-4 ">
                                    <label for="data_nascita" class="form-label">Data di nascita</label>
                                    <input type="date" class="form-control" id="data_nascita" name="data_nascita"></input>
                                  </div>

                                  <!-- Squadra -->
                                  <div class="col-12 col-sm-6 col-lg-4">
                                    <label for="squadra" class="form-label">Squadra</label>
                                    <select name="squadra" class="form-select" >
                                      <?php
                                      // Ottenere l'id_squadra dalla richiesta
                                      $selectedTeamID = isset($_GET['id_squadra']) ? $_GET['id_squadra'] : null;

                                      // Eseguire la query per ottenere l'elenco delle squadre disponibili
                                      $querySquadre = "SELECT id, nome_societa FROM societa";
                                      $resultSquadre = mysqli_query($con, $querySquadre);

                                      // Iterare attraverso le squadre e creare le opzioni
                                      while ($rowSquadra = mysqli_fetch_assoc($resultSquadre)) {
                                        $optionValue = $rowSquadra['id'];
                                        $optionText = $rowSquadra['nome_societa'];

                                        // Determina se questa opzione dovrebbe essere selezionata
                                        $isSelected = ($selectedTeamID == $optionValue) ? 'selected' : '';

                                        // Stampa l'opzione nella select
                                        echo '<option value="' . htmlspecialchars($optionValue) . '" ' . $isSelected . '>' . htmlspecialchars($optionText) . '</option>';
                                      }
                                      ?>
                                      <!-- Altre opzioni della select -->
                                    </select>
                                  </div>


                                  <!-- Ruolo -->
                                  <div class="col-12 col-sm-6 col-lg-4 ">
                                    <label for="ruolo" class="form-label">Ruolo</label>
                                    <select class="form-select" id="ruolo" name="ruolo">
                                        <option value="Portiere">Portiere</option>
                                        <option value="Centrale">Centrale</option>
                                        <option value="Laterale">Laterale</option>
                                        <option value="Universale">Universale</option>
                                        <option value="Pivot">Pivot</option>
                                    </select>
                                  </div>

                                  <!-- Codice fiscale -->
                                  <div class="col-12 col-sm-6 col-lg-4">
                                    <label for="codice_fiscale" class="form-label">Codice fiscale</label>
                                    <input type="text"  class="form-control" id="codice_fiscale" name="codice_fiscale" />
                                  </div>

                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="col-12 col-lg-4">
                            <div class="card">
                              <div class="card-body">
                                <h4>Altre info</h4>

                                <div class="row my-3 g-3">
                                  <!-- Piede -->
                                  <div class="col-6">
                                    <label for="piede_preferito" class="form-label">Piede:</label>
                                    <select class="form-select" aria-label="Default select example" name="piede_preferito" id="piede_preferito" >
                                      <option value="DX">DX</option>
                                      <option value="SX">SX</option>
                                      <option value="Entrambi">Entrambi</option>
                                    </select>
                                  </div>

                                  <!-- Taglia -->
                                  <div class="col-6 ">
                                    <label for="taglia" class="form-label">Taglia</label>
                                    <select class="form-select " id="taglia" name="taglia">
                                      <option value="XS">XS</option>
                                      <option value="S">S</option>
                                      <option value="M">M</option>
                                      <option value="L">L</option>
                                      <option value="XL">XL</option>
                                      <option value="XXL">XXL</option>
                                    </select>
                                  </div>

                                  <!-- Altezza -->
                                  <div class="col-6 ">
                                    <label for="altezza" class="form-label">Altezza:</label>
                                    <input type="number" class="form-control" id="altezza" name="altezza" value="" />
                                  </div>
                                  
                                  <!-- Peso -->
                                  <div class="col-6 ">
                                    <label for="peso" class="form-label">Peso:</label>
                                    <input type="number" class="form-control" id="peso" name="peso" value="" />
                                  </div>
                                  
                                </div>

                              </div>
                            </div>
                          </div>
                            
                          
                          <div class="col-12">
                            <div class="card">
                              <div class="card-body">
                                <h4>Info contrattuali</h4>

                                  <div class="row my-3 g-3">
                                    <!-- Visita medica -->
                                    <div class="col-6 col-lg-2">
                                      <label for="visita_medica" class="form-label">Visita medica:</label>
                                      <input type="date" class="form-control" id="visita_medica" name="visita_medica" value="" />
                                    </div>
                                  
                                    <!-- Tipo contratto -->
                                    <div class="col-6 col-lg-2 ">
                                      <label for="tipo_contratto" class="form-label">Tipo contratto:</label>
                                      <select class="form-select" aria-label="Default select example" name="tipo_contratto" id="tipo_contratto" >
                                        <option value="Proprietari">Proprietari</option>
                                        <option value="Prestito">Prestito</option>
                                      </select>
                                    </div>

                                    <!-- Matricola tesseramento -->
                                    <div class="col-6 col-lg-2">
                                      <label for="matricola" class="form-label">Matricola</label>
                                      <input type="text" class="form-control"  id="matricola" name="matricola" />
                                    </div>

                                    <!-- Data tesseramento -->
                                    <div class="col-6 col-lg-2">
                                      <label for="data_tesseramento" class="form-label">Data tesseraemento</label>
                                      <input type="date"  class="form-control" id="data_tesseramento" name="data_tesseramento" />
                                    </div>
                                    
                                    <!-- Scadenza -->
                                    <div class="col-6 col-lg-2">
                                      <label for="anno_scadenza_tesseramento" class="form-label">Anno scadenza</label>
                                      <input type="text"  class="form-control" id="anno_scadenza_tesseramento" name="anno_scadenza_tesseramento" />
                                    </div>

                                    <!-- Numero di maglia -->
                                    <div class="col-3 col-lg">
                                      <label for="maglia" class="form-label">N:</label>
                                      <input typer="text" class="form-control" id="maglia" name="maglia"/>
                                    </div>

                                    <!-- Capitano -->
                                    <div class="col-3 col-lg">
                                      <label for="capitano" class="form-label">Capitano:</label>
                                      <select class="form-select" aria-label="Default select example" name="capitano" id="capitano" >
                                        <option value="Giocatore">-</option>
                                        <option value="VC">VC</option>
                                        <option value="C">C</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                          <!-- Submit -->
                          <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-outline-dark mt-2">Conferma</button>
                          </div>
                          
                        </div>

                      </form>
                    </div>
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
    

  </body>
</html>