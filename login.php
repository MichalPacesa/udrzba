<?php
include_once('config.php');
include_once('head.php');
include_once('lib.php');
generateToken(); //vygeneruje sa token

if(isset($_POST['Login_Prihlasovacie_meno']) AND isset($_POST['Login_Prihlasovacie_heslo']))
{
	$Login_Prihlasovacie_meno = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST['Login_Prihlasovacie_meno'])));
	$Login_Prihlasovacie_heslo = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST['Login_Prihlasovacie_heslo'])));
    $token =strip_tags($_POST['token']);// phpinfo();exit;
    if(!checkToken($token)) {
        echo "Chyba tokenu!</br>";
        die(); // ukonci login
    }
	if (!$dblink) 
	{ 
		echo "Chyba pripojenia na DB!</br>";
		die(); // ukonci login
	}
	mysqli_set_charset($dblink, "utf8mb4");
    $sql = "SELECT * FROM pouzivatel, zamestnanec WHERE Pouz_meno='$Login_Prihlasovacie_meno' AND pouzivatel.PouzivatelID=zamestnanec.ZamestnanecID";


	$vysledok = mysqli_query($dblink,$sql);
	$row = mysqli_num_rows($vysledok);
	if($row > 0) 
    {
        $riadok = mysqli_fetch_assoc($vysledok);
        //echo $riadok["Pouz_heslo"];
        //exit;
        if(password_verify($Login_Prihlasovacie_heslo, $riadok["Pouz_heslo"])){

            $_SESSION['Login_ID_pouzivatela'] = $riadok["PouzivatelID"];
            $_SESSION['Login_Prihlasovacie_meno'] = $Login_Prihlasovacie_meno;
            $_SESSION['Login_Meno_Priezvisko'] = $riadok["Zam_meno"]." ".$riadok["Zam_priezvisko"];
            $_SESSION['Login_RolaID'] = $riadok["RolaID"];
            $Login_ID_pouzivatela=$riadok["PouzivatelID"];

        }
	}       
	mysqli_close($dblink);

}


if (!isset($_SESSION['Login_ID_pouzivatela'])){

?>
        <div class=" d-flex justify-content-center align-items-center" style="height: 100%">
            <form id="formlogin" class="graybox_login" action="index.php" method="POST">
                <div id="login" class=" login container-fluid align-content-center border pt-3 col-12 col-md-12">
                    <div class="row">
                        <h3>Prihlásenie</h3><br>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-md-12 roboto-regular">
                        <?php
                        if (isset($Login_Prihlasovacie_meno)) {
                        // Pokusil se prihlásit, ale nepodarilo sa mu to
                        echo '<div class="oznam cervene fw-bold fs-6">Nesprávne prihlasovacie meno alebo heslo.</div><br>';
                        }
                        ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 roboto-regular">Prihlasovacie meno:</div>
                        <div class="col-12 col-md-6"><input required type="text" class="form-control input-login" name="Login_Prihlasovacie_meno" value="pacesa"></br></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-md-6 roboto-regular">Heslo:</div>
                        <div class="col-12 col-md-6 ">
                            <div class="input-group flex-nowrap heslo_login">
                                <input required class="form-control input-login" type="password" name="Login_Prihlasovacie_heslo" id="password" value="pacesa">
                                <button
                                        type="button" class="input-group-text"
                                        @click="toggleIcon"
                                        onclick="ShowPassword()">
                                    <i :class="currentIcon" style="color: #777777 !important;"></i>
                                </button>
                            </div>
                            <input type="hidden" name="token" value="<?php if($_SESSION['token']) echo $_SESSION['token']; ?>">
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end align-content-center">
                            <button type="submit" title="Prihlásiť" value="Prihlásiť" class="btn-light btn-labeled btn_ikony">
                                <span class="btn-label"><i class="fas fa-arrow-right-to-bracket"></i></span>Prihlásiť
                            </button>
                        </div>

                    </div>
                    <br>
            </form>
        </div>


    <script>
        var app = new Vue({
            el: '#formlogin',
            data: {
                icons: { eye: 'fa-regular fa-eye', eyeSlash: 'fa-regular fa-eye-slash' },
                iconState: true,
            },
            mounted: function(){
            },
            computed: {
                currentIcon() {
                    return this.iconState ? this.icons.eyeSlash : this.icons.eye;
                },
            },
            methods: {

                toggleIcon() {
                    this.iconState = !this.iconState;
                }
            }
        })
    </script>

<?php } ?>




