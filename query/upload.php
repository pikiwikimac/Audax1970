<?php
$targetDir = "../uploads/"; // Cartella di destinazione per gli allegati
$uploadOk = 1; // Flag per verificare se il caricamento è avvenuto con successo
$id = $_POST["id"]; // Ottieni l'ID del giocatore dalla richiesta

// Verifica se la cartella di destinazione esiste, altrimenti creala
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}
// Crea una sottodirectory per l'utente se non esiste
$userDir = $targetDir . $id . '/';
if (!file_exists($userDir)) {
    mkdir($userDir, 0755, true);
}

$targetFile = $userDir . basename($_FILES["formFile"]["name"]); // Percorso completo del file di destinazione
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Estensione del file

// Verifica se è stato effettuato un caricamento del file
if(isset($_POST["submit"])) {
    // Verifica se il file è un'immagine (puoi personalizzare questa verifica)
    $check = getimagesize($_FILES["formFile"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}

// Verifica se il file esiste già nella cartella di destinazione
if (file_exists($targetFile)) {
    $uploadOk = 0;
}

// Verifica la dimensione del file (esempio: massimo 5MB)
if ($_FILES["formFile"]["size"] > 5000000) {
    $uploadOk = 0;
}

// Consentire solo alcuni tipi di file (puoi personalizzare questa lista)
$allowedFileTypes = array("jpg", "jpeg", "png", "gif", "pdf", "txt", "doc", "docx");
if (!in_array($imageFileType, $allowedFileTypes)) {
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    // Il file non è stato caricato, mostra un alert
    echo "<script>alert('Il file non è stato caricato.');</script>";
} else {
    if (move_uploaded_file($_FILES["formFile"]["tmp_name"], $targetFile)) {
        // Reindirizza l'utente a player.php con l'ID del giocatore
        header("Location:../admin/player.php?id=" . $id);
        exit;
    } else {
        // Si è verificato un errore durante il caricamento del file, mostra un alert
        echo "<script>alert('Si è verificato un errore durante il caricamento del file.');</script>";
    }
}


?>
