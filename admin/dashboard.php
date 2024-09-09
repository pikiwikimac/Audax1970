<?php
    session_start();
    require_once('../config/db.php');
        
    // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
    if (!isset($_SESSION['username'])) {
        header('Location: ../login/login.php');
        exit;
    }
    

    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
    $superuser = $_SESSION['superuser'];
    $id_societa=$_SESSION['id_societa_riferimento'];



    $query_tipo_societa="
    SELECT tipo FROM societa WHERE id='$id_societa'
    ";
    $tipologie = mysqli_query($con,$query_tipo_societa);
    $tipo = mysqli_fetch_assoc($tipologie);

    # QUERY per trovare tutte le info relative al prossimo Match
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
            s.squadraCasa = '$id_societa' OR s.squadraOspite = '$id_societa'
        ) AND s.played = 0 
   
    ORDER BY data
    LIMIT 1
    ";

    $prossimo_match = mysqli_query($con,$query_prossimo_match);
    $num_match = mysqli_num_rows($prossimo_match);

    if ($num_match > 0) {
        // Almeno un record trovato
        $row2 = mysqli_fetch_assoc($prossimo_match);
    }
    #END QUERY

    # QUERY per trovare il prossimo allenamento
    $query_prossimo_allenamento="
    SELECT a.*
    FROM `allenamenti` a
    WHERE a.data >= CURDATE()
    ORDER BY a.data ASC
    LIMIT 1;
    ";
    $prossimo_allenamento = mysqli_query($con,$query_prossimo_allenamento);
    $num_allenamenti = mysqli_num_rows($prossimo_allenamento);

    if ($num_allenamenti > 0) {
        // Almeno un record trovato
        $row = mysqli_fetch_assoc($prossimo_allenamento);
    }
    # END QUERY

    # QUERY giocatori Indisponibili
    $query = "
    SELECT g.nome , g.cognome , i.*
    FROM indisponibili i
    INNER JOIN giocatori g  on g.id=i.id_giocatore
    WHERE i.a_data >= CURRENT_DATE
    ORDER BY i.a_data DESC";
    $result = mysqli_query($con,$query);
    $num_indisponibili = mysqli_num_rows($result);
    # END QUERY

    # QUERY cerca giocatori
    $query_rosa = "
    SELECT g.*,ag.id_societa,g.id_squadra as squadra_madre,(  
            SELECT count(*) as tot_player
            FROM partecipazione_allenamenti pa
            WHERE pa.id_giocatore = g.id
            )as numero_allenamenti,
            (
                SELECT count(*) as convocazioni
                FROM convocazioni c
                INNER JOIN partite p on p.id=c.id_partita
                WHERE c.id_giocatore=g.id
                AND p.id_stagione in (1,2)
            ) as convocazioni
    FROM giocatori g
    INNER JOIN affiliazioni_giocatori ag ON ag.id_giocatore = g.id
    WHERE ag.id_societa = '$id_societa'
    AND ag.data_fine IS NULL
    ORDER BY ruolo,cognome,nome asc";
    $giocatori = mysqli_query($con,$query_rosa);
    
    # END QUERY

    # QUERY conta numero di giocatori
    $query_contarosa = "
    SELECT count(*) as numero_giocatori
    FROM giocatori g
    INNER JOIN affiliazioni_giocatori ag on ag.id_giocatore=g.id
    WHERE ag.id_societa = '$id_societa'
    AND ag.data_fine is NULL";
    $res = mysqli_query($con,$query_contarosa);
    $numero_giocatori = mysqli_fetch_assoc($res);
    # END QUERY

    # QUERY conta numero di giocatori
    $query_contarichieste = "select count(*) as numero_richieste FROM users where accettato=0 ";
    $res2 = mysqli_query($con,$query_contarichieste);
    $numero_richieste=mysqli_fetch_assoc($res2);

?>


<style>
    .equal-height-card {
        display: flex;
        flex-direction: column;
    }
