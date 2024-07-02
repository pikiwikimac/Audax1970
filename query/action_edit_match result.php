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

// Verifica se sono stati forniti i parametri da aggiornare
$golCasa = isset($_POST['golCasa']) ? (int)$_POST['golCasa'] : null;
$golOspiti = isset($_POST['golOspiti']) ? (int)$_POST['golOspiti'] : null;
if ($golCasa === null && $golOspiti === null) {
    echo "Nessun campo da aggiornare.";
    exit;
}

// Prepara gli aggiornamenti
$updates = array();
if ($golCasa !== null) {
    $updates[] = "golCasa=" . mysqli_real_escape_string($con, $golCasa);
}
if ($golOspiti !== null) {
    $updates[] = "golOspiti=" . mysqli_real_escape_string($con, $golOspiti);
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
