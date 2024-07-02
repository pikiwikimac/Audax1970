<?php
  require_once('../config/db.php');

  // Sanitize input
  $id_nota = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $id_allenamento = isset($_GET['id_allenamento']) ? intval($_GET['id_allenamento']) : 0;

  // Check if the IDs are valid
  if ($id_nota > 0 && $id_allenamento > 0) {
    // Prepare the SQL statement
    $stmt = $con->prepare("DELETE FROM note_allenamenti WHERE id = ?");
      
    if ($stmt) {
      // Bind parameters
      $stmt->bind_param("i", $id_nota);
      
      // Execute the statement
      if ($stmt->execute()) {
        // Redirect on success
        header("Location: ../admin/edit_presenza_allenamento.php?id=$id_allenamento");
        exit;
      } else {
        // Handle execution error
        echo "ERRORE: Impossibile eseguire la query. " . htmlspecialchars($stmt->error);
      }
      
      // Close the statement
      $stmt->close();
    } else {
      // Handle preparation error
      echo "ERRORE: Impossibile preparare la query. " . htmlspecialchars($con->error);
    }
  } else {
    echo "ERRORE: Input non valido.";
  }

  // Close the connection
  $con->close();
?>