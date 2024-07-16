<!-- Pagina che mostra tutti i giocatori -->
<?php
  session_start();
  require_once('config/db.php');

  include('check_user_logged.php');

  $id_squadra=$_REQUEST['id_squadra'];

  $query_tipo_squadra="SELECT tipo FROM societa WHERE id=$id_squadra";
  $tipo_squadra = mysqli_query($con,$query_tipo_squadra);
  $tipo = mysqli_fetch_assoc($tipo_squadra);

  //INIZIO QUERY
  $query = "select * FROM giocatori where id_squadra=$id_squadra order by ruolo,cognome,nome asc";
  $result = mysqli_query($con,$query);


  $queryDirigenza = "select nome,ruolo,ordinamento,image_path FROM dirigenti WHERE ordinamento > 0  order by ordinamento,nome ";
  $dirigenti = mysqli_query($con,$queryDirigenza);


  $query = "select * FROM giocatori order by ruolo,cognome,nome asc";
?>


<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>
        
    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>
    

    <!-- Descrizione iniziale -->
    <div class="container my-5">
      <span class="fw-bold fs-2" id="font_diverso">
        <?php echo $tipo['tipo'] ?>
      </span>
        
      <?php if(mysqli_num_rows($result)=== 0 ){ ?>
        <br/>
        <span class="text-muted">Nessun giocatore ancora inserito</span>
      <?php } ?>
      
      <!--  -->
      <?php if(mysqli_num_rows($result)>0){ ?>
      <div class="row gy-3 ">
        

        <?php while($row = mysqli_fetch_assoc($result)) {  ?>

          <div class="col-12 col-sm-6 col-lg-3  p-3 ">

            <div class="card player hover-box-shadow " >
              <!-- Immagine giocatore -->
              <?php if ($row['image_path'] != null ) { ?>
                <img src="image/player/<?php echo $row['image_path'];?>" class="card-img-top image-clickable " alt="..." style="width:100%;cursor:pointer" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
              <?php } else { ?>
                <img src="image/default_user.jpg" class="card-img-top image-clickable " alt="..." style="width:100%;cursor:pointer" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
              <?php } ?>
              <!-- Info giocatore -->
              <div class="card-body bg-dark rounded-bottom text-white p-3">
                <div class="row">
                  <!-- Nome giocatore -->
                  <div class="col-sm-9 col-10 ">
                    <span class="fs-5"><?php echo $row['nome'].'<br/> ' .$row['cognome'] ?></span>
                  </div>
                  <!-- Numero di maglia giocatore -->
                  <div class="col-sm-3 col-2 ">
                    <span class="text-nowrap numero_maglia"><?php echo $row['maglia'] ?></span>
                  </div>
                </div>
              </div>
            </div>
            

          </div>

        <?php } ?>

      </div>
      <?php } ?>
      
      
      <!-- Dirigenza -->
      <?php if(mysqli_num_rows($dirigenti)>0){ ?>
      <div class="row mt-5 gy-3">
        <span class="fw-bold fs-2" class="" id="font_diverso"> Organigramma </span>

        <hr id="separatore"/>

        <?php while($row = mysqli_fetch_assoc($dirigenti)) {  ?>

          <div class="col-12 col-sm-6 col-lg-3  p-3">

            <div class="card player hover-box-shadow ">
              <!-- Immagine dirigente -->
              <?php if($row['image_path']!= NULL){ ?>
                <img src="image/staff/<?php echo $row['image_path']; ?>" class="card-img-top image-clickable " alt="..." style="width:100%;max-height:200px;cursor:pointer;" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
              <?php }else{ ?>
                <img src="image/default_user.jpg" class="card-img-top image-clickable " alt="..." style="width:100%;max-height:200px;cursor:pointer;" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
              <?php } ?>

              <!-- Info dirigente -->
              <div class="card-body bg-dark rounded-bottom text-white p-3">
                <div class="row">
                  <!-- Nome dirigente -->
                  <div class="col-12">
                    <span class="fs-5"><?php echo $row['nome'].'<br/> ' .$row['ruolo'] ?></span>
                  </div>
                </div>
              </div>
            </div>
            

          </div>

        <?php } ?>

      </div>
      <?php } ?>
      

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