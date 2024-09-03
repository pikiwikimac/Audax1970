<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesso Negato</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 40px;
            text-align: center;
        }
        h1 {
            font-size: 2.5rem;
            color: #dc3545;
        }
        p {
            margin-bottom: 20px;
        }
        .icon {
            font-size: 3rem;
            color: #dc3545;
        }
        .btn {
            background-color: #dc3545;
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle icon"></i>
                <h4>Accesso Negato!<h4>
                <p>Non hai i privilegi di amministratore per accedere a questa pagina.</p>
                <a href="../admin/dashboard.php" class="btn">Torna alla Home</a>
            </div>
        </div>
    </div>
</body>
</html>
