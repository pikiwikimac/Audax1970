<?php
require_once('../config/db.php');

// Ottieni i dati inviati tramite POST
$playerId = $_POST['playerId'];
$materialName = $_POST['materialName'];
$checked = $_POST['checked'];

// Ottieni l'ID del materiale dalla tabella materiali
$queryMaterialId = "SELECT id FROM materiali WHERE nome_materiale = '$materialName'";
$resultMaterialId = mysqli_query($con, $queryMaterialId);

if ($resultMaterialId && mysqli_num_rows($resultMaterialId) > 0) {
    $rowMaterial = mysqli_fetch_assoc($resultMaterialId);
    $materialId = $rowMaterial['id'];

    // Se la checkbox è selezionata, aggiungi il record nella tabella giocatori_materiali
    if ($checked == 1) {
        $queryInsert = "INSERT INTO giocatori_materiali (id_giocatore, id_materiale) VALUES ('$playerId', '$materialId')";
        if (!mysqli_query($con, $queryInsert)) {
            echo "Errore nell'inserimento del materiale: " . mysqli_error($con);
        }
    } 
    // Se la checkbox è deselezionata, rimuovi il record dalla tabella giocatori_materiali
    else {
        $queryDelete = "DELETE FROM giocatori_materiali WHERE id_giocatore = '$playerId' AND id_materiale = '$materialId'";
        if (!mysqli_query($con, $queryDelete)) {
            echo "Errore nell'eliminazione del materiale: " . mysqli_error($con);
        }
    }
} else {
    echo "Errore nel recupero dell'ID del materiale: " . mysqli_error($con);
}

mysqli_close($con);
?>
