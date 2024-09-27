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
  
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  $id=  $_REQUEST['id'];

  $query="
  SELECT *
  FROM giocatori g
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine is NULL
  ORDER BY g.ruolo,g.cognome,g.nome
  ";

  $giocatori= mysqli_query($con,$query);

  // Ottenere l'elenco dei giocatori registrati per quell'allenamento dal database
  
  $query = "SELECT id_giocatore FROM convocazioni WHERE id_partita = $id";
  $result = mysqli_query($con, $query);

  // Creare un array vuoto per memorizzare gli ID dei giocatori selezionati
  $giocatoriSelezionati = array();

  // Aggiungere gli ID dei giocatori selezionati all'array
  while ($row = mysqli_fetch_assoc($result)) {
    $giocatoriSelezionati[] = $row['id_giocatore'];
  }


  $query2 = "
  SELECT soc.nome_societa as casa, soc2.nome_societa as ospite, golCasa,golOspiti,giornata,s.id,s.data,soc.sede,soc.citta
  FROM `partite` s
  INNER JOIN societa soc on soc.id=s.squadraCasa
  INNER JOIN societa soc2 on soc2.id=s.squadraOspite
  WHERE s.id='$id'
  ";

  $result = mysqli_query($con,$query2);
  $partita = mysqli_fetch_assoc($result);

  $_SESSION['squadra_casa']=$partita['casa'];
  $_SESSION['squadra_ospite']=$partita['ospite'];

  $data_partita=$partita['data'];

  $query = "
  SELECT g.*
  FROM giocatori g
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine is NULL
  AND g.id not in (SELECT i.id
                    FROM indisponibili i
                    WHERE i.da_data<='$data_partita'
                    AND i.a_data>= '$data_partita'
                    )
  ORDER BY ruolo,cognome,nome asc";
  $giocatori = mysqli_query($con,$query);
  $capitano = mysqli_query($con,$query);
  $vicecapitano = mysqli_query($con,$query);



  $query3 = "
  SELECT count(*) as numero_giocatori
  FROM giocatori g
  INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
  WHERE ag.id_societa = '$id_societa'
  AND ag.data_fine is NULL
  AND g.id not in (SELECT i.id
                    FROM indisponibili i
                    WHERE i.da_data<='$data_partita'
                    AND i.a_data>= '$data_partita'
                    )
  ORDER BY ruolo,cognome,nome asc";
  $result = mysqli_query($con,$query3);
  $numero_giocatori = mysqli_fetch_assoc($result);

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
                          Convocazione
                        </h4>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <span class="fs-6 text-muted">
                      <?php echo $partita['casa'] ?> - <?php echo $partita['ospite'] ?>
                    </span>

                    <br/>

                    <small class="text-muted">
                      <?php echo $partita['sede'] ?> - <?php echo date('d-m-Y',strtotime($partita['data'])); ?>
                    </small>

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row mt-3 gy-3">
                        <div class="col-12 col-lg-3">
                          <form action="../query/action_insert_convocati.php?id=<?php echo $id; ?>" method="POST">
                            <!-- Card convocabili -->
                            <div class="card">

                              <div class="card-header bg-dark">
                                <h6 class="text-white"> Convocabili </h6>
                              </div>

                              <div class="card-body">
                                <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>
                                  <div class="form-check">
                                    <!-- CheckBox selezione convocato -->
                                    <input class="form-check-input" name="presenza[]" type="checkbox" value="<?php echo $row['id']; ?>" <?php if (in_array($row['id'], $giocatoriSelezionati)) echo "checked"; ?>>
                                    <!-- Nome Cognome giocatore -->
                                    <label class="form-check-label" for="flexCheckDefault">
                                      <?php echo $row['nome'] .' ' .$row['cognome']; ?>
                                    </label>
                                    <!-- Ruolo -->
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

                              <div class="card-footer">
                                <span class="float-end"> <span id="conteggio">0</span> / <?php echo $numero_giocatori['numero_giocatori'] ?> giocatori selezionati </span>
                              </div>

                            </div>
                          <button type="submit" class="btn btn-sm btn-outline-dark mt-3 float-end float-md-start " id="btn-stampa">Salva convocati</button>
                        </form>
                      </div>
                          

                      <!-- Luogo, Giorno e Dirigenza -->
                      <div class="col-12 col-lg-7" id="info-match" name="info-match" >
                        <form action="genera_pdf.php?id=<?php echo $partita['id'] ?>" method="POST">
                          <div class="card mb-3">
                            <div class="card-header bg-dark">
                              <h4 class="text-white">Info partita</h4>
                            </div>
                            <div class="card-body">
                              
                              <div class="row p-3 g-4">

                                <div class="col-12 col-lg-6 ">
                                  <label for="sede" >Luogo</label>
                                  <input value="<?php echo $partita['sede'] ?>" type="text" class="form-control form-control-sm " id="sede" name="sede"></input>
                                </div>

                                <div class="col-6  col-lg-3">
                                  <label for="citta" >Città</label>
                                  <input value="<?php echo $partita['citta'] ?>" type="text" class="form-control form-control-sm " id="citta" name="citta"></input>
                                </div>

                                <div class="col-6 col-lg-3">
                                  <label for="data" >Giorno</label>
                                  <input value="<?php echo $partita['data'] ?>" type="date" class="form-control form-control-sm " id="data" name="data"></input>
                                </div>
                              
                                <div class="col-6 col-lg-3">
                                  <label for="allenatore" >Allenatore</label>
                                  <input class="form-control form-control-sm"  name="allenatore" id="allenatore" value="Diego Petrolati"></input>
                                </div>
                                
                                    
                                <div class="col-6 col-lg-3">
                                  <label for="doc_allenatore" > Documento </label>
                                  <input class="form-control form-control-sm"  name="doc_allenatore" id="doc_allenatore" value=""></input>
                                </div>

                                <div class="col-6 col-lg-3 ">
                                  <label for="capitano" >Capitano</label>
                                  <select class="form-select form-select-sm" id="capitano" name="capitano">
                                    <?php
                                    // Utilizziamo una nuova variabile $giocatoriCapitano per iterare sui giocatori
                                    while($rowCapitano = mysqli_fetch_assoc($capitano)) {
                                        // Se il ruolo del giocatore è "C", lo segna automaticamente come capitano
                                        $selected = ($rowCapitano['capitano'] === 'C') ? 'selected' : '';
                                        echo '<option value="' . $rowCapitano['id'] . '" ' . $selected . '>' . $rowCapitano['nome'] . ' ' . $rowCapitano['cognome'] . '</option>';
                                    }
                                    ?>
                                  </select>
                                  
                                </div>

                                <div class="col-6  col-lg-3">
                                  <label for="vicecapitano" >Vicecapitano</label>
                                  <select class="form-select form-select-sm" id="vicecapitano" name="vicecapitano">
                                    <?php
                                    // Utilizziamo una nuova variabile $giocatoriCapitano per iterare sui giocatori
                                    while($rowCapitano = mysqli_fetch_assoc($vicecapitano)) {
                                        // Se il ruolo del giocatore è "C", lo segna automaticamente come capitano
                                        $selected = ($rowCapitano['capitano'] === 'VC') ? 'selected' : '';
                                        echo '<option value="' . $rowCapitano['id'] . '" ' . $selected . '>' . $rowCapitano['nome'] . ' ' . $rowCapitano['cognome'] . '</option>';
                                    }
                                    ?>
                                  </select>
                                </div>
                                

                                <div class="col-6 col-lg-3 ">
                                  <label for="dirigente_1" >Dirigente 1</label>
                                  <div class="input-group">
                                    <input class="form-control form-control-sm"  name="dirigente_1" id="dirigente_1" value=""></input>
                                    <input type="hidden"  name="dirigente_1_doc" id="dirigente_1_doc" value=""></input>
                                    <span class="input-group-text dirigenti"  data-bs-toggle="offcanvas" data-bs-target="#dirigenteOffcanvas" data-bs-dirigente="1">
                                      <i class="bi bi-plus align-middle"></i>
                                    </span>
                                  </div>
                                </div>

                                <div class="col-6 col-lg-3 ">
                                  <label for="dirigente_2" >Dirigente 2</label>
                                  <div class="input-group">
                                    <input class="form-control form-control-sm"  name="dirigente_2" id="dirigente_2" value=""></input>
                                    <span class="input-group-text dirigenti"  data-bs-toggle="offcanvas" data-bs-target="#dirigenteOffcanvas" data-bs-dirigente="2">
                                      <i class="bi bi-plus align-middle"></i>
                                    </span>
                                  </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                  <label for="dirigente_3" >Dirigente 3</label>
                                  <div class="input-group">
                                    <input class="form-control form-control-sm"  name="dirigente_3" id="dirigente_3" value=""></input>
                                    <span class="input-group-text dirigenti"  data-bs-toggle="offcanvas" data-bs-target="#dirigenteOffcanvas" data-bs-dirigente="3">
                                      <i class="bi bi-plus align-middle"></i>
                                    </span>
                                  </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                  <label for="dirigente_4" >Dirigente 4</label>
                                  <div class="input-group">
                                    <input class="form-control form-control-sm"  name="dirigente_4" id="dirigente_4" value=""></input>
                                    <span class="input-group-text dirigenti"  data-bs-toggle="offcanvas" data-bs-target="#dirigenteOffcanvas" data-bs-dirigente="4">
                                      <i class="bi bi-plus align-middle"></i>
                                    </span>
                                  </div>
                                </div>

                                <div class="col-6 col-lg-3 ">
                                  <label for="luogo_convocazione" >Luogo convocazione</label>
                                  <input value="" type="text" class="form-control form-control-sm " id="luogo_convocazione" name="luogo_convocazione"></input>
                                </div>

                                <div class="col-6  col-lg-3">
                                  <label for="orario_convocazione" >Orario convocazione</label>
                                  <input value="" type="time" class="form-control form-control-sm " id="orario_convocazione" name="orario_convocazione"></input>
                                </div>

                              </div>
                            </div>
                        </div>
                          
                          <span class="float-end">
                            <button type="submit" class="btn btn-sm btn-outline-dark mb-3 float-end" target="_blank">
                              <i class='bi bi-file-pdf' ></i> Genera PDF
                            </button>
                          </span>

                          <span class="float-end me-2">
                          <a role="button" class="btn btn-sm btn-outline-dark" onclick="inviaTelegram()">
                            <i class="bi bi-telegram"></i> Telegram
                          </a>

                          </span>
                        </form>
                      </div>
                        
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


    <!-- Offcanvas per la selezione del dirigente -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="dirigenteOffcanvas">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">Seleziona il dirigente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="list-group" id="dirigentiList">
            <?php
            // Esempio di generazione dinamica degli elementi della lista
            $sql = "
            select *
            FROM dirigenti
            ";
            $dirigenti = mysqli_query($con,$sql);
            foreach ($dirigenti as $dirigente) {
              echo '<span class="mb-2 text-primary" style="cursor:pointer" data-dirigente-id="' . $dirigente['nome'] . '">' . $dirigente['nome'] . '</span> <small class="text-muted">( '.$dirigente['ruolo'] .' )</small> <hr/>';

            }
            ?>
          </ul>
        </div>
    </div>
    

    <script>
      const dirigentiList = document.getElementById('dirigentiList');
      var dirigenti = document.getElementsByClassName('dirigenti');
      var dirigenteInput; // Dichiarazione della variabile dirigenteInput

      for (var i = 0; i < dirigenti.length; i++) {
        dirigenti[i].addEventListener('click', function() {
          var dataBsDirigente = this.getAttribute('data-bs-dirigente');

          if (dataBsDirigente === '1') {
            dirigenteInput = document.getElementById('dirigente_1');
          } else if (dataBsDirigente === '2') {
            dirigenteInput = document.getElementById('dirigente_2');
          } else if (dataBsDirigente === '3') {
            dirigenteInput = document.getElementById('dirigente_3');
          } else {
            dirigenteInput = document.getElementById('dirigente_4');
          }

          dirigentiList.addEventListener('click', function(event) {
            const selectedDirigente = event.target;
            const dirigenteId = selectedDirigente.dataset.dirigenteId;

            dirigenteInput.value = dirigenteId;

            const offcanvas = document.getElementById('dirigenteOffcanvas');
            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
            offcanvasInstance.hide();
          });
        });
      }
    </script>

    <!-- Aggiungi questo script JavaScript alla tua pagina -->
    <script>
      // Funzione per aggiornare il conteggio delle checkbox selezionate
      function updateConteggio() {
        // Seleziona tutte le checkbox con un determinato nome (sostituisci 'checkbox_name' con il nome corretto)
        const checkboxes = document.querySelectorAll('input[name="presenza[]"]');
        
        // Conta quante checkbox sono selezionate
        let conteggio = 0;
        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                conteggio++;
            }
        });

        if (conteggio === 0) {
          var div = document.getElementById("info-match");
          div.style.display = "none";
        }else{
          var div = document.getElementById("info-match");
          div.style.display = "block";

        }

        
        // Aggiorna il testo nello span con l'ID "conteggio"
        document.getElementById('conteggio').textContent = conteggio;
      }

      // Aggiungi un listener di eventi per ciascuna checkbox
      const checkboxes = document.querySelectorAll('input[name="presenza[]"]');
      checkboxes.forEach((checkbox) => {
          checkbox.addEventListener('change', updateConteggio);
      });

      // Inizializza il conteggio iniziale
      updateConteggio();
    </script>

    <script>
      function inviaTelegram() {
          var id = "<?php echo $id ?>";
          var luogo_convocazione = document.getElementById('luogo_convocazione').value;
          var orario_convocazione = document.getElementById('orario_convocazione').value;
          
          // Reindirizza all'URL di Telegram con i valori come parametri GET
          window.location.href = "convocazioni_telegram.php?id=" + id + "&luogo_convocazione=" + luogo_convocazione + "&orario_convocazione=" + orario_convocazione;
      }
    </script>


    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>

</html>