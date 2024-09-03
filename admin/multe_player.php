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

  $id=$_REQUEST['id'];

  #Query che seleziona tutti i giocatori di una determinata squadra
  $query = "
  SELECT g.cognome,g.nome
  FROM giocatori g
  WHERE g.id='$id'
  ";
  $result = mysqli_query($con,$query);
  $giocatore = mysqli_fetch_assoc($result); 
  
  $query = "
  SELECT m.*
  FROM multe m
  WHERE m.id_giocatore='$id'
  ";
  $multe = mysqli_query($con,$query);
  
  $query = "
  SELECT sum(m.importo)
  FROM multe m
  WHERE m.id_giocatore='$id'
  ";
  $tot_multe = mysqli_query($con,$query);
  
  
  $query = "
  SELECT p.*
  FROM pagamenti p
  WHERE p.id_giocatore='$id'
  ";
  $pagamenti = mysqli_query($con,$query);
  
  $query = "
  SELECT sum(p.importo)
  FROM pagamenti p
  WHERE p.id_giocatore='$id'
  ";
  $tot_pagamenti = mysqli_query($con,$query);


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
                          <?php echo $giocatore['nome'].' ' .$giocatore['cognome'] ?>
                        <h4>
                        <!-- Bottoni a destra -->
                        <div class="cta-wrapper">
                          <a type="button" href="multe.php" class="btn btn-outline-dark float-end" >
                            <i class='bx bx-arrow-back '></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <!-- END:Intestazione -->

                    <!-- Core della pagina -->
                    <div class="">
                      <div class="row mb-3">
                        <div class="col-12 table-responsive">
                          <?php if (($multe->num_rows >0) || ($pagamenti->num_rows >0) ){ ?>
                          <table class="table table-sm table-hover table-striped table-rounded" >
                            
                            <thead class="table-dark">
                              <tr>
                                <th>Tipologia transazione</th>
                                <th class="">Data</th>
                                <th class="text-end">Importo</th>
                                <th class=""></th>
                              </tr>
                            </thead>

                            <tbody>

                              <?php while($row = mysqli_fetch_assoc($multe)) {  ?>
                              <tr >
                                <!-- Nome e Cognome -->
                                <td>
                                  Multa
                                </td>

                                <!-- Pulsante totale  -->
                                <td class="">
                                  <?php echo date('d/m/y',strtotime($row['data_multa'])) ?>
                                </td>

                                <!-- Pulsante versato  -->
                                <td class="text-end text-danger">
                                  <?php echo $row['importo'] ?>
                                </td>
                                
                                <td  class="text-end">
                                  <!-- Delete -->
                                  <a class="text-decoration-none text-dark" onclick="confirmDeleteMulta('<?php echo $row["id"]; ?>')">
                                    <i class='bx bx-trash'></i>
                                  </a>
                                </td>
                                
                              </tr>
                              <?php } ?>

                              <?php while($row = mysqli_fetch_assoc($pagamenti)) {  ?>
                              <tr >
                                <!-- Nome e Cognome -->
                                <td>
                                  Pagamento multa
                                </td>

                                <!-- Pulsante totale  -->
                                <td >
                                  <?php echo date('d/m/y',strtotime($row['data_pagamento'])) ?>
                                </td>

                                <!-- Pulsante versato  -->
                                <td class="text-end ">
                                  <?php echo $row['importo'] ?>
                                </td>
                                
                                <td class="text-end">
                                  <!-- Delete -->
                                  <a class="text-decoration-none text-dark" onclick="confirmDeletePagamento('<?php echo $row["id"]; ?>')">
                                    <i class='bx bx-trash'></i>
                                  </a>
                                </td>
                                
                              </tr>
                              <?php } ?>

                            </tbody>

                          </table>
                          <?php }else{ ?>
                            <span>Nessuna multa</span>
                            <span>torna alla pagina precedente per aggiungere una.</span>
                          <?php }?>
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
      function confirmDeleteMulta(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_multa.php?id=" + recordId;
        }
      }
      
      function confirmDeletePagamento(recordId) {
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo record?");
        if (confirmDelete) {
          // Effettua la richiesta di eliminazione al server
          window.location.href = "../query/action_delete_pagamento.php?id=" + recordId;
        }
      }
    </script>
   
  </body>
</html> 