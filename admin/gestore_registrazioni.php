<?php

  session_start();
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }

  // Verifica se l'utente è un superuser (superuser = 1)
  //if ($_SESSION['superuser'] !== 1) {
    // L'utente non è autorizzato ad accedere a questa pagina
    //header('Location: error_page/access_denied.php');
    //exit;
  //}
  
  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];
  
  if ($superuser === 0) {
    header('Location: ../error_page/access_denied.php');
    exit;
  }

  $query = "select * FROM users where accettato=0";
  $users_queue = mysqli_query($con,$query);

  $quey2="SELECT * FROM users WHERE accettato=1";
  $users_accettati= mysqli_query($con,$quey2)
  
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
                          Lista utenti in attesa di approvazione
                        </h4>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">

                      <?php if ($users_queue->num_rows >0 ){ ?>
                      <div class="row mb-3 ">
                        <div class="col-12 table-responsive  ">
                            <table class="table table-hover table-striped table-rounded sortable" >
                              <thead class="table-dark">

                                <tr>
                                  <th></th>
                                  <th>Nome</th>
                                  <th>Cognome</th>
                                  <th>Email</th>
                                  <th>Ruolo</th>
                                  <th>Azioni</th>

                                </tr>
                              </thead>
                              <tbody>
                                <?php while($row = mysqli_fetch_assoc($users_queue)) {  ?>
                                  <tr>
                                    <td></td>
                                    <td><?php echo $row['firstname'] ?></td>
                                    <td><?php echo $row['lastname'] ?></td>
                                    <td><?php echo $row['email'] ?></td>
                                    <td><?php if($row['superuser']===1){ echo 'Admin';}else{ echo 'Giocatore';}; ?></td>
                                    <td>
                                      <a href="../query/accettazione_user.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                        <i class='bi bi-check text-success'></i>
                                      </a>

                                      <a href="../query/rifiuto_user.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                        <i class='bi bi-x text-danger'></i>
                                      </a>
                                    </td>

                                  </tr>
                                <?php } ?>

                              </tbody>

                            </table>

                        </div>

                      </div>

                      <?php }else{ ?>
                        <h6 class="mb-5">Nessuna richiesta di registrazione</h6>
                      <?php } ?>

                      
                      <div class="row mt-5 mb-3 ">
                        <div class="col-12 table-responsive  ">
                            <table class="table table-hover table-striped table-rounded sortable" >
                              <thead class="table-dark">

                                <tr>
                                  <th></th>
                                  <th>Nome</th>
                                  <th>Cognome</th>
                                  <th>Email</th>
                                  <th class="text-end">Admin</th>

                                </tr>
                              </thead>
                              <tbody>
                                <?php while($user = mysqli_fetch_assoc($users_accettati)) {  ?>
                                  <tr >
                                    <td><img src="../image/username/<?php echo $user['image']; ?>" class="rounded-circle " alt="..." width="30" height="30"/></td>
                                    <td  onclick="window.location='edit_user.php?id=<?php echo $user['id']; ?>';" style="cursor:pointer"><?php echo $user['firstname'] ?></td>
                                    <td  onclick="window.location='edit_user.php?id=<?php echo $user['id']; ?>';" style="cursor:pointer"><?php echo $user['lastname'] ?></td>
                                    <td><?php echo $user['email'] ?></td>
                                    <td class="text-end">
                                      <?php if($user['superuser']== 1){ ?>
                                        <i class="bi bi-check text-success"></i>
                                      <?php } ?>
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