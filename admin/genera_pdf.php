<?php
require_once('../config/db.php');

ob_start();
session_start();
define('FPDF_FONTPATH','../font');
require('fpdf.php');


// definisci l'array con i giocatori da inserire nella tabella
$sql="select *
    from societa
    where id=1 ";
$result = mysqli_query($con,$sql);
$valmisa = mysqli_fetch_assoc($result);


$squadra_casa=$_SESSION['squadra_casa'];
$squadra_ospite=$_SESSION['squadra_ospite'];
$campo=$_POST['sede'];
$citta=$_POST['citta'];
$giorno=$_POST['data'];
$capitano=$_POST['capitano'];
$vicecapitano=$_POST['vicecapitano'];

//$capitano = getCapitano($con,$giorno);
//$vice = getViceCapitano($con,$giorno);

// Allenatore
$allenatore=$_POST['allenatore'];
$doc_allenatore=mysqli_real_escape_string($con, $_POST['doc_allenatore']);

// Assicurati che il valore di $_POST sia opportunamente sanificato per prevenire attacchi di SQL injection

$dirigente_1 = mysqli_real_escape_string($con, $_POST['dirigente_1']);
$dirigente_2 = mysqli_real_escape_string($con, $_POST['dirigente_2']);
$dirigente_3 = mysqli_real_escape_string($con, $_POST['dirigente_3']);
$dirigente_4 = mysqli_real_escape_string($con, $_POST['dirigente_4']);

// Esegui le query per ottenere i documenti dei dirigenti

$sql_dirigente_1 = "SELECT documento FROM dirigenti WHERE nome = '$dirigente_1'";
$result_dirigente_1 = mysqli_query($con, $sql_dirigente_1);
$doc_dirigente_1 = '';

if ($result_dirigente_1 && mysqli_num_rows($result_dirigente_1) > 0) {
    $row = mysqli_fetch_assoc($result_dirigente_1);
    $doc_dirigente_1 = $row['documento'];
}

$sql_dirigente_2 = "SELECT documento FROM dirigenti WHERE nome = '$dirigente_2'";
$result_dirigente_2 = mysqli_query($con, $sql_dirigente_2);
$doc_dirigente_2 = '';

if ($result_dirigente_2 && mysqli_num_rows($result_dirigente_2) > 0) {
    $row = mysqli_fetch_assoc($result_dirigente_2);
    $doc_dirigente_2 = $row['documento'];
}

$sql_dirigente_3 = "SELECT documento FROM dirigenti WHERE nome = '$dirigente_3'";
$result_dirigente_3 = mysqli_query($con, $sql_dirigente_3);
$doc_dirigente_3 = '';

if ($result_dirigente_3 && mysqli_num_rows($result_dirigente_3) > 0) {
    $row = mysqli_fetch_assoc($result_dirigente_3);
    $doc_dirigente_3 = $row['documento'];
}

$sql_dirigente_4 = "SELECT documento FROM dirigenti WHERE nome = '$dirigente_4'";
$result_dirigente_4 = mysqli_query($con, $sql_dirigente_4);
$doc_dirigente_4 = '';

if ($result_dirigente_4 && mysqli_num_rows($result_dirigente_4) > 0) {
    $row = mysqli_fetch_assoc($result_dirigente_4);
    $doc_dirigente_4 = $row['documento'];
}



$indice=1;

$id=$_REQUEST['id'];

$sql="
SELECT c.id_giocatore,g.* 
FROM convocazioni c
INNER JOIN giocatori g ON g.id=c.id_giocatore
WHERE id_partita='$id';
";

$giocatori=mysqli_query($con, $sql);




// Creazione del PDF e definizione del contenuto della tabella
$pdf = new FPDF();
$pdf->AddPage();

// Aggiungi logo in alto a sinistra
$pdf->Image('../image/loghi/logo.png', 10, 10, 30);

// Aggiungi logo in alto a destra
$pdf->Image('../image/lnd_a2.png', $pdf->GetPageWidth()-40, 10, 30);

// Intestazione Società
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Audax 1970', 0, 1,'C');
$pdf->Ln(0);

// SottoIntestazione Società
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 8, $valmisa['sede']. ' - ' .$valmisa['citta'] , 0, 1,'C');

// Campionato
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 8, 'Campionato Serie A2 - Girona A 2024/2025 ' , 0, 1,'C');
$pdf->Ln(5);
$pdf->Cell(0, 8, $squadra_casa.' - '.$squadra_ospite , 0, 1,'C');
$pdf->Ln(0);

