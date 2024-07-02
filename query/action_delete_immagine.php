<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];

// Sanitize input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if the ID is valid
if ($id > 0) {
  // Prepare the SQL statement
  $stmt = $con->prepare("UPDATE giocatori SET image_path = 'image/user.png' WHERE id = ?");
  
  if ($stmt) {
    // Bind parameters
    $stmt->bind_param("i", $id);
    
    // Execute the statement
    if ($stmt->execute()) {
      // Redirect on success
      header("Location: $url_provenienza");
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
