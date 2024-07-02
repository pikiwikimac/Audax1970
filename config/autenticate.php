<?php
    session_start();
    require_once('db.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prendo tutte le info relative a un utente
    $query = "SELECT * FROM users WHERE username = '$username'";
    $row = mysqli_query($con, $query);
    $users = mysqli_fetch_assoc($row);

    // Verifica le credenziali dell'utente
    if ($users['username'] == $username  && $users['password'] == $password) {
        
        $_SESSION['username'] = $users['username'];
        $_SESSION['superuser'] = $users['superuser'];
        $_SESSION['user_id'] = $users['id'];

        // Reindirizza l'utente alla pagina successiva
        header('Location: ../admin/dashboard.php');
        exit;

    } else {
        $_SESSION['authenticated'] = false;
        // Reindirizza l'utente alla pagina di login con un messaggio di errore
        header('Location: ../login/login.php?error=invalid_credentials');
        exit;

    }
?>
