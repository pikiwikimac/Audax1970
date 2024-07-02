<?php
    require_once('../config/db.php');

    $url_provenienza = $_SERVER['HTTP_REFERER'];

    $id = $_REQUEST['id'];

    $nome_societa =  isset($_POST['nome_societa']) ? mysqli_real_escape_string($con, $_POST['nome_societa']) : null;
    $tipo =  isset($_POST['tipo']) ? mysqli_real_escape_string($con, $_POST['tipo']) : null;
    $sede = isset($_POST['sede']) ? mysqli_real_escape_string($con, $_POST['sede']) : null;
    $sede_legale = isset($_POST['sede_legale']) ? mysqli_real_escape_string($con, $_POST['sede_legale']) : null;
    $citta = isset($_POST['citta']) ? mysqli_real_escape_string($con, $_POST['citta']) : null;
    $giorno_settimana =  isset($_POST['giorno_settimana']) ? $_POST['giorno_settimana'] : null;
    $ora_match = isset($_POST['ora_match']) ? $_POST['ora_match'] : null;
    $presidente = isset($_POST['presidente']) ? $_POST['presidente'] : null;
    $vicepresidente = isset($_POST['vicepresidente']) ? $_POST['vicepresidente'] : null;
    $campionato = isset($_POST['campionato']) ? $_POST['campionato'] : null;
    $logo = isset($_POST['logo']) ? $_POST['logo'] : null;
    $contatto_riferimento = isset($_POST['contatto_riferimento']) ? $_POST['contatto_riferimento'] : null;
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : null;
    $instagram = isset($_POST['instagram']) ? $_POST['instagram'] : null;
    $sito_web = isset($_POST['sito_web']) ? $_POST['sito_web'] : null;
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
  
    // Prepare the SQL query with placeholders for the fields that may be updated
    $query = "UPDATE societa SET ";
    $updates = array();

  if ($nome_societa !== null) {
      $updates[] = "nome_societa='$nome_societa'";
  }
  if ($tipo !== null) {
      $updates[] = "tipo='$tipo'";
  }
  if ($sede !== null) {
      $updates[] = "sede='$sede'";
  }
  if ($sede_legale !== null) {
      $updates[] = "sede_legale='$sede_legale'";
  }
  if ($citta !== null) {
      $updates[] = "citta='$citta'";
  }
  if ($giorno_settimana !== null) {
      $updates[] = "giorno_settimana='$giorno_settimana'";
  }
  if ($ora_match !== null) {
      $updates[] = "ora_match='$ora_match'";
  }
  if ($presidente !== null) {
      $updates[] = "presidente='$presidente'";
  }
  if ($vicepresidente !== null) {
      $updates[] = "vicepresidente='$vicepresidente'";
  }
  if ($campionato !== null) {
      $updates[] = "id_campionato='$campionato'";
  }
  if ($logo !== null) {
      $updates[] = "logo='$logo'";
  }
  
  if ($contatto_riferimento !== null) {
      $updates[] = "contatto_riferimento='$contatto_riferimento'";
  }
  
  if ($telefono !== null) {
      $updates[] = "telefono='$telefono'";
  }
  
  if ($email !== null) {
      $updates[] = "email='$email'";
  }
  
  if ($whatsapp !== null) {
      $updates[] = "whatsapp='$whatsapp'";
  }
  
  if ($instagram !== null) {
      $updates[] = "instagram='$instagram'";
  }
  
  if ($sito_web !== null) {
      $updates[] = "sito_web='$sito_web'";
  }

  if ($parent_id !== null) {
      $updates[] = "parent_id='$parent_id'";
  }
  
  

  // Combine the updates into the query
  if (!empty($updates)) {
      $query .= implode(', ', $updates);
      $query .= " WHERE id = '$id'";
  } else {
      echo "Nessun campo da aggiornare.";
      exit; // Exit the script if no fields are updated.
  }


  
  // ...

if(mysqli_query($con, $query)){
  header("Location: ../admin/societa.php?");
} else{
  echo "ERROR: Hush! Sorry $query. ". mysqli_error($con);
}

// Close connection
mysqli_close($con);

?>



