<?php
// Definisci il token del bot e i dati del messaggio prima di effettuare la chiamata cURL
$apiToken = "6385291749:AAF6lncYgQJIibB9pPNa41_Wce-k-0G6_LU";
$content = '';

foreach ($_POST as $key => $value) {
    if ($value) {
        $content .= "<b>$key</b>: <i>$value</i>\n";
    }
}

if (trim($content)) {
    $content = "<b>Message from Site:</b>\n" . $content;
    $data = [
        'chat_id' => '@Valmisafutsal',
        'text' => $content
    ];

    // Ora puoi effettuare la chiamata cURL
    $ch = curl_init("https://api.telegram.org/bot$apiToken/sendMessage");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo "Error: " . curl_error($ch);
    } else {
        echo "Message sent successfully!";
    }

    curl_close($ch);
}
?>
