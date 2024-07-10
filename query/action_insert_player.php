<?php
require_once('../config/db.php');
$url_provenienza = $_SERVER['HTTP_REFERER'];

// Taking all values from the form data(input)
$maglia = isset($_REQUEST['maglia']) ? $_REQUEST['maglia'] : null;
$taglia = isset($_REQUEST['taglia']) ? $_REQUEST['taglia'] : null;
$nome = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : null;
$cognome = isset($_REQUEST['cognome']) ? $_REQUEST['cognome'] : null;

$data_nascita = isset($_REQUEST['data_nascita']) ? $_REQUEST['data_nascita'] : null;
$data_nascita_timestamp = date('Y-m-d', strtotime($data_nascita));

$squadra = isset($_REQUEST['squadra']) ? $_REQUEST['squadra'] : null;
$ruolo = isset($_REQUEST['ruolo']) ? $_REQUEST['ruolo'] : null;

$visita_medica = isset($_REQUEST['visita_medica']) ? $_REQUEST['visita_medica'] : null;
$visita_medica_timestamp = date('Y-m-d', strtotime($visita_medica));

$tipo_contratto = isset($_REQUEST['tipo_contratto']) ? $_REQUEST['tipo_contratto'] : null;
$capitano = isset($_REQUEST['capitano']) ? $_REQUEST['capitano'] : null;
$matricola = isset($_REQUEST['matricola']) ? $_REQUEST['matricola'] : null;

$data_tesseramento = isset($_REQUEST['data_tesseramento']) ? $_REQUEST['data_tesseramento'] : null;
$data_tesseramento_timestamp = date('Y-m-d', strtotime($data_tesseramento));

$anno_scadenza_tesseramento = isset($_REQUEST['anno_scadenza_tesseramento']) ? $_REQUEST['anno_scadenza_tesseramento'] : null;
$codice_fiscale = isset($_REQUEST['codice_fiscale']) ? $_REQUEST['codice_fiscale'] : null;



$sql = "INSERT INTO giocatori (nome, cognome, taglia, maglia, data_nascita, 
                            id_squadra, ruolo, visita_medica, tipo_contratto,
                            capitano, matricola, data_tesseramento,
                            anno_scadenza_tesseramento, codice_fiscale) 
        VALUES ('$nome', '$cognome', '$taglia', '$maglia', '$data_nascita_timestamp',
                '$squadra', '$ruolo', '$visita_medica_timestamp', '$tipo_contratto',
                 '$capitano', '$matricola', '$data_tesseramento_timestamp',
                '$anno_scadenza_tesseramento', '$codice_fiscale')";

if (mysqli_query($con, $sql)) {
    // Get the last inserted ID
    $id_giocatore = mysqli_insert_id($con);

    // Insert into affiliazioni_giocatori
    $sql_affiliazione = "INSERT INTO affiliazioni_giocatori (id_giocatore, id_societa) VALUES ('$id_giocatore', '$squadra')";
    
    if (mysqli_query($con, $sql_affiliazione)) {
        // Store details in session to display after redirect
        header("Location: $url_provenienza?msg=success&nome=$nome&cognome=$cognome&squadra=$squadra");
    } else {
        echo "ERROR: Hush! Sorry $sql_affiliazione. " . mysqli_error($con);
    }
} else {
    echo "ERROR: Hush! Sorry $sql. " . mysqli_error($con);
}

// Close connection
mysqli_close($con);
?>
