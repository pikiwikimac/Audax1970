<?php
session_start();
require_once('../config/db.php');

// Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

// Funzione per la gestione degli errori di query SQL
function handle_sql_error($con, $query)
{
    if (!$result = mysqli_query($con, $query)) {
        die("Errore nella query SQL: " . mysqli_error($con));
    }
    return $result;
}

// Query per ottenere i dati dei giocatori
$query = "
SELECT g.*, 
(
    SELECT COUNT(*)
    FROM ammoniti a
    JOIN partite p ON a.id_partita = p.id
    WHERE a.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
) AS numero_ammonizioni,
(
    SELECT COUNT(*)
    FROM rossi r
    JOIN partite p ON r.id_partita = p.id
    WHERE r.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
) AS numero_espulsioni,
(
    SELECT COUNT(*)
    FROM marcatori m
    JOIN partite p ON m.id_partita = p.id
    WHERE m.id_giocatore = g.id
    AND p.id_stagione IN (1, 2)
) AS numero_gol,
s.nome_societa
FROM giocatori g
INNER JOIN societa s ON s.id = g.id_squadra
WHERE g.id_squadra != 1
ORDER BY id_squadra, ruolo, cognome, nome ASC";

$giocatori_result = handle_sql_error($con, $query);

// Query per ottenere il numero totale di giocatori
$query2 = "SELECT COUNT(*) as numero_giocatori FROM giocatori";
$numero_giocatori_result = handle_sql_error($con, $query2);
$numero_giocatori_row = mysqli_fetch_assoc($numero_giocatori_result);
$numero_giocatori = $numero_giocatori_row['numero_giocatori'];

// Inizializza un array per i giocatori
$giocatori_array = array();

// Popola l'array con i dati dei giocatori
while ($row = mysqli_fetch_assoc($giocatori_result)) {
    $giocatori_array[] = $row;
}

// Crea un array associativo contenente sia i dati dei giocatori che il numero totale di giocatori
$data = array(
    'giocatori' => $giocatori_array,
    'numero_giocatori' => $numero_giocatori
);

// Imposta l'intestazione Content-Type per indicare che la risposta è JSON
header('Content-Type: application/json');

// Restituisci i dati in formato JSON
echo json_encode($data);

?>