// Intestazione Partita
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(0, 8, $campo. ' ' .$citta , 0, 1,'C');

$pdf->Ln(10);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);


// Intestazione tabella
$pdf->Cell(10, 10, 'N', 1, 0, 'C', true);
$pdf->Cell(95, 10, 'Nome', 1, 0, 'L', true);
$pdf->Cell(10, 10, 'CAP', 1, 0, 'L', true);
$pdf->Cell(30, 10, 'Data nascita', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Documento', 1, 0, 'C', true);
$pdf->Cell(8, 10, 'A', 1, 0, 'C', true);
$pdf->Cell(8, 10, 'R', 1, 0, 'C', true);
// Stampa dei valori selezionati
foreach($giocatori as $g) {
// QUERY CHE SELEZIONA I GIOCATORI SELEZIONATI

$maglia= $g['maglia'];
$nome= $g['cognome'] .' '. $g['nome'];
$data= formatItalianDate($g['data_nascita'] );
$documento= $g['documento'] ;
// Controlla se il nome del giocatore corrisponde al capitano o al vicecapitano
if ($g['id'] == $capitano) {
    $ruolo = 'C'; // Segna il giocatore come capitano
} elseif ($g['id'] == $vicecapitano) {
    $ruolo = 'VC'; // Segna il giocatore come vicecapitano
} else {
    $ruolo = ''; // Altrimenti, lascia vuoto
}

$pdf->SetFont('Helvetica','',10);
$pdf->Ln();
$pdf->Cell(10,8,$maglia, 1, 0, 'C', true);
$pdf->Cell(95,8,$nome, 1, 0, 'L', true);
$pdf->Cell(10,8,$ruolo, 1, 0, 'C', true);
$pdf->Cell(30,8,$data, 1, 0, 'C', true);
$pdf->Cell(30,8,$documento, 1, 0, 'C', true);
$pdf->Cell(8,8,' ', 1, 0, 'C', true);
$pdf->Cell(8,8,' ', 1, 0, 'C', true);
}

// Allentatore
$pdf->Ln(10);
$pdf->Cell(85,8,'DIRGENTE/ALLEN.', 1, 0, 'C', true);
$pdf->Cell(75,8,$allenatore, 1, 0, 'C', true);

// SHOW Dirigente 1
$pdf->Ln(10);
$pdf->Cell(85,8,'DIRIGENTE ACCOMPAGNATORE UFFICIALE', 1, 0, 'C', true);
$pdf->Cell(75,8,$dirigente_1, 1, 0, 'C', true);
$pdf->Cell(30,8,$doc_dirigente_1, 1, 0, 'C', true);

// SHOW Dirigente 2
$pdf->Ln(10);
$pdf->Cell(85,8,'DIRIGENTE ADDETTO UFFICIALI DI GARA', 1, 0, 'C', true);
$pdf->Cell(75,8,$dirigente_2, 1, 0, 'C', true);
$pdf->Cell(30,8,$doc_dirigente_2, 1, 0, 'C', true);

// SHOW Dirigente 3
$pdf->Ln(10);
$pdf->Cell(85,8,'MEDICO SOCIALE', 1, 0, 'C', true);
$pdf->Cell(75,8,$dirigente_3, 1, 0, 'C', true);
$pdf->Cell(30,8,$doc_dirigente_3, 1, 0, 'C', true);

// SHOW Dirigente 4
$pdf->Ln(10);
$pdf->Cell(85,8,'ALTRO DIRIGENTE ', 1, 0, 'C', true);
$pdf->Cell(75,8,$dirigente_4, 1, 0, 'C', true);
$pdf->Cell(30,8,$doc_dirigente_4, 1, 0, 'C', true);

$pdf->Ln(15);
// Intestazione tabella
$pdf->Cell(100, 10, 'Firma arbitro',0,0,'C');
$pdf->Cell(100, 10, 'Firma dirigente',0,0,'C');
$pdf->Ln(10);
$pdf->Cell(100,10,'________________________________',0,0,'C');
$pdf->Cell(100,10,'________________________________',0,0,'C');

// Output del PDF
$pdf->Output();
ob_end_flush();
function formatItalianDate($date) {
// Dividi la data in giorno, mese e anno
$parts = explode('-', $date);
// Ordina i componenti della data nel formato italiano
$formattedDate = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
return $formattedDate;
}

?>