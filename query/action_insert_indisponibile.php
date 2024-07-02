<?php
require_once('../config/db.php');

// Verifica se è stato inviato un referer sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Recupera i dati dal form, assicurandoti di sanitizzare solo se necessario
$giocatore = $_POST['giocatore'] ?? '';
$motivo = $_POST['motivo'] ?? '';
$note = $_POST['note'] ?? '';
$da_data = $_POST['da_data'] ?? '';
$a_data = $_POST['a_data'] ?? '';

// Utilizza la funzione di escape dei dati fornita dalla connessione al database
$note = mysqli_real_escape_string($con, $note);

// Prepara l'istruzione SQL utilizzando i segnaposti
$sql = "INSERT INTO indisponibili (id_giocatore, motivo, note, da_data, a_data)
        VALUES (?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($con, $sql)) {
    // Associa i valori ai segnaposti
    mysqli_stmt_bind_param($stmt, "issss", $giocatore, $motivo, $note, $da_data, $a_data);

    // Esegui lo statement
    if(mysqli_stmt_execute($stmt)) {
        // Reindirizza alla pagina degli indisponibili per gli amministratori
        header("Location: ../admin/indisponibili_admin.php");
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

// Chiudi la connessione al database
mysqli_close($con);
?>
