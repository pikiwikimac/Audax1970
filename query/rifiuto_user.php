<?php
require_once('../config/db.php');

// Verifica se è stato fornito l'ID del record da eliminare
if (!isset($_GET['id'])) {
    echo "Parametro id non fornito.";
    exit;
}

// Sanifica l'ID del record da eliminare
$id = mysqli_real_escape_string($con, $_GET['id']);

// Query per eliminare il record dalla tabella 'users'
$query = "DELETE FROM users WHERE id = $id";

if (mysqli_query($con, $query)) {
    // Reindirizza alla pagina di gestione registrazioni dopo l'eliminazione riuscita
    header('Location: ../admin/gestore_registrazioni.php');
    exit;
} else {
    // Errore nell'eliminazione
    echo "Si è verificato un errore nell'eliminazione del record: " . mysqli_error($con);
}

// Chiudi la connessione al database
mysqli_close($con);
?>
