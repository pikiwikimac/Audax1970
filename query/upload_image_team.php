<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$id = $_POST['id'];

// Get the referring page URL
$referrer = $_SERVER['HTTP_REFERER'];

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
    return imagecreatefromstring(file_get_contents($file)); // Se non è necessario ridimensionare, restituisce l'immagine originale
}

// Verifica se è stato inviato un file
if (isset($_FILES['playerImage'])) {
    $file_name = $_FILES['playerImage']['name'];
    $file_size = $_FILES['playerImage']['size'];
    $file_tmp = $_FILES['playerImage']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    if (in_array($file_ext, $valid_extensions)) {
        $max_size = 5 * 1024 * 1024; // 5 MB
        if ($file_size <= $max_size) {
            $target_dir = "../image/loghi/";
            $target_file = $target_dir . $id . ".webp"; // Salva come WebP
            
            // Rimuovi file esistente con lo stesso ID
            foreach ($valid_extensions as $extension) {
                $existing_file = $target_dir . $id . "." . $extension;
                if (file_exists($existing_file)) {
                    unlink($existing_file); // Elimina il file precedente
                }
            }

            // Ridimensiona l'immagine
            $resized_image = resizeImage($file_tmp, 800, 800); // 800x800 è l'area massima
            if ($resized_image) {
                // Salva l'immagine in formato WebP
                if (imagewebp($resized_image, $target_file, 85)) { // qualità 85
                    imagedestroy($resized_image); // Libera la memoria

                    // Aggiorna il percorso dell'immagine nel database
                    $update_query = "UPDATE societa SET logo = '$id.webp' WHERE id = '$id'";
                    if (mysqli_query($con, $update_query)) {
                        header("Location: $referrer");
                        exit;
                    } else {
                        echo "Errore durante l'aggiornamento del database: " . mysqli_error($con);
                    }
                } else {
                    echo "Errore durante il salvataggio dell'immagine.";
                }
            } else {
                echo "Errore durante il ridimensionamento dell'immagine.";
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
