<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];

$id = $_REQUEST['id'];

$nome = isset($_POST['nome']) ? $_POST['nome'] : null;
$cognome = isset($_POST['cognome']) ? $_POST['cognome'] : null;
$maglia = isset($_POST['maglia']) ? $_POST['maglia'] : null;
$taglia = isset($_POST['taglia']) ? $_POST['taglia'] : null;
$data_nascita = isset($_POST['data_nascita']) ? $_POST['data_nascita'] : null;
$data_nascita_timestamp = date('Y-m-d', strtotime($data_nascita));
$squadra = isset($_POST['squadra']) ? $_POST['squadra'] : null;
$piede_preferito = isset($_POST['piede_preferito']) ? $_POST['piede_preferito'] : null;
$altezza = isset($_POST['altezza']) ? $_POST['altezza'] : null;
$peso = isset($_POST['peso']) ? $_POST['peso'] : null;
// Format the 'visita_medica' value as a valid date
$visita_medica = isset($_POST['visita_medica']) ? date('Y-m-d', strtotime($_POST['visita_medica'])) : null;
$tipo_contratto = isset($_POST['tipo_contratto']) ? $_POST['tipo_contratto'] : null;
$ruolo = isset($_POST['ruolo']) ? $_POST['ruolo'] : null;
// Handle the 'matricola' field properly for integer values
$matricola = isset($_POST['matricola']) ? intval($_POST['matricola']) : null;
$data_tesseramento = isset($_POST['data_tesseramento']) ? date('Y-m-d', strtotime($_POST['data_tesseramento'])) : null;
$anno_scadenza_tesseramento = isset($_POST['anno_scadenza_tesseramento']) ? intval($_POST['anno_scadenza_tesseramento']) : null;
$codice_fiscale = isset($_POST['codice_fiscale']) ? $_POST['codice_fiscale'] : null;
$capitano = isset($_POST['capitano']) ? $_POST['capitano'] : null;

// Prepare the SQL query with placeholders for the fields that may be updated
$query = "UPDATE giocatori SET ";
$updates = array();

if ($nome !== null) {
    $updates[] = "nome='$nome'";
}
if ($cognome !== null) {
    $updates[] = "cognome='$cognome'";
}
if ($taglia !== null) {
    $updates[] = "taglia='$taglia'";
}

if ($data_nascita !== null) {
    $updates[] = "data_nascita='$data_nascita_timestamp'";
}

if ($squadra !== null) {
    $updates[] = "id_squadra='$squadra'";
}

if ($piede_preferito !== null) {
    $updates[] = "piede_preferito='$piede_preferito'";
}

if ($altezza !== null) {
    $updates[] = "altezza='$altezza'";
}

if ($peso !== null) {
    $updates[] = "peso='$peso'";
}

if ($visita_medica !== null) {
    $updates[] = "visita_medica='$visita_medica'";
}
if ($tipo_contratto !== null) {
    $updates[] = "tipo_contratto='$tipo_contratto'";
}

if ($maglia !== null) {
    $updates[] = "maglia='$maglia'";
}

if ($ruolo !== null) {
    $updates[] = "ruolo='$ruolo'";
}

if ($matricola !== null) {
    $updates[] = "matricola='$matricola'";
}

if ($data_tesseramento !== null) {
    $updates[] = "data_tesseramento='$data_tesseramento'";
}
if ($anno_scadenza_tesseramento !== null) {
    $updates[] = "anno_scadenza_tesseramento='$anno_scadenza_tesseramento'";
}

if ($codice_fiscale !== null) {
    $updates[] = "codice_fiscale='$codice_fiscale'";
}
if ($capitano !== null) {
    $updates[] = "capitano='$capitano'";
}

// Combine the updates into the query
if (!empty($updates)) {
    $query .= implode(', ', $updates);
    $query .= " WHERE id = '$id'";
} else {
    echo "Nessun campo da aggiornare.";
    exit; // Exit the script if no fields are updated.
}

// Esegui la query di aggiornamento
if (mysqli_query($con, $query)) {
    // Aggiorna le affiliazioni giocatore
    // Ottieni le affiliazioni selezionate dalla form
    $affiliazioni_selezionate = isset($_POST['affiliazioni']) ? $_POST['affiliazioni'] : array();

    // Elimina tutte le affiliazioni esistenti per questo giocatore
    $delete_query = "DELETE FROM affiliazioni_giocatori WHERE id_giocatore = '$id'";
    mysqli_query($con, $delete_query);

    // Inserisci le nuove affiliazioni selezionate
    foreach ($affiliazioni_selezionate as $societa_id) {
        $insert_query = "INSERT INTO affiliazioni_giocatori (id_giocatore, id_societa) VALUES ('$id', '$societa_id')";
        mysqli_query($con, $insert_query);
    }

    // Reindirizza alla pagina di visualizzazione della squadra
    header("Location: ../admin/player.php?id=".$id);
} else {
    echo "ERRORE: Si è verificato un errore nell'esecuzione della query: " . mysqli_error($con);
}

// Chiudi la connessione al database
mysqli_close($con);
?>
