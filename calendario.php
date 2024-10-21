<!-- Calendario partite per utente semplice -->
<?php
    session_start();
    require_once('config/db.php');
    include('admin/check_user_logged.php'); 

    $societa_id=$_REQUEST['id_squadra'];
    $stagione_id=$_REQUEST['id_stagione'];

    // Query per ottenere le informazioni sulla stagione
    $query1 = "SELECT * FROM stagioni s WHERE s.id_stagione = ?";
    $stmt = $con->prepare($query1);
    $stmt->bind_param("i", $stagione_id);
    $stmt->execute();
    $info_stagione = $stmt->get_result();
    $info = $info_stagione->fetch_assoc();
  
    $query = "SELECT DISTINCT CAST(giornata AS UNSIGNED) AS giornata_numero  FROM `partite` p WHERE p.id_stagione = $stagione_id ORDER BY giornata_numero";
    $result = mysqli_query($con, $query);
?>


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

        
        <!-- Descrizione iniziale -->
        <div class="container my-5 px-4">

            <div class="row">
                <div class="col-12">
                    <h1 class="bebas"><?php echo $info['descrizione'] ?></h1>
                </div>
            </div>

            <hr />
            
            <?php if(mysqli_num_rows($result)=== 0 ){ ?>
                <span class="text-muted">Nessuna partita inserita ancora a calendario</span>
            <?php } ?>
            <div class="row gy-3">
            <?php 
            while ($row = mysqli_fetch_assoc($result)) {
                $giornata_numero = $row['giornata_numero'];
                $query = "SELECT 
                            soc.nome_societa as casa, 
                            soc2.nome_societa as ospite, 
                            soc.logo as logo_casa, 
                            soc2.logo as logo_ospite, 
                            soc.id as id_casa, 
                            soc2.id as id_ospite, 
                            golCasa, 
                            golOspiti, 
                            CAST(giornata AS UNSIGNED) AS giornata_numero,
                            s.id, 
                            s.data,
                            soc.ora_match,
                            s.played,
                            CASE
                                WHEN s.orario_modificato IS NOT NULL THEN s.orario_modificato
                                ELSE soc.ora_match
                            END AS orario_partita,
                            CASE
                                WHEN s.data_modificata IS NOT NULL THEN s.data_modificata
                                ELSE s.data
                            END AS giornata_partita
                        FROM `partite` s
                        LEFT JOIN societa soc on soc.id=s.squadraCasa
                        LEFT JOIN societa soc2 on soc2.id=s.squadraOspite
                        WHERE s.id_stagione = '$stagione_id'
                        AND giornata = '$giornata_numero'
                        ORDER BY giornata_numero,s.data,ora_match, casa, ospite";
                $campionato = mysqli_query($con, $query);
            ?>
            

                <div class="col-12 col-xl-6 table-responsive">

                    <table class="table table-sm table-hover no-th table-striped table-rounded caption-top ">
                        <caption class="fs-5 text-dark fw-bold"><?php echo $giornata_numero ?> ° GIORNATA </caption>
                                 

                        <tbody class="">
                            <?php while($row = mysqli_fetch_assoc($campionato)) {  ?>

                            <a href="show_partita.php?id=<?php echo $row['id']?>" class="text-decoration-none" style="cursor:pointer">
                            <tr class="<?php echo $rowClass; ?> align-middle"  onclick="window.location.href = 'show_partita.php?id=' + <?php echo $row['id']?>;">
                                <!-- Risultato -->
                                <td class="text-center">
                                      <!-- Gol casa -->
                                      <?php echo isset($row['golCasa']) && $row['golCasa'] !== '' ? $row['golCasa'] : '-'; ?>
                                      <br/>
                                      <!-- Gol ospite -->
                                      <?php echo isset($row['golOspiti']) && $row['golOspiti'] !== '' ? $row['golOspiti'] : '-'; ?>
                                    </td>
                                    
                                    <!-- Squadre -->
                                    <td>
                                      <!-- Squadra casa -->
                                      <div class="<?= $row['casa'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">
                                        <?php if ($row['logo_casa']) { ?>
                                          <img src="image/loghi/<?php echo $row['logo_casa'];?>" class="rounded-circle"  width="15" height="15"/>
                                        <?php } else { ?>
                                          <img src="image/default_societa.png" class="rounded-circle"  width="15" height="15"/>
                                        <?php } ?>
                                        &nbsp;
                                        <?php echo $row['casa']; ?>
                                      </div>
                                      
                                      <!-- Squadra ospite -->
                                      <div class="<?= $row['ospite'] === 'Audax 1970' ? 'fw-bold' : 'text-dark'?>">
                                        <?php if ($row['logo_ospite']) { ?>
                                          <img src="../image/loghi/<?php echo $row['logo_ospite'];?>" class="rounded-circle"  width="15" height="15"/>
                                        <?php } else { ?>
                                          <img src="../image/default_societa.png" class="rounded-circle"  width="15" height="15"/>
                                        <?php } ?>
                                        &nbsp;
                                        <?php echo $row['ospite']; ?>
                                      </div>
                                    </td>
                                    
                                    <!-- Data -->
                                    <td class="text-end" >
                                      <small class="d-block">
                                        <?php echo date('d/m/y',strtotime( $row['data'])) ?> &nbsp;
                                      </small>
                                      
                                      <small class="d-block">
                                        <?php 
                                          setlocale(LC_TIME, 'it_IT.utf8');
                                          $dayOfWeek = strftime('%A', strtotime($row['data']));
                                          $abbreviatedDay = substr($dayOfWeek, 0, 3);
                                          echo $abbreviatedDay;
                                        ?> &nbsp;
                                      </small>
                                    </td>
                            </tr>
                            </a>

                            <?php } ?>
                        </tbody>
                    </table>

                </div>

                <?php } ?>
            </div>
        </div>


       

        <!-- Footer -->
        <footer class="p-5">
            <?php include 'elements/footer.php'; ?>
        </footer>

        <!-- Import -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"
            integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous">
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"
            integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous">
        </script>

        <script>
        $(document).ready(function() {
            var url = location;
            console.log(url);
            $('.nav-link').each(function() {
            if ($(this).attr('href') === url) {
                $(this).addClass('active');
            }
            });
        });
        </script>

    </body>

</html>