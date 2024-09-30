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

  
  $id= $_REQUEST['id'];
  $stagione_request= $_REQUEST['id_stagione'];

  $flag_provenienza = $_REQUEST['f'];
  $link_indietro = ($flag_provenienza == 0) ? 'calendario_admin.php?' : 'calendario_completo_admin.php?';
  

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
                          <h4>
                            Giornata <?php echo $partita['giornata'] ?>
                          </h4>

                          <!-- Bottoni a destra -->
                          <div class="cta-wrapper">	
                            <a type="button" class="btn btn-sm btn-outline-dark float-end me-2"  href="edit_risultato.php?id=<?php echo $partita["id"]; ?>">
                              Live
                            </a>   
                            
                            <a href="<?php echo $link_indietro .'&id_stagione=' .$stagione_request .'&id_societa=' .$id_societa ?>" type="button" class="btn btn-sm btn-outline-dark float-end me-2">
                              <i class='bi bi-arrow-left '></i>  Indietro
                            </a>
                                       
                          </div>
                        </div>
                      </div>

                      <!-- Riga casa vs ospite -->
                      <div class="row ">
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
                          <form action="../query/action_edit_risultato_massivo.php" method="post">
                            
                            

                            <table class="table table-sm table-hover table-striped table-rounded">
                              <caption> 
                                <?php echo $count_gol_squadra_a_giornata['gol_totali_giornata'] ?> segnati su <?php echo $golCasa ?> totali  
                                
                              </caption>
                              <thead class="table-dark">
                                <tr>
                                  <th>Cognome</th>
                                  <th>Nome</th>
                                  <th class="text-center"><img src="/image/icon/calcio.svg" width="15" height="15" alt="Gol"></th>
                                  <th class="text-center"><i class='bi bi-square-fill align-middle' style='color:#ffb900'></i></th>
                                  <th class="text-center"><i class='bi bi-square-fill align-middle' style='color:#FF0000'></i></th>
                                </tr>
                              </thead>

                              <tbody>
                                <?php while($row = mysqli_fetch_assoc($giocatori_casa)) {  ?>
                                <tr>
                                  <input type="hidden" value="<?php echo $id_partita ?>" id="match" name="match"/>
                                  <input type="hidden" value="<?php echo $row['id_squadra'] ?>" id="id_societa_casa" name="id_societa_casa"/>
                                  <td><?php echo $row['cognome'] ?></td>
                                  <td><?php echo $row['nome'] ?></td>
                                  <!-- Goal -->
                                  <td class="text-center" style="width:50px">
                                    
                                    
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

                                      <input value="<?php echo $count_gol_giornata['gol_fatti'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="gol-<?php echo $row['id'] ?>" name="gol-<?php echo $row['id'] ?>"></input>
                                      
                                      
                                  </td>
                                  <!-- Cartellino giallo -->
                                  <td class="text-center" style="width:50px">
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
                                      <input value="<?php echo $count_ammonito['ammoniti'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="giallo-<?php echo $row['id'] ?>" name="giallo-<?php echo $row['id'] ?>"></input>

                                      
                                    
                                  </td>
                                  <!-- Cartellino rosso -->
                                  <td class="text-center" style="width:50px">
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
                                    <input value="<?php echo $count_rossi['rossi'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="rosso-<?php echo $row['id'] ?>" name="rosso-<?php echo $row['id'] ?>"></input>
                                    
                                    
                                    
                                  </td>

                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                            <button type="submit" class="btn btn-sm btn-outline-dark float-end">Salva</button>
                          </form>
                        </div>
                        <!-- Colonna OSPITE  -->
                        <div class="col-12 col-lg-6 table-responsive">
                          <!-- Intestazione squadra ospite con gol segnati -->
                          <h4>
                            <a href="show_societa.php?id=<?php echo $partita['idOspite'] ?>" class="text-decoration-none text-dark">
                              <?php echo $partita['ospite'] ?>
                            </a>
                            <span class="float-end">
                              <?php echo $partita['golOspiti'] ?>
                            </span>
                          </h4>

                          <hr/>
                          <!-- Tabella OSPITE  -->
                          <form action="../query/action_edit_risultato_massivo_f.php" method="post">
                            
                            
                            <table class="table table-sm table-hover table-striped table-rounded">
                              <caption> 
                                <?php echo $count_gol_squadra_b_giornata['gol_totali_giornata'] ?> segnati su <?php echo $golOspiti ?> totali  
                                
                              </caption>
                              <thead class="table-dark">
                                <tr>
                                  <th>Cognome</th>
                                  <th>Nome</th>
                                  <th class="text-center"><img src="/image/icon/calcio.svg" width="15" height="15" alt="Gol"></th>
                                  <th class="text-center"><i class='bi bi-square-fill align-middle' style='color:#ffb900'></i></th>
                                  <th class="text-center"><i class='bi bi-square-fill align-middle' style='color:#FF0000'></i></th>
                                </tr>
                              </thead>

                              <tbody>
                                <?php while($row2 = mysqli_fetch_assoc($giocatori_ospiti)) {  ?>
                                <tr>
                                  <input type="hidden" value="<?php echo $id_partita ?>" id="match" name="match"/>
                                  <input type="hidden" value="<?php echo $row2['id_squadra'] ?>" id="id_societa_ospite" name="id_societa_ospite"/>
                                  
                                  <td><?php echo $row2['cognome'] ?></td>
                                  <td><?php echo $row2['nome'] ?></td>
                                  <!-- Gol modal -->
                                  <td class="text-center" style="width:50px">
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
                                    <input value="<?php echo $count_gol_giornata['gol_fatti'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="golF-<?php echo $row2['id'] ?>" name="golF-<?php echo $row2['id'] ?>"></input>
                                    
                                    

                                  </td>
                                  <!-- Cartellino giallo -->
                                  <td class="text-center" style="width:50px">
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
                                      <input value="<?php echo $count_ammonito['ammoniti'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="gialloF-<?php echo $row2['id'] ?>" name="gialloF-<?php echo $row2['id'] ?>"></input>
                                      
                                  </td>
                                  <!-- Cartellino rosso -->
                                  <td class="text-center" style="width:50px">
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
                                    
                                    <input value="<?php echo $count_rossi['rossi'] ;?>" class="form-control form-control-sm p-1 text-center" style="font-size:10px;width:30px;border:none;background-color:transparent" id="rossoF-<?php echo $row2['id'] ?>" name="rossoF-<?php echo $row2['id'] ?>"></input>
                                    
                                      
                                  
                                  </td>
                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                            <button type="submit" class="btn btn-sm btn-outline-dark float-end">Salva</button>
                          </form>
                        </div>
                        
                      </div>
                      
                      <br/>
                      <br/>
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