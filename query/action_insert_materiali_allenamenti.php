<?php
require_once('../config/db.php');

// Verifica se è stato inviato un referer sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Recupera i dati dal form, assicurandoti di sanitizzare solo se necessario
$nome_materiale = $_POST['nome_materiale'] ?? '';
$quantita = $_POST['quantita'] ?? '';
$costo = $_POST['costo_unitario'] ?? '';
$id_stagione = $_POST['id_stagione'] ?? '';

// Controlla che tutti i dati siano corretti (opzionale ma utile per il debug)
if (empty($nome_materiale) || !is_numeric($quantita) || !is_numeric($costo) || !is_numeric($id_stagione)) {
    die("ERROR: I dati inviati non sono corretti.");
}

// Prepara l'istruzione SQL utilizzando i segnaposti
$sql = "INSERT INTO materiale_allenamento (nome_materiale, costo, quantita, id_stagione) VALUES (?, ?, ?, ?)";

if ($stmt = mysqli_prepare($con, $sql)) {
    // Associa i valori ai segnaposti
    mysqli_stmt_bind_param($stmt, "siii", $nome_materiale, $costo, $quantita, $id_stagione);

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

// Chiudi la connessione al database
mysqli_close($con);
?>
