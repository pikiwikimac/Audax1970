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
  $superuser=  $_SESSION['superuser'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT s.*, c.descrizione, c.girone, c.anno_inizio, c.anno_fine
  FROM societa s
  LEFT JOIN stagioni c ON c.id_stagione = s.id_campionato
  WHERE s.id != 99
    AND (s.tipo IS NULL OR s.tipo != 'Prima squadra')
  ORDER BY s.id_campionato DESC,c.girone, c.descrizione, c.girone, s.nome_societa;
  ";

  $squadre = mysqli_query($con,$query);

  $campionati_query = "
  SELECT DISTINCT c.descrizione
  FROM stagioni c
  JOIN societa s ON c.id_stagione = s.id_campionato
  WHERE s.id != 99 AND (s.tipo IS NULL OR s.tipo != 'Prima squadra')
  ORDER BY c.descrizione;
  ";
  $campionati_result = mysqli_query($con, $campionati_query);
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
                          Società
                        </h4>
                        <div class="cta-wrapper">	
                          <a href="societa.php" type="button" class="btn btn-sm btn-outline-dark ms-2">
                            Prime squadre
                          </a>
                          <a href="insert_societa.php" type="button" class="btn btn-sm btn-outline-dark ms-2">
                            <i class="bi bi-plus"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <!-- Badge per i tipi di campionato -->
                      <div class="mb-3">
                        <span class="badge bg-secondary text-white me-2" onclick="filterTable('all')">Tutti</span>
                        <?php while ($campionato = mysqli_fetch_assoc($campionati_result)) { ?>
                          <span class="badge bg-secondary text-white me-2" data-campionato="<?php echo $campionato['descrizione']; ?>" onclick="filterTable('<?php echo $campionato['descrizione']; ?>')">
                            <?php echo $campionato['descrizione']; ?>
                          </span>
                        <?php } ?>
                      </div>

                      <div class="row mb-3">
                        <div class="col-12 table-responsive">
                          <table class="table table-sm table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                <th width="5%"></th>
                                <th width="25%">Nome</th>
                                <th width="15%">Campionato</th>
                                <th width="10%">Citta</th>
                                <th width="8%">Giorno</th>
                                <th width="8%">Orario</th>
                                <th width="47%">Sede</th>
                                <th width="2%"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($squadre)){ ?>
                              <tr class="align-middle" data-campionato="<?php echo $row['descrizione']; ?>">
                                <td>
                                  <?php if ($row['logo']) { ?>
                                    <img src="../image/loghi/<?php echo $row['logo'];?>" class="rounded-circle image-clickable"  width="25" height="25"/>
                                  <?php } else { ?>
                                    <img src="../image/default_societa.png" class="rounded-circle image-clickable"  width="25" height="25"/>
                                  <?php } ?>
                                </td>
                                <td>
                                  <a href="show_societa.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark text-nowrap">
                                    <?php echo $row['nome_societa'] ?>
                                  </a>
                                </td>
                                <td>
                                  <a href="show_societa.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark text-nowrap">
                                    <?php echo $row['descrizione'] .' - ' .$row['girone'] ?>
                                  </a>
                                </td>
                                <td class="text-nowrap"><i class='bi bi-pin-map'></i> &nbsp;  <?php echo $row['citta'] ?></td>
                                <td><?php echo substr($row['giorno_settimana'], 0, 3); ?></td>
                                <td><?php echo date('H:i', strtotime($row['ora_match'])); ?></td>
                                <td class="text-nowrap"><?php echo $row['sede'] ?></td>
                                <td class="text-center">
                                  <a class="text-decoration-none" href="edit_societa.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bi bi-pencil text-dark ms-2'></i>
                                  </a>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>

                          </table>
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

    <!-- Import -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

    <!-- Tooltip -->
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

    <script>
      function filterTable(campionato) {
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
          const rowCampionato = row.getAttribute('data-campionato');
          
          if (campionato === 'all' || rowCampionato === campionato) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }
    </script>

  </body>
</html> 