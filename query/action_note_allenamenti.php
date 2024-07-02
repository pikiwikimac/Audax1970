<?php
session_start();
require_once('../config/db.php'); // Assicurati che il percorso sia corretto

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

$nota = isset($_POST['note_allenamento']) ? $_POST['note_allenamento'] : null;

// Use prepared statements to avoid SQL injection
$queryInserisciNota = "INSERT INTO note_allenamenti (id_allenamento, descrizione) VALUES (?, ?)";

// Create a prepared statement
$stmt = mysqli_prepare($con, $queryInserisciNota);

// Bind parameters and execute the statement
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "is", $id, $nota);
    if (mysqli_stmt_execute($stmt)) {
        // Reindirizzamento alla pagina allenamenti_admin
        header("Location: ../admin/edit_presenza_allenamento.php?id=" . $id);
        exit();
    } else {
        echo "ERROR: Impossibile. " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "ERROR: Unable to prepare the statement. " . mysqli_error($con);
}

mysqli_close($con);
?>
