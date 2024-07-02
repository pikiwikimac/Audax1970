<?php
require_once('../config/db.php');
session_start(); // Inizia la sessione, se non è già stata avviata

// Verifica le autorizzazioni dell'utente (aggiungi condizioni appropriate)
if (!isset($_SESSION['username'])) {
    echo "Utente non autorizzato.";
    exit;
}

$url_provenienza = $_SERVER['HTTP_REFERER'];
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0; // Converte l'id in un intero

// Verifica se l'ID è valido
if ($id <= 0) {
    echo "ID utente non valido.";
    exit;
}

$updates = array();
$fields = array(
    'firstname' => 'firstname',
    'lastname' => 'lastname',
    'email' => 'email',
    'image' => 'image',
    'superuser' => 'superuser',
    'id_societa_riferimento' => 'id_societa_riferimento'
);

// Costruisci l'array degli aggiornamenti
foreach ($fields as $postKey => $dbField) {
    if (isset($_POST[$postKey])) {
        $value = mysqli_real_escape_string($con, $_POST[$postKey]);
        $updates[] = "$dbField='$value'";
    }
}

// Verifica se ci sono campi da aggiornare
if (empty($updates)) {
    echo "Nessun campo da aggiornare.";
    exit;
}

// Prepara la query SQL con prepared statements
$query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id); // Associa il parametro dell'ID
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../admin/user.php?id=$id");
        exit;
    } else {
        echo "Errore nell'esecuzione della query: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Errore nella preparazione della query: " . mysqli_error($con);
}

// Chiudi la connessione
mysqli_close($con);
?>
