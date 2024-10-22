<?php
require_once('config/db.php');

// Funzione per ottenere le informazioni della squadra
function getInfoSquadra($con, $id) {
    $query = "
      SELECT s.*
      FROM societa s
      WHERE s.id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function getDirigenti($con) {
    $query = "
    SELECT nome, ruolo, ordinamento, image_path
    FROM dirigenti
    WHERE ordinamento > 0
    ORDER BY CAST(ordinamento AS UNSIGNED) ASC, nome;";

    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->get_result();
}
?>
