<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $titolo = $_POST['titolo'];
    $data_pubblicazione = $_POST['data_pubblicazione'];
    $autore = $_POST['autore'];
    $id_stagione = $_POST['id_stagione'];
    $id_partita = !empty($_POST['id_partita']) ? $_POST['id_partita'] : null; // Gestione di id_partita come NULL
    $tags = $_POST['tags'];
    $contenuto = $_POST['contenuto'];
    $data_ultima_modifica = date('Y-m-d H:i:s');

    $sql = "UPDATE articoli
            SET titolo=?, data_pubblicazione=?, autore=?, id_stagione=?, id_partita=?, tags=?, contenuto=?, data_ultima_modifica=?
            WHERE id=?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssiisssi", $titolo, $data_pubblicazione, $autore, $id_stagione, $id_partita, $tags, $contenuto, $data_ultima_modifica, $id);

        if (mysqli_stmt_execute($stmt)) {
            // Chiudere lo statement e la connessione
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            // Reindirizzamento all'URL referer
            $url_provenienza = $_SERVER['HTTP_REFERER'];
            header("Location: $url_provenienza");
            exit;
        } else {
            echo "ERRORE: Impossibile eseguire l'istruzione. " . mysqli_error($con);
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "ERRORE: Impossibile preparare l'istruzione. " . mysqli_error($con);
    }
    // Chiudere la connessione in caso di errore
    mysqli_close($con);
} else {
    echo "ERRORE: Richiesta non valida.";
}
?>
