<?php
session_start();
require_once('../config/db.php');

$id = $_REQUEST['id'];
$played = $_REQUEST['played'];

$url_provenienza = $_SERVER['HTTP_REFERER'];
$newPlayedValue = ($played == 0) ? 1 : 0; // Calcola il nuovo valore di 'played'

$query = "UPDATE partite SET played = '$newPlayedValue' WHERE id = '$id'";

if (mysqli_query($con, $query)) {
    header("Location: $url_provenienza");
    exit();
} else {
    echo "ERROR: Si è verificato un errore durante l'aggiornamento dei dati: " . mysqli_error($con);
    exit();
}
?>
