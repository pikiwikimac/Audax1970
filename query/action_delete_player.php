<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];

// Sanitizzazione input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verifica se l'ID è valido
if ($id > 0) {
  // Prepara l'istruzione SQL
  $stmt = $con->prepare("DELETE FROM giocatori WHERE id = ?");
  
  if ($stmt) {
    // Associa i parametri
    $stmt->bind_param("i", $id);
    
    // Esegui l'istruzione
    if ($stmt->execute()) {
      // Reindirizza in caso di successo
      header("Location: $url_provenienza");
      exit;
    } else {
      // Gestisci errore di esecuzione
      echo "ERRORE: Impossibile eseguire la query. " . htmlspecialchars($stmt->error);
    }
    
    // Chiudi l'istruzione
    $stmt->close();
  } else {
    // Gestisci errore di preparazione
    echo "ERRORE: Impossibile preparare la query. " . htmlspecialchars($con->error);
  }
} else {
  echo "ERRORE: Input non valido.";
}

// Chiudi la connessione
$con->close();
?>
