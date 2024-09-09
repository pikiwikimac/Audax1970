<?php
  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }

  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  
  $id=  $_REQUEST['id'];

  $query =
  "
  SELECT soc.nome_societa as casa, soc2.nome_societa as ospite, golCasa,golOspiti,giornata,s.id,s.data,soc.id as idCasa,soc2.id as idOspite
  FROM `partite` s
  INNER JOIN societa soc on soc.id=s.squadraCasa
  INNER JOIN societa soc2 on soc2.id=s.squadraOspite
  WHERE s.id='$id'
  ";

  $result = mysqli_query($con,$query);
  $partita = mysqli_fetch_assoc($result);


  $id_partita=$partita['id'];
  $data_partita=$partita['data'];
  $casa=$partita['idCasa'];
  $ospite=$partita['idOspite'];
  $golCasa=$partita['golCasa'];
  $golOspiti=$partita['golOspiti'];


  # QUERY che seleziona tutti i giocatori disponibili squadra casa
  $query = 
  "SELECT
    g.*
  FROM
      giocatori g
  INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id
  WHERE ag.id_societa = '$casa'
  AND g.id NOT IN (SELECT i.id_giocatore FROM indisponibili i WHERE i.a_data > '$data_partita' AND i.da_data<'$data_partita') 
  ORDER BY
      ruolo,
      cognome,
      nome ASC";
  $giocatori_casa = mysqli_query($con,$query);

  # QUERY che seleziona tutti i giocatori disponibili squadra ospite
  $query2 = "
  SELECT
    g.*
  FROM
    giocatori g
  INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id
  WHERE ag.id_societa = '$ospite'
  AND g.id NOT IN (SELECT i.id_giocatore FROM indisponibili i WHERE i.a_data > '$data_partita' AND i.da_data<'$data_partita') 
  ORDER BY
    ruolo,
    cognome,
    nome ASC";
  $giocatori_ospiti = mysqli_query($con,$query2);

  # QUERY che conta per ogni squadra i gol fatti in questa partita dalla squadra A(casa)
  $sql = "
  select coalesce(count(*),0) as gol_totali_giornata
  FROM marcatori m
  where id_societa='$casa'
  and id_partita='$id_partita'
  ";

  $result = mysqli_query($con,$sql);
  $count_gol_squadra_a_giornata = mysqli_fetch_assoc($result);

  # QUERY che conta per ogni squadra i gol fatti in questa partita dalla squadra B(ospite)
  $sql = "
  select coalesce(count(*),0) as gol_totali_giornata
  FROM marcatori m
  where id_societa='$ospite'
  and id_partita='$id_partita'
  ";

  $result = mysqli_query($con,$sql);
  $count_gol_squadra_b_giornata = mysqli_fetch_assoc($result);

?>



