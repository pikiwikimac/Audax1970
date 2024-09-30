<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$id = $_POST['id'];

// Funzione per ridimensionare l'immagine
function resizeImage($file, $max_width, $max_height) {
    list($width, $height) = getimagesize($file);
    $ratio = $width / $height;

    if ($width > $max_width || $height > $max_height) {
        if ($ratio > 1) {
            $new_width = $max_width;
            $new_height = $max_width / $ratio;
        } else {
            $new_height = $max_height;
            $new_width = $max_height * $ratio;
        }

        $src = imagecreatefromstring(file_get_contents($file));
        $dst = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        return $dst;
    }
    return null;
}

// Verifica se è stato inviato un file
if (isset($_FILES['playerImage']) && $_FILES['playerImage']['error'] == UPLOAD_ERR_OK) {
    $file_name = $_FILES['playerImage']['name'];
    $file_size = $_FILES['playerImage']['size'];
    $file_tmp = $_FILES['playerImage']['tmp_name'];

    // Verifica la dimensione massima consentita (in bytes)
    $max_size = 5 * 1024 * 1024; // 5 MB
    if ($file_size <= $max_size) {
        $target_dir = "../image/articoli/";
        $target_file = $target_dir . $id . ".webp"; // Salva come WebP

        // Elimina il file esistente se presente
        if (file_exists($target_file)) {
            unlink($target_file);
        }

        // Ridimensiona l'immagine
        $resized_image = resizeImage($file_tmp, 800, 800); // 800x800 è l'area massima
        if ($resized_image) {
            // Salva l'immagine in formato WebP
            imagewebp($resized_image, $target_file, 85); // qualità 85
            imagedestroy($resized_image); // Libera la memoria

            // Aggiorna il percorso dell'immagine nel database
            $update_query = "UPDATE articoli SET immagine_url = '$id.webp' WHERE id = '$id'";
            mysqli_query($con, $update_query);
            header("Location: ../admin/edit_articolo.php?id=$id&v=" . time());
            exit;
        } else {
            echo "Errore durante il ridimensionamento dell'immagine.";
        }
    } else {
        echo "Il file è troppo grande. La dimensione massima consentita è 5 MB.";
    }
} else {
    echo "Nessun file inviato o errore nel caricamento del file.";
}
?>
