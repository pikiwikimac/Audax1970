<?php
  require_once('../config/db.php');

  if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Esegui la query per aggiornare il campo "accettato" a 1
    $query = "UPDATE users SET accettato = 1 WHERE id = $id";
    $result = mysqli_query($con, $query);

    if($result) {
      // Aggiornamento riuscito
      header('Location: ../admin/gestore_registrazioni.php');
      exit;
    } else {
      // Errore nell'aggiornamento
      echo "Si è verificato un errore nell'aggiornamento del record.";
    }
  } else {
    // Parametro id non fornito
    echo "Parametro id non fornito.";
  }
?>

