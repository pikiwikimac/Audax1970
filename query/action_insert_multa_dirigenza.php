<?php

  require_once('../config/db.php');
  $url_provenienza = $_SERVER['HTTP_REFERER'];
 
  $dirigente = mysqli_real_escape_string($con, $_REQUEST['dirigente']);
  $data = mysqli_real_escape_string($con, $_REQUEST['data']);
  $importo = mysqli_real_escape_string($con, $_REQUEST['importo']);

  // Prepara l'istruzione SQL con i valori obbligatori
  $sql = "INSERT INTO multe_dirigenza (id_dirigente, data_multa, importo)
  VALUES (?, ?, ?)";

  if ($stmt = mysqli_prepare($con, $sql)) {
  // Associa i valori ai segnaposto
  mysqli_stmt_bind_param($stmt, "iss", $dirigente, $data, $importo);

  if(mysqli_stmt_execute($stmt)){
    header("Location: $url_provenienza");
  } else{
    echo "ERROR: Hush! Sorry ". mysqli_stmt_error($stmt);
  }

  // Chiudi lo statement
  mysqli_stmt_close($stmt);
}

  // Close connection
  mysqli_close($con);
?>



