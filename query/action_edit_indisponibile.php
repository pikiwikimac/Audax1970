<?php
require_once('../config/db.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $da_data = $_POST['da_data'];
    $a_data = $_POST['a_data'];
    $note = $_POST['note'];
    $motivo = $_POST['motivo'];
    $id_giocatore = (int)$_POST['giocatore'];

    $sql = "UPDATE indisponibili
            SET da_data=?, a_data=?, note=?, motivo=?, id_giocatore=?
            WHERE id=?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssii", $da_data, $a_data, $note, $motivo, $id_giocatore, $id);

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