</style>

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
                            <div class="row">
                                <div class="col-12 ">
                                    
                                    <div class="container-fluid">
                                        <!-- Intestazione -->
                                        <div class="tpl-header">
                                            <div class="tpl-header--title">
                                                <h4 class="">
                                                    Ciao  <?php echo $username ?> ! &nbsp; <span class="badge bg-secondary text-white" style="font-size:12px"><?php echo $tipo['tipo'] ?></span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END:Intestazione -->

                                    <!-- Core della pagina -->
                                    <div class="container-fluid">
                                        <div class="row g-3 mb-3">
                                            <!-- Prossimo match -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <!-- Prima card -->
                                                <div class="card equal-height-card" style="min-height:12rem;">
                                                    <div class="card-header bg-dark text-light">  
                                                        <?php if (!empty($row2["id"])) : ?>
                                                            <i class="bx bx-football align-middle"></i> <?php echo $row2["descrizione"] .' - Giornata ' .$row2["giornata"] .'°' ?>
                                                            <a class="text-decoration-none text-light float-end " href="edit_presenza_convocazione.php?id=<?php echo $row2["id"]; ?>" >
                                                                <i class='bx bx-right-arrow-alt align-middle' ></i>
                                                            </a>
                                                        <?php else : ?>
                                                            <span class=""><i class="bx bx-football align-middle"></i> Prossimo match</span>
                                                        <?php endif; ?>
                                                    </div>
                                                                
                                                    <div class="card-body">
                                                        <?php if (!empty($row2["id"])) : ?>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <!-- Prossimo match -->
                                                                    <span class="fs-6">
                                                                        <a href="show_societa.php?id=<?php echo ($row2['squadraCasa'] === 1) ? $row2['squadraCasa'] : $row2['squadraOspite']; ?>" class="text-decoration-none text-dark" style="cursor:pointer">
                                                                            <?php echo $row2['casa'] . '-' . $row2['ospite']; ?>
                                                                        </a>
                                                                    </span>

                                                                    <br/>

                                                                    <!-- Data prossimo match -->
                                                                    <div class="fs-7 mt-3">
                                                                        <i class='bx bx-calendar' ></i>
                                                                        <?php echo  date("d/m/y", strtotime($row2['giornata_partita'])) .' - ' .date("H:i",strtotime($row2['orario_partita'])); ?>
                                                                    </div>

                                                                    <!-- Luogo prossimo match -->
                                                                    <div class="fs-7 text-muted">
                                                                        <i class='bx bx-map-pin' ></i>
                                                                        <?php echo $row2['sede'] . ' - ' . $row2['citta']; ?>
                                                                    </div>
                                                                    
                                                                    <span class="text-muted float-end" style="cursor:pointer" onclick="apriGoogleMapsConIndirizzo('<?php echo $row2['sede'] . ', ' . $row2['citta']; ?>')"> Google maps <i class='bx bx-right-arrow-alt align-middle' ></i></span>

                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <span class="">Nessun match fissato</span>
                                                        <?php endif; ?> 
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Prossimo allenamento -->
                                            <div class="col-12 col-md-6 col-lg-4">

                                                <div class="card equal-height-card" style="min-height:12rem;">

                                                    <div class="card-header bg-dark text-light">
                                                        <i class='bx bx-dumbbell align-middle'></i> Prossimo allenamento
                                                        <a class="text-decoration-none text-light float-end " href="allenamenti_admin.php?id_societa=<?php echo $id_societa ?>" >
                                                            <i class='bx bx-right-arrow-alt align-middle' ></i>
                                                        </a>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if($num_allenamenti > 0){ ?>
                                                        <a class="text-decoration-none text-dark align-middle" href="edit_presenza_allenamento.php?id=<?php echo $row['id']; ?>" >
                                                            <div class="row">

                                                                <div class="col-12">

                                                                    <!-- Tipologia -->
                                                                    <span class="fs-6">
                                                                        <?php echo $row['tipologia'] ?>
                                                                    </span>

                                                                    <br/>

                                                                    <div class="fs-7 mt-3">
                                                                        <i class='bx bx-calendar'></i>
                                                                        <?php
                                                                            $dataOggi = date("d/m/y");
                                                                            $dataDaVisualizzare = date("d/m/y", strtotime($row['data']));

                                                                            if ($dataDaVisualizzare === $dataOggi) {
                                                                            echo "<span class='text-danger'>$dataDaVisualizzare</span>" ;
                                                                            } else {
                                                                            echo $dataDaVisualizzare;
                                                                            }
                                                                        ?> - 
                                                                        
                                                                        <?php echo date("H:i",strtotime($row['orario'])) ?>
                                                                    </div>

                                                                    
                                                                    <!-- Luogo -->
                                                                    <div class="fs-7 text-muted">
                                                                        <i class='bx bx-map-pin' ></i> <?php echo $row['luogo'] ?>
                                                                    </div>
                                                                    <!-- Note -->
                                                                    <small class="text-muted">
                                                                        <?php echo $row['note'] ?>
                                                                    </small>

                                                                </div>
                                                            </div>
                                                        </a>
                                                        <?php } ?>

                                                        <?php if($num_allenamenti == 0){ ?>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="fs-7">Nessun allenamento fissato</span>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>

                                                </div>

                                            </div>

                                            <!-- Indisponibili -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="card equal-height-card" style="min-height:12rem;">
                                                    <div class="card-header bg-dark text-light">
                                                        <i class="bx bxs-ambulance align-middle"></i> Indisponibili
                                                        <a class="text-decoration-none text-light float-end " href="indisponibili_admin.php" >
                                                            <i class='bx bx-right-arrow-alt align-middle' ></i>
                                                        </a>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <?php while($indisponibili = mysqli_fetch_assoc($result)){  ?>
                                                                    <?php
                                                                        $a_data = $indisponibili['a_data'];

                                                                        // Creazione dell'oggetto DateTime per la data di fine
                                                                        $a_data_dt = DateTime::createFromFormat('Y-m-d', $a_data);

                                                                        // Data di oggi
                                                                        $oggi = new DateTime();

                                                                        // Calcolo della differenza tra oggi e la data di fine
                                                                        $differenza = $oggi->diff($a_data_dt);

                                                                        // Accesso ai componenti della differenza
                                                                        $giorni_rimasti = $differenza->days;

                                                                        if($giorni_rimasti > 1){
                                                                        echo $indisponibili['nome'] . ' ' . $indisponibili['cognome'] 
                                                                            . '<span class="float-end"> (' . $giorni_rimasti . ' giorni rimasti)
                                                                            </span>
                                                                            <br/>';
                                                                        }elseif($giorni_rimasti == 1){
                                                                            echo $indisponibili['nome'] . ' ' . $indisponibili['cognome'] 
                                                                            . '<span class="float-end"> (1 giorno rimasto)
                                                                            </span>
                                                                            <br/>';

                                                                        }else{
                                                                            echo $indisponibili['nome'] . ' ' . $indisponibili['cognome'] 
                                                                            . '<span class="float-end"> (ultimo giorno)
                                                                            </span>
                                                                            <br/>';
                                                                        }
                                                                    ?>  

                                                                <?php } ?>

                                                                <!-- Se non ho nessuno indisponibile -->
                                                                <?php if($num_indisponibili==0) {?>
                                                                    <span class="fs-7">
                                                                        Tutti i giocatori sono disponibili
                                                                    </span>
                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bottoni link rapidi -->
                                            <div class="col-12">
                                                <!-- Gestione registrazioni -->
                                                <?php if($_SESSION['superuser']==1 && $numero_richieste['numero_richieste']>0){ ?>
                                                <a href="gestore_registrazioni.php" type="button" class="btn btn-sm btn-outline-dark me-2 position-relative" data-bs-toggle="tooltip" data-bs-title="Richieste registrazioni" data-bs-placement="bottom">
                                                    <i class='bx bx-user-plus'></i> 
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        <?php echo $numero_richieste['numero_richieste'] ?>
                                                    </span>
                                                </a>
                                                <?php } ?>
                                                <!-- Comunicazioni telegram -->
                                                <a role="button" class="btn btn-sm btn-outline-dark me-2" data-bs-toggle="modal"  data-bs-title="Comunicazione telegram"  
                                                    data-bs-target="#insertModal" href="comunicazione_telegram.php" data-bs-placement="bottom">
                                                    <i class='bx bxl-telegram'></i>  
                                                </a>
                                                <!-- Calendario -->
                                                <a role="button" class="btn btn-sm btn-outline-dark me-2" href="google_calendar.php" data-bs-toggle="tooltip" data-bs-title="Google calendar" data-bs-placement="bottom">
                                                    <i class='bx bxs-calendar'></i> 
                                                </a>
                                                <!-- Vai ai comunicati calcio a 5 marche -->
                                                <a type="button" href="https://www.figcmarche.it/categoria-comunicati/calcio-a-5" class="btn btn-sm btn-outline-dark  me-2" data-bs-toggle="tooltip" data-bs-title="Comunicati LND" data-bs-placement="bottom" target="blank">
                                                    <i class='bx bx-archive'></i>
                                                </a>
                                                <!-- Link mercato -->
                                                <?php if($_SESSION['superuser']==1 ){ ?>
                                                    <a href="mercato.php" type="button" class="btn btn-sm btn-outline-dark  me-2 position-relative" data-bs-toggle="tooltip" data-bs-title="Mercato" data-bs-placement="bottom">
                                                        <i class='bx bx-store'></i> 
                                                    </a>
                                                <?php } ?>
                                                <!-- Link a gallery -->
                                                <?php if($_SESSION['superuser']==1 ){ ?>
                                                    <a href="create_gallery.php" type="button" class="btn btn-sm btn-outline-dark me-2 position-relative" data-bs-toggle="tooltip" data-bs-title="Gallery" data-bs-placement="bottom">
                                                        <i class='bx bx-photo-album'></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                            
                                            <!-- Tabella -->
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-striped table-rounded " >

                                                        <caption>
                                                            <?php echo $numero_giocatori['numero_giocatori'] ?> giocatori totali
                                                        </caption>

                                                        <thead class="table-dark">

                                                            <tr>
                                                                <th width="5%"></th>
                                                                <th>Nome</th>
                                                                <th class="text-center" width="8%">Ruolo</th>
                                                                <th class="text-center" width="8%">Anno</th>
                                                                <th class="text-center" width="5%">N.</th>
                                                                <th class="text-center" width="5%"><span data-bs-toggle="tooltip" data-bs-title="Allenamenti svolti" data-bs-placement="bottom">A</span></th>
                                                                <th class="text-center" width="5%"><span data-bs-toggle="tooltip" data-bs-title="Partite" data-bs-placement="bottom">P</span></th>
                                                            </tr>

                                                        </thead>

                                                        <tbody class="mb-5">
                                                            <?php while($row = mysqli_fetch_assoc($giocatori)) {  ?>

                                                            <tr onclick="window.location='player.php?id=<?php echo $row['id']; ?>';" style="cursor:pointer" class="align-middle">
                                                                <!-- Immagine -->
                                                                <td class="text-center">
                                                                    <?php if ($row['image_path']) { ?>
                                                                        <img src="../image/player/<?php echo $row['image_path'];?>" class="rounded-circle " alt="<?php echo $row['cognome'].' '.$row['nome'];?>" data-player-name="<?php echo $row['cognome'].' '.$row['nome'];?>" width="30" height="30"/>
                                                                    <?php } else { ?>
                                                                        <img src="../image/default_user.jpg" class="rounded-circle" alt="Immagine di default" data-player-name="<?php echo $row['cognome'] .' ' .$row['nome'];?>" width="30" height="30" />
                                                                    <?php } ?>
                                                                </td>
                                                                <!-- Nome e cognome -->
                                                                <td class="fw-semibold" >
                                                                    <?php echo $row['cognome'] . '  ' .$row['nome']   ?> 
                                                                    <?php
                                                                        if ($row['capitano'] == 'C' && $row['squadra_madre'] == $id_societa) {
                                                                            echo '<i class="bx bx-copyright  float-end"></i>';
                                                                        } elseif ($row['capitano'] == 'VC' && $row['squadra_madre'] == $id_societa) {
                                                                            echo '<span class="float-end">VC</span>';
                                                                        }
                                                                    ?>
                                                                    <?php
                                                                        // Verifica se il giocatore è presente nella tabella indisponibili
                                                                        $giocatoreId = $row['id'];
                                                                        $queryVerificaIndisponibilita = "SELECT motivo FROM indisponibili WHERE id_giocatore = $giocatoreId AND a_data >= CURRENT_DATE AND da_data < CURRENT_DATE ";
                                                                        $resultVerifica = mysqli_query($con, $queryVerificaIndisponibilita);
                                                                        
                                                                        if (mysqli_num_rows($resultVerifica) > 0) {
                                                                            // Il giocatore è indisponibile, otteniamo il motivo
                                                                            $rowIndisponibilita = mysqli_fetch_assoc($resultVerifica);
                                                                            $motivo = $rowIndisponibilita['motivo'];
                                                                            
                                                                            // In base al motivo, mostriamo l'icona corrispondente
                                                                            if ($motivo === 'Lavoro') {
                                                                                echo '<i class="bx bx-briefcase " data-bs-toggle="tooltip" data-bs-title="Lavoro" data-bs-placement="bottom"></i>'; // Icona per Lavoro
                                                                            } elseif ($motivo === 'Malattia') {
                                                                                echo '<i class="bx bxs-ambulance text-danger" data-bs-toggle="tooltip" data-bs-title="Malattia" data-bs-placement="bottom"></i>'; // Icona per Malattia
                                                                            } elseif ($motivo === 'Viaggio') {
                                                                                echo '<i class="bx bxs-plane-alt" data-bs-toggle="tooltip" data-bs-title="Viaggio" data-bs-placement="bottom"></i >'; // Icona per Viaggio
                                                                            } else {
                                                                                // Motivo sconosciuto, puoi gestirlo in modo appropriato
                                                                            }
                                                                        }
                                                                    ?>
                                                                </td>
                                                            

                                                                <td class="text-center"> 
                                                                    <?php if($row['ruolo']==='Portiere'){
                                                                    echo '
                                                                    <span class="badge bg-warning text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Portiere" data-bs-placement="bottom">
                                                                        P'
                                                                    .'</span>';
                                                                    }elseif($row['ruolo']==='Centrale'){
                                                                    echo '
                                                                    <span class="badge bg-success text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Centrale" data-bs-placement="bottom">
                                                                        C'
                                                                    .'</span>';
                                                                    }elseif($row['ruolo']==='Laterale'){
                                                                    echo '
                                                                    <span class="badge bg-primary text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Laterale" data-bs-placement="bottom">
                                                                        L'
                                                                    .'</span>';
                                                                    }elseif($row['ruolo']==='Pivot'){
                                                                    echo '
                                                                    <span class="badge bg-danger text-light" style="width:30px" data-bs-toggle="tooltip" data-bs-title="Pivot" data-bs-placement="bottom">
                                                                        P'
                                                                    .'</span>';
                                                                    }else{
                                                                    echo '
                                                                    <span class="badge bg-info text-light" style="width:30px;"  data-bs-toggle="tooltip" data-bs-title="Universale" data-bs-placement="bottom">
                                                                        U'
                                                                    .'</span>';
                                                                    } ?>
                                                                </td>
                                                                
                                                            <td class="text-center">
                                                                <?php if($row['data_nascita']==='0000-00-00'){
                                                                        echo '-';
                                                                    }else{
                                                                        echo date('Y', strtotime($row['data_nascita']));
                                                                } ?>
                                                                
                                                            </td>
                                                            <td class="text-center"><?php echo $row['maglia'] ?></td>
                                                            <td class="text-center"><?php echo $row['numero_allenamenti'] ?></td>
                                                            <td class="text-center"><?php echo $row['convocazioni'] ?></td>
                                                            </tr>

                                                            <?php } ?>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END:Core della pagina -->

                                    <!-- Modal  -->
                                    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="insertModalLabel">Comunicazione</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="insertForm" method="post" action="comunicazioni_telegram.php">
                                                        <div class="row">
                                                            <!-- Nome -->
                                                            <div class="col-12 mb-3">
                                                                <label for="comunicazione" class="form-label">Comunicazione</label>
                                                                <textarea  class="form-control" id="comunicazione" name="comunicazione" ></textarea>
                                                            </div> 
                                                        </div> 
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="submitInsertForm()">Inserisci</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Modal  -->
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        
        <!-- Tooltip Initialization -->
        <script>
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>

        
        <!-- Script : per il submit del form -->
        <script>
            function submitInsertForm() {
                // Effettua la richiesta di inserimento al server tramite il form
                document.getElementById("insertForm").submit();
            }
        </script>

        <!-- Script : per aprire indirizzo google maps next match -->
        <script>
            function apriGoogleMapsConIndirizzo(indirizzo) {
                // L'indirizzo che desideri preimpostare
                // Codifica l'indirizzo per l'URL
                var indirizzoCodificato = encodeURIComponent(indirizzo);

                // Crea l'URL di Google Maps con l'indirizzo preimpostato
                var url = "https://www.google.com/maps/search/?api=1&query=" + indirizzoCodificato;

                // Apri Google Maps in una nuova finestra o scheda
                window.open(url, "_blank");
            }
        </script>
        

        
    </body>

</html>