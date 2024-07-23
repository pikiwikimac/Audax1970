<?php
session_start();

require_once('config/db.php');

    # QUERY
    $query = "
    SELECT g.*,(
      SELECT COALESCE(COUNT(*),0) as convocazioni
      FROM convocazioni c
      INNER JOIN partite p on p.id=c.id_partita
      WHERE c.id_giocatore=g.id
      AND p.id_stagione = 1
    ) as convocazioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM ammoniti a
      JOIN partite p
      ON a.id_partita = p.id
      WHERE a.id_giocatore = g.id
      AND p.id_stagione = 1
      
    ) AS numero_ammonizioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM rossi r
      JOIN partite p
      ON r.id_partita = p.id
      WHERE r.id_giocatore = g.id
      AND p.id_stagione = 1
    ) AS numero_espulsioni,
    (
      SELECT COALESCE(COUNT(*),0)
      FROM marcatori m
      JOIN partite p
      ON m.id_partita = p.id
      WHERE m.id_giocatore = g.id
      AND p.id_stagione = 1
    ) AS numero_gol
    FROM giocatori g
    WHERE id_squadra = 106
    ORDER BY ruolo, cognome, nome ASC";
    $result = mysqli_query($con,$query);

    

    # QUERY
    $query_ultimo_match=
      "
      SELECT s.*,soc.nome_societa as casa, soc2.nome_societa as ospite,soc.sede,soc.citta,soc.logo as logo_casa,soc2.logo as logo_ospiti,stag.descrizione
      FROM `partite` s
      INNER JOIN societa soc on soc.id=s.squadraCasa
      INNER JOIN societa soc2 on soc2.id=s.squadraOspite
      INNER JOIN stagioni stag on stag.id_stagione=s.id_stagione
      where (s.squadraCasa='1' or s.squadraOspite='1')
      and s.played=1
      and s.data =  ( select max(data)
                      FROM (select * FROM `partite` st where st.played=1) x
                      where x.squadraCasa=1
                      or x.squadraOspite=1
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
    soc2.nome_societa AS ospite,
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
            s.squadraCasa = '1' OR s.squadraOspite = '1'
        ) AND s.played = 0 
   
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


