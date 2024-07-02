<?php
    session_start();
    // Rimuove le informazioni di sessione relative all'utente
    session_unset();
    session_destroy();

    // Reindirizza l'utente alla pagina di login
    header('Location: ../index.php');
    exit;
?>