<?php
session_start();
require_once('../config/db.php'); // Assicurati che il percorso sia corretto

$url_provenienza = $_SERVER['HTTP_REFERER'];

// Verifica che la connessione al database sia stata inclusa correttamente
if (!$con) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

$id_giocatore = isset($_REQUEST['id_giocatore']) ? intval($_REQUEST['id_giocatore']) : null;
$id_societa = isset($_REQUEST['id_societa']) ? intval($_REQUEST['id_societa']) : null;

// Controlla se i parametri sono validi
if ($id_giocatore === null || $id_societa === null) {
    die("ID del giocatore e ID della società sono richiesti.");
}

// Ottieni la data attuale
$data_attuale = date("Y-m-d");

// Query per aggiornare la data_fine con la data attuale
$sql = "UPDATE affiliazioni_giocatori SET data_fine = ? WHERE id_giocatore = ? AND id_societa = ?";

// Prepara la query
$stmt = $con->prepare($sql);
if ($stmt) {
    // Bind dei parametri
    $stmt->bind_param("sii", $data_attuale, $id_giocatore, $id_societa);

    // Esegui la query
    if ($stmt->execute()) {
        // Reindirizza alla pagina di provenienza
        header("Location: $url_provenienza");
        exit();
    } else {
        echo "Errore nell'aggiornamento dei record: " . $stmt->error;
    }

    // Chiudi lo statement
    $stmt->close();
} else {
    echo "Errore nella preparazione della query: " . $con->error;
}

// Chiudi la connessione
$con->close();
?>
