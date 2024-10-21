<?php
session_start();

require_once('config/db.php');
require_once('config/variables.php');


    # QUERY
    $query = "
    SELECT g.*,(
      SELECT COALESCE(COUNT(*),0) as convocazioni
      FROM convocazioni c
      INNER JOIN partite p on p.id=c.id_partita
      WHERE c.id_giocatore=g.id
      AND p.id_stagione = $stagioneAttuale
    ) as convocazioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM ammoniti a
      JOIN partite p
      ON a.id_partita = p.id
      WHERE a.id_giocatore = g.id
      AND p.id_stagione = $stagioneAttuale
      
    ) AS numero_ammonizioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM rossi r
      JOIN partite p
      ON r.id_partita = p.id
      WHERE r.id_giocatore = g.id
      AND p.id_stagione = $stagioneAttuale
    ) AS numero_espulsioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM marcatori m
      JOIN partite p
      ON m.id_partita = p.id
      WHERE m.id_giocatore = g.id
      AND p.id_stagione = $stagioneAttuale
    ) AS numero_gol
    FROM giocatori g
    INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id  
    WHERE ag.id_societa = $IdSocieta
    ORDER BY ruolo, cognome, nome ASC";
    $result = mysqli_query($con,$query);

    # QUERY
    $query2 = "
    SELECT * FROM $vistaClassifica";
    $classifica = mysqli_query($con,$query2);

    # QUERY
    $query_ultimo_match=
      "
      SELECT s.*,
      soc.nome_societa as casa, 
      soc2.nome_societa as ospite,
      soc.logo as logo_casa, 
      soc2.logo as logo_ospite,
      soc.sede,soc.citta,soc.logo as logo_casa,soc2.logo as logo_ospiti,stag.descrizione
      FROM `partite` s
      INNER JOIN societa soc on soc.id=s.squadraCasa
      INNER JOIN societa soc2 on soc2.id=s.squadraOspite
      INNER JOIN stagioni stag on stag.id_stagione=s.id_stagione
      where (s.squadraCasa='$IdSocieta' or s.squadraOspite='$IdSocieta')
      and s.played=1
      and s.data =  ( select max(data)
                      FROM (select * FROM `partite` st where st.played=1) x
                      where x.squadraCasa=$IdSocieta
                      or x.squadraOspite=$IdSocieta
                    )
    ";
    $ultimo_match = mysqli_query($con,$query_ultimo_match);
    // Ensure that $row is not null before accessing its elements
    $row = mysqli_fetch_assoc($ultimo_match);

    if ($row !== null) {
        $squadraCasa = $row['squadraCasa'] ?? '';
        $squadraOspite = $row['squadraOspite'] ?? '';
        $id_partita = $row['id'] ?? 0;
    } else {
      // Handle the case when no match is found
      $squadraCasa = '';
      $squadraOspite = '';
      $id_partita = 0;
    }


    # QUERY
    $query_prossimo_match="
    SELECT
    s.*,
    soc.nome_societa AS casa,
    soc.logo AS logo_casa,
    soc2.nome_societa AS ospite,
    soc2.logo AS logo_ospite,
    soc.sede,
    soc.citta,
    s.data,
    soc.ora_match,
    s.giornata,
    stag.descrizione,
    CASE
        WHEN s.orario_modificato IS NOT NULL THEN s.orario_modificato
        ELSE soc.ora_match
    END AS orario_partita,
    CASE
        WHEN s.data_modificata IS NOT NULL THEN s.data_modificata
        ELSE s.data
    END AS giornata_partita
    FROM
        `partite` s
    INNER JOIN societa soc ON
        soc.id = s.squadraCasa
    INNER JOIN societa soc2 ON
        soc2.id = s.squadraOspite
    INNER JOIN stagioni stag ON
        stag.id_stagione = s.id_stagione
    WHERE
        (
            s.squadraCasa = '$IdSocieta' OR s.squadraOspite = '$IdSocieta'
        ) 
    AND s.played = 0
    AND s.giornata < 500 
    ORDER BY data
    LIMIT 1
    ";
    $prossimo_match = mysqli_query($con,$query_prossimo_match);
    $row2 = mysqli_fetch_assoc($prossimo_match);

    $posizione=1;


    # QUERY per ottenere i marcatori dell'ultima partita della squadra di casa
    $query_marcatori_casa_ultima_partita = "
    SELECT g.cognome, g.nome,count(*) as gol_fatti
    FROM marcatori m
    INNER JOIN `partite` s22 ON s22.id = m.id_partita
    INNER JOIN societa s ON s.id = s22.squadraCasa
    INNER JOIN societa s2 ON s2.id = s22.squadraOspite
    INNER JOIN giocatori g ON g.id = m.id_giocatore
    WHERE s22.id = '$id_partita'
    AND m.id_societa = '$squadraCasa'
    GROUP BY 1,2
    ORDER BY gol_fatti asc -- Ordina per data in modo decrescente per ottenere l'ultima partita";
    $marcatori_casa_ultima_partita = mysqli_query($con,$query_marcatori_casa_ultima_partita);

    # QUERY per ottenere i marcatori dell'ultima partita della squadra ospite
    $query_marcatori_ospite_ultima_partita = "
    SELECT g.cognome, g.nome,count(*) as gol_fatti
    FROM marcatori m
    INNER JOIN `partite` s22 ON s22.id = m.id_partita
    INNER JOIN societa s ON s.id = s22.squadraCasa
    INNER JOIN societa s2 ON s2.id = s22.squadraOspite
    INNER JOIN giocatori g ON g.id = m.id_giocatore
    WHERE s22.id = '$id_partita'
    AND m.id_societa = '$squadraOspite'
    GROUP BY 1,2
    ORDER BY gol_fatti asc -- Ordina per data in modo decrescente per ottenere l'ultima partita";
    $marcatori_ospite_ultima_partita = mysqli_query($con, $query_marcatori_ospite_ultima_partita);

    
    $query_articoli = "select a.*,ai.descrizione as intestazione
    FROM articoli a
    LEFT JOIN articoli_intestazioni ai ON ai.id = a.id_intestazione
    ORDER BY data_pubblicazione desc
    LIMIT 4";
    $articoli = mysqli_query($con,$query_articoli);

