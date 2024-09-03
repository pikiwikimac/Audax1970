<?php
  session_start();
  // Controlla se l'utente è autenticato
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  require_once('../config/db.php');

  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  $superuser=  $_SESSION['superuser'];
  $id=  $_REQUEST['id'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "SELECT u.*,s.tipo FROM users u INNER JOIN societa s ON s.id=u.id_societa_riferimento WHERE u.id = $id";
  
  $result = mysqli_query($con,$query);
  $user = mysqli_fetch_assoc($result);
  
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
                          Scheda utente
                        <h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                         
                          
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">

                      <div class="row  gy-3">
                        <!-- Immagine utente -->
                        <div class="col-12 col-lg-2">
                          <img src="../image/username/<?php echo $user['image']; ?>" class="rounded img-fluid " alt="..." width="500" height="500"/>
                        </div>
                        <!-- Info utente -->
                        <div class="col-12 col-lg-8">
                          <div class="row  gy-3">
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bxs-contact' ></i> &nbsp;Nome:
                                <?php echo $user['firstname'] .' ' .$user['lastname']?> 
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bx-envelope' ></i> &nbsp;Email:
                                <?php echo $user['email'] ?>
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bxs-user-circle' ></i> &nbsp; Username:
                                <?php echo $user['username'] ?>
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bx-lock-alt'></i> &nbsp; Autorizzazioni:
                                <?php if($user['superuser']==1){echo 'Admin';}else{echo 'Giocatore';}?>
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bxl-instagram'></i> &nbsp; Instagram:
                                <?php echo !empty($user['instagram']) ? $user['instagram'] : '-'; ?>
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bxl-whatsapp'></i> &nbsp; Whatsapp:
                                <?php echo !empty($user['whatsapp']) ? $user['whatsapp'] : '-'; ?>
                              </span>
                            </div>
                            <div class="col-12">
                              <span class="text-muted mt-0">
                                <i class='bx bx-desktop'></i> &nbsp; Squadra riferimento:
                                <?php echo !empty($user['tipo']) ? $user['tipo'] : '-'; ?>
                              </span>
                            </div>    
                          </div>
                        </div>
                        <!-- Bottoni -->
                        <div class="col-12 col-lg-2">
                          <div class="row gy-3 ">
                            <!-- Bottone gestione registrazioni -->
                            <div class="col-12">
                              <a class="btn btn-outline-dark" href="gestore_registrazioni.php" style="width:250px">
                                <i class='bx bx-user-plus'></i> &nbsp; Gestione registrazioni
                              </a>
                            </div>

                            <!-- Bottone -->
                            <div class="col-12">
                              <a class="btn btn-outline-dark" href="show_societa.php?id=<?php echo $id_societa ?>" style="width:250px">
                                <i class='bx bxs-building-house'></i> &nbsp; Modifica info squadra
                              </a>
                            </div>

                            <!-- Bottone -->
                            <div class="col-12">
                              <a href="edit_user.php?id=<?php echo $user['id'] ?>" type="button" class="btn btn-outline-dark " style="width:250px">
                                <i class='bx bx-pencil '></i> &nbsp; Modifica utente
                              </a>
                            </div>

                            <!-- Bottone  -->
                            <div class="col-12">
                              <a href="../login/change_password.php?id=<?php echo $user['id'] ?>" type="button" class="btn btn-outline-dark " style="width:250px">
                                <i class='bx bxs-lock' ></i> &nbsp; Modifica password
                              </a>
                            </div>

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