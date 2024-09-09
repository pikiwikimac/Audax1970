<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $username = $_SESSION['username'];

    // Verifica che la vecchia password sia corretta
    $query = "SELECT password FROM users WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];

        if (password_verify($oldPassword, $hashedPassword)) {
            // La vecchia password è corretta, verifica le nuove password
            if ($newPassword === $confirmPassword) {
                // Le due nuove password coincidono, aggiorna la password dell'utente nel database
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password = '$hashedNewPassword' WHERE username = '$username'";
                mysqli_query($con, $updateQuery);
                header('Location: ../admin/dashboard.php'); // Reindirizza dopo il cambio password
                exit;
            } else {
                $error = "Le nuove password non corrispondono.";
            }
        } else {
            $error = "La vecchia password non è corretta.";
        }
    }
}
?>

<!-- Form per il cambio password -->
<!doctype html>
<html lang="it">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        h2 {
            text-align: center;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid #000000;
            background-color: #000000;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 15px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 8px;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 
                        0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 500px;
            max-width: 100%;
            min-height: 480px;
        }
    </style>

    <body>

        <?php if (isset($error)): ?>
            <div class="row">
                <div class="col-8 offset-2 mt-2">
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            <form action="change_password.php" method="post" class="">
                <h4 class="mb-3">Cambia Password</h4>
                <input type="text" placeholder="Vecchia password" id="old_password" name="old_password" required>
                <input type="text" placeholder="Nuova password" id="new_password" name="new_password" required>
                <input type="text" placeholder="Conferma password" id="confirm_password" name="confirm_password" class="mb-5" required>
                
                <button type="submit mt-5">Cambia Password</button>
            </form>  
        </div>

        <!-- Import -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    </body>
</html>
