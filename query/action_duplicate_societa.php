<?php
require_once('../config/db.php');

// Controlla se è stato passato un parametro ID valido per la società da duplicare
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id_societa = $_REQUEST['id'];

    // Seleziona tutti i dati della società da duplicare
    $query = "SELECT * FROM societa WHERE id = '$id_societa'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Ottieni i dati della società
        $row = mysqli_fetch_assoc($result);

        // Duplica i dati della società in variabili locali
        $nome_societa = isset($row['nome_societa']) ? mysqli_real_escape_string($con, $row['nome_societa']) : null;
        $tipo = isset($row['tipo']) ? $row['tipo'] : null;
        $sede = isset($row['sede']) ? $row['sede'] : null;
        $sede_legale = isset($row['sede_legale']) ? $row['sede_legale'] : null;
        $citta = isset($row['citta']) ? $row['citta'] : null;
        $giorno_settimana = isset($row['giorno_settimana']) ? $row['giorno_settimana'] : null;
        $ora_match = isset($row['ora_match']) ? $row['ora_match'] : null;
        $presidente = isset($row['presidente']) ? $row['presidente'] : null;
        $vicepresidente = isset($row['vicepresidente']) ? $row['vicepresidente'] : null;
        $campionato = isset($row['id_campionato']) ? $row['id_campionato'] : null;
        $contatto_riferimento = isset($row['contatto_riferimento']) ? $row['contatto_riferimento'] : null;
        $telefono = isset($row['telefono']) ? $row['telefono'] : null;
        $email = isset($row['email']) ? $row['email'] : null;
        $whatsapp = isset($row['whatsapp']) ? $row['whatsapp'] : null;
        $instagram = isset($row['instagram']) ? $row['instagram'] : null;
        $sito_web = isset($row['sito_web']) ? $row['sito_web'] : null;
        $logo = isset($row['logo']) ? $row['logo'] : null;

        // Modifica il nome della società per evitarne la duplicazione
        $nome_societa = $nome_societa . ' - copy';

        // Inserisci i dati duplicati nella tabella societa
        $insert_query = "INSERT INTO societa (nome_societa, tipo, sede, sede_legale, citta, giorno_settimana, ora_match, presidente, vicepresidente, id_campionato, contatto_riferimento, telefono, email, whatsapp, instagram, sito_web, parent_id, logo) 
                         VALUES ('$nome_societa', '$tipo', '$sede', '$sede_legale', '$citta', '$giorno_settimana', '$ora_match', '$presidente', '$vicepresidente', '$campionato', '$contatto_riferimento', '$telefono', '$email', '$whatsapp', '$instagram', '$sito_web', '$id_societa', '$logo')";

        if (mysqli_query($con, $insert_query)) {
            // In caso di successo, reindirizza alla pagina di provenienza con un messaggio di successo
            header("Location: ../admin/societa.php?msg=duplicate_success&id=$id_societa");
            exit; // Termina lo script dopo il reindirizzamento
        } else {
            // In caso di errore durante l'inserimento, mostra un messaggio di errore
            echo "Errore durante la duplicazione della società: " . mysqli_error($con);
        }
    } else {
        // Se non è possibile trovare i dati della società, mostra un messaggio di errore
        echo "Errore: Impossibile recuperare i dati della società da duplicare.";
    }
} else {
    // Se non è stato fornito un ID valido nella query string, mostra un messaggio di errore
    echo "Errore: ID della società non valido per la duplicazione.";
}

// Chiudi la connessione al database
mysqli_close($con);
?>
