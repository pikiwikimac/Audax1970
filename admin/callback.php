<?php
  session_start();
  // Controlla se l'utente è autenticato
   // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
   if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  require_once('../config/db.php');
  require_once '../vendor/autoload.php'; // Includi l'autoloader

$client = new Google_Client();
$client->setApplicationName('FutsalHub');
$client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
$client->setAuthConfig('client_secret.json'); // Percorso al file delle credenziali
$client->setAccessType('offline');

// Gestisci il codice di autorizzazione
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;

    // Reindirizza alla pagina principale
    header('Location: dashboard.php');
    exit;
}

if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] == '') {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
}

$client->setAccessToken($_SESSION['access_token']);

$service = new Google_Service_Calendar($client);
$calendarId = 'primary';
$events = $service->events->listEvents($calendarId, array(
    'maxResults' => 10,
    'singleEvents' => true,
    'orderBy' => 'startTime',
    'timeMin' => date('c'),
));

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
                          Calendario Google
                        </h4>
                      </div>
                    </div>
                    <!-- END:Intestazione -->
                    
                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row  ">
                        <div class="col-12">
                        <?php
                          if (count($events->getItems()) == 0) {
                              echo 'Nessun evento trovato.';
                          } else {
                              echo '<ul>';
                              foreach ($events->getItems() as $event) {
                                  $start = $event->start->dateTime;
                                  if (empty($start)) {
                                      $start = $event->start->date;
                                  }
                                  echo '<li>' . $start . ' - ' . $event->getSummary() . '</li>';
                              }
                              echo '</ul>';
                          }
                          ?>
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
      </main>



    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

  </body>
</html>