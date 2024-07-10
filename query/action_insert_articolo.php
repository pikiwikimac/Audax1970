<?php
require_once('../config/db.php');
$url_provenienza = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

function normalizeCharacters($input) {
    // Mappa dei caratteri Unicode formattati ai caratteri normali
    $charMap = [
        '𝗮' => 'a', '𝗯' => 'b', '𝗰' => 'c', '𝗱' => 'd', '𝗲' => 'e', '𝗳' => 'f', '𝗴' => 'g', '𝗵' => 'h', '𝗶' => 'i', '𝗷' => 'j', '𝗸' => 'k', '𝗹' => 'l', '𝗺' => 'm',
        '𝗻' => 'n', '𝗼' => 'o', '𝗽' => 'p', '𝗾' => 'q', '𝗿' => 'r', '𝘀' => 's', '𝘁' => 't', '𝘂' => 'u', '𝘃' => 'v', '𝘄' => 'w', '𝘅' => 'x', '𝘆' => 'y', '𝘇' => 'z',
        '𝗔' => 'A', '𝗕' => 'B', '𝗖' => 'C', '𝗗' => 'D', '𝗘' => 'E', '𝗙' => 'F', '𝗚' => 'G', '𝗛' => 'H', '𝗜' => 'I', '𝗝' => 'J', '𝗞' => 'K', '𝗟' => 'L', '𝗠' => 'M',
        '𝗡' => 'N', '𝗢' => 'O', '𝗣' => 'P', '𝗤' => 'Q', '𝗥' => 'R', '𝗦' => 'S', '𝗧' => 'T', '𝗨' => 'U', '𝗩' => 'V', '𝗪' => 'W', '𝗫' => 'X', '𝗬' => 'Y', '𝗭' => 'Z',
        '𝙖' => 'a', '𝙗' => 'b', '𝙘' => 'c', '𝙙' => 'd', '𝙚' => 'e', '𝙛' => 'f', '𝙜' => 'g', '𝙝' => 'h', '𝙞' => 'i', '𝙟' => 'j', '𝙠' => 'k', '𝙡' => 'l', '𝙢' => 'm',
        '𝙣' => 'n', '𝙤' => 'o', '𝙥' => 'p', '𝙦' => 'q', '𝙧' => 'r', '𝙨' => 's', '𝙩' => 't', '𝙪' => 'u', '𝙫' => 'v', '𝙬' => 'w', '𝙭' => 'x', '𝙮' => 'y', '𝙯' => 'z',
        '𝘼' => 'A', '𝘽' => 'B', '𝘾' => 'C', '𝘿' => 'D', '𝙀' => 'E', '𝙁' => 'F', '𝙂' => 'G', '𝙃' => 'H', '𝙄' => 'I', '𝙅' => 'J', '𝙆' => 'K', '𝙇' => 'L', '𝙈' => 'M',
        '𝙉' => 'N', '𝙊' => 'O', '𝙋' => 'P', '𝙌' => 'Q', '𝙍' => 'R', '𝙎' => 'S', '𝙏' => 'T', '𝙐' => 'U', '𝙑' => 'V', '𝙒' => 'W', '𝙓' => 'X', '𝙔' => 'Y', '𝙕' => 'Z',
        '𝟬' => '0', '𝟭' => '1', '𝟮' => '2', '𝟯' => '3', '𝟰' => '4', '𝟱' => '5', '𝟲' => '6', '𝟳' => '7', '𝟴' => '8', '𝟵' => '9',
        '𝟶' => '0', '𝟷' => '1', '𝟸' => '2', '𝟹' => '3', '𝟺' => '4', '𝟻' => '5', '𝟼' => '6', '𝟽' => '7', '𝟾' => '8', '𝟿' => '9'
    ];

    // Sostituisce i caratteri formattati con i caratteri normali
    $normalized = strtr($input, $charMap);

    return $normalized;
}

// Recupera i dati dal form, fai attenzione alla sanitizzazione dei dati solo se necessario
$titolo = normalizeCharacters($_REQUEST['titolo']);
$data_pubblicazione = $_REQUEST['data_pubblicazione'];
$autore = $_REQUEST['autore'];
$intestazione = $_REQUEST['intestazione'];
$tags = $_REQUEST['tags'];
$contenuto = normalizeCharacters($_REQUEST['contenuto']);
$data_ultima_modifica = date('Y-m-d H:i:s');

// Controlla se id_partita è impostato e non è nullo, altrimenti impostalo a NULL
$id_partita = isset($_REQUEST['id_partita']) && $_REQUEST['id_partita'] !== 'null' ? $_REQUEST['id_partita'] : null;

// Prepara la query SQL con istruzioni preparate
$sql = "INSERT INTO articoli (titolo, data_pubblicazione, autore, id_intestazione, tags, contenuto, data_ultima_modifica";
$values = "VALUES (?, ?, ?, ?, ?, ?, ?";
$types = "sssssss";
$params = [$titolo, $data_pubblicazione, $autore, $intestazione, $tags, $contenuto, $data_ultima_modifica];

if (!is_null($id_partita)) {
    $sql .= ", id_partita";
    $values .= ", ?";
    $types .= "s";
    $params[] = $id_partita;
}

$sql .= ") " . $values . ")";

// Prepara lo statement
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    // Bind dei parametri
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    // Esegui lo statement
    if (mysqli_stmt_execute($stmt)) {
        // Reindirizza alla pagina di provenienza
        header("Location: $url_provenienza");
        exit();
    } else {
        // Gestisci errori nell'esecuzione dello statement
        echo "ERROR: Impossibile eseguire la query. " . mysqli_stmt_error($stmt);
    }

    // Chiudi lo statement
    mysqli_stmt_close($stmt);
} else {
    // Gestisci errori nella preparazione dello statement
    echo "ERROR: Impossibile preparare la query. " . mysqli_error($con);
}

// Chiudi la connessione
mysqli_close($con);
?>
