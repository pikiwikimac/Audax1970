<?php
// Verifica che il metodo di richiesta sia POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera l'email fornita dal modulo
    $email = $_POST['email'];

    // Verifica se l'email è valida
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Connessione al database
        require_once('../config/db.php');

        // Genera una nuova password casuale
        $new_password = generateRandomString(8); // Lunghezza della nuova password

        // Codifica la nuova password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Aggiorna la password nel database
        $query_update = "UPDATE users SET password = ? WHERE email = ?";
        $stmt_update = mysqli_prepare($con, $query_update);
        mysqli_stmt_bind_param($stmt_update, 'ss', $hashed_password, $email);
        mysqli_stmt_execute($stmt_update);

        // Invia l'email con la nuova password
        $to = $email;
        $subject = 'Nuova password';
        $message = "Ciao,\n\nLa tua nuova password è: $new_password\n\nTi consigliamo di cambiarla dopo aver effettuato l'accesso.\n\nGrazie.";
        $headers = 'From: info@valmisafutsal.com' . "\r\n" .
            'Reply-To: your_email@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // Invia l'email
        if (mail($to, $subject, $message, $headers)) {
            echo "Email inviata con successo. Controlla la tua casella di posta per la nuova password.";
        } else {
            echo "Errore durante l'invio dell'email. Riprova più tardi.";
        }
    } else {
        echo "Indirizzo email non valido.";
    }
} else {
    // Se il metodo di richiesta non è POST, reindirizza l'utente alla pagina di login
    header('Location: login.php');
    exit();
}

// Funzione per generare una stringa casuale
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}
?>
