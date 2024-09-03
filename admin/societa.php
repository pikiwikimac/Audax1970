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

  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT s.*,c.descrizione,c.girone,c.anno_inizio,c.anno_fine
  FROM societa s
  LEFT JOIN stagioni c on c.id_stagione = s.id_campionato
  WHERE s.id != 99
  AND s.id_campionato=1
  AND s.tipo='Prima squadra'
  ORDER BY s.id_campionato DESC,c.descrizione,c.girone,s.nome_societa ";

  $squadre_campionato = mysqli_query($con,$query);


  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT s.*,c.descrizione,c.girone,c.anno_inizio,c.anno_fine
  FROM societa s
  LEFT JOIN stagioni c on c.id_stagione = s.id_campionato
  WHERE (s.id_campionato != 1 OR s.id_campionato is null)
  AND s.tipo ='Prima squadra'
  ORDER BY s.nome_societa;";

  $squadre_altre = mysqli_query($con,$query);
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
                        <h4>
                        
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a href="settore_giovanile.php" type="button" class="btn btn-outline-dark ms-2 ">
                            Settori giovanili
                          </a>	
                          <a href="insert_societa.php" type="button" class="btn btn-outline-dark ms-2 ">
                            <i class='bx bx-plus '></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <h4>
                        Serie A2 
                      <h4>
                      <div class="row mb-3">
                        <div class="col-12 table-responsive">
                          <table class="table table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                <th width="5%"></th>
                                <th width="25%">Nome</th>
                                <th width="25%">Citta</th>
                                <th width="8%">Giorno</th>
                                <th width="8%">Orario</th>
                                <th width="47%">Sede</th>
                                <th width="2%"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($squadre_campionato)){?>
                              <tr class="align-middle">
                                <td><?php if ($row['logo']) { ?>
                                    <img src="../image/loghi/<?php echo $row['logo'];?>" class="rounded-circle image-clickable" width="30" height="30"/>
                                  <?php } else { ?>
                                    <img src="../image/default_user.jpg" class="rounded-circle image-clickable" width="30" height="30"/>
                                  <?php } ?>
                                </td>
                                <td>
                                  <a href="show_societa.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark text-nowrap">
                                    <?php echo $row['nome_societa'] ?>
                                  </a>
                                </td>
                                <td class="text-nowrap"><?php echo $row['citta'] ?></td>
                                <td><?php echo $row['giorno_settimana'] ?></td>
                                <td><?php echo $row['ora_match'] ?></td>
                                <td class="text-nowrap"><?php echo $row['sede'] ?></td>
                                <!-- Pulsante Edit -->
                                <td class="text-center">
                                  <!-- Duplica -->
                                  <a class="text-decoration-none" href="../query/action_duplicate_societa.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bx bx-copy text-dark ms-2' ></i>
                                  </a> 
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="edit_societa.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bx bx-pencil text-dark ms-2'></i>
                                  </a>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <?php if($squadre_altre->num_rows > 0){?>
                      <h4>
                        Altri campionati 
                      <h4>
                      <div class="row mb-3">
                        <div class="col-12">
                          <table class="table table-striped table-hover table-rounded">
                            <thead class="table-dark">
                              <tr>
                                <th width="5%"></th>
                                <th width="25%">Nome</th>
                                <th width="25%">Citta</th>
                                <th width="8%">Giorno</th>
                                <th width="8%">Orario</th>
                                <th width="47%">Sede</th>
                                <th width="2%"></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($squadre_altre)){?>
                              <tr class="align-middle">
                                <td><?php if ($row['logo']) { ?>
                                    <img src="../image/loghi/<?php echo $row['logo'];?>" class="rounded-circle image-clickable"  width="30" height="30"/>
                                  <?php } else { ?>
                                    <img src="../image/default_societa.png" class="rounded-circle image-clickable"  width="30" height="30"/>
                                  <?php } ?>
                                </td>
                                <td>
                                  <a href="show_societa.php?id=<?php echo $row['id'] ?>" class="text-decoration-none text-dark text-nowrap">
                                    <?php echo $row['nome_societa'] ?>
                                  </a>
                                </td>
                                <td class="text-nowrap"><?php echo $row['citta'] ?></td>
                                <td><?php echo $row['giorno_settimana'] ?></td>
                                <td><?php echo $row['ora_match'] ?></td>
                                <td class="text-nowrap"><?php echo $row['sede'] ?></td>

                                <!-- Pulsante Edit -->
                                <td class="text-center">
                                  <!-- Duplica -->
                                  <a class="text-decoration-none" href="../query/action_duplicate_societa.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bx bx-copy text-dark ms-2' ></i>
                                  </a> 
                                  <!-- Edit -->
                                  <a class="text-decoration-none" href="edit_societa.php?id=<?php echo $row["id"]; ?>" >
                                    <i class='bx bx-pencil text-dark ms-2'></i>
                                  </a>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <?php } ?>
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
  </body>
</html> 