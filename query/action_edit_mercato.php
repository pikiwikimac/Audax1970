<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];

// Verifica che sia una richiesta POST valida
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "ERRORE: Metodo di richiesta non valido.";
    exit;
}

// Ottieni l'id dall'URL o dal form
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id === 0) {
    echo "ERRORE: ID non valido.";
    exit;
}

// Escape dei dati provenienti dal form
$nome = mysqli_real_escape_string($con, $_POST['nome'] ?? null);
$cognome = mysqli_real_escape_string($con, $_POST['cognome'] ?? null);
$realizzazione = mysqli_real_escape_string($con, $_POST['realizzazione'] ?? null);
$note = mysqli_real_escape_string($con, $_POST['note'] ?? null);
$ruolo = mysqli_real_escape_string($con, $_POST['ruolo'] ?? null);

// Prepara la query SQL per gli aggiornamenti
$updates = [];
if ($nome !== null) {
    $updates[] = "nome='$nome'";
}
if ($cognome !== null) {
    $updates[] = "cognome='$cognome'";
}
if ($realizzazione !== null) {
    $updates[] = "realizzazione='$realizzazione'";
}
if ($note !== null) {
    $updates[] = "note='$note'";
}
if ($ruolo !== null) {
    $updates[] = "ruolo='$ruolo'";
}

// Verifica se ci sono campi da aggiornare
if (empty($updates)) {
    echo "Nessun campo da aggiornare.";
    exit;
}

// Costruisci la query completa
$query = "UPDATE mercato SET " . implode(', ', $updates) . " WHERE id = '$id'";

// Esegui la query e gestisci l'esito
if (mysqli_query($con, $query)) {
    header("Location:$url_provenienza");
    exit;
} else {
    echo "ERRORE: Si è verificato un errore nell'esecuzione della query: " . mysqli_error($con);
}

// Chiudi la connessione
mysqli_close($con);
?>