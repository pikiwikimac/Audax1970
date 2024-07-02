<?php
  require_once('../config/db.php');
  $url_provenienza = $_SERVER['HTTP_REFERER'];

  $importo = mysqli_real_escape_string($con, $_REQUEST['importo']);
  $mittente = mysqli_real_escape_string($con, $_REQUEST['mittente']);
  $motivazione = mysqli_real_escape_string($con, $_REQUEST['motivazione']);
  $mese_competenza = mysqli_real_escape_string($con, $_REQUEST['mese_competenza']);

  // Prepara l'istruzione SQL utilizzando i segnaposto
  $sql = "INSERT INTO entrate (importo, nome_intestatario, giustificativo, mese_competenza)
          VALUES (?, ?, ?, ?)";

  if ($stmt = mysqli_prepare($con, $sql)) {
      // Associa i valori ai segnaposto
      mysqli_stmt_bind_param($stmt, "dsss", $importo, $mittente, $motivazione, $mese_competenza);

      if(mysqli_stmt_execute($stmt)){
          header("Location: $url_provenienza");
          exit();
      } else{
          echo "ERROR: Hush! Sorry ". mysqli_stmt_error($stmt);
          exit();
      }

      // Chiudi lo statement
      mysqli_stmt_close($stmt);
  } else {
      echo "ERROR: Hush! Sorry ". mysqli_error($con);
      exit();
  }

  // Close connection
  mysqli_close($con);
?>