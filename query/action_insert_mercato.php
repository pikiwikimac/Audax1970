<?php
require_once('../config/db.php');

$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

$nome = $_POST['nome'] ?? '';
$cognome = $_POST['cognome'] ?? '';
$realizzazione = $_POST['realizzazione'] ?? '';
$ruolo = $_POST['ruolo'] ?? '';
$note = $_POST['note'] ?? '';

$sql = "INSERT INTO mercato (nome, cognome, realizzazione, ruolo, note) 
        VALUES (?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, "sssss", $nome, $cognome, $realizzazione, $ruolo, $note);

    if(mysqli_stmt_execute($stmt)) {
        header("Location: $url_provenienza");
        exit();
    } else {
        echo "ERROR: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "ERROR: Impossibile preparare la query. " . mysqli_error($con);
}

mysqli_close($con);
?>
