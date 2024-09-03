<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('vendor/autoload.php'); // Carica la libreria

// Inizializza un nuovo oggetto PHPMailer
$mail = new PHPMailer(true);

try {
    // Configura il server SMTP
    $mail->isSMTP();
    $mail->Host = 'mail.audax1970.it';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@audax1970.it';
    $mail->Password = 'TizianoTarsi';
    $mail->SMTPSecure = 'ssl';  // Usa ssl o tls a seconda delle tue preferenze
    $mail->Port = 465;  // Porta SMTP per SSL

    // Destinatario
    $mail->setFrom($_POST['email'], $_POST['nome']);  // Indirizzo e nome del mittente presi dal modulo
    $mail->addAddress('info@audax1970.it', 'Audax1970');  // Indirizzo e nome del destinatario fisso

    // Contenuto dell'email
    $mail->isHTML(true);
    $mail->Subject = 'Richiesta di acquisto';
    
    // Costruzione del corpo dell'email usando le informazioni dal modulo
    $message = '<p><strong>Prodotto:</strong> ' . htmlspecialchars($_POST['tipo_prodotto']) . '</p>';
    $message .= '<p><strong>Nome:</strong> ' . htmlspecialchars($_POST['nome']) . '</p>';
    $message .= '<p><strong>Telefono:</strong> ' . htmlspecialchars($_POST['telefono']) . '</p>';
    $message .= '<p><strong>Email:</strong> ' . htmlspecialchars($_POST['email']) . '</p>';
    $message .= '<p><strong>Taglia:</strong> ' . htmlspecialchars($_POST['taglia']) . '</p>';
    $message .= '<p><strong>Quantità:</strong> ' . htmlspecialchars($_POST['quantita']) . '</p>';
    $message .= '<p><strong>Note:</strong><br>' . nl2br(htmlspecialchars($_POST['note'])) . '</p>';
    $message .= '<p><strong>Metodo di contatto preferito:</strong> ' . htmlspecialchars($_POST['contatto_preferito']) . '</p>';

    $mail->Body = $message;

    // Invia l'email
    $mail->send();
    echo 'Email inviata con successo';
} catch (Exception $e) {
    echo "Errore nell'invio dell'email: {$mail->ErrorInfo}";
}
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
    
    <!-- Carousel di sfondo (opzionale) -->
    <?php include 'elements/carousel.php'; ?>

    <!-- Descrizione iniziale -->
    <div class="container my-5">
      <div class="row">
        <div class="col-12">
          <span class="fs-3 fw-bold" id="font_diverso">Prodotti in vendita</span>
        </div>
      </div>
      
      <!-- Sezione Prodotti -->
      <div class="row mt-4">
        <!-- Maglia da gioco -->
        <div class="col-md-4">
          <div class="card">
            <img src="image/default.jpeg" class="card-img-top" alt="Maglia da gioco">
            <div class="card-body">
              <h5 class="card-title" id="font_diverso">Maglia da gioco</h5>
              <p class="card-text">€29,99</p>
              <button class="btn btn-primary btn-acquista" data-bs-toggle="modal" data-bs-target="#acquistoModal" data-product="Maglia da gioco">Acquista</button>
            </div>
          </div>
        </div>
        <!-- Felpa tifosi -->
        <div class="col-md-4">
          <div class="card">
            <img src="image/default.jpeg" class="card-img-top" alt="Felpa tifosi">
            <div class="card-body">
              <h5 class="card-title" id="font_diverso">Felpa</h5>
              <p class="card-text">€39,99</p>
              <button class="btn btn-primary btn-acquista" data-bs-toggle="modal" data-bs-target="#acquistoModal" data-product="Felpa tifosi">Acquista</button>
            </div>
          </div>
        </div>
        <!-- Cappello invernale -->
        <div class="col-md-4">
          <div class="card">
            <img src="image/default.jpeg" class="card-img-top" alt="Cappello invernale">
            <div class="card-body">
              <h5 class="card-title" id="font_diverso">Cappellino</h5>
              <p class="card-text">€19,99</p>
              <button class="btn btn-primary btn-acquista" data-bs-toggle="modal" data-bs-target="#acquistoModal" data-product="Cappello invernale">Acquista</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="acquistoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalProductTitle">Acquista Prodotto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="">
              <input type="hidden" name="tipo_prodotto" id="tipoProdottoInput">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
              </div>
              <div class="mb-3">
                <label for="telefono" class="form-label">Telefono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="row mb-3">
                <div class="col mb-3">
                  <label for="quantita" class="form-label">Quantità</label>
                  <input type="number" class="form-control" id="quantita" name="quantita">
                </div>
                <div class="col mb-3">
                  <label for="taglia" class="form-label">Taglia</label>
                  <input type="number" class="form-control" id="taglia" name="taglia" >
                </div>
              </div>
              <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Metodo di contatto preferito</label>
                <div>
                  <input type="radio" id="contattoEmail" name="contatto_preferito" value="Email" required>
                  <label for="contattoEmail">Email</label>
                </div>
                <div>
                  <input type="radio" id="contattoWhatsapp" name="contatto_preferito" value="Whatsapp" required>
                  <label for="contattoWhatsapp">Whatsapp</label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Invia</button>
            </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        $('.btn-acquista').on('click', function() {
          var productTitle = $(this).data('product');
          $('#modalProductTitle').text('Acquista ' + productTitle);
          $('#tipoProdottoInput').val(productTitle);
        });
      });
    </script>
  </body>
</html>
