<!-- Pagina che mostra tutti i giocatori -->
<?php
  session_start();
  require_once('config/db.php');

  include('check_user_logged.php');

  $id=$_REQUEST['id'];

  #Query che seleziona le info della squadra
  $query = "
  SELECT s.*
  FROM societa s
  WHERE s.id=$id";

  $squadra = mysqli_query($con,$query);
  $info = mysqli_fetch_assoc($squadra);

  $query2 = "
  SELECT g.*
  FROM giocatori g
  INNER JOIN societa s on s.id=g.id_squadra
  WHERE s.id=$id
  ORDER BY g.ruolo,g.cognome,g.nome";

  $giocatori = mysqli_query($con,$query2);

  $query3 = "
  SELECT soc.nome_societa as casa, soc2.nome_societa as ospite, golCasa,golOspiti,CAST(giornata AS UNSIGNED) AS giornata_,giornata,s.id,s.data,s.played
  FROM `partite` s
  INNER JOIN societa soc on soc.id=s.squadraCasa
  INNER JOIN societa soc2 on soc2.id=s.squadraOspite
  WHERE  s.id_stagione = 1
  AND (squadraCasa=$id or squadraOspite=$id OR squadraCasa=$id or squadraOspite=$id)
  ORDER BY giornata_,casa,ospite";

  $calendario = mysqli_query($con,$query3);
?>


<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_orange.php'; ?>
    </div>
        
    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>

    <!-- Descrizione iniziale -->
    <div class="container my-5">
      
      <!-- Società -->
      <div class="row gy-3 ">
        <h4 id="font_diverso">
          <?php echo $info['nome_societa'] ?>
        <h4>
        <hr id="separatore" />

        <!-- Info squadra -->
        <div class="row mb-3">
          <div class="col-12 col-md-2">
            <img src="image/loghi/<?php echo $info['logo'] ?>" class="img-thumbnail"/>
          </div>
          <div class="col-12 col-md-10">
            <div class="row gy-2 mt-3">
              <!-- Sede -->
              <div class="col-12 col-md-12">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['sede']  ?>" value="<?php echo $info['sede']  ?>">
                  <label for="floatingPlaintextInput">Sede:</label>
                </div>
              </div>

              <!-- Città -->
              <div class="col-12 col-lg-4 ">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['citta']  ?>" value="<?php echo $info['citta']  ?>">
                  <label for="floatingPlaintextInput" >Città:</label>
                </div>
              </div>
              
              <!-- Giorno settimana -->
              <div class="col-12 col-lg-4">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['giorno_settimana']  ?>" value="<?php echo $info['giorno_settimana']  ?>">
                  <label for="floatingPlaintextInput">Giorno settimana:</label>
                </div>
              </div>
              
              <!-- Orario match -->
              <div class="col-12 col-lg-4">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['ora_match']  ?> " value="<?php echo $info['ora_match']  ?>"> 
                  <label for="floatingPlaintextInput">Orario match:</label>
                </div>
              </div>
              <!-- Presidente -->
              <?php if($info['presidente'] != null){ ?>
              <div class="col-4 ">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['presidente']  ?> " value="<?php echo $info['presidente']  ?>"> 
                  <label for="floatingPlaintextInput">Presidente:</label>
                </div>
              </div>
              <?php } ?>
              <!-- VicePresidente -->
              <?php if($info['vicepresidente'] != null){ ?>
              <div class="col-4 ">
                <div class="form-floating mb-3">
                  <input type="text" readonly class="form-control-plaintext" id="floatingPlaintextInput" placeholder="<?php echo $info['vicepresidente']  ?> " value="<?php echo $info['vicepresidente']  ?>"> 
                  <label for="floatingPlaintextInput">Vice Presidente:</label>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>

          <?php if($giocatori->num_rows >0){ ?>
          <!-- Giocatori -->
          <div class="row mb-3">
            <div class="col-12 table-responsive">
              <table class="table table-sm table-striped table-hover">
                <thead class="table-dark text-white">
                  <tr>
                    <th></th>
                    <th>Nome</th>
                    <th>Anno</th>
                    <th>Ruolo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($row = mysqli_fetch_assoc($giocatori)){?>
                  <tr>
                     <!-- Immagine -->
                     <td class="text-center">
                      <?php if ($row['image_path']) { ?>
                        <img src="image/player/<?php echo $row['image_path'];?>" class="rounded-circle image-clickable" alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="30" height="30"/>
                      <?php } else { ?>
                        <img src="image/player/user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['player_name'];?>" width="30" height="30" />
                      <?php } ?>
                    </td>

                    <!-- Nome e Cognome -->
                    <td onclick="window.location='giocatore.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" >
                      <?php echo $row['cognome'] .' '. $row['nome']?>
                    </td>

                    <!-- Data di nascita -->
                    <td>
                      <?php if($row['data_nascita']==='1970-01-01'){
                        echo '&nbsp; &nbsp; &nbsp; &nbsp;  - ';
                      }else{
                        echo date('d/m/y',strtotime($row['data_nascita']));
                      } ?>
                    </td>

                    <!-- Ruolo -->
                    <td>
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
          </div>
          <?php } ?>
                        
          <!-- Calendario -->
          <div class="row mb-3">
            <div class="col-12 table-responsive">
              <table class="table table-striped table-hover">
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
                <tbody>
                  <?php while($partita = mysqli_fetch_assoc($calendario)){?>
                  <tr>
                    <!-- Numero giornata -->
                    <td class="text-center">
                      <small class="text-center">
                        &nbsp;<?php echo $partita['giornata'] ?>° 
                      </small>
                    </td>
                    
                    <!-- Data -->
                    <td class="text-center">
                      <small class="">
                        <?php echo date('d/m/y',strtotime( $partita['data'])) ?>
                        <br/>
                        <?php 
                          setlocale(LC_TIME, 'it_IT.utf8');
                          $dayOfWeek = strftime('%A', strtotime($partita['data']));
                          $abbreviatedDay = substr($dayOfWeek, 0, 3);
                          echo $abbreviatedDay;
                        ?>
                      </small>
                    </td>
                    <!-- Squadra casa -->
                    <td class="text-end">
                      <div >  <?php echo $partita['casa'] ?></div>
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
                      <div class="">   <?php echo $partita['ospite'] ?></div>
                    </td>
                    
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
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