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
  
  $query = "select a.*,s1.nome_societa as nome_casa,s2.nome_societa as nome_ospiti,s.descrizione,s.anno_inizio,s.anno_fine,s.girone
  FROM articoli a
  inner join stagioni s on s.id_stagione=a.id_stagione
  left join partite p on p.id=a.id_partita
  left join societa s1 on s1.id=p.squadraCasa
  left join societa s2 on s2.id=p.squadraOspite
  ORDER BY data_pubblicazione desc";
  $articoli = mysqli_query($con,$query);
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
                        <h1>
                          Articoli 
                        </h1>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="new_articolo.php" class="btn btn-outline-dark float-end">
                            <i class='bx bx-plus'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                    <?php
                    if (isset($_GET['message'])) {
                      echo '<div class="alert alert-info">' . htmlspecialchars($_GET['message']) . '</div>';
                    }
                    ?>

                      <div class="row  ">
                        <div class="col-12 table-responsive">
                          <table class="table table-hover table-striped table-rounded sortable" id="tabella-giocatori">
                            <thead class="table-dark">

                              <tr>
                                <th>Titolo</th>
                                <th>Campionato</th>
                                <th>Tags</th>
                                <th class="text-end" width="5%"></th>
                              </tr>

                            </thead>

                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($articoli)) {  ?>
                              <tr class="align-middle" onclick="window.location='edit_articolo.php?id=<?php echo $row['id']; ?>';">
                                
                                <td style="min-width:500px">
                                  <span class="fw-bold">
                                    <?php echo $row['titolo']?>
                                  </span>
                                  <div>
                                    <small>
                                      <?php 
                                      $data_pubblicazione = $row['data_pubblicazione'];
                                      $formatted_date = date("d-m-Y H:i", strtotime($data_pubblicazione));
                                      echo $formatted_date;
                                      ?> - <?php echo $row['autore']?>
                                    </small>
                                  </div>
                                </td>
                                
                                <td>
                                  <?php echo $row['descrizione'] .' '.$row['girone']?> 
                                </td>
                                
                                <td>
                                  <small>
                                    <?php echo $row['tags']?>
                                  </small>
                                </td>
                                <td class="text-end">
                                  <a href="edit_articolo.php?id=<?php echo $row['id'] ?>" class="text-decoration-none">
                                    <i class='bx bx-pencil'></i>
                                  </a>
                                  <a href="../query/delete_articolo.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                    <i class='bx bx-trash text-danger'></i>
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

  </body>
</html>