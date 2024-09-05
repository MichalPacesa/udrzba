
<nav class="navbar navbar-expand-lg navbar-light d-lg-block" style="background:white;margin-bottom:1em">
	<!-- aby boli logo a menu vedla seba-->

	<div class="container d-flex align-items-center justify-content-center">
		<!--  logo -->
        <div class="logo" style="line-height:150%;flex-wrap: nowrap;">
				<a class="logo_a" style="white-space: nowrap;" href="index.php">Údržba&nbsp<i class="fas fa-wrench"></i></a><br>
                <a class="logo_b" href="index.php">Informačný systém</a>
		</div>

        <!-- ikona menu pre mobily -->
        <button id="ikona-menu" class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarExample01"
              aria-controls="navbarExample01" aria-expanded="false" aria-label="Toggle navigation" onclick="menuPreMobily();">
              <i class="fas fa-bars"></i>
        </button>

        <!-- hlavne menu  -->
        <div class="navbar-collapse collapse" id="navbarExample01">
              <ul class="navbar-nav  me-auto mb-2 mb-lg-0" style = "margin-left:4rem; font-size:1.25rem;white-space: nowrap;position-relative;z-index:1000">
                <li class="nav-item">
                  <a class="nav-link" href="index.php">Poruchy</a>
                </li>

                <?php if(ZistiPrava("zamestnanci",$dblink) == 1):  ?>
                <li class="nav-item">
                  <a class="nav-link" href="index_zamestnanci.php">Zamestnanci</a>
                </li>
                <?php endif;  ?>

                <?php if(ZistiPrava("stroje",$dblink) == 1):  ?>
                <li class="nav-item">
                  <a class="nav-link" href="index_stroje.php">Stroje</a>
                </li>
                <?php endif;  ?>
                <?php if(ZistiPrava("zobrazNahradneDiely",$dblink) == 1):  ?>
                      <li class="nav-item">
                          <a class="nav-link" href="index_nahradne_diely.php">Náhradné diely</a>
                      </li>
                 <?php endif;  ?>
                 <?php if(ZistiPrava("zobrazCinnostiOpravy",$dblink) == 1):  ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index_cinnosti_opravy.php">Činnosti opráv</a>
                        </li>
                        <?php endif;  ?>
              </ul>


              <ul id="vedlajsie-menu" class="navbar-nav d-flex flex-row flex-lg-row align-items-start" style="white-space: nowrap;position-relativez-index:2000">
                <!-- Icons -->
                <?php if($_SESSION['Login_Meno_Priezvisko']): ?>
                <li class="nav-item me-3 me-lg-0 mb-10">
                  <a class="nav-link" rel="nofollow">
                    Ste prihlásení ako: <b> <?php echo $_SESSION['Login_Meno_Priezvisko']; ?></b>
                  </a>
                </li>
                    <li class="nav-item me-3 me-lg-0 mb-10">
                        <a class="nav-link" href="src/zmena/zmena_hesla.php" rel="nofollow">
                            Zmena hesla
                        </a>
                    </li>
                <li class="nav-item me-3 me-lg-0 mb-10">
                  <a class="nav-link" href="logout.php" rel="nofollow">
                    Odhlásiť sa
                  </a>
                </li>
              <?php endif; ?>
              </ul>
        </div>

    </div>

</nav>
