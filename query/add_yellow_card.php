<?php
  session_start();
  require_once('../config/db.php');

  $username = $_SESSION['username'];
  $image = $_SESSION['image'];
  $superuser = $_SESSION['superuser'];
  
  // Ottieni i dati inviati dal form
  $giocatore = $_GET['id_giocatore'];
  $matchID = $_GET['match'];
  $societaID = $_GET['societa'];

  // Prepara l'istruzione SQL utilizzando i segnaposto
  $query = "INSERT INTO ammoniti (id_giocatore, id_societa, id_partita) VALUES (?, ?, ?)";

  if ($stmt = mysqli_prepare($con, $query)) {
    // Associa i valori ai segnaposto
    mysqli_stmt_bind_param($stmt, "iii", $giocatore, $societaID, $matchID);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../admin/edit_risultato.php?id=' . $matchID);
        exit();
    } else {
        echo "ERROR: Si è verificato un errore durante l'inserimento dei marcatori: " . mysqli_stmt_error($stmt);
        exit();
    }

    // Chiudi lo statement
    mysqli_stmt_close($stmt);
  }

?>
