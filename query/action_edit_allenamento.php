<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $data = $_POST['data'];
    $luogo = $_POST['luogo'];
    $note = $_POST['note'];
    $stato = $_POST['stato'];
    $orario = $_POST['orario'];
    $tipologia = $_POST['tipologia'];

    $sql = "UPDATE allenamenti
            SET data = ?, luogo = ?, note = ?, stato = ?, orario = ?, tipologia = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $data, $luogo, $note, $stato, $orario, $tipologia, $id);

        if (mysqli_stmt_execute($stmt)) {
            // Chiudere lo statement e la connessione
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            // Debug del valore di $url_provenienza
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
