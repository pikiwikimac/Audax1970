<?php
// Includi il file di configurazione del database
require_once('../config/db.php');
$url_provenienza = $_SERVER['HTTP_REFERER'];

// Controlla se sono stati inviati dati tramite il metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera l'id della partita dalla form
    $id_partita = $_POST['match'];
    $id_societa = $_POST['id_societa_casa'];

    // Ciclo sui giocatori e per ognuno inserisco le statistiche
    foreach ($_POST as $key => $value) {
        // Verifica se il campo è relativo ai gol, ammonizioni o espulsioni
        if (strpos($key, 'gol-') === 0 || strpos($key, 'giallo-') === 0 || strpos($key, 'rosso-') === 0) {
            // Ottieni l'id del giocatore dall'indice del campo
            $id_giocatore = substr($key, strpos($key, '-') + 1);

            // Ottieni il tipo di statistica dal prefisso del campo
            if (strpos($key, 'gol-') === 0) {
                $tipo_statistica = 'marcatori';
            } elseif (strpos($key, 'giallo-') === 0) {
                $tipo_statistica = 'ammoniti';
            } elseif (strpos($key, 'rosso-') === 0) {
                $tipo_statistica = 'rossi';
            }

            // Conta il numero di record per questo giocatore e questa partita nella tabella delle statistiche
            $query_count = "SELECT COUNT(*) AS count FROM {$tipo_statistica} WHERE id_giocatore = '$id_giocatore' AND id_partita = '$id_partita'";
            $result_count = mysqli_query($con, $query_count);
            $row_count = mysqli_fetch_assoc($result_count);
            $count_statistica = isset($row_count['count']) ? $row_count['count'] : 0;

            // Calcola la differenza tra il valore attuale e il numero di record esistenti
            $differenza_valore = $value - $count_statistica;

            // Se la differenza è positiva, aggiungi nuovi record
            if ($differenza_valore > 0) {
                for ($i = 0; $i < $differenza_valore; $i++) {
                    $query_insert = "INSERT INTO {$tipo_statistica} (id_giocatore,id_societa, id_partita) VALUES ('$id_giocatore', '$id_societa', '$id_partita')";
                    mysqli_query($con, $query_insert);
                }
            } elseif ($differenza_valore < 0) {
                // Se la differenza è negativa, elimina i record in eccesso
                $query_delete = "DELETE FROM {$tipo_statistica} WHERE id_giocatore = '$id_giocatore' AND id_partita = '$id_partita' LIMIT " . abs($differenza_valore);
                mysqli_query($con, $query_delete);
            }
        }
    }

    // Ora che i dati sono stati salvati, puoi reindirizzare l'utente a una pagina di conferma o ad altro luogo
    header("Location: $url_provenienza");
    exit;
} else {
    // Se non ci sono dati inviati tramite POST, reindirizza altrove o gestisci l'errore come preferisci
    header('Location: errore.php');
    exit;
}
?>
