<?php

session_start();
require_once('config/db.php');
include('check_user_logged.php');

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
$superuser = $_SESSION['superuser'];

$id = $_REQUEST['id'];

// Controllo se l'ID è valido
if (!is_numeric($id)) {
    die("ID non valido.");
}

// Query per ottenere le stagioni delle squadre a cui il giocatore è affiliato
$query_stagioni = "
  SELECT DISTINCT stag.id_stagione, stag.descrizione as competizione, stag.girone
  FROM societa s
  INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
  INNER JOIN stagioni stag ON stag.id_stagione = s.id_campionato
  WHERE ag.id_giocatore = $id";

$stagioni_result = mysqli_query($con, $query_stagioni);

// Controlla eventuali errori nella query
if (!$stagioni_result) {
    die("Errore nella query: " . mysqli_error($con));
}

// Prepariamo un array per memorizzare i risultati
$risultati_giocatore = [];

// Iteriamo attraverso ogni stagione e otteniamo le statistiche per il giocatore
while ($stagione_row = mysqli_fetch_assoc($stagioni_result)) {
    $id_stagione = $stagione_row['id_stagione'];

    $query = "
      SELECT g.*, s.nome_societa,
      (
        SELECT COUNT(*)
        FROM ammoniti a
        JOIN partite p ON a.id_partita = p.id
        WHERE a.id_giocatore = g.id
        AND p.id_stagione = $id_stagione
      ) AS numero_ammonizioni,
      (
        SELECT COUNT(*)
        FROM rossi r
        JOIN partite p ON r.id_partita = p.id
        WHERE r.id_giocatore = g.id
        AND p.id_stagione = $id_stagione
      ) AS numero_espulsioni,
      (
        SELECT COUNT(*)
        FROM marcatori m
        JOIN partite p ON m.id_partita = p.id
        WHERE m.id_giocatore = g.id
        AND p.id_stagione = $id_stagione
      ) AS numero_gol,
      (
        SELECT COUNT(*) as convocazioni
        FROM convocazioni c
        INNER JOIN partite p ON p.id = c.id_partita
        WHERE c.id_giocatore = g.id
        AND p.id_stagione = $id_stagione
      ) as convocazioni
      FROM giocatori g
      INNER JOIN societa s ON s.id = g.id_squadra
      WHERE g.id = $id 
      LIMIT 1;";

    $giocatore = mysqli_query($con, $query);
    
    if (!$giocatore) {
        die("Errore nella query del giocatore: " . mysqli_error($con));
    }
    
    // Recupera i risultati per la stagione corrente
    if ($row = mysqli_fetch_assoc($giocatore)) {
        $row['id_stagione'] = $id_stagione; // Aggiungi id_stagione ai risultati
        $row['competizione'] = $stagione_row['competizione']; // Aggiungi competizione
        $row['girone'] = $stagione_row['girone']; // Aggiungi girone
        $risultati_giocatore[] = $row; // Aggiungi i risultati all'array
    }
}

// Recupera le squadre del giocatore
$query_squadre = "
  SELECT s.nome_societa, s.tipo, s.id
  FROM societa s
  INNER JOIN affiliazioni_giocatori ag ON ag.id_societa = s.id
  WHERE ag.id_giocatore = $id";
$squadre_giocatore = mysqli_query($con, $query_squadre);

// Controlla eventuali errori nella query delle squadre
if (!$squadre_giocatore) {
    die("Errore nella query delle squadre: " . mysqli_error($con));
}

// Verifica se ci sono risultati per il giocatore
if (empty($risultati_giocatore)) {
    die("Nessun risultato trovato per il giocatore.");
}

?>

<!doctype html>
<html lang="it">
<?php include 'elements/head_base.php'; ?>

