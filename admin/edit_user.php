<?php
  session_start();
  // Controlla se l'utente è autenticato
  // Controlla se l'utente è loggato, altrimenti reindirizza alla pagina di login
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  require_once('../config/db.php');

  $superuser = $_SESSION['superuser'];
  $user_id = $_REQUEST['id'];
  $image = isset($_SESSION['image']) ? $_SESSION['image'] : null; 
  
  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT u.*,s.tipo,s.id as id_societa FROM users u INNER JOIN societa s ON s.id=u.id_societa_riferimento WHERE u.id = '$user_id';
  ";
  
  $result = mysqli_query($con,$query);
  $user = mysqli_fetch_assoc($result);

  $id_societa=$user['id_societa'];

  // Eseguire una query iniziale per ottenere il parent_id della società con id_societa
  $checkParentQuery = "SELECT parent_id FROM societa WHERE id = '$id_societa'";
  $checkParentResult = mysqli_query($con, $checkParentQuery);
  $row = mysqli_fetch_assoc($checkParentResult);

  // Controllare il valore di parent_id
  if ($row['parent_id'] !== null) {
      // Se parent_id non è null, selezionare tutte le squadre con lo stesso parent_id inclusa la squadra con id = parent_id
      $parent_id = $row['parent_id'];
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.parent_id = '$parent_id'
      OR s.id = '$parent_id'
      ";
  } else {
      // Se parent_id è null, selezionare la società con id_societa e tutte le sue società figlie
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.id = '$id_societa'
      OR s.parent_id = '$id_societa'
      ";
  }

  // Eseguire la query e ottenere i risultati
  $societa_collegate = mysqli_query($con, $query4);
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
                        <h1 >
                          Modifica user
                        </h1>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">

                      <form action="../query/action_edit_user.php?id=<?php echo $user['id'] ?>" method="POST">
                        <div class="card p-3">
                          <div class="card-body">
                            <div class="row gy-3 mb-3  ">
                              <div class="col-12 col-lg-3  ">
                                <label for="firstname" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname'];?>" required/>
                              </div>
                              
                              <div class="col-12 col-lg-3  ">
                                <label for="lastname" class="form-label">Cognome</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname'];?>" />
                              </div>

                              <div class="col-12 col-lg-3  ">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $user['email'];?>" />
                              </div> 
                          
                              <div class="col-12 col-lg-3  ">
                                <label for="../image" class="form-label">Immagine path</label>
                                <input type="text" class="form-control" id="../image" name="../image" value="<?php echo $user['image'];?>" />
                              </div>
                              
                              <?php if ($superuser==1) { ?>
                                <div class="col-12 col-lg-3  ">
                                  <label for="superuser" class="form-label">Autorizzazioni</label>
                                  <select class="form-select" id="superuser" name="superuser">
                                    <option value="0" <?php if ($user['superuser']==0) { ?>selected="selected"<?php } ?>>User</option>
                                    <option value="1" <?php if ($user['superuser']==1) { ?>selected="selected"<?php } ?>>Admin</option>
                                  </select>
                                </div>
                              <?php } ?>

                              <div class="col-12 col-lg-3  ">
                                <label for="instagram" class="form-label">Instagram</label>
                                <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo $user['instagram'];?>" />
                              </div>
                              
                              <div class="col-12 col-lg-3  ">
                                <label for="whatsapp" class="form-label">Whatsapp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo $user['whatsapp'];?>" />
                              </div>
                              
                              <div class="col-12 col-lg-3  ">
                                <label for="id_societa_riferimento" class="form-label">Societa di riferimento</label>
                                <select class="form-select" id="id_societa_riferimento" name="id_societa_riferimento">
                                  <?php while($row = mysqli_fetch_assoc($societa_collegate)) {  ?>
                                    <option value="<?php echo $row['id'];?>" <?php if ($user['id_societa_riferimento']==$row['id']) { ?>selected="selected"<?php } ?>><?php echo $row['tipo'];?></option>
                                  <?php } ?>
                                </select>
                              </div>
                              

                            </div>
                          </div>
                        </div>
                        <!-- Submit -->
                        <div class="mt-3">
                          <button type="submit" class="btn btn-outline-dark float-end">Conferma</button>
                          <button type="button" class="btn btn-outline-dark float-end me-2" onclick="window.location.href = 'user.php?id=' + <?php echo $user_id ?>">
                            Indietro</button>
                        </div>
                      </form>

                    </div>
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