<!doctype html>
<html lang="it">
  <!-- Head -->
  <?php include '../elements/head.php'; ?>

  <body>
    <main role="main" class="tpl">

      <?php include '../elements/sidebar.php'; ?>

      <!-- Corpo della pagina -->
      <div class="tpl--content">
        <div class="tpl--content--inner">
          <div class="tpl-inner">
            <div class="tpl-inner-content">
              <div class="row pe-3">
                <div class="col-12 ">            
                  <div class="container-fluid">
                    <!-- Intestazione -->
                    <div class="tpl-header">
                      <div class="tpl-header--title">
                        <h4>Giornata <?php echo $partita['giornata'] ?> </h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <button type="button" class="btn btn-sm btn-outline-dark float-end me-2"  onclick="window.location.href='edit_risultato_massivo.php?id=<?php echo $partita["id"]; ?>'">
                            Massiva
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="row">
                      <!-- Colonna Casa -->
                      <div class="col-12 mb-5  col-lg-6 mb-lg-0 table-responsive">
                        <!-- Intestazione squadra casa con gol segnati -->
                        <h4>
                          <a href="show_societa.php?id=<?php echo $partita['idCasa'] ?>" class="text-decoration-none text-dark">
                            <?php echo $partita['casa'] ?>
                          </a>
                          <span class="float-end">
                            <?php echo $partita['golCasa'] ?>
                          </span>
                        </h4>

                        <hr/>

                        <table class="table table-sm table-hover table-striped table-rounded">
                          <caption> 
                            <?php echo $count_gol_squadra_a_giornata['gol_totali_giornata'] ?> segnati su <?php echo $golCasa ?> totali  
                            <a href="calendario_admin.php?id_stagione=<?php echo $stagione ?>&id_societa=<?php echo $id_societa ?>" class="text-decoration-none text-muted float-end">
                              <i class='bx bx-arrow-back '></i>  Indietro
                            </a>
                          </caption>
                          <thead class="table-dark">
                            <tr>
                              <th>Cognome</th>
                              <th>Nome</th>
                              <th class="text-center"><i class='bx bx-football align-middle'></i></th>
                              <th class="text-center"><i class='bx bxs-card align-middle' style='color:#ffb900'></i></th>
                              <th class="text-center"><i class='bx bxs-card align-middle' style='color:#FF0000'></i></th>
                            </tr>
                          </thead>

                          <tbody>
                            <?php while($row = mysqli_fetch_assoc($giocatori_casa)) {  ?>
                            <tr>
                              <td><?php echo $row['cognome'] ?></td>
                              <td><?php echo $row['nome'] ?></td>
                              <!-- Goal -->
                              <td class="text-center">
                                <!-- Diminuisci gol -->
                                <a href="../query/delete_gol.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                  <i class='bx bx-minus align-middle'></i>
                                </a>
                                
                                  <?php
                                    # QUERY che conta per ogni giocatore i gol fatti in questa partita
                                    $sql = "
                                    select coalesce(count(*),0) as gol_fatti
                                    FROM marcatori m
                                    where id_giocatore={$row['id']}
                                    and id_partita='$id_partita'
                                    ";

                                    $result = mysqli_query($con,$sql);
                                    $count_gol_giornata = mysqli_fetch_assoc($result);
                                  ?>

                                  <span>
                                    <?php echo $count_gol_giornata['gol_fatti'] ;?>
                                  </span>
                                  
                                  <!-- Aumenta gol -->
                                  <a href="../query/add_gol.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                    <i class='bx bx-plus align-middle'></i>
                                  </a>
                              </td>
                              <!-- Cartellino giallo -->
                              <td class="text-center">
                                <?php
                                    # QUERY che conta per ogni giocatore le ammonizioni in questa partita
                                    $sql = "
                                    select coalesce(count(*),0) as ammoniti
                                    FROM ammoniti a
                                    where id_giocatore={$row['id']}
                                    and id_partita='$id_partita'
                                    ";

                                    $result = mysqli_query($con,$sql);
                                    $count_ammonito = mysqli_fetch_assoc($result);
                                  ?>
                                  <!-- Diminuisci cartellino giallo-->
                                  <a href="../query/delete_yellow_card.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                    <i class='bx bx-minus align-middle'></i>
                                  </a> 
                                  <?php echo $count_ammonito['ammoniti'] ;?>
                                  
                                  <!-- Aumenta cartellino giallo -->
                                  <a href="../query/add_yellow_card.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                    <i class='bx bx-plus align-middle'></i>
                                  </a>
                              </td>
                              <!-- Cartellino rosso -->
                              <td class="text-center">
                                <?php
                                  # QUERY che conta per ogni giocatore i gol fatti in questa partita
                                  $sql = "
                                  select coalesce(count(*),0) as rossi
                                  FROM rossi r
                                  where id_giocatore={$row['id']}
                                  and id_partita='$id_partita'
                                  ";

                                  $result = mysqli_query($con,$sql);
                                  $count_rossi = mysqli_fetch_assoc($result);
                                ?>
                                <!-- Diminuisci cartellino rosso -->
                                <a href="../query/delete_red_card.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                  <i class='bx bx-minus align-middle'></i>
                                </a>
                                <?php echo $count_rossi['rossi'] ;?>
                                
                                <!-- Aumenta cartellino rosso -->
                                <a href="../query/add_red_card.php?id_giocatore=<?php echo $row['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $casa?>" class="text-decoration-none">
                                  <i class='bx bx-plus align-middle'></i>
                                </a>
                              </td>

                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>

                              
                      <!-- Colonna OSPITI  -->
                      <div class="col-12 col-lg-6 table-responsive">
                        <!-- Intestazione squadra casa con gol segnati -->
                        <h4>
                          <a href="show_societa.php?id=<?php echo $partita['idOspite'] ?>" class="text-decoration-none text-dark">
                            <?php echo $partita['ospite'] ?>
                          </a>
                          <span class="float-end">
                            <?php echo $partita['golOspiti'] ?>
                          </span>
                        </h4>

                        <hr/>

                        <table class="table table-sm table-hover table-striped table-rounded">
                          <caption> 
                            <?php echo $count_gol_squadra_b_giornata['gol_totali_giornata'] ?> segnati su <?php echo $golOspiti ?> totali  
                            <a href="calendario_admin.php?id_stagione=<?php echo $stagione ?>&id_societa=<?php echo $id_societa ?>" class="text-decoration-none text-muted float-end">
                              <i class='bx bx-arrow-back '></i>  Indietro
                            </a>
                          </caption>
                          <thead class="table-dark">
                            <tr>
                              <th>Cognome</th>
                              <th>Nome</th>
                              <th class="text-center"><i class='bx bx-football align-middle'></i></th>
                              <th class="text-center"><i class='bx bxs-card align-middle' style='color:#ffb900'></i></th>
                              <th class="text-center"><i class='bx bxs-card align-middle' style='color:#FF0000'></i></th>
                            </tr>
                          </thead>

                          <tbody>
                            <?php while($row2 = mysqli_fetch_assoc($giocatori_ospiti)) {  ?>
                            <tr>
                              <td><?php echo $row2['cognome'] ?></td>
                              <td><?php echo $row2['nome'] ?></td>
                              <!-- Gol modal -->
                              <td class="text-center">
                                <!-- Diminuisci gol -->
                                <a href="../query/delete_gol.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                  <i class='bx bx-minus align-middle'></i>
                                </a>

                                <?php
                                  # QUERY che conta per ogni giocatore i gol fatti in questa partita
                                  $sql = "
                                  select coalesce(count(*),0) as gol_fatti
                                  FROM marcatori m
                                  where id_giocatore={$row2['id']}
                                  and id_partita='$id_partita'
                                  ";

                                  $result = mysqli_query($con,$sql);
                                  $count_gol_giornata = mysqli_fetch_assoc($result);
                                ?>

                                <span><?php echo $count_gol_giornata['gol_fatti'] ;?></span>
                                
                                <!-- Aumenta gol -->
                                <a href="../query/add_gol.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                  <i class='bx bx-plus align-middle'></i>
                                </a>

                              </td>
                              <!-- Cartellino giallo -->
                              <td class="text-center">
                                <?php
                                    # QUERY che conta per ogni giocatore i gol fatti in questa partita
                                    $sql = "
                                    select coalesce(count(*),0) as ammoniti
                                    FROM ammoniti a
                                    where id_giocatore={$row2['id']}
                                    and id_partita='$id_partita'
                                    ";

                                    $result = mysqli_query($con,$sql);
                                    $count_ammonito = mysqli_fetch_assoc($result);
                                  ?>
                                  <!-- Diminuisci cartellino giallo -->
                                  <a href="../query/delete_yellow_card.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                    <i class='bx bx-minus align-middle'></i>
                                  </a>
                                  <?php echo $count_ammonito['ammoniti'] ;?>
                                  <!-- Aumenta cartellino giallo -->
                                  <a href="../query/add_yellow_card.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                    <i class='bx bx-plus align-middle'></i>
                                  </a>
                              </td>
                              <!-- Cartellino rosso -->
                              <td class="text-center">
                                <?php
                                  # QUERY che conta per ogni giocatore i gol fatti in questa partita
                                  $sql = "
                                  select coalesce(count(*),0) as rossi
                                  FROM rossi r
                                  where id_giocatore={$row2['id']}
                                  and id_partita='$id_partita'
                                  ";

                                  $result = mysqli_query($con,$sql);
                                  $count_rossi = mysqli_fetch_assoc($result);
                                ?>
                                <!-- Diminuisci cartellino rosso -->
                                <a href="../query/delete_red_card.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                  <i class='bx bx-minus align-middle'></i>
                                </a>
                                
                                <?php echo $count_rossi['rossi'] ;?>
                                  
                                <!-- Aumenta cartellino rosso -->
                                <a href="../query/add_red_card.php?id_giocatore=<?php echo $row2['id']; ?>&match=<?php echo $id_partita; ?>&societa=<?php echo $ospite?>" class="text-decoration-none">
                                  <i class='bx bx-plus align-middle'></i>
                                </a>
                              </td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

</body>

</html>