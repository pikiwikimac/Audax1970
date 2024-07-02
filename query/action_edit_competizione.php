<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_stagione'])) {
    $id_stagione = $_POST['id_stagione'];
    $nome_stagione = !empty($_POST['nome_stagione']) ? $_POST['nome_stagione'] : null;
    $anno_inizio = !empty($_POST['anno_inizio']) ? $_POST['anno_inizio'] : null;
    $anno_fine = !empty($_POST['anno_fine']) ? $_POST['anno_fine'] : null;
    $descrizione = !empty($_POST['descrizione']) ? $_POST['descrizione'] : null;
    $girone = !empty($_POST['girone']) ? $_POST['girone'] : null;
    $prima_squadra = !empty($_POST['prima_squadra']) ? $_POST['prima_squadra'] : null;
    

    $sql = "UPDATE stagioni
            SET nome_stagione=?, anno_inizio=?, anno_fine=?, descrizione=?,  girone=?, prima_squadra=?
            WHERE id_stagione=?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $nome_stagione, $anno_inizio, $anno_fine, $descrizione, $girone,$prima_squadra,$id_stagione);

        if (mysqli_stmt_execute($stmt)) {
            // Chiudere lo statement e la connessione
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            header("Location: ../admin/competizioni.php");
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
