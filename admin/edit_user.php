<?php
  session_start();
  if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
  }
  
  require_once('../config/db.php');
  
  $superuser = $_SESSION['superuser'];
  $user_id = $_REQUEST['id'];

  // Recupera i dati dell'utente
  $query = "SELECT u.*, s.tipo, s.id as id_societa FROM users u 
            INNER JOIN societa s ON s.id=u.id_societa_riferimento 
            WHERE u.id = '$user_id'";
  
  $result = mysqli_query($con, $query);
  $user = mysqli_fetch_assoc($result);

  // Recupera le società collegate
  $id_societa = $user['id_societa'];
  $checkParentQuery = "SELECT parent_id FROM societa WHERE id = '$id_societa'";
  $checkParentResult = mysqli_query($con, $checkParentQuery);
  $row = mysqli_fetch_assoc($checkParentResult);

  if ($row['parent_id'] !== null) {
      $parent_id = $row['parent_id'];
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.parent_id = '$parent_id' OR s.id = '$parent_id'";
  } else {
      $query4 = "
      SELECT s.nome_societa, s.id, s.tipo
      FROM societa s
      WHERE s.id = '$id_societa' OR s.parent_id = '$id_societa'";
  }
  $societa_collegate = mysqli_query($con, $query4);
?>

<!doctype html>
<html lang="it">
  <?php include '../elements/head.php'; ?>
  <body>
    <main role="main" class="tpl">
      <?php include '../elements/sidebar.php'; ?>
      <div class="tpl--content">
        <div class="tpl--content--inner">
          <div class="tpl-inner">
            <div class="tpl-inner-content">
              <div class="row pe-3">
                <div class="col-12">
                  <div class="container-fluid">
                    <div class="tpl-header">
                      <h4>Modifica user</h4>
                    </div>

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row gy-4">
                        
                        <!-- Immagine -->
                        <div class="col-12 col-lg-2">
                          <div class="card mb-3">
                            <img src="../image/username/<?php echo $user['image']; ?>"
                            class="img-fluid  rounded " alt="Immagine attuale">
                          </div>
                          
                          <!-- Form separato per il caricamento dell'immagine -->
                          <form action="../query/upload_image_user.php?id=<?php echo $user['id']; ?>" method="POST" enctype="multipart/form-data" >
                            <div class="mb-3">
                              
                              <label for="userImage" class="form-label">Carica Immagine Profilo</label>
                              <input type="file" class="form-control form-control-sm" id="userImage" name="userImage" required />
                              
                              <button type="submit" class="btn btn-sm btn-outline-dark mt-3">Carica Immagine</button>
                              
                            </div>
                          </form>
                        </div>

                        <div class="col-12 col-lg-10">
                          <!-- Form per la modifica dei dati utente -->
                          <form action="../query/action_edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
                            <div class="card p-3">
                              <div class="card-body">
                                <div class="row gy-3 mb-3">
                                  <!-- Campi di modifica -->
                                  <div class="col-12 col-lg-3">
                                    <label for="firstname" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>" required />
                                  </div>
                                  <div class="col-12 col-lg-3">
                                    <label for="lastname" class="form-label">Cognome</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>" />
                                  </div>
                                  <div class="col-12 col-lg-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" />
                                  </div>
                                  <div class="col-12 col-lg-3">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo $user['instagram']; ?>" />
                                  </div>
                                  <div class="col-12 col-lg-3">
                                    <label for="whatsapp" class="form-label">Whatsapp</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo $user['whatsapp']; ?>" />
                                  </div>

                                  <?php if ($superuser == 1) { ?>
                                    <div class="col-12 col-lg-3">
                                      <label for="superuser" class="form-label">Autorizzazioni</label>
                                      <select class="form-select" id="superuser" name="superuser">
                                        <option value="0" <?php if ($user['superuser'] == 0) echo 'selected'; ?>>User</option>
                                        <option value="1" <?php if ($user['superuser'] == 1) echo 'selected'; ?>>Admin</option>
                                      </select>
                                    </div>
                                  <?php } ?>

                                  <div class="col-12 col-lg-3">
                                    <label for="id_societa_riferimento" class="form-label">Società di riferimento</label>
                                    <select class="form-select" id="id_societa_riferimento" name="id_societa_riferimento">
                                      <?php while ($row = mysqli_fetch_assoc($societa_collegate)) { ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if ($user['id_societa_riferimento'] == $row['id']) echo 'selected'; ?>><?php echo $row['tipo']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-dark float-end mt-3">Conferma</button>
                          </form>
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
  </body>
</html>
