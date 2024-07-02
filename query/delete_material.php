<?php
session_start();
require_once('../config/db.php');

// Verifica se l'utente è loggato, altrimenti restituisci un errore 401 (Non autorizzato)
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Non sei autorizzato ad accedere a questa risorsa.');
}

// Verifica se è stato inviato il nome del materiale tramite metodo POST
if (!isset($_POST['materialName'])) {
    http_response_code(400);
    exit('Il nome del materiale è obbligatorio.');
}

// Ottieni il nome del materiale dalla richiesta POST e effettua la sanitizzazione
$materialName = mysqli_real_escape_string($con, $_POST['materialName']);

// Query per eliminare il materiale dalla tabella materiali
$query = "DELETE FROM materiali WHERE nome_materiale = ? AND id_stagione = 1";

// Prepara lo statement SQL
if ($stmt = mysqli_prepare($con, $query)) {
    // Associa il parametro al segnaposto nella query preparata
    mysqli_stmt_bind_param($stmt, "s", $materialName);

    // Esegui lo statement
    if (mysqli_stmt_execute($stmt)) {
        echo 'Materiale eliminato con successo.';
    } else {
        http_response_code(500);
        echo 'Si è verificato un errore durante l\'eliminazione del materiale: ' . mysqli_stmt_error($stmt);
    }

    // Chiudi lo statement
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo 'Si è verificato un errore durante la preparazione dell\'istruzione SQL: ' . mysqli_error($con);
}

// Chiudi la connessione al database
mysqli_close($con);
?>
