<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../config/db.php');

$id = $_POST['id'];

// Recupera il nome del giocatore dal database
$query = "SELECT cognome, image_path FROM giocatori WHERE id = '$id'";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Errore nella query: " . mysqli_error($con));
}
$player = mysqli_fetch_assoc($result);

if (!$player) {
    die("Giocatore non trovato.");
}

$nome_giocatore = $player['cognome'];
$image_path_precedente = $player['image_path'];

// Funzione per ridimensionare l'immagine
function resizeImage($file, $max_width, $max_height) {
    list($width, $height) = getimagesize($file);
    if ($width === false || $height === false) {
        return null; // Non è possibile ottenere le dimensioni dell'immagine
    }
    
    $ratio = $width / $height;

    // Controlla se il ridimensionamento è necessario
    if ($width <= $max_width && $height <= $max_height) {
        return imagecreatefromstring(file_get_contents($file)); // Nessun ridimensionamento necessario
    }

    if ($ratio > 1) {
        $new_width = $max_width;
        $new_height = $max_width / $ratio;
    } else {
        $new_height = $max_height;
        $new_width = $max_height * $ratio;
    }

    $src = imagecreatefromstring(file_get_contents($file));
    if (!$src) {
        return null; // Impossibile caricare l'immagine
    }

    $dst = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return $dst;
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
            $target_dir = "../image/player/";

            // Genera il nuovo nome del file: id_nomegiocatore.webp
            $file_name_sanitized = preg_replace('/[^a-zA-Z0-9-_]/', '', $nome_giocatore); // Rimuove caratteri speciali
            $target_file = $target_dir . $id . "_" . $file_name_sanitized . ".webp"; // Salva come WebP

            // Rimuovi l'immagine precedente se esiste
            if ($image_path_precedente && file_exists($target_dir . $image_path_precedente)) {
                if (!unlink($target_dir . $image_path_precedente)) {
                    die("Errore nella rimozione dell'immagine precedente.");
                }
            }

            // Ridimensiona l'immagine
            $resized_image = resizeImage($file_tmp, 800, 800); // 800x800 è l'area massima
            if ($resized_image) {
                // Salva l'immagine in formato WebP
                if (!imagewebp($resized_image, $target_file, 85)) {
                    die("Errore nel salvataggio dell'immagine in formato WebP.");
                }
                imagedestroy($resized_image); // Libera la memoria

                // Aggiorna il percorso dell'immagine nel database
                $update_query = "UPDATE giocatori SET image_path = '" . $id . "_" . $file_name_sanitized . ".webp' WHERE id = '$id'";
                if (!mysqli_query($con, $update_query)) {
                    die("Errore nell'aggiornamento del database: " . mysqli_error($con));
                }

                // Reindirizza alla pagina di modifica del giocatore
                header("Location: ../admin/edit_player.php?id=$id");
                exit;
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
    echo "Nessun file caricato.";
}
?>
