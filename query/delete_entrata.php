<?php
require_once('../config/db.php');

$url_provenienza = $_SERVER['HTTP_REFERER'];

// Verifica se l'id è stato passato correttamente
if (isset($_GET['id'])) {
    // Sanitizzazione dell'id
    $id = mysqli_real_escape_string($con, $_GET['id']);

    // Prepara l'istruzione SQL
    $sql = "DELETE FROM entrate WHERE id = ?";

    // Prepara lo statement SQL
    if ($stmt = mysqli_prepare($con, $sql)) {
        // Associa l'id al parametro nella query preparata
        mysqli_stmt_bind_param($stmt, "i", $id);

        // Esegui lo statement
        if (mysqli_stmt_execute($stmt)) {
            // Reindirizza alla pagina precedente dopo l'eliminazione
            header("Location: $url_provenienza");
            exit();
        } else {
            // Gestione degli errori di esecuzione dello statement
            echo "ERRORE: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
            exit();
        }

        // Chiudi lo statement
        mysqli_stmt_close($stmt);
    } else {
        // Gestione degli errori nella preparazione dello statement
        echo "ERRORE: Impossibile preparare la query. " . mysqli_error($con);
        exit();
    }
} else {
    // Gestione del caso in cui l'id non sia stato passato
    echo "ERRORE: Id mancante.";
    exit();
}

// Chiudi la connessione al database
mysqli_close($con);
?>
