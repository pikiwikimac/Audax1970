<?php
session_start();
require_once('../config/db.php');

// Verifica se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
  header('Location: ../login/login.php');
  exit;
}

// Recupera i dati inviati dal form o dall'URL
$giocatore = isset($_REQUEST['id_giocatore']) ? intval($_REQUEST['id_giocatore']) : 0;
$matchID = isset($_REQUEST['match']) ? intval($_REQUEST['match']) : 0;

// Verifica che gli ID siano validi
if ($giocatore > 0 && $matchID > 0) {
  // Prepara l'istruzione SQL per eliminare il marcatore
  $query = "DELETE FROM marcatori WHERE id_giocatore = ? AND id_partita = ? LIMIT 1";

  // Prepara lo statement SQL
  if ($stmt = mysqli_prepare($con, $query)) {
    // Associa i valori ai segnaposto
    mysqli_stmt_bind_param($stmt, "ii", $giocatore, $matchID);

    // Esegui lo statement
    if (mysqli_stmt_execute($stmt)) {
      // Reindirizza alla pagina di modifica risultato con un messaggio di successo
      header('Location: ../admin/edit_risultato.php?id=' . $matchID);
      exit();
    } else {
      // Gestione degli errori di esecuzione dello statement
      echo "ERROR: Si è verificato un errore durante l'eliminazione del marcatore: " . mysqli_stmt_error($stmt);
      exit();
    }

    // Chiudi lo statement
    mysqli_stmt_close($stmt);
  } else {
    // Gestione degli errori nella preparazione dello statement
    echo "ERROR: Si è verificato un errore durante la preparazione dell'istruzione SQL: " . mysqli_error($con);
    exit();
  }
} else {
  // Gestione del caso in cui gli ID non siano validi o non siano stati passati
  echo "ERROR: ID giocatore o ID partita non validi.";
  exit();
}

// Chiudi la connessione al database
mysqli_close($con);
?>
