<?php
require_once('../config/db.php');

// Verifica che sia una richiesta POST valida
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "ERROR: Metodo di richiesta non valido.";
    exit;
}

// Verifica che l'id sia stato fornito correttamente
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id === 0) {
    echo "ERROR: ID non valido.";
    exit;
}

// Esegui l'escape dei dati provenienti dal form
$casa = mysqli_real_escape_string($con, $_POST['casa']);
$ospite = mysqli_real_escape_string($con, $_POST['ospite']);
$giornata = mysqli_real_escape_string($con, $_POST['giornata']);
$golCasa = isset($_POST['golCasa']) ? (int)$_POST['golCasa'] : null;
$golOspiti = isset($_POST['golOspiti']) ? (int)$_POST['golOspiti'] : null;

// Inizializza un array per gli aggiornamenti
$updates = array();

// Aggiungi gli aggiornamenti solo se i valori non sono vuoti o nulli
if (!empty($casa)) {
    $updates[] = "casa='$casa'";
}

if (!empty($ospite)) {
    $updates[] = "ospite='$ospite'";
}

if ($golCasa !== null) {
    $updates[] = "golCasa=$golCasa";
}

if ($golOspiti !== null) {
    $updates[] = "golOspiti=$golOspiti";
}

if (!empty($giornata)) {
    $updates[] = "giornata='$giornata'";
}

// Verifica se ci sono campi da aggiornare
if (empty($updates)) {
    echo "Nessun campo da aggiornare.";
    exit;
}

// Costruisci la query di aggiornamento
$query = "UPDATE partite SET " . implode(', ', $updates) . " WHERE id = " . $id;

// Esegui la query e gestisci l'esito
if (mysqli_query($con, $query)) {
    // Reindirizzamento all'URL referer
    $url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../admin/partite.php';
    header("Location: $url_provenienza");
    exit;
} else {
    echo "ERROR: Impossibile eseguire l'aggiornamento. " . mysqli_error($con);
}

// Chiudi la connessione
mysqli_close($con);
?>
