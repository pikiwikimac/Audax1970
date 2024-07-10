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

  $id=$_REQUEST['id'];

  $query = "
  SELECT g.*, s.nome_societa,
  (
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p
    ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione = 1
    
  ) AS numero_ammonizioni,
  (
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p
    ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione = 1
  ) AS numero_espulsioni,
  (
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p
    ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione = 1
  ) AS numero_gol,
  (
    SELECT count(*) as convocazioni
    FROM convocazioni c
    INNER JOIN partite p on p.id=c.id_partita
    WHERE c.id_giocatore=g.id
    AND p.id_stagione = 1
  ) as convocazioni,stag.descrizione as competizione, stag.girone,stag.id_stagione
  
  FROM giocatori g
  INNER JOIN societa s on s.id=id_squadra
  INNER JOIN stagioni stag on stag.id_stagione=s.id_campionato
  WHERE g.id=$id 
  ORDER BY ruolo,cognome,nome asc;";
  $giocatore = mysqli_query($con,$query);
  $row = mysqli_fetch_assoc($giocatore);
  
  
  $query_coppa = "
  SELECT g.*, s.nome_societa,
  (
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p
    ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione = 2
    
  ) AS numero_ammonizioni,
  (
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p
    ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione = 2
  ) AS numero_espulsioni,
  (
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p
    ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione = 2
  ) AS numero_gol,
  (
    SELECT count(*) as convocazioni
    FROM convocazioni c
    INNER JOIN partite p on p.id=c.id_partita
    WHERE c.id_giocatore=g.id
    AND p.id_stagione = 2
  ) as convocazioni
  
  FROM giocatori g
  INNER JOIN societa s on s.id=id_squadra 
  WHERE g.id=$id 
  ORDER BY ruolo,cognome,nome asc";
  $giocatore_coppa = mysqli_query($con,$query_coppa);
  $row_coppa = mysqli_fetch_assoc($giocatore_coppa);


  $query_indisponibili = "
  SELECT g.nome , g.cognome , i.*
  FROM indisponibili i
  INNER JOIN giocatori g  on g.id=i.id_giocatore
  WHERE g.id =$id
  ";
  $indisponibili = mysqli_query($con,$query_indisponibili);

  $query = "
  SELECT count(*) as tot_allenamenti
  FROM allenamenti a
  WHERE a.stato='Svolto'
  ";
  $result = mysqli_query($con,$query);
  $tot_allenamenti_svolti = mysqli_fetch_assoc($result);
  
  $query = "
  SELECT count(*) as tot_player
  FROM partecipazione_allenamenti pa
  WHERE pa.id_giocatore='$id'
  ";
  $result2 = mysqli_query($con,$query);
  $tot_allenamenti_svolti_player = mysqli_fetch_assoc($result2);

  $query="
    SELECT s.nome_societa, s.tipo
    FROM societa s
    INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
    WHERE ag.id_giocatore = $id
  ";
  $squadre_giocatore = mysqli_query($con,$query);
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
                          <?php echo $row['nome']. ' ' . $row['cognome'] ?>
                        </h1>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <!-- Edit button -->    
                          <?php if($_SESSION['superuser'] === 1 ){?>      
                          <a type="button" href="edit_player.php?id=<?php echo $id; ?>" class="btn btn-outline-dark float-end" >
                            <i class='bx bx-pencil'></i>
                          </a>                             
                          <?php } ?>                             
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <!-- Visualizzazione a card -->
                      <div class="row  ">
                        <div class="col-12 col-lg-2  ">
                          <div class="row gy-3">
                            <!-- Foto -->
                            <div class="col-12 col-sm-8 col-lg-12">
                              <?php if ($row['image_path']) { ?>
                                <img src="../image/player/<?php echo $row['image_path'];?>" class="rounded img-fluid "  width="500" height="500" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
                              <?php } else { ?>
                                <img src="../image/default_user.jpg" class="rounded img-fluid "  width="500" height="500" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>"/>
                              <?php } ?>
                            </div>
                            <!-- Sezione allenamenti -->
                            <div class="col-12 col-sm-4 col-lg-12">
                              <div class="row gy-3">
                                <?php if($row['nome_societa']==='Audax 1970'){?>
                                  <!-- Grafico -->
                                  <div class="col-12">
                                    <div class="card">
                                      <div class="card-header bg-dark text-white">
                                        % Presenze
                                      </div>
                                      <div class="card-body  ">
                                        <div class="row">
                                          <div class="col-6 ">
                                            <!-- Allenamenti totali -->
                                            <h6 class="my-auto">
                                              Allenamenti 
                                            </h6>
                                            <span class="fs-2 ">
                                              <?php echo $tot_allenamenti_svolti_player['tot_player'] ?> 
                                            </span>
                                            <span class="fs-6 text-muted">
                                              su <?php echo $tot_allenamenti_svolti['tot_allenamenti'] ?> totali
                                            </span>
                                          </div>
                                          <div class="col-6">
                                            <canvas id="myChart"></canvas>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <?php }?>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-lg-10 ps-lg-5 ">
                          <div class="row gy-2 mt-3">
                            <!-- Nome -->
                            <div class="col-6 col-md-8">
                              <div class="form-floating mb-3" >
                                <input type="text" readonly class="form-control-plaintext mt-3" id="" placeholder="<?php echo $row['nome_societa']  ?>" value="<?php echo $row['nome_societa']  ?>">
                                <label style="margin-top:-10px">Squadra:</label>
                                <?php if(mysqli_num_rows($squadre_giocatore )>1)  { ?>
                                <?php while($squadra = mysqli_fetch_assoc($squadre_giocatore)) {  ?>
                                  <span class="badge bg-secondary"> <?php echo $squadra['tipo'] ?> </span>
                                <?php } ?>
                                <?php } ?>
                              </div> 
                            </div>

                            <div class="col-6 col-md-4">
                              <div class="float-end">
                                <?php if($row['capitano'] != '' ){ ?>
                                  <!-- Capitano -->
                                  <span class="badge bg-danger text-light fw-bold p-2 " >
                                    &nbsp; <?php echo $row['capitano']  ?> &nbsp; 
                                  </span>
                                <?php }?>
                                <?php if($row['maglia'] != null ){ ?>
                                    <!-- Maglia -->
                                    <span class="badge bg-dark  text-light fw-bold p-2 " >
                                      <?php echo $row['maglia']  ?>
                                    </span>
                                <?php }?>
                              </div>
                            </div>
                            
                            <!-- Ruolo -->
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" placeholder="<?php echo $row['ruolo']  ?>" value="<?php echo $row['ruolo']  ?>">
                                <label style="margin-top:-10px">Ruolo:</label>
                              </div>
                            </div>

                            <!-- Piede -->
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext"  placeholder="<?php echo $row['piede_preferito']  ?>" value="<?php echo $row['piede_preferito']  ?>">
                                <label style="margin-top:-10px">Piede:</label>
                              </div>
                            </div>
                            
                            <!-- Altezza -->
                            <?php if($row['altezza'] != null ){?>
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['altezza']  ?> cm" value="<?php echo $row['altezza']  ?> cm">
                                <label style="margin-top:-10px">Altezza:</label>
                              </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Peso -->
                            <?php if($row['peso'] != null ){?>
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['peso']  ?> kg" value="<?php echo $row['peso']  ?> kg"> 
                                <label style="margin-top:-10px">Peso:</label>
                              </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Data nascita -->
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" 
                                placeholder="<?php if($row['data_nascita']==='0000-00-00'){
                                      echo '-';
                                    }else{
                                      echo date('d/m/y',strtotime($row['data_nascita']));
                                    } ?>" 
                                value="<?php if($row['data_nascita']==='0000-00-00'){
                                      echo '-';
                                    }else{
                                      echo date('d/m/y',strtotime($row['data_nascita']));
                                    } ?>">
                                <label style="margin-top:-10px">Data nascita:</label>
                              </div>
                            </div>

                            <!-- Visita medica -->
                            <?php if($row['visita_medica'] != '1970-01-01' ){?>
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['visita_medica']  ?>" value="<?php echo date('d/m/y',strtotime($row['visita_medica'])) ?>">
                                <label style="margin-top:-10px">Scadenza visita medica:</label>
                              </div> 
                            </div>
                            <?php } ?>

                            <!-- Tipo contratto -->
                            <div class="col-6 col-md-3">
                              <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $row['tipo_contratto']  ?>" value="<?php echo $row['tipo_contratto']  ?>">
                                <label style="margin-top:-10px">Contratto:</label>
                              </div> 
                            </div>
                            
                            
                            <div class="col-12 table-responsive mt-5">
                              <h4>Stagione attuale</h4>
                              <table class="table table-sm table-hover table-striped table-rounded">
                                <thead class="table-dark">
                                <tr>
                                  <th class="">Competizione</th>
                                  <th class="text-center"><i class='bx bxs-t-shirt align-middle'></i></th>
                                  <th class="text-center"><i class='bx bx-football align-middle'></i></th>
                                  <th class="text-center"><i class='bx bxs-card align-middle' style='color:#ffb900'  ></i></th>
                                  <th class="text-center"><i class='bx bxs-card align-middle' style='color:#FF0000'  ></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <!-- Competizione -->
                                    <td class="">
                                      <?php echo $row['competizione'] ?> - Girone <?php echo $row['girone'] ?>
                                    </td>
                                    <!-- Convocazioni -->
                                    <td class="text-center">
                                      <?php if($row['convocazioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row['convocazioni'] ;
                                      } ?>
                                    </td>

                                    <!-- Numero gol -->
                                    <td class="text-center">
                                      <?php if($row['numero_gol']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row['numero_gol'] ;
                                      } ?>
                                      
                                    </td>

                                    <!-- Numero ammonizioni -->
                                    <td class="text-center">
                                      <?php if($row['numero_ammonizioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row['numero_ammonizioni'] ;
                                      } ?>
                                      
                                    </td>

                                    <!-- Numero espulsioni -->
                                    <td class="text-center">
                                      <?php if($row['numero_espulsioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row['numero_espulsioni'] ;
                                      } ?>
                                    </td>
                                  </tr>
                                  <!-- Coppa marche -->
                                  <?php if($row['id_stagione']==='1'){?>
                                  
                                  <tr>
                                    <td class="">
                                      Coppa marche
                                    </td>
                                    
                                    <!-- Convocazioni -->
                                    <td class="text-center">
                                      <?php if($row_coppa['convocazioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row_coppa['convocazioni'] ;
                                      } ?>
                                      
                                    </td>

                                    <!-- Numero gol -->
                                    <td class="text-center">
                                      <?php if($row_coppa['numero_gol']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row_coppa['numero_gol'] ;
                                      } ?>
                                      
                                    </td>

                                    <!-- Numero ammonizioni -->
                                    <td class="text-center">
                                      <?php if($row_coppa['numero_ammonizioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row_coppa['numero_ammonizioni'] ;
                                      } ?>
                                      
                                    </td>

                                    <!-- Numero espulsioni -->
                                    <td class="text-center">
                                      <?php if($row_coppa['numero_espulsioni']==='0'){
                                        echo '-';
                                      }else{
                                        echo $row_coppa['numero_espulsioni'] ;
                                      } ?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>  
                            <?php if(mysqli_num_rows($indisponibili )>0)  { ?>
                            <div class="col-12 table-responsive mt-5">
                              <h4>Storico indisponibilità</h4>
                              <table class="table table-sm table-hover table-striped table-rounded">
                                <thead class="table-dark">
                                <tr>
                                  <th class="text-center"><i class='bx bx-calendar '></i> &nbsp; Da</th>
                                  <th class="text-center"><i class='bx bx-calendar '></i> &nbsp;  A</th>
                                  <th class="">Motivo</th>
                                  <th class="">Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                  <?php while($row = mysqli_fetch_assoc($indisponibili)) {  ?>
                                  <tr>
                                    <!-- Da -->
                                    <td class="text-center">
                                      <?php echo date('d/m/y',strtotime($row['da_data']));?>
                                    </td> 
                                    <!-- A -->
                                    <td class="text-center">
                                      
                                      <?php echo date('d/m/y',strtotime($row['a_data']));?>
                                    </td> 
                                    
                                    <!-- A -->
                                    <td class="">
                                      <?php echo $row['motivo'] ;?>
                                    </td> 
                                    
                                    <!-- Note -->
                                    <td class="">
                                      <?php echo $row['note'] ;?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>  
                            <?php } ?>
                            <!-- Allegati -->
                            <?php
                              $directory = "uploads/$id/"; // Sostituisci con la tua cartella di destinazione

                              // Ottieni la lista dei file nella directory
                              $files = scandir($directory);

                              // Verifica se ci sono allegati
                              if ($files !== false && count($files) > 2) { // La condizione > 2 tiene conto di "." e ".."
                                  echo '<!-- Allegati -->
                                  <div class="col-12 table-responsive ">
                                      <table class="table table-rounded">
                                          <thead class="table-dark">
                                              <tr>
                                                  <th>Allegato</th>
                                                  <th class="text-end">Azioni</th>
                                              </tr>
                                          </thead>
                                          <tbody>';

                                  foreach ($files as $file) {
                                      // Ignora le directory e i file nascosti
                                      if ($file != "." && $file != "..") {
                                          // Crea una riga per ogni file con un link per eliminare il file
                                          echo "<tr>";
                                          echo "<td><a href=\"{$directory}{$file}\" target=\"_blank\">{$file}</a></td>";
                                          echo "<td class='text-end'>";
                                          echo "<a href=\"../query/delete_file.php?file={$file}\" class='text-decoration-none'><i class='bx bx-trash text-danger'></i></a>";
                                          echo "</td>";
                                          echo "</tr>";
                                      }
                                  }

                                  echo '</tbody>
                                      </table>
                                  </div>';
                              }
                            ?>

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
      </div>

    </main>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <script>
      const ctx = document.getElementById('myChart');

      // Calcola la percentuale di allenamenti svolti rispetto al totale degli allenamenti
      const allenamentiSvolti = <?php echo $tot_allenamenti_svolti_player['tot_player']; ?>;
      const allenamentiTotali = <?php echo $tot_allenamenti_svolti['tot_allenamenti']; ?>;
      const percentualeSvolti = (allenamentiSvolti / allenamentiTotali) * 100 ;
      const percentualeDaSvolgere = 100 - percentualeSvolti;

      // Dati per il grafico a torta
      const data = {
        labels: ['Allenamenti svolti', 'Allenamenti assenti'],
        datasets: [{
          data: [percentualeSvolti, percentualeDaSvolgere],
          backgroundColor: ['#D24E01', '#303134'],
        }],
      };
      
      new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false, // Nascondi la legenda
            },
          },
        },
      });
    </script>




    <script>
    $(document).ready(function() {
        $(".delete-file").click(function(event) {
            event.preventDefault();
            var fileToDelete = $(this).data("file");
            
            // Invia una richiesta AJAX per eliminare il file
            $.ajax({
                url: "delete_file.php", // Crea un file PHP per gestire l'eliminazione del file
                type: "POST",
                data: { file: fileToDelete },
                success: function(response) {
                    if (response == "success") {
                        // Aggiorna la tabella o l'elenco degli allegati
                        $(this).closest("tr").remove(); // Rimuovi la riga dalla tabella
                    } else {
                        alert("Si è verificato un errore durante l'eliminazione del file.");
                    }
                }
            });
        });
    });
    </script>
  </body>


</html>