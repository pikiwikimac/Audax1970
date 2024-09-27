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

  
  $query = "select *
  from stagioni
  order by anno_inizio desc ,anno_fine desc,priorita desc, nome_stagione desc,descrizione,girone";
  $competizioni = mysqli_query($con,$query);
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
                          Competizioni 
                        </h4>

                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">	
                          <a type="button" href="insert_competizione.php" class="btn btn-sm btn-outline-dark float-end">
                            <i class='bi bi-plus'></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                   
                      <div class="row  ">
                        <div class="col-12 table-responsive">
                          <table class="table table-hover table-striped table-rounded sortable" id="tabella-giocatori">
                            <thead class="table-dark">

                              <tr>
                                <th>Nome stagione</th>
                                <th>Periodo</th>
                                <th>Descrizione</th>
                                <th>Girone</th>
                                <th>Settore giovanile</th>
                                <th class="text-end" width="5%"></th>
                              </tr>

                            </thead>

                            <tbody>
                              <?php while($row = mysqli_fetch_assoc($competizioni)) {  ?>
                              <tr class="align-middle" onclick="window.location='edit_competizione.php?id_stagione=<?php echo $row['id_stagione']; ?>';">
                                
                                <td>
                                  <span class="fw-bold">
                                    <?php echo $row['nome_stagione']?>
                                  </span>
                                </td>
                                
                                <td>
                                  <small>
                                    <?php echo $row['anno_inizio'] .'-' .$row['anno_fine']?>
                                  </small>
                                </td>
                                
                                <td>
                                  <?php echo $row['descrizione'] ?> 
                                </td>
                                
                                <td>
                                  <?php echo $row['girone'] ?> 
                                </td>
                                
                                <td>
                                  <?php if($row['prima_squadra'] ==1){
                                    echo 'Prima squadra';
                                  }else{
                                    echo 'Settore giovanile';
                                  } ?> 
                                </td>
                                
                                <td class="text-end">
                                  <a href="edit_competizione.php?id_stagione=<?php echo $row['id_stagione'] ?>" class="text-decoration-none">
                                    <i class="bi bi-pencil"></i>
                                  </a>
                                  <a href="../query/action_delete_competizione.php?id_stagione=<?php echo $row['id_stagione']; ?>" class="text-decoration-none">
                                    <i class='bi bi-trash text-danger'></i>
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