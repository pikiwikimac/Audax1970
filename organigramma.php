<!-- Pagina che mostra tutti i giocatori -->
<?php
  session_start();
  require_once('config/db.php');

  include('check_user_logged.php');

  $queryDirigenza = "
  SELECT nome, ruolo, ordinamento, image_path
  FROM dirigenti
  WHERE ordinamento > 0
  ORDER BY CAST(ordinamento AS UNSIGNED) ASC, nome;
  ";
  $dirigenti = mysqli_query($con,$queryDirigenza);
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
      
      <!-- Dirigenza -->
      <?php if(mysqli_num_rows($dirigenti)>0){ ?>
      <div class="row gy-3">
        <span class="fw-bold fs-2" class="" id="font_diverso"> Organigramma </span>

        <hr id="separatore"/>

        <?php while($row = mysqli_fetch_assoc($dirigenti)) {  ?>

          <div class="col-12 col-sm-6 col-lg-4 col-xl-3  p-3">

            <div class="card player hover-box-shadow ">
              <!-- Immagine dirigente -->
              <?php if($row['image_path']!= NULL){ ?>
                <img src="image/staff/<?php echo $row['image_path']; ?>" class="card-img-top" alt="..." style="width:100%;height:250px;cursor:pointer;" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
              <?php }else{ ?>
                <img src="image/default_user.jpg" class="card-img-topx" alt="..." style="width:100%;height:250px;cursor:pointer;" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>"/>
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

    
    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    
  </body>

</html>