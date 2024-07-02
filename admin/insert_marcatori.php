<?php
  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }

  
  require_once('config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];  
  
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  
  // Controlla se sono stati inviati i dati del form
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottieni i dati inviati dal form
    $giocatore = $_POST['giocatore'];
    $matchID = $_POST['match_id'];
    $societaID = $_POST['societa_id'];
    $minuto = $_POST['minuto'];
    

  
   // Esegui la query per inserire i marcatori nel database
    // Assicurati di adattare la query alla tua struttura del database
    $query = "
              INSERT INTO marcatori (id_giocatore, 
                                    id_societa, 
                                    id_partita, 
                                    minuto) 
              VALUES (
                                    '$giocatore',
                                    '$societaID',
                                    '$matchID',
                                    '$minuto')";
    
    

    if(mysqli_query($con, $query)){
      header('Location: edit_risultato2.php?id=' .$matchID);
      exit();
    } else{
      echo "ERROR: Si è verificato un errore durante l'inserimento dei marcatori: " . mysqli_error($con);
      exit();
    }
    
  }

  
  ?>
  