?>
<!doctype html>

<style>
  
  .card {
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.30s ease, box-shadow 0.30s ease;
  }

  .card-img-wrapper {
    position: relative;
    width: 100%;
    height: 200px; /* Altezza fissa per la card, puoi modificarla */
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center; /* Centra l'immagine */
  }

  .card:hover {
    transform: scale(1.01);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }

  .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1 1 auto;
  }

  .card-title {
    font-size: 1.25rem; /* Regola la dimensione del font come preferisci */
    line-height: 1.5; /* Regola l'altezza della linea per migliorare la leggibilità */
    overflow: hidden; /* Nasconde il testo in eccesso */
    text-overflow: ellipsis; /* Mostra i puntini di sospensione per il testo troncato */
    white-space: nowrap; /* Impedisce il ritorno a capo */
    display: block;
    width: 100%; /* Assicura che l'elemento occupi tutto lo spazio disponibile */
    font-family: 'Bebas Neue';
  }

  .card-title-container {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Limita il numero di righe */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .card-text {
    flex: 1;
    font-size: 12px;
    font-weight: 400;
  }

  .card-text-footer {
    text-align: right;
    margin-top: auto;
  }

  .table {
    --bs-table-bg: unset!important; /* o 'initial' per il valore predefinito */
  }

  .card{
    --bs-card-bg: unset!important;
  }

   
</style>



<html lang="it">
  <!-- Head -->
  <?php include 'elements/head_base.php'; ?>

  <body>
    <!-- Navbar -->
    <div class="mb-5" id="navbar-orange">
      <?php include 'elements/navbar_red.php'; ?>
    </div>

    <!-- Carousel di sfondo  -->
    <?php include 'elements/carousel.php'; ?>

    <!-- Corpo del testo -->
    
    <!-- Articoli -->
<div class="container my-5 px-4">
  <h1 class="bebas">Articoli</h1>

  <hr/>

  <div class="row g-4">
    <!-- Ultimi 4 Articoli -->
    <?php while ($articolo = mysqli_fetch_assoc($articoli)) { ?>
      <link rel="preload" href="image/articoli/<?php echo htmlspecialchars($articolo['immagine_url']); ?>" as="image">

      <div class="col-12 col-sm-6 col-lg-3 p-3">
        <a href="articolo.php?id=<?php echo htmlspecialchars($articolo['id']); ?>" class="text-decoration-none">
          <div class="card mb-2">
            <?php if($articolo['immagine_url']){ ?>
              <img 
                src="image/articoli/<?php echo htmlspecialchars($articolo['immagine_url']); ?>" 
                class="img-fluid card-img" 
                alt="<?php echo htmlspecialchars($articolo['titolo']); ?>" 
                style="max-height:280px;width:auto"
                
              >
            <?php } else { ?>
              <img 
                src="image/lnd_a2.webp" 
                class="img-fluid card-img" 
                alt="Immagine predefinita" 
                style="max-height:280px;width:auto"

              >
            <?php } ?>
          </div>

          <!-- Intestazione -->
          <?php if($articolo['intestazione']) { ?>
            <span class="badge bg-secondary">
              <?php echo htmlspecialchars($articolo['intestazione']); ?>
            </span>
          <?php } ?>

          <br/>

          <div class="card-title-container">
            <span class="card-title text-dark mt-2">
              <?php echo htmlspecialchars($articolo['titolo']); ?>
            </span>
          </div>

          <!-- Contenuto dell'articolo -->
          <p class="text-dark" style="font-size:12px;">
            <?php 
                $content = trim($articolo['contenuto']);
                $content = preg_replace('/\s+/', ' ', $content);
                if (strlen($content) > 132) {
                    $content = substr($content, 0, 132) . '...';
                }
                echo nl2br(htmlspecialchars($content));
            ?>
          </p>

          <!-- Data pubblicazione -->
          <span class="text-muted float-end" style="font-size:12px;">
            <?php
            $data_pubblicazione = $articolo['data_pubblicazione'];
            $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
            echo $formatted_date;
            ?>
          </span>

        </a>
      </div>
    <?php } ?>
  </div>
</div>


    <!-- Prossima partita & Ultima partita  -->
    <div class="container my-5 px-4">
      <div class="row row-cols-1 row-cols-lg-2 g-3">

        <!-- Prossima partita -->
        <div class="col">
          <a class="text-decoration-none" href="show_partita.php?id=<?php echo $row2['id'] ?>">
            <div class="card h-100 card-wrapper bebas">
               
              <div class="card-header bg-dark text-light">
                <?php echo $row2['descrizione'] ?>
                <?php if ($row2['giornata'] < 900){ ?> 
                  <span class="float-end">
                    Giornata <?php echo $row2['giornata'] ?>° 
                  </span>
                <?php } ?>
              </div>
             

              <div class="card-body card-content">
                <!-- Luogo partita prossimo match-->
                <div class="row ">
                  <div class="col-12 text-center">
                    <small class="text-muted" id="luogo_match"><?php echo $row2['sede'] .' - ' .$row2['citta']?></small>
                  </div>
                </div>

                <!-- Team casa vs team fuori casa prossimo match -->
                <div class="row align-items-center my-2">
                  <!-- Team casa prossimo match-->
                  <div class="col-5 text-center">
                    <img src="image/loghi/<?php echo $row2['logo_casa'] ?>" alt="<?php echo $row2['logo_casa'] ?>" class="img-fluid rounded-circle" width="60" height="60"/>
                    
                    <div class="bebas fw-bold fs-6 mt-2"><?php echo $row2['casa'] ?></div>
                  </div>

                  <div class="col-2 text-center">
                    
                    <span class="fw-bold fs-1"> - </span>
                    
                    <br/>
                    
                    <small class="text-muted text-center text-nowrap" style="letter-spacing:-0.5px; font-size: 0.85rem;">
                    <?php 
                      if (isset($row2['data'], $row2['orario_partita'])) {
                        $formatted_date = date("d/m/y", strtotime($row2['data']));
                        $formatted_time = date('H:i', strtotime($row2['orario_partita']));
                      } else {
                        $formatted_date = '';
                        $formatted_time = '';
                      }
                    ?>
                    <?php echo $formatted_date; ?><br> <!-- Aggiunto un tag <br> per andare a capo -->
                    <?php echo $formatted_time; ?>
                  </small>

                    
                    
                  </div>

                  <!-- Team ospite prossimo match -->
                  <div class="col-5 text-center">
                    
                    <img src="image/loghi/<?php echo $row2['logo_ospite'] ?>" alt="<?php echo $row2['logo_ospite'] ?>" class="img-fluid rounded-circle" width="60" height="60"/>
                    <div class="bebas fw-bold fs-6" id=""><?php echo $row2['ospite'] ?></div>
                      
                  </div>
                </div>

                <div class="row mt-3">
                  &nbsp;
                </div>
              </div>
              
            </div>
          </a>
        </div>

        <!-- Ultimo match -->
        <div class="col">
          <a class="text-decoration-none" href="show_partita.php?id=<?php echo $row['id'] ?>">
            <div class="card h-100 card-wrapper bebas">

              <!-- Header partita -->
              <div class="card-header bg-dark text-light">
                <?php echo $row['descrizione'] ?>
                <?php if ($row['giornata'] < 900){ ?> 
                  <span class="float-end">Giornata <?php echo $row['giornata'] ?>°</span>
                <?php } ?>
              </div>

              <!-- Corpo card -->
              <div class="card-body card-content">

                <!-- Luogo partita -->
                <div class="row">
                  <div class="col-12 text-center">
                    <small class="text-muted"><?php echo $row['sede'] .' - ' .$row['citta'] ?></small>
                  </div>
                </div>

                <!-- Sezione logo e risultato -->
                <div class="row align-items-center my-2">
                  <!-- Logo casa e nome squadra -->
                  <div class="col-5 text-center">
                    <img src="image/loghi/<?php echo $row['logo_casa'] ?>" alt="<?php echo $row['logo_casa'] ?>" class="img-fluid rounded-circle" width="60" height="60"/>
                    <div class="bebas fw-bold fs-6 mt-2"><?php echo $row['casa'] ?></div>
                  </div>

                  <!-- Risultato -->
                  <div class="col-2 text-center">
                    <span class="fw-bold fs-3"><?php echo $row['golCasa'] ?> - <?php echo $row['golOspiti'] ?></span>
                    <div>
                      <small class="text-muted text-nowrap">
                        <?php echo date("d/m/y", strtotime($row['data'])) ?>
                      </small>
                    </div>
                  </div>

                  <!-- Logo ospite e nome squadra -->
                  <div class="col-5 text-center">
                    <img src="image/loghi/<?php echo $row['logo_ospite'] ?>" alt="<?php echo $row['logo_ospite'] ?>" class="img-fluid rounded-circle" width="60" height="60"/>
                    <div class="bebas fw-bold fs-6 mt-2"><?php echo $row['ospite'] ?></div>
                  </div>
                </div>

                <!-- Marcatori -->
                <div class="row mt-3">
                  <!-- Marcatori casa -->
                  <div class="col-5 text-start">
                    <?php while ($marcatore = mysqli_fetch_assoc($marcatori_casa_ultima_partita)) { ?>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                          <?php echo $marcatore['cognome'] .' ' .(mb_substr($marcatore['nome'], 0, 1)) . '.' ?>
                        </span>
                        <span class="text-muted">
                          <?php if ($marcatore['gol_fatti'] > 1) { ?>
                            <i class='bx bx-football'></i> x<?php echo $marcatore['gol_fatti'] ?>
                          <?php } else { ?>
                            <i class='bx bx-football'></i>
                          <?php } ?>
                        </span>
                      </div>
                    <?php } ?>
                  </div>
                  
                  <div class="col-2">
                  </div>

                  <!-- Marcatori ospite -->
                  <div class="col-5 text-start ">
                    <?php while ($marcatore = mysqli_fetch_assoc($marcatori_ospite_ultima_partita)) { ?>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                          <?php echo $marcatore['cognome'] .' ' .(mb_substr($marcatore['nome'], 0, 1)) . '.' ?>
                        </span>
                        <span class="text-muted">
                          <?php if ($marcatore['gol_fatti'] > 1) { ?>
                            <i class='bx bx-football'></i> x<?php echo $marcatore['gol_fatti'] ?>
                          <?php } else { ?>
                            <i class='bx bx-football'></i>
                          <?php } ?>
                        </span>
                      </div>
                    <?php } ?>
                  </div>
                </div>

              </div>
            </div>
          </a>
        </div>




      </div>
    </div>
    
    <!-- Giocatori & Classifica -->
    <div class="container my-5 px-4">
      <div class="row gy-3">
        <!-- Tabella rosa -->
        <div class="col-12 col-lg-6 table-responsive">
          <table class="table table-sm table-hover table-rounded">

            <thead class="table-dark">
              <tr>
                <th class="text-center">
                  <i class='bx bxs-t-shirt align-middle'></i>
                </th>
                <th>
                  Nome
                </th>
                <th class="text-center">
                  Ruolo
                </th>        
                <th class="text-center">
                  <i class='bx bxs-t-shirt align-middle'></i>
                </th>
                <th class="text-center">
                  <i class='bx bxs-card align-middle' style='color:#ffb900'  ></i>
                </th>
                <th class="text-center">
                  <i class='bx bxs-card align-middle' style='color:#FF0000'  ></i>
                </th>
                <th class="text-center">
                  <i class='bx bx-football' ></i>
                </th>
              </tr>
            </thead>

            <tbody class="">
              <?php while($row = mysqli_fetch_assoc($result)) {  ?>
                <tr>
                  <!-- Maglia --> 
                  <td class="text-center">
                    <span class="badge text-dark border border-dark" style="width:3rem">
                      # <?php echo $row['maglia'] ?>
                    </span>
                  </td>
                  <!-- Nome e cognome -->
                  <td>
                    <a class="text-decoration-none text-dark fw-semibold" href="giocatore.php?id=<?php echo $row['id'] ?>">
                      <?php echo $row['nome'] .' '.$row['cognome']  ?>
                    </a>
                  </td>
                  <!-- Ruolo -->
                  <td class="text-center">
                    <?php if($row['ruolo']==='Portiere'){
                        echo '<span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere" title="Portiere">P'.'</span>';
                      }elseif($row['ruolo']==='Centrale'){
                        echo '<span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale" title="Centrale">C'.'</span>';
                      }elseif($row['ruolo']==='Laterale'){
                        echo '<span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale" title="Laterale">L'.'</span>';
                      }elseif($row['ruolo']==='Pivot'){
                        echo '<span class="badge bg-danger text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Pivot" title="Pivot">P'.'</span>';
                      }else{
                        echo '<span class="badge bg-info text-light" style="width:30px;"  data-bs-toggle="tooltip" data-bs-title="Universale " title="Universale ">U'.'</span>';
                      } 
                    ?>
                  </td>
                  <!-- Numero di convocazioni -->
                  <td class="text-center">
                    <?php 
                      if($row['convocazioni']==='0'){
                        echo '-';
                      }else{
                        echo $row['convocazioni'] ;
                      } 
                    ?>
                  </td>
                  <!-- Ammonizioni -->
                  <td class="text-center">
                    <?php if($row['numero_ammonizioni']==='0'){
                      echo '-';
                      }else{
                        echo $row['numero_ammonizioni'] ;
                      } 
                    ?>
                  </td>
                  <!-- Espulsioni -->
                  <td class="text-center">
                    <?php if($row['numero_espulsioni']==='0'){
                      echo '-';
                      }else{
                        echo $row['numero_espulsioni'] ;
                      } 
                    ?>
                  </td>
                  <!-- Gol -->
                  <td class="text-center">
                    <?php if($row['numero_gol']==='0'){
                      echo '-';
                      }else{
                        echo $row['numero_gol'] ;
                      } 
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <!-- END: Tabella rosa -->

        <!-- Tabella: classifica -->
        <div class="col-12 col-lg-6 table-responsive">
          <table class="table table-sm table-rounded table-hover">
            <thead class="table-dark">
              <tr>
                <th></th>
                <th></th>
                <th>Squadra</th>
                <th>G</th>
                <th class="">V</th>
                <th class="">P</th>
                <th class="">S</th>
                <th class="text-center">Punti</th>
              </tr>
            </thead>
            <tbody class="align-middle">
              <?php 
                $posizione = 1;
                while($row = mysqli_fetch_assoc($classifica)) {
                  // Classi CSS e tooltip in base al posizionamento in classifica
                  $rowClass = '';
                  $tooltip = '';
                  if ($posizione == 1) {
                    $rowClass = 'bg-success';
                    $tooltip = 'Promozione diretta';
                  } elseif ($posizione >= 2 && $posizione <= 5) {
                    $rowClass = 'bg-primary';
                    $tooltip = 'Playoff';
                  } elseif ($posizione >= 8 && $posizione <= 9) {
                    $rowClass = 'bg-orange';
                    $tooltip = 'Playout';
                  } elseif ($posizione > mysqli_num_rows($classifica) - 2) {
                    $rowClass = 'bg-danger';
                    $tooltip = 'Retrocessione';
                  }

                  // Codice per mostrare un pallino colorato con tooltip
                  $circle = '<span class="position-relative d-inline-block">
                              <span class="bg-opacity-50 ' . $rowClass . ' rounded-circle d-inline-block" style="width: 15px; height: 15px;"></span>
                            </span>';
                ?>
                <tr >
                  <!-- Posizione in classifica -->
                  <td class="text-center">
                    <?php echo $posizione ?>°
                  </td>
                  <!-- Colonna per il pallino colorato -->
                  <td class="text-center">
                    <?php echo $circle; ?>
                  </td>
                  <!-- Nome società -->
                  <td class="<?php if($row['societa'] === 'Audax 1970'){ echo 'fw-semibold'; }?> ">
                    <a href="team.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark d-none d-md-block">
                      <?php echo $row['societa'] ?>
                    </a>

                    <a href="team.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark d-block d-md-none">
                      <?php 
                        $societa = $row['societa'];
                        if(strlen($societa) > 20) {
                          $societa = substr($societa, 0, 20) . '...';
                        }
                        echo htmlspecialchars($societa, ENT_QUOTES, 'UTF-8');
                      ?>
                    </a>
                  </td>
                  <!-- Numero partite giocate -->
                  <td>
                    <?php echo $row['played'] ?> 
                  </td>
                  <!-- Numero partite vinte -->
                  <td>
                    <?php echo $row['vinte'] ?> 
                  </td>
                  <!-- Numero partite pareggiate -->
                  <td>
                    <?php echo $row['pareggi'] ?> 
                  </td>
                  <!-- Numero partite perse -->
                  <td>
                    <?php echo $row['perse'] ?> 
                  </td>
                  <td class="fw-bold text-center">
                    <?php echo $row['risultato'] ?> 
                  </td>
                </tr>
              <?php $posizione += 1; } ?>
            </tbody>

          </table>
        </div>
        <!-- END: Tabella classifica -->
      </div>
    </div>
    

    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

  </body>

</html>