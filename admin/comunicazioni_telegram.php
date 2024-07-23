<?php
require_once('../vendor/autoload.php'); // Carica la libreria
require_once('../config/db.php'); // Carica la configurazione del database

// Token del tuo bot Telegram
$apiToken = "6710427860:AAEL0z-fBD5i6L7ZsIMIRwVUWWDGJbWUC-Y";

// Chat ID a cui inviare il messaggio
$chatId = "386698462";

// Controlla se il messaggio è stato inviato tramite POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comunicazione'])) {
    // Crea un'istanza del bot
    try {
        $bot = new \TelegramBot\Api\BotApi($apiToken);
        
        // Componi il messaggio con l'elenco dei giocatori
        $message = $_POST['comunicazione'];

        // Invia il messaggio
        $bot->sendMessage($chatId, $message);
        
    } catch (\TelegramBot\Api\Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }

    // Chiudi la connessione al database
    mysqli_close($con);

    // Reindirizza l'utente alla dashboard
    header("Location: dashboard.php");
    exit();
} else {
    echo 'No message to send.';
}
?>
