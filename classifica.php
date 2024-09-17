<!-- Calendario partite per utente semplice -->
<?php
  session_start();
  require_once('config/db.php');

  $query = "
  SELECT * FROM vista_classifica_A2_2024_2025
  ";

  $classifica = mysqli_query($con,$query);
  $posizione=1;
?>



<!doctype html>
<html lang="it">
    <!-- Head -->
    <?php include 'elements/head_base.php'; ?>


    <body>
        <!-- Navbar -->
        <div class="mb-5" id="navbar-orange">
            <?php include 'elements/navbar_red.php'; ?>
        </div>
        
        <!-- Carousel di sfondo  -->
        <?php include 'elements/carousel.php'; ?>

        <!-- Descrizione iniziale -->
        <div class="container my-5 px-4">

            <div class="row">
                <div class="col-12">
                    <h1 id="font_diverso">Classifica Serie A2</h1>
                </div>
            </div>

            <hr />

            <div class="row">

                <div class="col-12 table-responsive">
                    <table class="table table-striped table-hover table-rounded bebas" style="font-size:18px;">
                        <thead class="table-dark ">
                            <tr>
                            <th style="width:3%"></th>
                            <th style="width:3%"></th>
                            <th>Squadra</th>
                            <th class="text-center">G</th>
                            <th class="text-center">V</th>
                            <th class="text-center">P</th>
                            <th class="text-center">S</th>
                            <th class="text-center">GF</th>
                            <th class="text-center">GS</th>
                            <th class="text-center">+/-</th>
                            <th class="text-center">Punti</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php 
                            $posizione = 1;
                            while($row = mysqli_fetch_assoc($classifica)) {
                                // Classi CSS e tooltip in base al posizionamento in classifica
                                $rowClass = '';
                                $tooltip = '';
                                if ($posizione == 1) {
                                $rowClass = 'bg-success';
                                $tooltip = 'Promozione diretta';
                                } elseif ($posizione >= 2 && $posizione <= 5) {
                                $rowClass = 'bg-primary';
                                $tooltip = 'Playoff';
                                } elseif ($posizione >= 8 && $posizione <= 9) {
                                $rowClass = 'bg-orange';
                                $tooltip = 'Playout';
                                } elseif ($posizione > mysqli_num_rows($classifica) - 2) {
                                $rowClass = 'bg-danger';
                                $tooltip = 'Retrocessione';
                                }

                                // Codice per mostrare un pallino colorato con tooltip
                                $circle = '<span class="position-relative d-inline-block" data-bs-toggle="tooltip" data-bs-title="' . $tooltip . '">
                                            <span class="bg-opacity-50 ' . $rowClass . ' rounded-circle d-inline-block" style="width: 15px; height: 15px;"></span>
                                        </span>';
                            ?>

                            <tr>
                                <!-- Posizione in classifica -->
                                <td class="text-center">
                                <?php echo $posizione ?>°
                                </td>

                                <!-- Colonna per il pallino colorato -->
                                <td class="text-center">
                                    <?php echo $circle; ?>
                                </td>

                                <!-- Nome squadra -->
                                <td class=" text-nowrap " style="cursor:pointer;" onclick="window.location='show_societa.php?id=<?php echo $row['id']; ?>';">
                                <?php echo $row['societa'] ?>
                                </td>

                                <!-- Partite giocate -->
                                <td class="text-center">
                                <?php echo $row['played'] ?>
                                </td>

                                <!-- Partite vinte -->
                                <td class="text-center">
                                <?php echo $row['vinte'] ?>
                                </td>

                                <!-- Partite pareggiate -->
                                <td class="text-center">
                                <?php echo $row['pareggi'] ?>
                                </td>
                                <!-- Partite perse -->
                                <td class="text-center">
                                <?php echo $row['perse'] ?>
                                </td>
                                <!-- Gol Fatti -->
                                <td class="text-center">
                                <?php echo $row['golFatti'] ?>
                                </td>
                                <!-- Gol Subiti -->
                                <td class="text-center">
                                <?php echo $row['golSubiti'] ?>
                                </td>
                                <!-- Gol differenza -->
                                <td class="text-center">
                                <?php echo $row['goal_diff'] ?>
                                </td>
                                <!-- Punti totali -->
                                <td class="fw-bold text-center">
                                <?php echo $row['risultato'] ?>
                                </td>

                            </tr>

                            <?php $posizione += 1; } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="p-5">
            <?php include 'elements/footer.php'; ?>
        </footer>

        <!-- Import -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"
            integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous">
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"
            integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous">
        </script>

        <script>
        $(document).ready(function() {
            var url = location;
            console.log(url);
            $('.nav-link').each(function() {
            if ($(this).attr('href') === url) {
                $(this).addClass('active');
            }
            });
        });
        </script>

    </body>
</html>