<!-- Pagina chi siamo per l'utente semplice -->
<?php
  session_start();
?>
<?php include('check_user_logged.php'); ?>


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
    <div class="container my-5 px-4">

      <div class="row gy-3 ">
        
        <div class="col-12">
          <h1 id="font_diverso">Chi siamo</h1>
        </div>
     
        <div class="col-12 col-lg-8">
          <span class="" >
            La Società Sportiva AUDAX 1970 è nata nel giugno del 1970 grazie all'iniziativa del Presidente Sandro Mantovani, insieme a un gruppo di amici appassionati di calcio. 
            Negli anni, l'attività principale della Società è stata la scuola calcio, un'eccellenza nell'insegnamento di questo sport a Senigallia. 
            <br/>
            <br/>
            Da questa scuola sono emersi giocatori che hanno raggiunto il professionismo e altri che hanno militato a lungo nei campionati dilettantistici locali e regionali, permettendo all'AUDAX di competere nei campionati di seconda e prima categoria.
            <br/>
            <br/>Nel giugno del 2003, AUDAX 1970 e GS Domenico Moroni di S.Angelo hanno deciso di fondersi, dando vita all'attuale SS AUDAX 1970 S.Angelo ASD. 
            <br/>Nei primi due anni, la nuova società ha partecipato al campionato di seconda categoria e successivamente, a causa di una retrocessione, al campionato di terza categoria. In questo periodo, la presidenza è passata da Sandro Mantovani a Tiziano Tarsi.
            <br/>Nel giugno 2004, la società ha avviato anche l'attività di Calcio a 5, iscrivendosi al suo primo campionato di Serie D e ottenendo subito la promozione in Serie C2. Tuttavia, nel giugno 2005, si è deciso di abbandonare il settore del calcio a 11 a causa della mancanza di nuove generazioni, dovuta alla chiusura della scuola calcio, per concentrarsi esclusivamente sul calcio a 5, sviluppando nel tempo un valido settore giovanile.
            <br/>Dal giugno 2006 fino ad oggi, dopo una iniziale retrocessione in Serie D e una pronta risalita, la società ha sempre disputato i campionati di Serie C2 e Serie C1, ottenendo ottimi risultati con giocatori locali e dominando spesso nei campionati giovanili, dall'Under 21 fino ai Giovanissimi ed Esordienti, passando anche per le categorie Juniores e Allievi.
            <br/>Attualmente, la SS AUDAX 1970 S.Angelo ASD conta un centinaio di tesserati tra giocatori, allenatori, preparatori e dirigenti. 
            <br/>La nostra sede è a Senigallia (AN), dove svolgiamo sia le partite casalinghe che la preparazione atletica.
            <br/>
            <br/>
            Senigallia è una città con una popolazione di 44.618 abitanti, rinomata come meta turistica internazionale grazie alle sue spiagge di sabbia chilometriche, tre ristoranti stellati e una vasta gamma di comfort. L'Aeroporto di Ancona dista solo 18 km, rendendo Senigallia una meta ambita per chi desidera investire nel territorio.
            Per ulteriori informazioni, non esitate a contattarci.
          </span>
        </div>

        <div class="col-12 col-lg-4">
          <img src="image/chisiamo.jpg" class="w-100 rounded  p-3"/>
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
        var url = window.location.pathname;
        console.log(url===($(this).attr('href')));
        $('.nav-link').each(function() {
          if ($(this).attr('href') === url) {
            $(this).addClass('active');
          }
        });
      });
    </script>
  </body>
</html>