<body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
        <?php include 'elements/navbar_red.php'; ?>
    </div>

    
    <!-- Descrizione iniziale -->
    <div class="container" style="margin-top:7rem!important;">
        <h1 class="bebas">
            <?php echo $row['nome'] . ' ' . $row['cognome']; ?>
            <?php if ($row['maglia'] !== null) { ?>
                <div class="float-end">
                    <span class="badge bg-dark text-light"># <?php echo $row['maglia']; ?></span>
                </div>
            <?php } ?>
        </h1>
        <hr />
        <!-- Visualizzazione a card -->
        <div class="row">
            <div class="col-12 col-lg-4">
                <?php if ($row['image_path']) { ?>
                    <img src="image/player/<?php echo $row['image_path']; ?>" class="img-fluid rounded" alt="<?php echo $row['cognome'] . ' ' . $row['nome']; ?>" data-player-name="<?php echo $row['cognome'] . ' ' . $row['nome']; ?>" />
                <?php } else { ?>
                    <img src="image/default_user.jpg" class="img-fluid rounded" alt="Immagine di default" data-player-name="<?php echo $row['player_name']; ?>" />
                <?php } ?>
            </div>

            <div class="col-12 col-lg-8 ps-lg-3">
                <div class="row gy-2">
                    <!-- Squadra -->
                    <div class="col-6 col-md-12">
                        <label class="fw-bold text-muted">Squadra:</label>
                        <a class="text-decoration-none text-dark" href="team.php?id=<?php echo $row['id_squadra']; ?>">
                            <?php echo $row['nome_societa']; ?>
                        </a>
                    </div>
                    <!-- Squadre attive -->
                    <?php if (mysqli_num_rows($squadre_giocatore) > 1) { ?>
                        <div class="col-6 col-md-12">
                            <label class="fw-bold text-muted">Squadre attive:</label>
                            <?php while ($squadra = mysqli_fetch_assoc($squadre_giocatore)) { ?>
                                <span class="badge bg-secondary">
                                    <?php echo $squadra['tipo']; ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <!-- Ruolo -->
                    <div class="col-6 col-md-12">
                        <label class="fw-bold text-muted">Ruolo:</label>
                        <span><?php echo $row['ruolo']; ?></span>
                    </div>
                    <!-- Piede -->
                    <div class="col-6 col-md-12">
                        <label class="fw-bold text-muted">Piede:</label>
                        <span><?php echo $row['piede_preferito']; ?></span>
                    </div>
                    <!-- Data di nascita -->
                    <div class="col-6 col-md-12">
                        <label class="fw-bold text-muted">Data di nascita:</label>
                        <span>
                            <?php if ($row['data_nascita'] === '1970-01-01') {
                                echo '-';
                            } else {
                                echo date('d/m/Y', strtotime($row['data_nascita']));
                            } ?>
                        </span>
                    </div>

                    <div class="col-12">
                        <!-- Futsalmarche -->
                        <a class="btn btn-sm btn-outline-dark" href="https://www.google.com/search?q=<?php echo urlencode($row['nome'] . ' ' . $row['cognome'] . ' Futsalmarche'); ?>" target="_blank" rel="noopener noreferrer">
                            <span class="d-flex align-items-center">
                                <i class="bi bi-google"></i>
                                Cerca su Google
                            </span>
                        </a>
                    </div>
                    <!-- Statistiche -->
                    <div class="col-12 mt-5">
                      <!-- Statistiche -->
                      <h2 class="bebas">Statistiche</h2>
                      <div class="table-responsive">
                          <table class="table table-striped table-hover">
                              <thead>
                                  <tr>
                                      <th>Competizione</th>
                                      <th>Girone</th>
                                      
                                      <th>Ammonizioni</th>
                                      <th>Espulsioni</th>
                                      <th>Gol</th>
                                      <th>Convocazioni</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php foreach ($risultati_giocatore as $risultato) { ?>
                                      <tr>
                                          <td><?php echo htmlspecialchars($risultato['competizione']); ?></td>
                                          <td><?php echo htmlspecialchars($risultato['girone']); ?></td>
                                          
                                          <td><?php echo htmlspecialchars($risultato['numero_ammonizioni']); ?></td>
                                          <td><?php echo htmlspecialchars($risultato['numero_espulsioni']); ?></td>
                                          <td><?php echo htmlspecialchars($risultato['numero_gol']); ?></td>
                                          <td><?php echo htmlspecialchars($risultato['convocazioni']); ?></td>
                                      </tr>
                                  <?php } ?>
                              </tbody>
                          </table>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        
    </div>
    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>
    
</body>
</html>
