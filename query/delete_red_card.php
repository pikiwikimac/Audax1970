<?php
session_start();
require_once('../config/db.php');

// Verifica se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

// Verifica che siano stati inviati tutti i dati necessari
if (!isset($_REQUEST['id_giocatore'], $_REQUEST['match'])) {
    echo "ERROR: Parametri mancanti.";
    exit;
}

// Ottieni e sanifica i dati inviati dal form o dall'URL
$giocatore = mysqli_real_escape_string($con, $_REQUEST['id_giocatore']);
$matchID = mysqli_real_escape_string($con, $_REQUEST['match']);

// Query per eliminare il record dalla tabella 'rossi'
$query = "DELETE FROM rossi
          WHERE id_giocatore = '$giocatore'
          AND id_partita = '$matchID'
          LIMIT 1";

if (mysqli_query($con, $query)) {
    // Reindirizza alla pagina di modifica risultato dopo l'eliminazione
    header('Location: ../admin/edit_risultato.php?id=' . $matchID);
    exit;
} else {
    echo "ERROR: Si è verificato un errore durante l'eliminazione del record: " . mysqli_error($con);
    exit;
}

// Chiudi la connessione al database
mysqli_close($con);
?>
