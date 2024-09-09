<?php
session_start();
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ? AND accettato = 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['image'] = $user['image'];
            $_SESSION['superuser'] = $user['superuser'];
            $_SESSION['user_id'] = $user['id'];

            header('Location: ../admin/dashboard.php');
            exit();
        } else {
            $error = "Credenziali non valide";
        }
    } else {
        $error = "Credenziali non valide";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrati</title>
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
                
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: transform 80ms ease-in;
                padding: 15px 15px;
                margin: 8px 8px;
                width: 100%;
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

            input, select {
                background-color: #eee;
                border: none;
                padding: 15px 15px;
                margin: 8px 8px;
                width: 100%;
                border-radius:8px;
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

            .form-container {
                height: 100%;
                width: 100%;
            }

           
        </style>
    </head>
    <body>
        <div class="container" id="container" style="padding-top:2rem;padding-bottom:2rem">
            
            <div class="form-container " >
                <form action="../query/insert_user.php" method="POST">
                    <h4>AUDAX 1970</h4>
                    <div class="" >
                        <input type="text" placeholder="Nome" id="nome" name="nome" class=""/>
                        <input type="text" placeholder="Cognome" id="cognome" name="cognome" class=""/>
                        <input type="text" placeholder="Email" id="email" name="email" />
                        <input type="text" placeholder="Username" id="username" name="username" />
                        <input type="password" placeholder="Password" id="password" name="password"/>
                        <select id="superuser" name="superuser">
                            <option value="0">Giocatore</option>
                            <option value="1">Dirigente</option>
                        </select>
                    </div>
                    <button type="submit">Registrati</button>
                    <button type="button" class="mt-3" onclick="window.location='login.php'">Indietro</button>
                </form>
            </div>
            
        </div>
    </body>
</html>

  