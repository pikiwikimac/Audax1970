<?php
session_start();
require_once('../config/db.php');
$url_provenienza = $_SERVER['HTTP_REFERER'];

// Controlla se l'utente è autenticato
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

// Verifica se il nome della nuova cartella è stato inviato tramite il modulo
if (isset($_POST['newFolderName'])) {
    // Percorso della directory in cui creare la nuova cartella
    $directory = '../image/partite';
    
    // Nome della nuova cartella (pulito da eventuali caratteri non consentiti)
    $newFolderName = preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['newFolderName']);
    
    // Percorso completo della nuova cartella
    $newFolderPath = $directory . '/' . $newFolderName;

    // Verifica se la cartella già esiste
    if (!file_exists($newFolderPath)) {
        // Crea la nuova cartella
        if (mkdir($newFolderPath, 0777, true)) {
            header("Location: $url_provenienza");
        } else {
            header("Location: $url_provenienza");
        }
    } else {
        header("Location: $url_provenienza");
    }
} else {
    // Se il nome della nuova cartella non è stato fornito, reindirizza alla pagina principale
    header("Location: $url_provenienza");
    exit;
}
?>
