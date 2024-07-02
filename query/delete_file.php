<?php
if (isset($_GET["file"])) {
    $fileToDelete = basename($_GET["file"]); // Assicurati di ottenere solo il nome del file, per sicurezza
    $directory = "../uploads/";

    // Verifica se il file esiste nella directory
    if (file_exists($directory . $fileToDelete)) {
        // Prova a eliminare il file
        if (unlink($directory . $fileToDelete)) {
            // Dopo l'eliminazione, reindirizza l'utente alla pagina precedente
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        } else {
            echo "Si è verificato un errore durante l'eliminazione del file.";
        }
    } else {
        echo "Il file non esiste.";
    }
} else {
    echo "Parametro del file mancante.";
}
?>
