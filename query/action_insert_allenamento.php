<?php
// Includiamo il file di configurazione del database
require_once('../config/db.php');

// Verifichiamo se il referer è stato impostato in modo sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Verifichiamo che i dati siano stati inviati tramite il metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Utilizziamo le istruzioni preparate per evitare SQL injection
    $sql = "INSERT INTO allenamenti (data, luogo, note, stato, tipologia, orario,id_societa) VALUES (?, ?, ?, ?, ?, ?,?)";
    $stmt = mysqli_prepare($con, $sql);

    // Verifichiamo se la preparazione dello statement è avvenuta con successo
    if ($stmt) {
        // Associamo i parametri con i tipi corrispondenti
        mysqli_stmt_bind_param($stmt, "ssssssi", $data, $luogo, $note, $stato, $tipologia, $orario,$id_societa);

        // Definiamo i valori dei parametri dalle variabili $_POST
        $data = $_POST['data'];
        $luogo = $_POST['luogo'];
        $note = $_POST['note'];
        $stato = $_POST['stato'];
        $orario = $_POST['orario'];
        $tipologia = $_POST['tipologia'];
        $id_societa = $_POST['id_societa'];

        // Eseguiamo lo statement
        if (mysqli_stmt_execute($stmt)) {
            // Se l'inserimento è riuscito, reindirizziamo alla pagina di provenienza
            header("Location: $url_provenienza");
            exit(); // Terminiamo lo script dopo il reindirizzamento
        } else {
            // Se c'è stato un errore nell'esecuzione dello statement
            echo "ERROR: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
        }

        // Chiudiamo lo statement
        mysqli_stmt_close($stmt);
    } else {
        // Se la preparazione dello statement è fallita
        echo "ERROR: Impossibile preparare la query. " . mysqli_error($con);
    }
}

// Chiudiamo la connessione al database
mysqli_close($con);
?>
