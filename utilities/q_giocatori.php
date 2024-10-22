<?php
require_once('config/db.php');


// Funzione per ottenere i giocatori affiliati a una società
function getGiocatoriBySocieta($con, $id_societa) {
  $query = "
  SELECT g.*
  FROM giocatori g
  INNER JOIN societa s ON s.id = g.id_squadra
  INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id
  WHERE ag.id_societa = ?
  ORDER BY g.ruolo, g.cognome, g.nome";

  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $id_societa);
  $stmt->execute();
  return $stmt->get_result();
}


// Funzione per ottenere le stagioni delle squadre a cui il giocatore è affiliato
function getStagioniGiocatore($con, $id) {
    $query_stagioni = "
      SELECT DISTINCT stag.id_stagione, stag.descrizione as competizione, stag.girone
      FROM societa s
      INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
      INNER JOIN stagioni stag ON stag.id_stagione = s.id_campionato
      WHERE ag.id_giocatore = ?";

    $stmt = $con->prepare($query_stagioni);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

// Funzione per ottenere le statistiche del giocatore per una stagione
function getStatisticheGiocatorePerStagione($con, $id, $id_stagione) {
    $query = "
      SELECT g.*, s.nome_societa,
      (
        SELECT COUNT(*)
        FROM ammoniti a
        JOIN partite p ON a.id_partita = p.id
        WHERE a.id_giocatore = g.id
        AND p.id_stagione = ?
      ) AS numero_ammonizioni,
      (
        SELECT COUNT(*)
        FROM rossi r
        JOIN partite p ON r.id_partita = p.id
        WHERE r.id_giocatore = g.id
        AND p.id_stagione = ?
      ) AS numero_espulsioni,
      (
        SELECT COUNT(*)
        FROM marcatori m
        JOIN partite p ON m.id_partita = p.id
        WHERE m.id_giocatore = g.id
        AND p.id_stagione = ?
      ) AS numero_gol,
      (
        SELECT COUNT(*) as convocazioni
        FROM convocazioni c
        INNER JOIN partite p ON p.id = c.id_partita
        WHERE c.id_giocatore = g.id
        AND p.id_stagione = ?
      ) as convocazioni
      FROM giocatori g
      INNER JOIN societa s ON s.id = g.id_squadra
      WHERE g.id = ?
      LIMIT 1;";

    $stmt = $con->prepare($query);
    $stmt->bind_param("iiiii", $id_stagione, $id_stagione, $id_stagione, $id_stagione, $id);
    $stmt->execute();
    return $stmt->get_result();
}

// Funzione per ottenere le squadre del giocatore
function getSquadreGiocatore($con, $id) {
    $query_squadre = "
      SELECT s.nome_societa, s.tipo, s.id
      FROM societa s
      INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
      WHERE ag.id_giocatore = ?";

    $stmt = $con->prepare($query_squadre);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
