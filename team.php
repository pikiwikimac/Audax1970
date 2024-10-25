<!-- Pagina che mostra tutti i giocatori -->
<?php
  session_start();
  include('check_user_logged.php');

  require_once('utilities/q_giocatori.php');
  require_once('utilities/q_societa.php');
  require_once('utilities/q_calendario.php');


  $id=$_REQUEST['id'];

  // Controllo se l'ID è valido
  if (!is_numeric($id)) {
    die("ID non valido.");
  }

  // Ottieni le informazioni della squadra
  $squadra_result = getInfoSquadra($con, $id);

  // Controlla eventuali errori nella query
  if (!$squadra_result) {
    die("Errore nella query della squadra: " . mysqli_error($con));
  }

  // Recupera i risultati per la squadra
  $info = mysqli_fetch_assoc($squadra_result);

  // Verifica se ci sono risultati per la squadra
  if (!$info) {
    die("Nessun risultato trovato per la squadra.");
  }

  $campionato_squadra=$info['id_campionato'];

  $giocatori = getGiocatoriBySocieta($con,$id);

  $calendario = getCalendarioPartite($con,$campionato_squadra,$id);
?>


<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar.php'; ?>
    </div>
        
    <!-- Descrizione iniziale -->
    <div class="container" style="margin-top:6rem!important">
      
      <!-- Società -->
      <div class="row gy-3 ">
        <h1 class="bebas">
          <img src="image/loghi/<?php echo $info['logo'] ?>" class="img-fluid rounded-circle" width="80" height="80"/> &nbsp; <?php echo $info['nome_societa'] ?>
          
            <!-- Instagram -->
            <?php if($info['instagram'] != null){ ?>
              &nbsp; 
              <a href="https://www.instagram.com/<?php echo $info['instagram']; ?>" class="text-decoration-none text-dark" target="_blank">
                <span class="">
                  <i class='bx bxl-instagram' style="font-size:1.2rem"></i>
                </span>
              </a>
            <?php } ?>
            
            <!-- Facebook -->
            <?php if($info['facebook'] != null){ ?>
              <a href="https://www.facebook.com/<?php echo $info['facebook']; ?>" class="text-decoration-none text-dark" target="_blank">
                <span class="">
                <i class='bx bxl-facebook' style="font-size:1.2rem"></i>
                </span>
              </a>
            <?php } ?>
          
        </h1>

        <!-- Info squadra -->
        <div class="row mb-3">
          
            
          
          <div class="col-12">
            <!-- Sede e città  -->
            <div class="">
              <span class="text-muted" style="font-size:14px">
                <i class='bx bxs-map-pin'></i> &nbsp;
                <a href="https://www.google.com/maps/search/<?php echo urlencode($info['sede'] . ' ' . $info['citta']); ?>" class="text-decoration-none text-dark" target="_blank">
                  <?php echo $info['sede'] ?> - <?php echo $info['citta'] ?>
                </a>
              </span>
              <!-- Link a Futsalmarche e tuttocampo -->
              <div class="float-end">

                <a class="btn btn-sm btn-outline-dark " href="https://www.google.com/search?q=<?php echo urlencode($info['nome_societa']  . ' Futsalmarche'); ?>" target="_blank">
                  <img src="image/loghi/favicon_fm.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp; Futsalmarche
                </a>

                <a class="btn btn-sm btn-outline-dark" href="https://www.google.com/search?q=<?php echo urlencode($info['nome_societa']  . ' Tuttocampo'); ?>" target="_blank">
                  <img src="image/loghi/favicon_tt.ico" class="rounded-circle" width="18px" height="18px" /> &nbsp;Tuttocampo
                </a>

              </div>
            </div>

            
            <!-- Giorno e ora  -->
            <div class="">
              <span class="" style="font-size:14px">
                <i class='bx bx-calendar' ></i> &nbsp; <?php echo $info['giorno_settimana']  ?> - <?php echo $info['ora_match']  ?>
              </span>
            </div>

            


            <!-- Sito web -->
            <?php if($info['sito_web'] != null){ ?>
              <div class="">
                <span class="" style="font-size:14px">
                  <i class='bx bx-link'></i> &nbsp; Sito web :&nbsp;<a class="text-decoration-none text-dark" href="<?php echo $info['sito_web']?> "><?php echo $info['sito_web']?> </a>
                </span>
              </div>
            <?php } ?>

            <!-- Presidente -->
            <?php if($info['presidente'] != null){ ?>
              <div class="">
                <span class="" style="font-size:14px">
                  Presidente :&nbsp;<?php echo $info['presidente']  ?>
                </span>
            </div>
            <?php } ?>
            <!-- VicePresidente -->
            <?php if($info['vicepresidente'] != null){ ?>
              <div class="">
                <span class="" style="font-size:14px">
                  Vicepresidente :&nbsp;<?php echo $info['vicepresidente']  ?>
                </span>
            </div>
            <?php } ?>

            <!-- Allenatore -->
            <?php if($info['allenatore'] != null){ ?>
              <div class="">
                <span class="" style="font-size:14px">
                Allenatore :&nbsp;<?php echo $info['allenatore']  ?>
                </span>
            </div>
            <?php } ?>
            
            
          </div>

        </div>
        <hr id="separatore" />

        

        
        <!-- Giocatori -->
        
          <?php if($giocatori->num_rows >0){ ?>
          <div class="table-responsive col-12 col-md-6">
          
            <table class="table table-sm table-striped table-hover table-rounded w-100">
              <thead class="table-dark text-white">
                <tr>
                  <th></th>
                  <th>Nome</th>
                  <th class="text-center">Anno</th>
                  <th class="text-center">Ruolo</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = mysqli_fetch_assoc($giocatori)){?>
                <tr class="align-middle">
                  <!-- Immagine -->
                  <td class="text-center">
                    <?php if ($row['image_path']) { ?>
                      <img src="image/player/<?php echo $row['image_path'];?>" class="rounded-circle image-clickable" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="30" height="30"/>
                    <?php } else { ?>
                      <img src="image/default_user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" width="30" height="30" />
                    <?php } ?>
                  </td>

                  <!-- Nome e Cognome -->
                  <td onclick="window.location='giocatore.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                    <?php echo $row['cognome'] .' '. $row['nome']?>
                  </td>

                  <!-- Data di nascita -->
                  <td class="text-center">
                    <small>

                      <?php 
                      if($row['data_nascita'] === '1970-01-01' || $row['data_nascita'] == NULL){
                        echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                      }else{
                        echo date('Y', strtotime($row['data_nascita']));
                      } 
                      ?>
                    </small>
                  </td>

                  <!-- Ruolo -->
                  <td class="text-center">
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
                        
                  </td>
                  
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          
          <?php } ?>
           
          <!-- Calendario -->
          <div class="table-responsive col-12 col-md-6">
            <table class="table table-sm table-striped table-hover table-rounded ">
              <thead class="table-dark text-white">
                <tr>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-end">Casa</th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class=""> Ospite</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <?php while($partita = mysqli_fetch_assoc($calendario)){?>
                <tr style="height:39px">
                  
                  <!-- Data -->
                  <td class="text-center">
                    <small class="">
                      <?php echo $partita['giornata'] ?>° 
                      &nbsp;
                      <?php 
                        setlocale(LC_TIME, 'it_IT.utf8');
                        $dayOfWeek = strftime('%A', strtotime($partita['data']));
                        $abbreviatedDay = substr($dayOfWeek, 0, 3);
                        echo $abbreviatedDay;
                      ?>
                    </small>
                  </td>

                  <td class="text-center">
                    <small class="">
                      <?php echo date('d/m/y',strtotime( $partita['data'])) ?>
                    </small>
                  </td>
                  <!-- Squadra casa -->
                  <td class="text-end">
                    <div class="
                      <?php 
                        // Verifica se la squadra ospite è quella dell'utente e assegna 'fw-bold'
                        if ($partita['casa'] == $info['nome_societa']) {
                          echo 'fw-bold ';
                        }

                        // Verifica il risultato per assegnare il colore
                        if ($partita['risultato'] === '1' && $partita['casa'] == $info['nome_societa']) {
                          echo 'text-danger';
                        } elseif ($partita['risultato'] === 'X') {
                          echo 'text-primary';
                        } elseif ($partita['risultato'] === '2' && $partita['casa'] == $info['nome_societa']) {
                          echo 'text-success';
                        } else {
                          echo 'text-dark';
                        }
                      ?>
                      ">
                      <?php echo $partita['casa'] ?>
                    </div>
                  </td>
                  <!-- Gol casa -->
                  <td class="text-center">
                    <?php echo $partita['golCasa'] ?>
                  </td>

                  <!-- Gol ospite -->
                  <td class="text-center">
                    <?php echo $partita['golOspiti'] ?>
                  </td>
                  <!-- Squadra ospite -->
                  <td class="">
                       
                      <div class="
                        <?php 
                          // Verifica se la squadra ospite è quella dell'utente e assegna 'fw-bold'
                          if ($partita['ospite'] == $info['nome_societa']) {
                            echo 'fw-bold ';
                          }

                          // Verifica il risultato per assegnare il colore
                          if ($partita['risultato'] === '1' && $partita['ospite'] == $info['nome_societa']) {
                            echo 'text-danger';
                          } elseif ($partita['risultato'] === 'X') {
                            echo 'text-primary';
                          } elseif ($partita['risultato'] === '2' && $partita['ospite'] == $info['nome_societa']) {
                            echo 'text-success';
                          } else {
                            echo 'text-dark';
                          }
                        ?>
                      ">
                        <?php echo $partita['ospite'] ?>
                    </div>
                  </td>
                  
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        

      </div>
      
      

    </div>

    <!-- Modal Visualizzazione immagine -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-body">
            <img id="modalImage" src="" alt="" class="img-fluid w-100 h-100">
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    <script>
      $(document).ready(function() {
        // Gestisci il click sull'immagine
        $('.image-clickable').click(function() {
          var imagePath = $(this).attr('src');
          var playerName = $(this).data('player-name'); // Ottieni il nome del giocatore
          $('#modalImage').attr('src', imagePath); // Imposta l'immagine nel modal
          $('#editModalLabel').html(playerName); // Imposta il nome del giocatore nel modal-header
          $('#imageModal').modal('show'); // Apri il modal
        });
      });
    </script>
  </body>

</html>