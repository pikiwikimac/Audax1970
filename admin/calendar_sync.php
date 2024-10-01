<?php
// Verifica se l'utente è loggato
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit();
}

// Includi la configurazione del database
require_once('../config/db.php');

// Google Calendar integration
require_once '../vendor/autoload.php'; // Modifica il percorso se necessario

$client = new Google_Client();
$client->setAuthConfig('client_secret.json'); // Percorso corretto del file
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setRedirectUri('https://www.audax1970.it/admin/callback.php'); // Modifica per l'ambiente live se necessario

// Autenticazione OAuth 2.0
if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
} elseif (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: calendar_sync.php'); // Reindirizza alla stessa pagina o a una pagina di successo
    exit();
} else {
    $client->setAccessToken($_SESSION['access_token']);

    // Ora puoi sincronizzare gli allenamenti con Google Calendar
    $service = new Google_Service_Calendar($client);

    // Esempio di query al database per prendere gli allenamenti
    $query = "
        SELECT * 
        FROM allenamenti 
        WHERE data <= NOW() 
        ORDER BY data ASC;
    ";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($allenamento = mysqli_fetch_assoc($result)) {
            $event = new Google_Service_Calendar_Event(array(
                'summary' => 'Allenamento',
                'location' => $allenamento['location'],
                'start' => array(
                    'dateTime' => $allenamento['start_time'],
                    'timeZone' => 'Europe/Rome',
                ),
                'end' => array(
                    'dateTime' => $allenamento['end_time'],
                    'timeZone' => 'Europe/Rome',
                ),
            ));

            $calendarId = 'primary';
            $service->events->insert($calendarId, $event);
        }
        echo 'Allenamenti sincronizzati con Google Calendar!';
    } else {
        echo 'Nessun allenamento trovato da sincronizzare.';
    }
}
?>
