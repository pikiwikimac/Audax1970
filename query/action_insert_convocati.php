<?php

require_once('../config/db.php');

// Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

// Verifica se il metodo di richiesta è POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ID della partita per cui si stanno salvando le convocazioni
    $id_partita = $_REQUEST['id'];

    // Array contenente gli ID dei giocatori convocati
    $convocati = $_POST['presenza'];

    // Verifica se almeno un giocatore è stato selezionato
    if (!empty($convocati)) {
        // Cancella le convocazioni precedenti per la stessa partita
        $delete_query = "DELETE FROM convocazioni WHERE id_partita = $id_partita";

        if ($con->query($delete_query) === TRUE) {
            // Prepara la query per inserire i nuovi giocatori convocati
            $insert_query = "INSERT INTO convocazioni (id_partita, id_giocatore) VALUES ";
            $values = array();

            foreach ($convocati as $giocatore_id) {
                $values[] = "($id_partita, $giocatore_id)";
            }

            $insert_query .= implode(", ", $values);

            // Esegui la query di inserimento
            if ($con->query($insert_query) === TRUE) {
                header('Location: ../admin/edit_presenza_convocazione.php?id=' . $id_partita . '&success=true');
                exit;
            } else {
                echo "Errore durante il salvataggio delle convocazioni: " . $con->error;
            }
        } else {
            echo "Errore durante la cancellazione delle convocazioni precedenti: " . $con->error;
        }
    } else {
        // Nessun giocatore selezionato per la convocazione, quindi cancella tutti i record di convocazioni associati a quella partita
        $delete_query = "DELETE FROM convocazioni WHERE id_partita = $id_partita";

        if ($con->query($delete_query) === TRUE) {
            header('Location: ../admin/edit_presenza_convocazione.php?id=' . $id_partita . '&success=true');
            exit;
        } else {
            echo "Errore durante la cancellazione delle convocazioni: " . $con->error;
        }
    }
} else {
    echo "Metodo di richiesta non valido.";
}

// Chiudi la connessione al database
$con->close();
?>
