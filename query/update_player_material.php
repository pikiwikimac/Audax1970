<?php
session_start();
require_once('../config/db.php');

// Controlla se l'utente è loggato, altrimenti restituisci un errore
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit;
}

// Controlla se tutti i parametri richiesti sono stati inviati
if (!isset($_POST['playerId'], $_POST['materialName'], $_POST['checked'])) {
    http_response_code(400);
    exit;
}

// Ottieni i valori dai parametri POST
$playerId = $_POST['playerId'];
$materialName = $_POST['materialName'];
$checked = $_POST['checked'];

// Verifica se la checkbox è stata selezionata o deselezionata
if ($checked == 1) {
    // Se la checkbox è stata selezionata, aggiungi il record alla tabella giocatori_materiali
    $query = "REPLACE INTO giocatori_materiali (id_giocatore, id_materiale) 
              VALUES ($playerId, (SELECT id FROM materiali WHERE nome_materiale = '$materialName'))";
} else {
    // Se la checkbox è stata deselezionata, rimuovi il record dalla tabella giocatori_materiali
    $query = "DELETE FROM giocatori_materiali 
              WHERE id_giocatore = $playerId 
              AND id_materiale = (SELECT id FROM materiali WHERE nome_materiale = '$materialName')";
}

if (mysqli_query($con, $query)) {
    echo 'Dati aggiornati con successo.';
} else {
    echo 'Si è verificato un errore durante l\'aggiornamento dei dati.';
}
?>
