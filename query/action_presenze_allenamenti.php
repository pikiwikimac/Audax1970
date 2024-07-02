<?php
session_start();
require_once('../config/db.php'); // Assicurati che il percorso sia corretto

$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($id === null) {
    echo "Errore: L'ID dell'allenamento non è stato fornito.";
    exit();
}

$presenze = isset($_POST['presenza']) ? $_POST['presenza'] : array();

// Rimuovi le vecchie presenze per questo allenamento
$queryDeletePresenze = "DELETE FROM partecipazione_allenamenti WHERE id_allenamento = $id";
if (mysqli_query($con, $queryDeletePresenze)) {
    // Aggiungi le nuove presenze selezionate
    foreach ($presenze as $giocatore_id) {
        $queryInserisciPresenza = "INSERT INTO partecipazione_allenamenti (id_allenamento, id_giocatore) VALUES ($id, $giocatore_id)";
        mysqli_query($con, $queryInserisciPresenza);
    }

    // Reindirizzamento alla pagina allenamenti_admin
    header("Location: ../admin/allenamenti_admin.php");
    exit();
} else {
    echo "ERROR: Impossibile eliminare le vecchie presenze. " . mysqli_error($con);
    exit();
}
?>
