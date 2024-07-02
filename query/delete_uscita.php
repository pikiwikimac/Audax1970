<?php
require_once('../config/db.php');

// Verifica se è stato fornito l'ID del record da eliminare
if (!isset($_GET['id'])) {
    echo "ERROR: Parametro ID mancante.";
    exit;
}

// Ottieni e sanifica l'ID del record da eliminare
$id = mysqli_real_escape_string($con, $_GET['id']);

// Query per eliminare il record dalla tabella 'uscite'
$sql = "DELETE FROM uscite WHERE id = '$id'";

if (mysqli_query($con, $sql)) {
    // Reindirizza alla pagina precedente dopo l'eliminazione
    $url_provenienza = $_SERVER['HTTP_REFERER'] ?? '../'; // Imposta un fallback nel caso $_SERVER['HTTP_REFERER'] non sia disponibile
    header("Location: $url_provenienza");
    exit;
} else {
    echo "ERROR: Si è verificato un errore durante l'eliminazione del record: " . mysqli_error($con);
}

// Chiudi la connessione al database
mysqli_close($con);
?>
