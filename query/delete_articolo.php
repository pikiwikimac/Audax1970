<?php 
session_start();

// Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
  header('Location: ../login/login.php');
  exit;
}

require_once('../config/db.php');

if (isset($_GET['id'])) {
  // Sanitizzazione dell'input per l'ID
  $id = intval($_GET['id']);

  if ($id <= 0) {
    // Reindirizza alla pagina degli articoli con un messaggio di errore se l'ID non è valido
    header('Location: ../admin/articoli.php?message=ID articolo non valido');
    exit;
  }

  // Preparazione della query di eliminazione
  $query = "DELETE FROM articoli WHERE id = ?";
  $stmt = mysqli_prepare($con, $query);
  
  if ($stmt) {
    // Bind dei parametri
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Esecuzione della query
    if (mysqli_stmt_execute($stmt)) {
      // Reindirizza alla pagina degli articoli con un messaggio di successo
      header('Location: ../admin/articoli.php?message=Articolo eliminato con successo');
    } else {
      // Reindirizza alla pagina degli articoli con un messaggio di errore
      header('Location: ../admin/articoli.php?message=Errore nell\'eliminazione dell\'articolo');
    }

    // Chiusura dello statement
    mysqli_stmt_close($stmt);
  } else {
    // Gestione errore nella preparazione dello statement
    header('Location: ../admin/articoli.php?message=Errore nella preparazione della query');
  }

} else {
  // Reindirizza alla pagina degli articoli se l'ID non è stato passato
  header('Location: ../admin/articoli.php?message=ID articolo non specificato');
}

// Chiusura della connessione
mysqli_close($con);
exit;
?>
