<?php
require_once('../config/db.php');

// Verifica se è stato inviato un referer sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Recupera i dati dal form, assicurandoti di sanitizzare solo se necessario
$nome_materiale = $_POST['nome_materiale'] ?? '';
$id_stagione = $_POST['id_stagione'] ?? '';

// Prepara l'istruzione SQL utilizzando i segnaposti
$sql = "INSERT INTO materiali (nome_materiale, id_stagione) VALUES (?, ?)";

if ($stmt = mysqli_prepare($con, $sql)) {
    // Associa i valori ai segnaposti
    mysqli_stmt_bind_param($stmt, "si", $nome_materiale, $id_stagione);

    // Esegui lo statement
    if(mysqli_stmt_execute($stmt)) {
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

// Chiudi la connessione al database
mysqli_close($con);
?>