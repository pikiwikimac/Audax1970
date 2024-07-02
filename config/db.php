<?php
    $host = '86.107.36.118';
    $username = 'xaaudax1';
    $password = 'WorphyuocuvPasNot4';
    $database = 'xaaudax1_audax_1970';

    // Crea la connessione
    $con = new mysqli($host, $username, $password, $database);

    // Verifica la connessione
    if ($con->connect_error) {
        die("Connessione al database fallita: " . $con->connect_error);
    }

    // Esegui una query di esempio
    $query = "SELECT * FROM users";
    $result = $con->query($query);

    // Verifica il risultato della query
    if ($result) {
        // Elabora i dati ottenuti dalla query
        while ($row = $result->fetch_assoc()) {
            // elabora i dati
        }
    } else {
        // Gestisci l'errore della query
        echo "Errore nella query: " . $con->error;
    }

?>