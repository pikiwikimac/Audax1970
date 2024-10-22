<?php
require_once('config/db.php');


function getCalendarioPartite($con, $campionato_squadra, $id) {
    $query = "
    SELECT 
        soc.nome_societa AS casa, 
        soc2.nome_societa AS ospite, 
        s.golCasa, 
        s.golOspiti, 
        CAST(s.giornata AS UNSIGNED) AS giornata_, 
        s.giornata, 
        s.id, 
        s.data, 
        s.played
    FROM 
        partite s
    INNER JOIN 
        societa soc ON soc.id = s.squadraCasa
    INNER JOIN 
        societa soc2 ON soc2.id = s.squadraOspite
    WHERE  
        s.id_stagione = ? 
        AND (s.squadraCasa = ? OR s.squadraOspite = ?)
    ORDER BY 
        giornata_, casa, ospite";

    $stmt = $con->prepare($query);
    $stmt->bind_param("iii", $campionato_squadra, $id, $id);
    $stmt->execute();
    return $stmt->get_result();
}

function getPartitaById($con, $id) {

    $query = 
    "
    SELECT 
      p.*,
      soc.nome_societa as casa,
      soc2.nome_societa as ospite,
      stag.descrizione,
      stag.girone,
      soc.sede,
      soc.citta,
      soc.giorno_settimana,
      soc.ora_match,
      soc.logo as logoCasa,
      soc2.logo as logoOspiti,
      CASE
          WHEN p.orario_modificato IS NOT NULL THEN p.orario_modificato
          ELSE soc.ora_match
      END AS orario_partita,
      CASE
          WHEN p.data_modificata IS NOT NULL THEN p.data_modificata
          ELSE p.data
      END AS giornata_partita
    FROM partite p
    INNER JOIN societa soc ON soc.id = p.squadraCasa
    INNER JOIN societa soc2 ON soc2.id = p.squadraOspite
    INNER JOIN stagioni stag ON stag.id_stagione = p.id_stagione
    WHERE p.id= ?
    ";
  
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
