<?php
    require_once('../vendor/autoload.php'); // Carica la libreria
    require_once('../config/db.php');

    // Token del tuo bot Telegram
    $apiToken = "6710427860:AAEL0z-fBD5i6L7ZsIMIRwVUWWDGJbWUC-Y";

    // Chat ID a cui inviare il messaggio
    $chatId = "386698462";                

    $id = $_REQUEST['id'];
    $squadra_casa = '';
    $squadra_ospite = '';
    $data = '';
    $campo = '';
    $luogo_convocazione = $_GET['luogo_convocazione'];
    $orario_convocazione = $_GET['orario_convocazione'];

    $sql = "
        SELECT c.*,g.nome,g.cognome,g.id,p.data,s.nome_societa as squadra_casa,s2.nome_societa as squadra_ospite,p.giornata,s.sede
        FROM convocazioni c
        INNER JOIN giocatori g on g.id=c.id_giocatore
        INNER JOIN partite p on p.id=c.id_partita
        INNER JOIN societa s on s.id=p.squadraCasa
        INNER JOIN societa s2 on s2.id=p.squadraOspite
        WHERE c.id_partita='$id'
    ";

    $giocatori = mysqli_query($con, $sql);

    // Itera sui risultati della query e accumula i nomi dei giocatori
    while ($row = mysqli_fetch_assoc($giocatori)) {
        $nomeGiocatore = $row['nome'];
        $cognomeGiocatore = $row['cognome'];
        $campo = $row['sede'];
        $data = $row['data'];
        $squadra_casa = $row['squadra_casa'];
        $squadra_ospite = $row['squadra_ospite'];
        
        // Aggiungi il nome del giocatore all'elenco
        $elencoGiocatori .= "• $nomeGiocatore $cognomeGiocatore\n";
    }

    // Chiudi la connessione al database
    mysqli_close($con);

    // Reindirizza alla pagina dopo aver chiuso la connessione al database
    header("Location: edit_presenza_convocazione.php?id=$id");

    // Crea un'istanza del bot
    $bot = new \TelegramBot\Api\BotApi($apiToken);

    // Componi il messaggio con l'elenco dei giocatori
    $message .= "Convocazione:\n";
    $message .= "⚽️ Partita: $squadra_casa vs $squadra_ospite \n";
    $message .= "📅 Data: $data \n";
    $message .= "🏟️ Campo: $campo \n\n";
    $message .= "👤 Giocatori:\n$elencoGiocatori\n";

    // Aggiungi la sezione di Convocazione
    $message .= "Convocazione:\n";

    // Aggiungi l'ora, se presente, altrimenti specifica "Non specificato"
    $message .= "⏰ Ora: " . (!empty($orario_convocazione) ? $orario_convocazione : "Non specificato") . "\n";

    // Aggiungi il luogo, se presente, altrimenti specifica "Non specificato"
    $message .= "📍 Luogo:" . (!empty($luogo_convocazione) ? $luogo_convocazione : "Non specificato") . "\n";


    // Invia il messaggio
    $bot->sendMessage($chatId, $message);
?>
