<?php
session_start();
require_once('../config/db.php');

// Ottieni l'URL di provenienza
$url_provenienza = $_SERVER['HTTP_REFERER'];

// Verifica che sia una richiesta POST valida
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "ERRORE: Metodo di richiesta non valido.";
    exit;
}

// Ottieni l'id del materiale dal form
$id_materiale = isset($_REQUEST['id_materiale']) ? (int)$_REQUEST['id_materiale'] : 0;
if ($id_materiale === 0) {
    echo "ERRORE: ID non valido.";
    exit;
}

// Escape dei dati provenienti dal form
$nome_materiale = mysqli_real_escape_string($con, $_POST['nome_materiale'] ?? null);
$costo_unitario = mysqli_real_escape_string($con, $_POST['costo_unitario'] ?? null);
$quantita = mysqli_real_escape_string($con, $_POST['quantita'] ?? null);

// Prepara l'array per gli aggiornamenti
$updates = [];
if ($nome_materiale !== null) {
    $updates[] = "nome_materiale='$nome_materiale'";
}
if ($costo_unitario !== null) {
    $updates[] = "costo='$costo_unitario'";
}
if ($quantita !== null) {
    $updates[] = "quantita='$quantita'";
}

// Verifica se ci sono campi da aggiornare
if (empty($updates)) {
    echo "Nessun campo da aggiornare.";
    exit;
}

// Costruisci la query SQL per l'aggiornamento
$query = "UPDATE materiale_allenamento SET " . implode(', ', $updates) . " WHERE id = '$id_materiale'";

// Esegui la query e gestisci l'esito
if (mysqli_query($con, $query)) {
    header("Location: $url_provenienza");
    exit;
} else {
    echo "ERRORE: Si è verificato un errore nell'esecuzione della query: " . mysqli_error($con);
}

// Chiudi la connessione al database
mysqli_close($con);
?>
