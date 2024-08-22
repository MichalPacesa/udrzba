<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>

    <?php
    include_once "head.php";
    include "config.php";
    include "lib.php";
    include 'login.php';

    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }
    ?>

</head>
<body>



<?php



// pripojenie na DB server a zapamatame si pripojenie do premennej $dblink
//$dblink = mysqli_connect($mysql_server, $mysql_user, $mysql_password, $mysql_db);

if (!$dblink) { // kontrola ci je pripojenie na db dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    die(); // ukonci vykonanie php
}

mysqli_set_charset($dblink, "utf8mb4");
$PouzivatelID=mysqli_real_escape_string($dblink,trim($_SESSION['Login_ID_pouzivatela']));
$PouzivatelID=intval($PouzivatelID);

if($PouzivatelID) {
    $zobrazit = strip_tags_html($_GET["zobrazit"]);
    $sql = "SELECT * FROM pouzivatel WHERE PouzivatelID = $PouzivatelID";

    $akcia = "update";
    $nadpis = "Zmena hesla";

    $vysledok = mysqli_query($dblink, $sql);
    $riadok = mysqli_fetch_assoc($vysledok);

}
?>

<h1><?php echo $nadpis; ?></h1>

<p>
<form id ="myapp_form" action="zmena_hesla_v_databaze.php" method="POST">

    <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>

    <table class="zoznam">

        <tr><td>Prihlasovacie heslo: <span class="hviezdicka">*</span></td>
            <td style="width:50%;">
                <div>
                    <div class="input-group flex-nowrap heslo_login">
                        <input required class="form-control input-login" name="Pouz_heslo" type="password" id="password">
                        <button
                                type="button" class="input-group-text"
                                @click="toggleIcon"
                                onclick="ShowPassword()">
                            <i :class="currentIcon" style="color: #777777 !important;"></i>
                        </button>
                    </div>
                </div>

            </td>
        <tr>

        <tr><td colspan="2"><br></td></tr>

        <tr><td colspan="2">
                <div class="d-flex" style="margin-right: 40px !important;">
                    <button type="submit" title="Uložiť" value="Uložiť"  class="btn-light btn-labeled btn_ikony">
                        <span class="btn-label"><i class="fa-regular fa-floppy-disk"></i></span>Uložiť
                    </button>

                    <button type="submit" name="back" form="back" value="Späť" title="Spať" class="btn-light btn-labeled btn_ikony ">
                        <span class="btn-label"><i class="fa fa-arrow-left" style="font-size: 1.3rem;"></i></span>Späť
                    </button>
                </div>
                <input type="hidden" name="akcia" value="<?php echo $akcia;?>">
                <input type="hidden" name="PouzivatelID" value="<?php echo $PouzivatelID;?>">
            </td></tr>
    </table>
</form>

<form id="back" action="zmena_hesla_v_databaze.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    var app = new Vue({
        el: '#myapp_form',
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

</body>

</html>