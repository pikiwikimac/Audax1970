<?php
    session_start();
    // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
    if (!isset($_SESSION['username'])) {
        header('Location: ../login/login.php');
        exit;
    }
    
    include '../query/convertiMese.php';
    require_once('../config/db.php');
    
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
    $superuser=  $_SESSION['superuser'];
    
    if($superuser ==0 ){
        header('Location: ../error_page/access_denied.php');
        exit;
    }
    // Query entrate totali
    $query = "
        SELECT sum(e.importo) as totale_entrate
        FROM `entrate` e    ";

    $entrate = mysqli_query($con, $query);
    $totale_entrate = mysqli_fetch_assoc($entrate);

    // Query lista di tutte le entrate
    $query = "
        SELECT e.*
        FROM `entrate` e   ";

    $lista_entrate = mysqli_query($con, $query);

    // Query uscite totali
    $query = "
        SELECT sum(e.importo) as totale_uscite
        FROM `uscite` e
        ";
    $uscite = mysqli_query($con, $query);
    $totale_uscite = mysqli_fetch_assoc($uscite);

    // Query lista di tutte le uscite

    $query = "
    SELECT u.*
    FROM `uscite` u   ";

    $lista_uscite = mysqli_query($con, $query);

    // Query uscite totali
    $query = "
        SELECT
        (SELECT COALESCE(SUM(importo), 0) FROM uscite WHERE mese_competenza = MONTH(CURRENT_DATE())) AS totale_uscite,
        (SELECT COALESCE(SUM(importo), 0) FROM entrate WHERE mese_competenza = MONTH(CURRENT_DATE())) AS totale_entrate,
        coalesce((
            SELECT COALESCE(SUM(importo), 0) FROM entrate WHERE mese_competenza = MONTH(CURRENT_DATE())
        ) - (
            SELECT COALESCE(SUM(importo), 0) FROM uscite WHERE mese_competenza = MONTH(CURRENT_DATE())
        ),0) AS differenza,
        (SELECT (COALESCE(sum(importo),0)/12) as entrate_annuali_ammortizzate FROM entrate WHERE mese_competenza=0) as entrate_amm_mens,
        (SELECT (COALESCE(sum(importo),0)/12) as spese_annuali_ammortizzate FROM uscite WHERE mese_competenza=0)as uscite_amm_mens;
        ";

    $result = mysqli_query($con, $query);
    $movimenti_mensili = mysqli_fetch_assoc($result);

    $effetivo_uscite_mese = $movimenti_mensili['totale_uscite'] + $movimenti_mensili['uscite_amm_mens'];
    $effetivo_entrate_mese = $movimenti_mensili['totale_entrate'] + $movimenti_mensili['entrate_amm_mens'];
    $differenza_effettivo = $effetivo_entrate_mese - $effetivo_uscite_mese
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
                                            <h1 class="">
                                                Amministrazione
                                                <!-- Tootlip di informazione -->
                                                <a class="text-decoration-none float-end text-dark " data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Le transazioni non possono essere modificate, in caso di errore, eliminare e reinserire.">
                                                    <i class='bx bx-info-circle'></i>
                                                </a>
                                            </h3>
                                        </div>
                                    </div>
                                    <!-- END: Intestazione -->

                                    <!-- Core della pagina -->
                                    <div class="container-fluid">

                                        <div class="row gy-3">
                                            <!-- Entrate -->
                                            <div class="col-12 col-lg-4">
                                                <div class="card" style="min-height:12rem;">
                                                    <div class="card-header bg-dark text-light">
                                                        <i class='bx bx-trending-up'></i> Entrate
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="fs-1 text-success"><?php echo $totale_entrate['totale_entrate'] ?></span>
                                                            </div>

                                                            <div class="col-12 mt-3">
                                                                <a type="button" href="" class="btn  btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAggiungiEntrata" aria-controls="offcanvasAggiungiEntrata">
                                                                    Aggiungi entrata
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END Entrate -->


                                            <!-- Uscite -->
                                            <div class="col-12 col-lg-4">
                                                <div class="card" style="min-height:12rem;">
                                                    <div class="card-header bg-dark text-light">
                                                        <i class='bx bx-trending-down'></i> Uscite
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="fs-1 text-danger"><?php echo $totale_uscite['totale_uscite'] ?></span>
                                                            </div>

                                                            <div class="col-12 mt-3">
                                                                <a type="button" href="" class="btn btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAggiungiSpesa" aria-controls="offcanvasAggiungiSpesa">
                                                                    Aggiungi spesa
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END Uscite -->



                                            <!-- Differenza mensile -->
                                            <div class="col-12 col-lg-4">
                                                <div class="card" style="min-height:12rem;">
                                                    <div class="card-header bg-dark text-light">
                                                        + / - Mese
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span class="fs-1 <?php echo ($differenza_effettivo >= 0) ? 'text-dark' : 'text-danger'; ?>">
                                                                    <?php echo number_format($differenza_effettivo, 2); ?>
                                                                </span>

                                                                <br />

                                                                <span class="text-muted">
                                                                    <?php echo convertiMeseItaliano(date('n'))  ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END Differenza mensile -->

                                        </div>



                                        <div class="row mt-5 gy-3">
                                            <div class="col-12 col-lg-6">
                                                <?php if($lista_entrate ->num_rows > 0){ ?>
                                                    <h3>Registro entrate</h3>
                                                    <hr>

                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped sortable table-rounded" id="tabella-giocatori">
                                                            <thead class="table-dark ">
                                                                <tr>
                                                                    <th>Nome</th>
                                                                    <th>Competenza</th>
                                                                    <th>Importo</th>
                                                                    <th>Note</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>

                                                                <?php while ($row = mysqli_fetch_assoc($lista_entrate)) {  ?>

                                                                    <tr>
                                                                        <td><?php echo $row['nome_intestatario'] ?></td>
                                                                        <td><?php if ($row['mese_competenza'] == 0) : ?>
                                                                                <span>Annuale</span>
                                                                            <?php else : ?>
                                                                                <?php echo convertiMeseItaliano($row['mese_competenza']); ?>
                                                                            <?php endif; ?>
                                                                        </td>

                                                                        <td><?php echo $row['importo'] ?></td>
                                                                        <td><?php echo $row['giustificativo'] ?></td>
                                                                        <td>
                                                                            <a class="text-decoration-none" href="#" onclick="confirmDelete('<?php echo $row["id"]; ?>')">
                                                                                <i class='bx bx-trash text-danger'></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>

                                                                <?php } ?>

                                                            </tbody>

                                                        </table>

                                                    </div>
                                                <?php } ?>
                                            </div>


                                            <div class="col-12 col-lg-6">
                                                <?php if($lista_uscite ->num_rows > 0){ ?>
                                                    <h3>Registro uscite</h3>
                                                    <hr>

                                                    <div class="table-responsive">

                                                        <table class="table table-hover table-striped sortable table-rounded" id="tabella-giocatori">

                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>Nome</th>
                                                                    <th>Competenza</th>
                                                                    <th>Importo</th>
                                                                    <th>Note</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php while ($row2 = mysqli_fetch_assoc($lista_uscite)) {  ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $row2['destinatario'] ?>
                                                                        </td>
                                                                        <td><?php if ($row2['mese_competenza'] == 0) : ?>

                                                                                <span>Annuale</span>

                                                                            <?php else : ?>

                                                                                <?php echo convertiMeseItaliano($row2['mese_competenza']); ?>

                                                                            <?php endif; ?>
                                                                        </td>
                                                                        
                                                                        <td>
                                                                            <?php echo $row2['importo'] ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php echo $row2['giustificativo'] ?>
                                                                        </td>

                                                                        <td>
                                                                            <a class="text-decoration-none" href="#" onclick="confirmDeleteUscita('<?php echo $row2["id"]; ?>')">
                                                                                <i class='bx bx-trash text-danger'></i>
                                                                            </a>
                                                                        </td>

                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                <?php } ?>

                                            </div>

                                        </div>

                                    </div>
                                    <!-- END:Core della pagina -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>


        <!-- Aggiungi spesa Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAggiungiSpesa" aria-labelledby="offcanvasAggiungiSpesaLabel">

            <div class="offcanvas-header bg-dark text-light">
                <h5 class="offcanvas-title" id="offcanvasAggiungiSpesaLabel">Aggiungi spesa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <form action="../query/crea_spesa.php" method="post">
                    <div class="mb-3">
                        <label for="importo" class="form-label">Importo</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">€</span>
                            <input type="number" class="form-control" id="importo" name="importo" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="destinatario" class="form-label">Destinatario</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class='bx bxs-user-circle'></i></span>
                            <input type="text" class="form-control" id="destinatario" name="destinatario" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivazione" class="form-label">Motivazione</label>
                        <textarea class="form-control" id="motivazione" name="motivazione"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="mese_competenza" class="form-label">
                            <!-- Tootlip di informazione -->
                            <a class="text-decoration-none   text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                data-bs-title="Non selezionare nessun mese se è una spesa annuale">
                                <i class='bx bx-info-circle align-middle '></i>
                            </a> &nbsp;
                            Mese di competenza
                        </label>
                        <select class="form-select" id="mese_competenza" name="mese_competenza">
                            <option value="">-- Spesa annuale --</option>
                            <option value="1">Gennaio</option>
                            <option value="2">Febbraio</option>
                            <option value="3">Marzo</option>
                            <option value="4">Aprile</option>
                            <option value="5">Maggio</option>
                            <option value="6">Giugno</option>
                            <option value="7">Luglio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Settembre</option>
                            <option value="10">Ottobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Dicembre</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-dark mt-3">Aggiungi</button>
                </form>
            </div>

        </div>

        <!-- Aggiungi entrata Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAggiungiEntrata" aria-labelledby="offcanvasAggiungiEntrataLabel">
            <div class="offcanvas-header bg-dark text-light">
                <h5 class="offcanvas-title" id="offcanvasAggiungiEntrataLabel">Aggiungi entrata</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>

            </div>

            <div class="offcanvas-body">
                <form action="../query/crea_entrata.php" method="post">
                    <div class="mb-3">
                        <label for="importo" class="form-label">Importo</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">€</span>
                            <input type="number" class="form-control" id="importo" name="importo" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mittente" class="form-label">Mittente</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class='bx bxs-user-circle'></i></span>
                            <input type="text" class="form-control" id="mittente" name="mittente" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivazione" class="form-label">Motivazione</label>
                        <textarea class="form-control" id="motivazione" name="motivazione"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="mese_competenza" class="form-label">
                            <!-- Tootlip di informazione -->
                            <a class="text-decoration-none   text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                data-bs-title="Non selezionare nessun mese se è un ricavo annuale">
                                <i class='bx bx-info-circle align-middle '></i>
                            </a> &nbsp;
                            Mese di competenza
                        </label>
                        <select class="form-select" id="mese_competenza" name="mese_competenza">
                            <option value="0">-- Ricavo annuale --</option>
                            <option value="1">Gennaio</option>
                            <option value="2">Febbraio</option>
                            <option value="3">Marzo</option>
                            <option value="4">Aprile</option>
                            <option value="5">Maggio</option>
                            <option value="6">Giugno</option>
                            <option value="7">Luglio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Settembre</option>
                            <option value="10">Ottobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Dicembre</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-dark mt-3">Aggiungi</button>
                </form>

            </div>
        </div>


        <!-- Import -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

        <script>
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            function confirmDelete(recordId) {
                var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
                if (confirmDelete) {
                    // Effettua la richiesta di eliminazione al server
                    window.location.href = "../query/delete_entrata.php?id=" + recordId;
                }
            }

            function confirmDeleteUscita(recordId) {
                var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
                if (confirmDelete) {
                    // Effettua la richiesta di eliminazione al server
                    window.location.href = "../query/delete_uscita.php?id=" + recordId;
                }

            }
        </script>

    </body>

</html>