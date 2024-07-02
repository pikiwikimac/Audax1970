<?php
require_once('../config/db.php');

// Verifica se è stato inviato un referer sicuro
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// Assicurati che i dati siano stati inviati tramite il metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizza e recupera i dati dal form
    $giocatore = $_POST['giocatore'] ?? '';
    $data = $_POST['data'] ?? '';
    $importo = $_POST['importo'] ?? '';

    // Verifica che tutti i campi obbligatori siano stati forniti
    if (!empty($giocatore) && !empty($data) && !empty($importo)) {
        // Prepara l'istruzione SQL con i segnaposti
        $sql = "INSERT INTO multe (id_giocatore, data_multa, importo)
                VALUES (?, ?, ?)";

        // Prepara lo statement SQL
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Associa i valori ai segnaposti
            mysqli_stmt_bind_param($stmt, "iss", $giocatore, $data, $importo);

            // Esegui lo statement
            if(mysqli_stmt_execute($stmt)) {
                // Reindirizza alla pagina di gestione multe dopo l'inserimento riuscito
                header("Location: ../admin/multe.php");
                exit(); // Termina lo script dopo il reindirizzamento
            } else {
                // Gestisci errori nell'esecuzione dello statement
                echo "ERROR: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
            }

            // Chiudi lo statement
            mysqli_stmt_close($stmt);
        } else {
            // Gestisci errori nella preparazione dello statement
            echo "ERROR: Impossibile preparare la query. " . mysqli_error($con);
        }
    } else {
        // Gestisci il caso in cui non tutti i campi obbligatori siano stati forniti
        echo "ERROR: Assicurati di compilare tutti i campi obbligatori.";
    }
} else {
    // Gestisci il caso in cui la richiesta non sia stata fatta tramite metodo POST
    echo "ERROR: Metodo di richiesta non consentito.";
}

// Chiudi la connessione al database
mysqli_close($con);
?>
