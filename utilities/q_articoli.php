<?php
// utilities/q_articoli.php

require_once('config/db.php');

// Funzione per ottenere tutti gli articoli
function getArticoli($con) {
    $query = "
    SELECT a.*, ai.descrizione as intestazione
    FROM articoli a
    LEFT JOIN articoli_intestazioni ai ON ai.id = a.id_intestazione
    ORDER BY a.data_pubblicazione DESC";
    
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Errore nella query: " . mysqli_error($con));
    }

    return $result;
}

// Funzione per ottenere un articolo specifico tramite ID
function getArticoloById($con, $id) {
    $query = "
    SELECT a.*, ai.descrizione as intestazione
    FROM articoli a
    LEFT JOIN articoli_intestazioni ai ON ai.id = '$id'";
    
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Errore nella query: " . mysqli_error($con));
    }

    return mysqli_fetch_assoc($result);
}
?>
