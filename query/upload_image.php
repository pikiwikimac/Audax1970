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
$player = mysqli_fetch_assoc($result);

$nome_giocatore = $player['cognome'];
$image_path_precedente = $player['image_path'];

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
            
            // Genera il nuovo nome del file: id_nomegiocatore.estensione
            $file_name_sanitized = preg_replace('/[^a-zA-Z0-9-_]/', '', $nome_giocatore); // Rimuove caratteri speciali
            $target_file = $target_dir . $id . "_" . $file_name_sanitized . "." . $file_ext;

            // Rimuovi l'immagine precedente se esiste
            if ($image_path_precedente && file_exists($target_dir . $image_path_precedente)) {
                unlink($target_dir . $image_path_precedente);
            }

            // Sposta il file dalla cartella temporanea alla cartella target
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Aggiorna il percorso dell'immagine nel database
                $update_query = "UPDATE giocatori SET image_path = '" . $id . "_" . $file_name_sanitized . "." . $file_ext . "' WHERE id = '$id'";
                mysqli_query($con, $update_query);

                // Reindirizza alla pagina di modifica del giocatore
                header("Location: ../admin/edit_player.php?id=$id");
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
