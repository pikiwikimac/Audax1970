<?php
session_start();
require_once('../config/db.php');

$id_allenamento = $_GET['id'];

$query = "SELECT file_path FROM allenamenti WHERE id = '$id_allenamento'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $file_path = $row['file_path'];
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            $query = "UPDATE allenamenti SET file_path = NULL WHERE id = '$id_allenamento'";
            if (mysqli_query($con, $query)) {
                $_SESSION['message'] = "File eliminato con successo.";
            } else {
                $_SESSION['message'] = "Errore durante l'aggiornamento del database: " . mysqli_error($con);
            }
        } else {
            $_SESSION['message'] = "Errore durante l'eliminazione del file dal server.";
            error_log("Error: Unable to delete file $file_path");
        }
    } else {
        $_SESSION['message'] = "File non trovato sul server.";
        error_log("Error: File $file_path not found");
    }
} else {
    $_SESSION['message'] = "Errore nel recupero del percorso del file dal database.";
    error_log("Error: Unable to fetch file path from database");
}

header("Location: ../admin/edit_presenza_allenamento.php?id=" . $id_allenamento);
exit;
?>
