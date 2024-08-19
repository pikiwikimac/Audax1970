<?php
session_start();
require_once('../config/db.php');

$id_allenamento = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_allegato'])) {
    $file_name = $_FILES['file_allegato']['name'];
    $file_tmp = $_FILES['file_allegato']['tmp_name'];
    $file_path = "../image/allenamenti/" . basename($file_name);

    if (move_uploaded_file($file_tmp, $file_path)) {
        $query = "UPDATE allenamenti SET file_path = '$file_path' WHERE id = '$id_allenamento'";
        mysqli_query($con, $query);
        $_SESSION['message'] = "File caricato con successo.";
    } else {
        $_SESSION['message'] = "Errore durante il caricamento del file.";
    }
}

header("Location: ../admin/edit_presenza_allenamento.php?id=" . $id_allenamento);
exit;
?>
