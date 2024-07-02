<?php
require_once('../config/db.php');
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Recupera i dati dal form, fai attenzione alla sanitizzazione dei dati solo se necessario
$titolo = $_REQUEST['titolo'];
$data_pubblicazione = $_REQUEST['data_pubblicazione'];
$autore = $_REQUEST['autore'];
$id_stagione = $_REQUEST['id_stagione'];
$tags = $_REQUEST['tags'];
$contenuto = $_REQUEST['contenuto'];
$data_ultima_modifica = date('Y-m-d H:i:s');

// Controlla se id_partita è impostato e non è nullo, altrimenti impostalo a NULL
$id_partita = isset($_REQUEST['id_partita']) && $_REQUEST['id_partita'] !== 'null' ? $_REQUEST['id_partita'] : null;

// Prepara la query SQL con istruzioni preparate
$sql = "INSERT INTO articoli (titolo, data_pubblicazione, autore, id_stagione, tags, contenuto, data_ultima_modifica";
$values = "VALUES (?, ?, ?, ?, ?, ?, ?";
$types = "sssssss";
$params = [$titolo, $data_pubblicazione, $autore, $id_stagione, $tags, $contenuto, $data_ultima_modifica];

if (!is_null($id_partita)) {
    $sql .= ", id_partita";
    $values .= ", ?";
    $types .= "s";
    $params[] = $id_partita;
}

$sql .= ") " . $values . ")";

// Prepara lo statement
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    // Bind dei parametri
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    // Esegui lo statement
    if (mysqli_stmt_execute($stmt)) {
        // Reindirizza alla pagina di provenienza
        header("Location: $url_provenienza");
        exit();
    } else {
        // Gestisci errori nell'esecuzione dello statement
        echo "ERROR: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
    }

    // Chiudi lo statement
    mysqli_stmt_close($stmt);
} else {
    // Gestisci errori nella preparazione dello statement
    echo "ERROR: Impossibile preparare la query. " . mysqli_error($con);
}

// Chiudi la connessione
mysqli_close($con);
?>