<style>
  .bebas{
    font-size: 14px!important;
    font-weight: 300;
    font-family: 'Bebas Neue';
    letter-spacing:1px;
  }
  .card {
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }

  .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1 1 auto;
  }

  .card-title {
    font-size: 20px;
    font-weight: 500;
    font-family: 'Bebas Neue';
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

  .card-img-top {
    object-fit: cover; /* Copre l'intero contenitore ritagliando l'immagine se necessario */
    height: 250px; /* Altezza fissa per tutte le immagini */
    width: 100%; /* Larghezza che riempie completamente il contenitore */
  }
</style>

<!doctype html>

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
    <div class="" >
      <!-- Articoli  -->
      <div class="container my-5">
        
        <span class="fs-2 fw-bold" id="font_diverso">Articoli</span>
        <hr/>

        <div class="row g-5">
          <?php while ($articolo = mysqli_fetch_assoc($articoli)) { ?>
            <div class="col-12 col-lg-3">
              <a href="articolo.php?id=<?php echo $articolo['id'] ?>" class="text-decoration-none">
                <div class="card h-100">
                  <?php if($articolo['immagine_url']){ ?>
                    <img src="image/articoli/<?php echo $articolo['immagine_url'] ?>" class="card-img-top" alt="..." style="max-height:250px">
                  <?php }else{ ?>
                    <img src="image/lnd_a2.png" class="card-img-top" alt="..." style="max-height:250px">
                  <?php } ?>
                  <div class="card-body">
                    <?php if($articolo['intestazione'] !== null ){ ?>
                    <div class="card-img-overlay">
                      <span class="badge bg-secondary bebas">
                        <?php echo $articolo['intestazione'] ?>
                      </span>
                    </div>
                    <?php } ?>
                    <h4 class="card-title"><?php echo $articolo['titolo'] ?></h4>
                    <span class="card-text">
                      <?php 
                        $content = $articolo['contenuto'];
                        if (strlen($content) > 180) {
                          $content = substr($content, 0, 180) . '...';
                        }
                        echo $content;
                      ?>
                    </span>
                    <br/>
                    <div class="card-text-footer">
                      <small class="text-body-secondary">
                        <?php 
                          $data_pubblicazione = $articolo['data_pubblicazione'];
                          $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
                          echo $formatted_date;
                        ?>
                      </small>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          <?php } ?>

        </div>
      </div>

      <!-- Prossima partita & Ultima partita  -->
      <div class="container my-5 d-none">
        <div class="row row-cols-1 row-cols-lg-2 g-4">

          <!-- Card: Ultimo match -->
          <div class="col">
            <a class="text-decoration-none" href="show_partita.php?id=<?php echo $row['id'] ?>">
              <div class="card h-100" >
                <!-- Intestazione  -->
                <div class="card-header bg-dark text-light">
                  <?php if($row['giornata']>500){
                    echo 'Amichevole' .'<span class="float-end">
                    <i class="bx bx-calendar"></i>
                    <span>' .date("d/m/y", strtotime($row['data'])) .'</span>
                  </span>';
                  }else{  ?>
                    
                    Giornata <?php echo $row['giornata'] ?>° - <?php echo $row['descrizione'] ?>
                    <span class="float-end">
                      <i class='bx bx-calendar'></i>
                      <span>
                        <?php echo  date("d/m/y", strtotime($row['data'])); ?>
                      </span>
                    </span>
                  <?php } ?>
                </div>

                <div class="card-body ">
                  <!-- Luogo partita -->
                  <div class="row ">
                    <div class="col-12 ">
                      <div class="text-center" id="luogo_match">
                        <span class="text-muted "><?php echo $row['sede'] .' - '  .$row['citta'] ?></span>
                      </div>
                    </div>
                  </div>

                  <div class="row ">
                    <!-- Team casa -->
                    <div class="col-6 ">
                      <!-- Nome team casa -->
                      <div class="row text-center">
                        <div class="col-12">
                          <span class="fw-bold fs-3" id="font_diverso">
                            <?php echo $row['casa'] ?>
                          </span>
                        </div>
                      </div>

                      <!-- Gol team casa -->
                      <div class="row text-center">
                        <div class="col-12">
                          <span class="fw-bold fs-4">
                          <span><?php echo $row['golCasa'] ?></span>
                          </span>
                        </div>
                      </div>

                      <!-- Marcatore team casa  -->
                      <div class="row mt-3">
                        <div class="col-12">
                          <?php while ($marcatore = mysqli_fetch_assoc($marcatori_casa_ultima_partita)) { ?>
                            <div class="row altezza_ridotta">
                              <div class="col-9">
                                <span class="text-muted " id="marcatore">
                                  <span class="d-none d-md-block"><?php echo $marcatore['cognome'] .' '.$marcatore['nome']?></span>
                                  <span class="d-block d-md-none"><?php echo $marcatore['cognome'] .' '.mb_substr($marcatore['nome'], 0, 1) .'.' ?></span>
                                </span>
                              </div>
                              <div class="col-3">
                                <span class="text-muted text-end text-nowrap " id="gol_segnati">
                                  
                                  <?php if( $marcatore['gol_fatti'] > 1 ){  ?>
                                    <i class='bx bx-football align-middle'></i>
                                    x
                                    <?php echo $marcatore['gol_fatti'] ?>
                                  <?php }else{ ?>
                                    <i class='bx bx-football align-middle'></i>
                                  <?php } ?>
                                </span>
                              </div>
                            </div>
                          <?php } ?>
                        </div>
                      </div>

                    </div>
                    <!-- END: Team casa -->

                    <!-- Team ospite -->
                    <div class="col-6 ">

                      <!-- Nome  team ospite -->
                      <div class="row text-center">
                        <div class="col-12">
                          <span class="fw-bold fs-3" id="font_diverso">
                            <?php echo $row['ospite'] ?>
                          </span>
                        </div>
                      </div>

                      <!-- Goal  team ospite -->
                      <div class="row text-center">
                        <div class="col-12">
                          <span class="fw-bold fs-4">
                            <span><?php echo $row['golOspiti'] ?></span>
                          </span>
                        </div>
                      </div>

                      <!-- Marcatore team ospite -->
                      <div class="row mt-3">
                        <div class="col-12">

                          <?php while ($marcatore = mysqli_fetch_assoc($marcatori_ospite_ultima_partita)) { ?>
                            <div class="row altezza_ridotta ">
                              <div class="col-9">
                                <span class="text-muted " id="marcatore">
                                  <span class="d-none d-md-block"><?php echo $marcatore['cognome'] .' '.$marcatore['nome']?></span>
                                  <span class="d-block d-md-none"><?php echo $marcatore['cognome'] .' '.mb_substr($marcatore['nome'], 0, 1) .'.' ?></span>
                                </span>
                              </div>
                              <div class="col-3">
                                <span class="text-muted text-end text-nowrap" id="gol_segnati">
                                  <?php if( $marcatore['gol_fatti'] > 1 ){  ?>
                                    <i class='bx bx-football align-middle'></i>
                                    x
                                    <?php echo $marcatore['gol_fatti'] ?>
                                  <?php }else{ ?>
                                    <i class='bx bx-football align-middle'></i>
                                  <?php } ?>
                                </span>
                              </div>
                            </div>
                          <?php } ?>

                        </div>
                      </div>

                    </div>
                    <!-- END: Team ospite -->
                  </div>
                </div>
              </div>
            </a>
          </div>
          <!-- END Card: Ultimo match -->


          <!-- Card: Prossimo match -->
          <div class="col">
            <a class="text-decoration-none" href="show_partita.php?id=<?php echo $row2['id'] ?>">
              <div class="card h-100">
                <div class="card-header bg-dark text-light">
                  Giornata <?php echo $row2['giornata'] ?>° - <?php echo $row2['descrizione'] ?>
                  <span class="float-end">
                    <i class='bx bx-calendar'></i>
                    <span>
                    <?php 
                      if (isset($row2['data'], $row2['orario_partita'])) {
                        $formatted_date = date("d/m/y", strtotime($row2['data']));
                        $formatted_time = date('H:i', strtotime($row2['orario_partita']));
                      } else {
                        $formatted_date = '';
                        $formatted_time = '';
                      }
                    ?>
                    </span>
                    
                  </span>
                </div>

                <div class="card-body mb-5">
                  <!-- Luogo partita prossimo match-->
                  <div class="row ">
                    <div class="col-12 text-center">
                      <span class="text-muted" id="luogo_match"><?php echo $row2['sede'] .' - ' .$row2['citta']?><span>
                    </div>
                  </div>

                  <!-- Team casa vs team fuori casa prossimo match -->
                  <div class="row ">
                    <!-- Team casa prossimo match-->
                    <div class="col-6 text-center">
                      <!-- Nome -->
                      <div class="row">
                        <div class="col-12">
                          <span class="fw-bold fs-3" id="font_diverso">
                            <?php echo $row2['casa'] ?>
                          </span>
                        </div>
                      </div>

                      <!-- Gol squadra casa prossimo match -->
                      <div class="row">
                        <div class="col-12">
                          <span class="fw-bold fs-4">-</span>
                        </div>
                      </div>

                    </div>

                    <!-- Team ospite prossimo match -->
                    <div class="col-6 text-center">
                      <!-- Nome -->
                      <div class="row">
                        <div class="col-12">
                          <span class="fw-bold fs-3" id="font_diverso">
                            <?php echo $row2['ospite'] ?>
                          </span>
                        </div>
                      </div>
                      <!-- Gol squadra ospite prossimo match -->
                      <div class="row">
                        <div class="col-12">
                          <span class="fw-bold fs-4">-</span>
                        </div>
                      </div>

                    </div>
                  </div>

                </div>
              </div>
            </a>
          </div>
          <!-- END Card: Prossimo match -->
        </div>
      </div>
      <!-- Giocatori & Classifica -->
      <div class="container my-5 d-none">
        <div class="row gy-3">
          <!-- Tabella rosa -->
          <div class="col-12 col-lg-6 table-responsive">
            <table class="table table-sm table-hover table-rounded ">

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
                      <?php echo $row['maglia'] ?>
                    </td>
                    <!-- Nome e cognome -->
                    <td>
                      <a class="text-decoration-none text-dark" href="giocatore.php?id=<?php echo $row['id'] ?>">
                        <?php echo $row['nome'] .' '.$row['cognome']  ?>
                      </a>
                    </td>
                    <!-- Ruolo -->
                    <td class="text-center">
                      <?php if($row['ruolo']==='Portiere'){
                          echo '<span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere">P'.'</span>';
                        }elseif($row['ruolo']==='Centrale'){
                          echo '<span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale">C'.'</span>';
                        }elseif($row['ruolo']==='Laterale'){
                          echo '<span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale">L'.'</span>';
                        }elseif($row['ruolo']==='Pivot'){
                          echo '<span class="badge bg-danger text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Pivot">P'.'</span>';
                        }else{
                          echo '<span class="badge bg-info text-light" style="width:30px;"  data-bs-toggle="tooltip" data-bs-title="Universale ">U'.'</span>';
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
                  <th>Squadra</th>
                  <th>G</th>
                  <th class="">V</th>
                  <th class="">P</th>
                  <th class="">S</th>
                  <th class="text-center">Punti</th>
                </tr>
              </thead>

              
            </table>
          </div>
          <!-- END: Tabella classifica -->
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="p-5">
      <?php include 'elements/footer.php'; ?>
    </footer>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

  </body>

</html>