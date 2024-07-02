<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $ruolo = $_POST['ruolo'];
    $documento = $_POST['documento'];
    $data_nascita = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : null;

    $sql = "UPDATE dirigenti
            SET nome=?, ruolo=?, data_nascita=?, documento=?
            WHERE id=?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $nome, $ruolo, $data_nascita, $documento, $id);

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
