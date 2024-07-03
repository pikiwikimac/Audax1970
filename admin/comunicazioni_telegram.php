<?php
    require_once('../vendor/autoload.php'); // Carica la libreria
    require_once('../config/db.php');

    // Token del tuo bot Telegram
    $apiToken = "6710427860:AAEL0z-fBD5i6L7ZsIMIRwVUWWDGJbWUC-Y";

    // Chat ID a cui inviare il messaggio
    $chatId = "386698462";                

    // Crea un'istanza del bot
    $bot = new \TelegramBot\Api\BotApi($apiToken);

    // Componi il messaggio con l'elenco dei giocatori
    $message = $_POST['comunicazione'];

    // Invia il messaggio
    $bot->sendMessage($chatId, $message);

    // Chiudi la connessione al database
    mysqli_close($con);

    header("Location: dashboard.php");
?>
