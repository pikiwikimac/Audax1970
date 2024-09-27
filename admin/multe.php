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


  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT
  g.nome, 
  g.cognome,
  g.id,
  COALESCE(multe.totale_multe, 0) AS totale_multe,
  COALESCE(pagamenti.totale_versato, 0) AS totale_versato
  FROM
    giocatori g
  LEFT JOIN (
    SELECT
        id_giocatore,
        SUM(importo) AS totale_multe
    FROM
        multe
    GROUP BY
        id_giocatore
  ) AS multe ON g.id = multe.id_giocatore
  LEFT JOIN (
    SELECT
        id_giocatore,
        SUM(importo) AS totale_versato
    FROM
        pagamenti
    GROUP BY
        id_giocatore
  ) AS pagamenti ON g.id = pagamenti.id_giocatore
  WHERE
    g.id_squadra = 1
  ORDER BY totale_multe DESC,totale_versato DESC
  ";
  
  $giocatori = mysqli_query($con,$query);

  $query_multe = "
  SELECT COALESCE(SUM(m.importo),0) as totale_multe
  FROM multe m";

  $result = mysqli_query($con,$query_multe);
  $multe = mysqli_fetch_assoc($result);

  $query_pagamenti = "
  SELECT COALESCE(SUM(p.importo),0) as totale_pagamenti
  FROM pagamenti p";

  $result = mysqli_query($con,$query_pagamenti);
  $pagamenti = mysqli_fetch_assoc($result);

  $plus_minus = (int)$pagamenti - (int)$multe;


  #Query che seleziona tutti i giocatori di una determinata squadra
  $queryDirigenti = "
  SELECT
  d.nome, 
  COALESCE(multe.totale_multe, 0) AS totale_multe,
  COALESCE(pagamenti.totale_versato, 0) AS totale_versato
  FROM
    dirigenti d
  LEFT JOIN (
    SELECT
        id_dirigente,
        SUM(importo) AS totale_multe
    FROM
        multe_dirigenza
    GROUP BY
    id_dirigente
  ) AS multe ON d.id = multe.id_dirigente
  LEFT JOIN (
    SELECT
    id_dirigente,
        SUM(importo) AS totale_versato
    FROM
        pagamenti_dirigenza
    GROUP BY
    id_dirigente
  ) AS pagamenti ON d.id = pagamenti.id_dirigente
  
  ORDER BY totale_multe DESC,totale_versato DESC
  ";
  
  $dirigenti = mysqli_query($con,$queryDirigenti);

  $query_multe_dirigenza = "
  SELECT COALESCE(SUM(m.importo),0) as totale_multe
  FROM multe_dirigenza m";

  $result_dirigenza = mysqli_query($con,$query_multe_dirigenza);
  $multe_dirigenza = mysqli_fetch_assoc($result_dirigenza);

  $query_pagamenti_dirigenza = "
  SELECT COALESCE(SUM(p.importo),0) as totale_pagamenti
  FROM pagamenti_dirigenza p";

  $result_dirigenza = mysqli_query($con,$query_pagamenti_dirigenza);
  $pagamenti_dirigenza = mysqli_fetch_assoc($result_dirigenza);

  $plus_minus_dirigenza = (int)$pagamenti_dirigenza - (int)$multe_dirigenza;

  $plus_minus_totale = (int)$plus_minus + (int)$plus_minus_dirigenza;
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
                          Multe
                        </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a class="text-decoration-none text-dark"  data-bs-toggle="offcanvas"   href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                            <i class='bi bi-info-circle' ></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->


                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row gy-3 mb-3">
                        
                        <div class="col-6 col-md-4">
                          <div class="card">
                            <div class="card-header bg-dark text-white">
                              Totale multe
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-12">
                                  <!-- Prossimo match -->
                                  <span class="fs-3 fw-bold">
                                    <?php echo $multe['totale_multe'] + $multe_dirigenza['totale_multe'] ?> €
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="card">
                            <div class="card-header bg-dark text-white">
                              Totale versato
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-12">
                                  <!-- Prossimo match -->
                                  <span class="fs-3 fw-bold">
                                      <?php echo $pagamenti['totale_pagamenti'] + $pagamenti_dirigenza['totale_pagamenti']  ?> €
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-6 col-md-4">
                          <div class="card">
                            <div class="card-header bg-dark text-white">
                              +/- 
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-12">
                                  <!-- Prossimo match -->
                                  <span class="fs-3 fw-bold">
                                      <?php echo $plus_minus_totale ?> €
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>


                      <div class="row g-3 my-3 ">
                        <div class="col-12 col-md-6 table-responsive">
                          <h4>
                            Multe giocatori
                            <a type="button" class="btn btn-sm btn-outline-dark float-end me-2"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModalPagamento" data-bs-info="dirigenza">
                              <i class="bi bi-plus"></i> Pagamento
                            </a>
                              
                            <a type="button" class="btn btn-sm btn-outline-dark float-end me-2"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModal" data-bs-info="dirigenza">
                              <i class="bi bi-plus"></i> Multa
                            </a>
                          </h4>
                          <table class="table table-sm table-hover table-striped table-rounded mt-3 " >
                            
                            <thead class="table-dark">
                              <tr>
                                <th>Nome</th>
                                <th class="text-end">Tot multe annuale</th>
                                <th class="text-end">Versato</th>
                                <th class="text-end">+/- Da versare</th>
                              </tr>
                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                              <tr >
                                <!-- Nome e Cognome -->
                                <td onclick="window.location='multe_player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row['cognome'] . '  ' .$row['nome']  ?>
                                </td>

                                <!-- Pulsante totale  -->
                                <td class="text-end fw-bold">
                                  <?php echo $row['totale_multe'] ?>
                                </td>

                                <!-- Pulsante versato  -->
                                <td class="text-end text-success">
                                  <?php echo $row['totale_versato'] ?>
                                </td>

                                <!-- Pulsante da versare -->
                                <td class="text-end text-danger">
                                  <?php echo ($row['totale_multe'] - $row['totale_versato']) ?>
                                </td>
                                
                                
                              </tr>
                              <?php } ?>

                            </tbody>

                          </table>
                        </div>
                      
                      
                        <div class="col-12 col-md-6 table-responsive">
                          <h4> 
                            Multe dirigenti 
                            <a type="button" class="btn btn-sm btn-outline-dark float-end me-2"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModalPagamentoDirigenza" data-bs-info="dirigenza">
                              <i class="bi bi-plus"></i> Pagamento
                            </a>
                              
                            <a type="button" class="btn btn-sm btn-outline-dark float-end me-2"  data-bs-toggle="modal" data-bs-title="Insert"  data-bs-target="#insertModalDirigenza" data-bs-info="dirigenza">
                              <i class="bi bi-plus"></i> Multa
                            </a>
                          </h4>
                          
                          <table class="table table-sm table-hover table-striped table-rounded mt-3 " >
                            
                            <thead class="table-dark">
                              <tr>
                                <th>Nome</th>
                                <th class="text-end">Tot multe annuale</th>
                                <th class="text-end">Versato</th>
                                <th class="text-end">+/- Da versare</th>
                              </tr>
                            </thead>

                            <tbody>

                              <?php while($row2 = mysqli_fetch_assoc($dirigenti)) {  ?>
                              <tr >
                                <!-- Nome e Cognome -->
                                <td onclick="window.location='multe_player.php?id=<?php echo $row2['id']; ?>';" style="cursor:pointer" >
                                  <?php echo $row2['nome']   ?>
                                </td>

                                <!-- Pulsante totale  -->
                                <td class="text-end fw-bold">
                                  <?php echo $row2['totale_multe'] ?>
                                </td>

                                <!-- Pulsante versato  -->
                                <td class="text-end text-success">
                                  <?php echo $row2['totale_versato'] ?>
                                </td>

                                <!-- Pulsante da versare -->
                                <td class="text-end text-danger">
                                  <?php echo ($row2['totale_multe'] - $row2['totale_versato']) ?>
                                </td>
                                
                                
                              </tr>
                              <?php } ?>

                            </tbody>

                          </table>
                          
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


    <!-- Modal Insert Multa-->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Inserisci multa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="formMulta" method="post" action="../query/action_insert_multa.php">
              <div class="row">
                <!-- Data -->
                <div class="col-6 mb-3 ">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data" name="data"  value="<?php echo date('Y-m-d'); ?>"/>
                </div>

                <!-- Importo -->
                <div class="col-6 mb-3">
                  <label for="orario" class="form-label">Importo</label>
                  <input type="number" class="form-control" id="importo" name="importo" />
                </div>

                <!-- Giocatore -->
                <div class="col-12 mb-3">
                <label for="giocatore" class="form-label">Giocatore</label>
                  <select class="form-select" id="giocatore" name="giocatore" required>
                    <option selected disabled>-- Seleziona un giocatore --</option>
                    <?php
                     // Query per ottenere l'elenco dei giocatori
                     $query_giocatori = "SELECT * FROM giocatori g INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
                      WHERE ag.id_societa = '$id_societa'
                      AND ag.data_fine is NULL";
                     $result_giocatori = mysqli_query($con, $query_giocatori);
                     while ($row_giocatore = mysqli_fetch_assoc($result_giocatori)) {
                       echo "<option value='" . $row_giocatore['id'] . "'>" . $row_giocatore['nome'] . " " . $row_giocatore['cognome'] . "</option>";
                     }
                    ?>
                  </select>
                </div>


              </div>
              <!-- Aggiungi altri campi per l'inserimento se necessario -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitMulta()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Insert Pagamento multa-->
    <div class="modal fade" id="insertModalPagamento" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Inserisci pagamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="formPagamento" method="post" action="../query/action_insert_pagamento.php">
              <div class="row">
                <!-- Data -->
                <div class="col-6 mb-3 ">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data_pagamento" name="data_pagamento"  value="<?php echo date('Y-m-d'); ?>"/>
                </div>

                <!-- Importo -->
                <div class="col-6 mb-3">
                  <label for="orario" class="form-label">Importo</label>
                  <input type="number" class="form-control" id="importo_pagamento" name="importo_pagamento" />
                </div>

                <!-- Giocatore -->
                <div class="col-12 mb-3">
                <label for="giocatore" class="form-label">Giocatore</label>
                  <select class="form-select" id="giocatore_pagamento" name="giocatore_pagamento" required>
                    <option selected disabled>-- Seleziona un giocatore --</option>
                    <?php
                     // Query per ottenere l'elenco dei giocatori
                     $query_giocatori = "SELECT * FROM giocatori g INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
                      WHERE ag.id_societa = '$id_societa'
                      AND ag.data_fine is NULL";
                     $result_giocatori = mysqli_query($con, $query_giocatori);
                     while ($row_giocatore = mysqli_fetch_assoc($result_giocatori)) {
                       echo "<option value='" . $row_giocatore['id'] . "'>" . $row_giocatore['nome'] . " " . $row_giocatore['cognome'] . "</option>";
                     }
                    ?>
                  </select>
                </div>
                

              </div>
              <!-- Aggiungi altri campi per l'inserimento se necessario -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitPagamento()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Insert Multa Dirigenza-->
    <div class="modal fade" id="insertModalDirigenza" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Inserisci multa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="formMultaDirigenza" method="post" action="../query/action_insert_multa_dirigenza.php">
              <div class="row">
                <!-- Data -->
                <div class="col-6 mb-3 ">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data" name="data"  value="<?php echo date('Y-m-d'); ?>"/>
                </div>

                <!-- Importo -->
                <div class="col-6 mb-3">
                  <label for="orario" class="form-label">Importo</label>
                  <input type="number" class="form-control" id="importo" name="importo" />
                </div>

                <!-- Giocatore -->
                <div class="col-12 mb-3">
                <label for="dirigente" class="form-label">Dirigente</label>
                  <select class="form-select" id="dirigente" name="dirigente" required>
                    <option selected disabled>-- Seleziona un giocatore --</option>
                    <?php
                     // Query per ottenere l'elenco dei giocatori
                     $query_dirigenti = "SELECT * FROM dirigenti ";
                     $result_dirigenti = mysqli_query($con, $query_dirigenti);
                     while ($row_dirigente = mysqli_fetch_assoc($result_dirigenti)) {
                       echo "<option value='" . $row_dirigente['id'] . "'>" . $row_dirigente['nome'] . "</option>";
                     }
                    ?>
                  </select>
                </div>


              </div>
              <!-- Aggiungi altri campi per l'inserimento se necessario -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitMultaDirigenza()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Insert Pagamento multa Dirigenza-->
    <div class="modal fade" id="insertModalPagamentoDirigenza" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="insertModalLabel">Inserisci pagamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Qui inserisci il form per l'inserimento della nuova partita -->
            <!-- Ad esempio, puoi creare un form che invia i dati tramite POST -->
            <!-- Ecco un esempio di form -->
            <form id="formPagamentoDirigenza" method="post" action="../query/action_insert_pagamento_dirigenza.php">
              <div class="row">
                <!-- Data -->
                <div class="col-6 mb-3 ">
                  <label for="data" class="form-label">Data</label>
                  <input type="date" class="form-control" id="data_pagamento" name="data_pagamento"  value="<?php echo date('Y-m-d'); ?>"/>
                </div>

                <!-- Importo -->
                <div class="col-6 mb-3">
                  <label for="orario" class="form-label">Importo</label>
                  <input type="number" class="form-control" id="importo_pagamento" name="importo_pagamento" />
                </div>

                <!-- Giocatore -->
                <div class="col-12 mb-3">
                <label for="dirigente_pagamento" class="form-label">Dirigente</label>
                  <select class="form-select" id="dirigente_pagamento" name="dirigente_pagamento" required>
                    <option selected disabled>-- Seleziona un giocatore --</option>
                    <?php
                     // Query per ottenere l'elenco dei giocatori
                     $query_dirigenti = "SELECT * FROM dirigenti ";
                     $result_dirigenti = mysqli_query($con, $query_dirigenti);
                     while ($row_dirigente = mysqli_fetch_assoc($result_dirigenti)) {
                       echo "<option value='" . $row_dirigente['id'] . "'>" . $row_dirigente['nome']  ."</option>";
                     }
                    ?>
                  </select>
                </div>
                

              </div>
              <!-- Aggiungi altri campi per l'inserimento se necessario -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="submitPagamentoDirigenza()">Inserisci</button>
          </div>
        </div>
      </div>
    </div>



    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Legenda multe</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <table class="table table-sm table-striped mb-5" >
          <thead>
            <tr>
              <th>Sanzione</th>
              <th>Importo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Espulsione per reazione </td>
              <td><span class="float-end fw-bold">5 €</span></td>
            </tr>
            
            <tr>
              <td>Ritardo non comunicato entro due ore </td>
              <td><span class="float-end fw-bold">2 €</span></td>
            </tr> 
            
            <tr>
              <td>Assenza non comunicato entro due ore</td>
              <td><span class="float-end fw-bold">2 €</span></td>
            </tr>
            
            <tr>
              <td>Assenza non comunicata</td>
              <td><span class="float-end fw-bold">5 €</span></td>
            </tr>
            
            <tr>
              <td>Ritardo convocazione</td>
              <td><span class="float-end fw-bold">5 €</span></td>
            </tr>

            <tr>
              <td>Materiale tecnico non indossato(allenamenti e partita)</td>
              <td><span class="float-end fw-bold">2 €</span></td>
            </tr>
            
            <tr>
              <td>Sconfitta partitella fine allenamento</td>
              <td><span class="float-end fw-bold">1 €</span></td>
            </tr>
            
          </tbody>
        </table>

        <div class=" mb-3">
          <span class="fst-italic">
            Le multe sono valide per tutti i dirigenti e giocatori.
            <br/>
            Gli importi per il capitano e mister sono raddoppiati.
          </span>
        </div>
        
      <div class=" mb-3">
        <span class=" fst-italic">
          Le multe per ritardi, assenze ed espulsioni saranno devolute in beneficienza ad associazioni locali.
          <br/>
          Le sanzioni riguardanti le partitelle e il materiale tecnico verranno utilizzate per cene e aperitivi.
        </span>
      </div>
     
       

      </div>
    </div>

     
    <script>
      function submitMulta() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("formMulta").submit();
      }
      
      function submitPagamento() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("formPagamento").submit();
      }
      
      function submitMultaDirigenza() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("formMultaDirigenza").submit();
      }
      function submitPagamentoDirigenza() {
        // Effettua la richiesta di inserimento al server tramite il form
        document.getElementById("formPagamentoDirigenza").submit();
      }
    </script>

    

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
   
  </body>
</html> 