<?php
require_once('../config/db.php');

// Verifica se è stato inviato un referer sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Recupera i dati dal form, assicurandosi di sanitizzare solo se necessario
$nome = $_POST['nome'] ?? '';
$ruolo = $_POST['ruolo'] ?? '';
$documento = $_POST['documento'] ?? '';

// Inizializza $data a NULL
$data = null;

// Verifica se è stata inviata la data di nascita e non è vuota
if(isset($_POST['data_nascita']) && !empty($_POST['data_nascita'])) {
    $data = $_POST['data_nascita'];
}

// Utilizza la funzione di escape dei dati fornita dalla connessione al database
$nome = mysqli_real_escape_string($con, $nome);
$ruolo = mysqli_real_escape_string($con, $ruolo);
$documento = mysqli_real_escape_string($con, $documento);

// Prepara l'istruzione SQL utilizzando i segnaposti
$sql = "INSERT INTO dirigenti (nome, ruolo, documento, data_nascita)
        VALUES (?, ?, ?, ?)";

if ($stmt = mysqli_prepare($con, $sql)) {
    // Associa i valori ai segnaposti e imposta il tipo di dato per la data (se presente)
    mysqli_stmt_bind_param($stmt, "ssss", $nome, $ruolo, $documento, $data);

    // Esegui lo statement
    if(mysqli_stmt_execute($stmt)) {
        // Reindirizza alla pagina dei dirigenti
        header("Location: ../admin/dirigenti.php");
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
