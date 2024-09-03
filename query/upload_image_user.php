<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$user_id = $_REQUEST['id'];

// Verifica se è stato inviato un file
if (isset($_FILES['userImage'])) {
    $file_name = $_FILES['userImage']['name'];
    $file_size = $_FILES['userImage']['size'];
    $file_tmp = $_FILES['userImage']['tmp_name'];
    $file_type = $_FILES['userImage']['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    if (in_array($file_ext, $valid_extensions)) {
        $max_size = 5 * 1024 * 1024; // 5 MB
        if ($file_size <= $max_size) {
            // Recupera l'username dell'utente
            $query = "SELECT username, image FROM users WHERE id = '$user_id'";
            $result = mysqli_query($con, $query);
            $user = mysqli_fetch_assoc($result);
            $username = $user['username'];

            $target_dir = "../image/username/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Rimuove l'immagine precedente se esiste
            if ($user['image']) {
                unlink($target_dir . $user['image']);
            }

            $target_file = $target_dir . $username . "." . $file_ext;

            // Sposta il file nella directory target
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Aggiorna il percorso dell'immagine nel database
                $update_query = "UPDATE users SET image = '$username.$file_ext' WHERE id = '$user_id'";
                mysqli_query($con, $update_query);
                header("Location: ../admin/edit_user.php?id=$user_id");
            } else {
                echo "Errore durante il caricamento del file.";
            }
        } else {
            echo "Il file è troppo grande. La dimensione massima consentita è 5 MB.";
        }
    } else {
        echo "Sono consentiti solo file di tipo JPG, JPEG, PNG e GIF.";
    }
}
?>
