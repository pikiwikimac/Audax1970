<?php
require_once('../config/db.php');

if (isset($_POST['id_stagione'])) {
    $id_stagione = intval($_POST['id_stagione']);
    $selected_partita = isset($_POST['selected_partita']) ? $_POST['selected_partita'] : null;

    // Prepare the SQL statement
    $sql = "SELECT s1.nome_societa AS nome_casa, s2.nome_societa AS nome_ospite, giornata, p.id
            FROM partite p
            INNER JOIN societa s1 ON s1.id = p.squadraCasa
            INNER JOIN societa s2 ON s2.id = p.squadraOspite
            WHERE p.id_stagione = ? 
            AND (p.squadraCasa = 1 OR p.squadraOspite = 1)
            ORDER BY CAST(giornata AS UNSIGNED)";
            
    $stmt = $con->prepare($sql);

    if ($stmt) {
        // Bind the parameter
        $stmt->bind_param("i", $id_stagione);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Initialize an empty string to store options
        $options = "";

        // Fetch the data and build the options
        while ($row = $result->fetch_assoc()) {
            $selected = ($row['id_partita'] == $selected_partita) ? 'selected' : '';
            $options .= "<option value='{$row['id']}' $selected>{$row['giornata']}° {$row['nome_casa']} - {$row['nome_ospite']}</option>";
        }

        // Output the options
        echo $options;

        // Close the statement
        $stmt->close();
    } else {
        // Handle errors with preparing the statement
        echo "<option value=''>Error: Could not prepare statement</option>";
    }
} else {
    // Handle the case where id_stagione is not set
    echo "<option value=''>Error: No id_stagione provided</option>";
}
?>
