<?php
session_start();
require_once('../config/db.php');

// Verifica se l'utente è loggato, altrimenti restituisci un errore 401 (Non autorizzato)
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Non sei autorizzato ad accedere a questa risorsa.');
}

// Verifica se è stato inviato l'ID dell'intestazione tramite metodo POST
if (!isset($_POST['intestazioneID'])) {
    http_response_code(400);
    exit('L\'ID dell\'intestazione è obbligatorio.');
}

// Ottieni l'ID dell'intestazione dalla richiesta POST e effettua la sanitizzazione
$intestazioneID = mysqli_real_escape_string($con, $_POST['intestazioneID']);

// Query per eliminare l'intestazione dalla tabella articoli_intestazioni
$query = "DELETE FROM articoli_intestazioni WHERE id = ?";

// Prepara lo statement SQL
if ($stmt = mysqli_prepare($con, $query)) {
    // Associa il parametro al segnaposto nella query preparata
    mysqli_stmt_bind_param($stmt, "i", $intestazioneID);

    // Esegui lo statement
    if (mysqli_stmt_execute($stmt)) {
        echo 'Intestazione eliminata con successo.';
    } else {
        http_response_code(500);
        echo 'Si è verificato un errore durante l\'eliminazione dell\'intestazione: ' . mysqli_stmt_error($stmt);
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
