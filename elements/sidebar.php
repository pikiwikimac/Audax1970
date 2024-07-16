<!-- Sidebar da utilizzare nelle pagine "admin" -->
<?php 
	session_start();
	$id_societa=$_SESSION['id_societa_riferimento'];

	$query="
	SELECT id_campionato
	FROM societa
	WHERE id='$id_societa'
	";
	$xyz = mysqli_query($con,$query);
	$id_stagione= mysqli_fetch_assoc($xyz);
	$stagione=$id_stagione['id_campionato'];

?>
<div class="tpl--sidenav d-print-none">
	<!-- SIDENAV -- Start -->
	<nav class="tpl--sidenav--nav">
	
		<div class="logo-button-wrap">
			<div class="toggle-menu js--menu-mobile--open">
				<button class="menu-icon main-nav-toggle">
					<i class="bi bi-list align-middle" style="margin-top:-6px"></i>
				</button>
			</div>
			
			<div class="logo-wrapper">
				<a href="../index.php">
					<!-- --><img src="../image/loghi/logo_audax.png" alt="" style="width:50px;height:50px"> 
				</a>
			</div>
		</div>
	
		<div id="js--nav-menu" class="nav-main-menu">
			<div class="mobile-navigation">
				<button class="js--menu-mobile--close menu-close-button main-nav-toggle">
					<i class="bi bi-x-lg align-middle"></i>
				</button>
				<!-- Logo -->
				<img src="../image/loghi/audax.jpeg" alt="Audax 1970" style="width:30px;height:30px">
			</div>
			
			<ul class="menu">
				
				<!-- Dashboard -->
				<li>
					<button class="" onclick="window.location.href = 'dashboard.php'">
						<i class="bx bx-home" title="Dashboard"></i>
						<h2>Dashboard</h2>
					</button>
				</li>

				<!-- Rosa -->
				<li>
					<button class="js--menu--first-level">
						<i class="bi bi-people" title="Rosa"></i>
						<h2>Rosa</h2>
					</button>
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						
						<h2>
							ROSA
						</h2>
						
						<ul class="accordion" id="accordionRosa">
							
							<li class="sub-menu-voice">
								<a href="rosa_admin.php?id_societa=<?php echo $id_societa ?>">Squadra</a>
							</li>
							
							<li class="sub-menu-voice">
								<a href="rose_campionati_dev.php">Elenco giocatori</a>
							</li>

							<li class="sub-menu-voice">
								<a href="indisponibili_admin.php">Indisponibili</a>
							</li>

							<hr/>

							<li class="sub-menu-voice">
								<a href="compleanni.php">Compleanni</a>
							</li>

							<li class="sub-menu-voice">
								<a href="visite_mediche.php">Visite mediche</a>
							</li>
							<?php if($_SESSION['superuser'] === 1 ){?>
							<li class="sub-menu-voice">
								<a href="materiali.php?id_societa=<?php echo $id_societa ?>">Gestione materiali</a>
							</li>
							<?php } ?>
							
						</ul>
					</div>
				</li>
				
				<!-- Campionato -->
				<li>
					<button class="js--menu--first-level ">
						<i class="bi bi-calendar" title="Campionato"></i>
						<h2>Calendario</h2>
					</button>
					
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						<h2>
							CALENDARIO
						</h2>
						<ul class="accordion" id="accordionCalendario">
							
							<li class="sub-menu-voice">
								<a href="calendario_admin.php?id_stagione=<?php echo $stagione ?>&id_societa=<?php echo $id_societa ?>">Partite</a>
							</li>
							
							<li class="sub-menu-voice">
								<a href="calendario_completo_admin.php?id_stagione=<?php echo $stagione ?>">Girone</a>
							</li>
							
						</ul>
					</div>
				</li>

				<!-- Classifica -->
				<li>
					<button class="js--menu--first-level ">
						<i class="bi bi-trophy" title="Classifica"></i>
						<h2>Classifica</h2>
					</button>
					
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						<h2>
							CLASSIFICA
						</h2>
						<ul class="accordion" id="accordionClassifica">
							
							
							<li class="sub-menu-voice">
								<a href="classifica_admin.php?id_societa=<?php echo $id_societa ?>">Classifica</a>
							</li>
							
							<li class="sub-menu-voice">
								<a href="classifica_marcatori.php?id_societa=<?php echo $id_societa ?>">Marcatori</a>
							</li>
						</ul>
					</div>
				</li>

				<!-- Allenamenti -->
				<li>
					<button class="js--menu--first-level ">
						<i class="bi bi-stopwatch" title="Allenamenti"></i>
						<h2>Allenamenti</h2>
					</button>
					
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						<h2>
							ALLENAMENTI
						</h2>
						<ul class="accordion" id="accordionAllenamenti">
							
							
							<li class="sub-menu-voice">
								<a href="allenamenti_admin.php?id_societa=<?php echo $id_societa ?>">Gestione allenamenti</a>
							</li>
							
						</ul>
					</div>
				</li>

				<!-- Squadre -->
				<?php if($_SESSION['superuser'] === 1 ){?>
				<li>
					<button class="js--menu--first-level">
						<i class="bi bi-clipboard-data" title="Squadre"></i>
						<h2>Squadre</h2>
					</button>
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						
						<h2>
							SQUADRE
						</h2>
						
						<ul class="accordion" id="accordionSquadre">
							<!-- Gestione squadre -->
							<li class="sub-menu-voice">
								<a href="societa.php">Gestione squadre</a>
							</li>
							<!-- Gestione stagioni -->
							<li class="sub-menu-voice">
								<a href="competizioni.php">Gestione stagioni</a>
							</li>
							
						</ul>
					</div>
				</li>
				<?php }?>

				<!-- Amministrazione -->
				<?php if($_SESSION['superuser'] === 1 ){?>
				<li>
					<button class="js--menu--first-level">
						<i class="bi bi-coin" title="Amministrazione"></i>
						<h2>Amministrazione</h2>
					</button>
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						
						<h2>
						AMMINISTRAZIONE
						</h2>
						
						<ul class="accordion" id="accordionAmministrazione">
							<!-- Gestione economica  -->
							<li class="sub-menu-voice">
								<a  href="amministrazione.php">Gestione economica</a>
							</li>
							
							<!-- Dirigenti  -->
							<?php if($_SESSION['superuser'] === 1 ){?>
							<li class="sub-menu-voice">
								<a href="dirigenti.php">Dirigenti</a>
							</li>
							<?php } ?>
							
							<!-- Multe  -->
							<li class="sub-menu-voice">
								<a href="multe.php">Multe</a>
							</li>
							
							<!-- Mercato  -->
							<li class="sub-menu-voice">
								<a href="mercato.php">Mercato</a>
							</li>
							
						</ul>
					</div>
				</li>
				<?php }?>
				
				<!-- Articoli -->
				<?php if($_SESSION['superuser'] === 1 ){?>
				<li>
					<button class="js--menu--first-level">
						<i class="bi bi-newspaper" title="Articoli"></i>
						<h2>Articoli</h2>
					</button>
					<div class="sub-menu">
						<button class="js--submenu--close menu-close-button">
							<i class="bi bi-chevron-double-left"></i>
						</button>
						
						<h2>
						ARTICOLI
						</h2>
						
						<ul class="accordion" id="accordionArticoli">
							<!-- Gestione  -->
							<li class="sub-menu-voice">
								<a href="articoli.php">Gestione articoli</a>
							</li>
							<!-- Crea articolo  -->
							<li class="sub-menu-voice">
								<a href="new_articolo.php">Crea articolo</a>
							</li>
							
						</ul>
					</div>
				</li>
				<?php }?>
				
				
				
			</ul>

		</div>
		
		<ul class="user-menu menu">
			<li>
				<a href="../login/logout.php">
					<i class="bi bi-box-arrow-in-left"></i>
				</a>
				<div class="sub-menu">
				<button class="js--submenu--close menu-close-button">
					<i class="icon-less-menu"></i>
				</button>
				</div>
			</li>
			
			<li>
				<a href="../admin/impostazioni.php">
					<i class="bi bi-gear"></i>
				</a>
				<div class="sub-menu">
				<button class="js--submenu--close menu-close-button">
					<i class="icon-less-menu"></i>
				</button>
				</div>
			</li>
			
			<li class="user-button">
				
				<a href="#" >
					<?php
						$image = isset($image) && !empty($image) ? $image : 'image/default_user.jpg';
					?>
					<span><img src="../<?php echo $image; ?>" alt="" width="30" height="30" class="rounded-circle ms-2 me-1"></span>

				</a>

			</li>
		</ul>
	</nav>
	<!-- SIDENAV -- END -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
        
<script src="../js/app.js"></script>








