<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$id = $_POST['id'];



// Verifica se è stato inviato un file
if (isset($_FILES['playerImage'])) {
    $file_name = $_FILES['playerImage']['name'];
    $file_size = $_FILES['playerImage']['size'];
    $file_tmp = $_FILES['playerImage']['tmp_name'];
    $file_type = $_FILES['playerImage']['type'];

    // Verifica il tipo di file (ad esempio, immagine)
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    if (in_array($file_ext, $valid_extensions)) {
        // Verifica la dimensione massima consentita (in bytes)
        $max_size = 5 * 1024 * 1024; // 5 MB
        if ($file_size <= $max_size) {
            $target_dir = "../image/articoli/";
            $target_file = $target_dir . $id . "." . $file_ext;
            
            // Sposta il file dalla cartella temporanea alla cartella target
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Aggiorna il percorso dell'immagine nel database
                $update_query = "UPDATE articoli SET immagine_url = '$id.$file_ext' WHERE id = '$id'";
                mysqli_query($con, $update_query);
                header("Location: ../admin/edit_articolo.php?id=$id");
                exit;
            } else {
                echo "Errore durante il caricamento del file.";
            }
        } else {
            echo "Il file è troppo grande. La dimensione massima consentita è 5 MB.";
        }
    } else {
        echo "Sono consentiti solo file di tipo JPG, JPEG, PNG e GIF.";
    }
} else {
    echo "Nessun file inviato.";
}
?>
