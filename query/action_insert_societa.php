<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];
$id = $_REQUEST['id'];

$nome_societa = isset($_POST['nome_societa']) ? mysqli_real_escape_string($con, $_POST['nome_societa']) : null;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
$sede = isset($_POST['sede']) ? $_POST['sede'] : null;
$sede_legale = isset($_POST['sede_legale']) ? $_POST['sede_legale'] : null;
$citta = isset($_POST['citta']) ? $_POST['citta'] : null;
$giorno_settimana = isset($_POST['giorno_settimana']) ? $_POST['giorno_settimana'] : null;
$ora_match = isset($_POST['ora_match']) ? $_POST['ora_match'] : null;
$presidente = isset($_POST['presidente']) ? $_POST['presidente'] : null;
$vicepresidente = isset($_POST['vicepresidente']) ? $_POST['vicepresidente'] : null;
$allenatore = isset($_POST['allenatore']) ? $_POST['allenatore'] : null;
$campionato = isset($_POST['campionato']) ? $_POST['campionato'] : null;
$contatto_riferimento = isset($_POST['contatto_riferimento']) ? $_POST['contatto_riferimento'] : null;
$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : null;
$instagram = isset($_POST['instagram']) ? $_POST['instagram'] : null;
$sito_web = isset($_POST['sito_web']) ? $_POST['sito_web'] : null;
$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;

// Initialize an array to hold the columns and an array to hold the values
$columns = [];
$values = [];

// Check each field and add it to the respective arrays if not null
if (!is_null($nome_societa)) {
    $columns[] = 'nome_societa';
    $values[] = "'$nome_societa'";
}
if (!is_null($tipo)) {
    $columns[] = 'tipo';
    $values[] = "'$tipo'";
}
if (!is_null($sede)) {
    $columns[] = 'sede';
    $values[] = "'$sede'";
}
if (!is_null($sede_legale)) {
    $columns[] = 'sede_legale';
    $values[] = "'$sede_legale'";
}
if (!is_null($citta)) {
    $columns[] = 'citta';
    $values[] = "'$citta'";
}
if (!is_null($giorno_settimana)) {
    $columns[] = 'giorno_settimana';
    $values[] = "'$giorno_settimana'";
}
if (!is_null($ora_match)) {
    $columns[] = 'ora_match';
    $values[] = "'$ora_match'";
}
if (!is_null($presidente)) {
    $columns[] = 'presidente';
    $values[] = "'$presidente'";
}
if (!is_null($vicepresidente)) {
    $columns[] = 'vicepresidente';
    $values[] = "'$vicepresidente'";
}
if (!is_null($allenatore)) {
    $columns[] = 'allenatore';
    $values[] = "'$allenatore'";
}

if (!is_null($campionato)) {
    $columns[] = 'id_campionato';
    $values[] = "'$campionato'";
}

if (!is_null($contatto_riferimento)) {
    $columns[] = 'contatto_riferimento';
    $values[] = "'$contatto_riferimento'";
}

if (!is_null($telefono)) {
    $columns[] = 'telefono';
    $values[] = "'$telefono'";
}
if (!is_null($email)) {
    $columns[] = 'email';
    $values[] = "'$email'";
}
if (!is_null($whatsapp)) {
    $columns[] = 'whatsapp';
    $values[] = "'$whatsapp'";
}
if (!is_null($instagram)) {
    $columns[] = 'instagram';
    $values[] = "'$instagram'";
}
if (!is_null($sito_web)) {
    $columns[] = 'sito_web';
    $values[] = "'$sito_web'";
}
if ($parent_id != 0) {
    $columns[] = 'parent_id';
    $values[] = "'$parent_id'";
}


// Check if any columns are set (i.e., data is provided)
if (!empty($columns)) {
    // Construct the SQL query using the collected columns and values
    $sql = "INSERT INTO societa (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
    
    if (mysqli_query($con, $sql)) {
        header("Location: ../admin/societa.php");
    } else {
        echo "ERROR: Hush! Sorry $sql. " . mysqli_error($con);
    }
} else {
    echo "No data provided for insertion.";
}

// Close connection
mysqli_close($con);
?>
