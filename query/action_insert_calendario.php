<?php
require_once('../config/db.php');
$url_provenienza = $_SERVER['HTTP_REFERER'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_stagione = $_POST['id_stagione'];
    $id_societa = $_POST['id_societa'];
    // Input validation
    $squadraOspite = isset($_POST['squadraOspite']) ? $_POST['squadraOspite'] : null;
    $squadraCasa = isset($_POST['squadraCasa']) ? $_POST['squadraCasa'] : null;
    $data = isset($_POST['data']) ? $_POST['data'] : null;
    $giornata = isset($_POST['giornata']) ? $_POST['giornata'] : null;
    $stagione = isset($_POST['stagione']) ? $_POST['stagione'] : null;

    if (empty($squadraOspite) || empty($squadraCasa) || empty($data) || empty($giornata)) {
        // Redirect to an error page or show an error message to the user
        echo "Error: All fields are required.";
        exit;
    }

    // Validate "giornata" to ensure it is a positive integer
    if (!ctype_digit($giornata) || (int)$giornata <= 0) {
        // Redirect to an error page or show an error message to the user
        echo "Error: Giornata must be a positive integer.";
        exit;
    }

    // Prepare the SQL statement using prepared statements
    $stmt = mysqli_prepare($con, "INSERT INTO `partite` (squadraCasa, squadraOspite, giornata, data,id_stagione) VALUES (?, ?, ?, ?, ?)");

    if ($stmt === false) {
        // Redirect to an error page or show an error message to the user
        echo "Error: Failed to prepare the statement.";
        exit;
    }

    // Bind the parameters to the statement
    mysqli_stmt_bind_param($stmt, "ssisi", $squadraCasa, $squadraOspite, $giornata, $data,$stagione);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        $url_provenienza = $_SERVER['HTTP_REFERER'];
        header("Location: $url_provenienza");
        exit;
    } else {
        // Error in execution
        // Redirect to an error page or show an error message to the user
        echo "Error: Unable to insert the record. " . mysqli_error($con);
        exit;
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Redirect to an error page or show an error message to the user
    echo "Error: Invalid request method.";
    exit;
}
// Close the connection
mysqli_close($con);
?>
