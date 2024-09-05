<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>

    <?php include_once '../partials/head.php'; 

    include "../../config.php";
    include_once "../../lib.php";
    include '../auth/login.php';


    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }

    if(ZistiPrava("zamestnanci",$dblink) == 0){
        include_once "src/partials/navbar.php"; // navigacia
        echo "<span class='cervene'>Nemáte práva na úpravu zamestnancov.</span>";
        exit;
    }

    ?>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />


    <script>
        $(function() {
            $("#datepicker").datepicker({
                dateFormat: "dd.mm.yy",
                yearRange: "1900:2030",
                changeMonth: true,
                changeYear: true
            });
            $("#datepicker2").datepicker({
                dateFormat: "dd.mm.yy",
                yearRange: "1900:2030",
                changeMonth: true,
                changeYear: true
            });
        } );
    </script>

</head>
<body>



<?php
/*novy zamestnanec a zmena */


// pripojenie na DB server a zapamatame si pripojenie do premennej $dblink
//$dblink = mysqli_connect($mysql_server, $mysql_user, $mysql_password, $mysql_db);

if (!$dblink) { // kontrola ci je pripojenie na db dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    die(); // ukonci vykonanie php
}

mysqli_set_charset($dblink, "utf8mb4");
$ZamestnanecID=mysqli_real_escape_string($dblink,strip_tags_html($_GET["ZamestnanecID"]));
$ZamestnanecID=intval($ZamestnanecID);

if($ZamestnanecID)
{
    $zobrazit=strip_tags_html($_GET["zobrazit"]);
    $sql="SELECT * FROM zamestnanec z LEFT JOIN pouzivatel p ON p.PouzivatelID = z.ZamestnanecID WHERE z.ZamestnanecID = $ZamestnanecID";
    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Zamestnanec č. ".$ZamestnanecID;
    }
    else
    {
        // zisti prava
        $akcia = "update";
        $nadpis = "Editácia zamestnanca č. ".$ZamestnanecID;
    }
    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Zam_meno = strip_tags_html($riadok["Zam_meno"]);
    $Zam_priezvisko = strip_tags_html($riadok["Zam_priezvisko"]);
    $Zam_datum_narodenia = strip_tags_html($riadok["Zam_datum_narodenia"]);
    $Zam_email = strip_tags_html($riadok["Zam_email"]);
    $Zam_telefon = strip_tags_html($riadok["Zam_telefon"]);
    $Zam_ulica_a_cislo = strip_tags_html($riadok["Zam_ulica_a_cislo"]);
    $Zam_mesto = strip_tags_html($riadok["Zam_mesto"]);
    $Zam_psc = strip_tags_html($riadok["Zam_psc"]);
    $Zam_pozicia = strip_tags_html($riadok["Zam_pozicia"]);
    $Zam_datum_nastupu = strip_tags_html($riadok["Zam_datum_nastupu"]);
    $Zam_poznamka = strip_tags_html($riadok["Zam_poznamka"]);

    $PouzivatelID = strip_tags_html($riadok["PouzivatelID"]);
    $Pouz_meno =  strip_tags_html($riadok["Pouz_meno"]);
    $Pouz_heslo =  "";
    $RolaID =  strip_tags_html($riadok["RolaID"]);


    if(!$zobrazit){ /* PRAVA */
        if(ZistiPrava("rola",$dblink) == 0 AND $RolaID == 1){
                include_once "src/partials/navbar.php"; // navigacia
                echo "<span class='cervene'>Nemáte práva na úpravu vybraného zamestnanca.</span>";
                exit;
        }
    }

    if($Zam_datum_narodenia == "0000-00-00") $Zam_datum_narodenia = "";
    else $Zam_datum_narodenia = date("d.m.Y", strtotime($Zam_datum_narodenia));

    if($Zam_datum_nastupu == "0000-00-00") $Zam_datum_nastupu = "";
    else $Zam_datum_nastupu = date("d.m.Y", strtotime($Zam_datum_nastupu));

}
else
{

    $akcia = "insert";
    $nadpis = "Nový zamestnanec";
    $ZamestnanecID = "";
    $Zam_meno = "";
    $Zam_priezvisko = "";
    $Zam_datum_narodenia = "";
    $Zam_email = "";
    $Zam_telefon = "";
    $Zam_ulica_a_cislo = "";
    $Zam_mesto = "";
    $Zam_psc = "";
    $Zam_pozicia = "";
    $Zam_datum_nastupu = "";
    $Zam_poznamka = "";

    $Pouz_meno =  "";
    $Pouz_heslo =  "";
    $RolaID =  "4";


}
?>

<h1><?php echo $nadpis; ?></h1>

<p>
<form id ="myapp_form" action="../zmena/zmena_zamestnanca.php" method="POST" onsubmit="return app.usernameChecked">

    <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>

    <?php
    if($_GET["vysledok"] == "chyba")
        $hlaska = "Zadané prihlasovacie meno už existuje.";
    ?>
    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <tr><td colspan="2"><b>Kontaktné údaje:</b></td></tr>
        <tr><td>Meno:<span class="hviezdicka">*</span></td><td style="width:50%;"><input required type="text" class="form-control" name="Zam_meno" value="<?php echo $Zam_meno;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Priezvisko:<span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Zam_priezvisko" value="<?php echo $Zam_priezvisko;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Email:<span class="hviezdicka">*</span></td><td> <input required type="email" class="form-control" name="Zam_email" value="<?php echo $Zam_email;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Telefon:<span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Zam_telefon" placeholder="Zadajte vo formate +421..." value="<?php echo $Zam_telefon;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Dátum narodenia:</td><td> <input type="text" class="form-control" id="datepicker" name="Zam_datum_narodenia" value="<?php echo $Zam_datum_narodenia;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Adresa:</b></td></tr>
        <tr><td>Ulica a číslo domu:<span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Zam_ulica_a_cislo" value="<?php echo $Zam_ulica_a_cislo;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Mesto:<span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Zam_mesto" value="<?php echo $Zam_mesto;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>PSČ:<span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Zam_psc" value="<?php echo $Zam_psc;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Údaje o zamestnaní:</b></td></tr>
        <tr><td>Pracovná pozícia:<span class="hviezdicka">*</span></td>
            <td>
                <ul>
                    <div>
                        <input
                                class="form-control"
                                type="text"
                                name="Zam_pozicia"
                                v-model="searchInput"
                                placeholder="Zadajte pracovnú pozíciu"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectJobPosition(filteredJobPositions[selectedIndex].Zam_pozicia)"
                                value="<?php echo $Zam_pozicia;?>"
                            <?php echo disabled($akcia);?>
                        />
                        <div class="results zoznam_pozicii" v-if="showResults && filteredJobPositions.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredJobPositions"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectJobPosition(result.Zam_pozicia)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Zam_pozicia }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </td>
        </tr>
        <tr><td>Dátum nástupu:</td><td><input type="text" class="form-control" id="datepicker2" name="Zam_datum_nastupu" value="<?php echo $Zam_datum_nastupu;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Prihlasovacie údaje:</b>&nbsp;&nbsp;<span class="cervene" v-if="message" >{{ message }}</span></td></tr>
        <tr><td>Prihlasovacie meno:<span class="hviezdicka">*</span></td><td>
                <input required type="text" class="form-control" v-model="username" name="Pouz_meno" @change="checkUsername()" value="<?php echo $Pouz_meno;?>" <?php echo disabled($akcia);?>>
                <input type="hidden" name="Pouz_meno_old" value="<?php echo $Pouz_meno;?>">
                </td></tr>
        <?php if($akcia=="insert"):?>
        <tr><td>Prihlasovacie heslo:<span class="hviezdicka">*</span></td><td>
                <div>
                    <div class="input-group flex-nowrap heslo">
                        <input required class="form-control" type="password" name="Pouz_heslo" id="password" value="" <?php echo disabled($akcia);?>>
                        <button
                                type="button" class="input-group-text"
                                @click="toggleIcon"
                                onclick="ShowPassword()">
                            <i :class="currentIcon" style="color: #777777 !important;"></i>
                        </button>
                    </div>
                </div>
                <?php else:?>
        <tr><td>Prihlasovacie heslo (nechajte prázdne ak sa nemení):</span></td><td>
                <div>
                    <div class="input-group flex-nowrap heslo">
                        <input class="form-control" type="password" name="Pouz_heslo" id="password" value="" <?php echo disabled($akcia);?>>
                        <button
                                type="button" class="input-group-text"
                                @click="toggleIcon"
                                onclick="ShowPassword()">
                            <i :class="currentIcon" style="color: #777777 !important;"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

        <!-- Rola -->
        <tr><td style = "vertical-align:middle !important;">Rola: <span class="hviezdicka">*</span></td>
            <td style = "vertical-align:middle !important;">
                <?php
                $sql = "Select * FROM rola";
                $vysledok=mysqli_query($dblink,$sql);
                if (!$vysledok):
                    echo "Doslo k chybe pri vytvarani SQL dotazu !";
                elseif(ZistiPrava("rola",$dblink)):
                    ?>

                        <select class = "form-select" name="RolaID"  <?php echo disabled($akcia);?>>
                            <?php while($riadok=mysqli_fetch_assoc($vysledok)): ?>
                                <option value="<?php echo $riadok["RolaID"];?>" <?php echo selected($RolaID,$riadok["RolaID"]); ?>><?php echo $riadok["Rola_nazov"]; ?></option>
                            <?php endwhile; ?>
                        </select>

                <?php  else:?>
                    <select disabled class = "form-select" name="RolaID"  <?php echo disabled($akcia);?>>
                        <?php while($riadok=mysqli_fetch_assoc($vysledok)): ?>
                            <option value="<?php echo $riadok["RolaID"];?>" <?php echo selected($RolaID,$riadok["RolaID"]); ?>><?php echo $riadok["Rola_nazov"]; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="hidden" name="RolaID" value="<?php echo $RolaID;?>">
                <?php
                endif;
                /* end Rola  */
                ?>
            </td></tr>

        <tr><td colspan="2"><br></td></tr>
        <tr><td style = "vertical-align:top !important;">Poznámka:</td><td><textarea type="text" class="form-control" name="Zam_poznamka" <?php echo disabled($akcia); ?>><?php echo $Zam_poznamka;?></textarea></td></tr>
        <tr><td colspan="2"><br></td></tr>

        <tr><td colspan="2">
                <?php if($akcia !='preview'):?>
                <div class="d-flex" style="margin-right: 40px !important;">
                    <button type="submit" title="Uložiť" value="Uložiť"  class="btn-light btn-labeled btn_ikony">
                        <span class="btn-label"><i class="fa-regular fa-floppy-disk"></i></span>Uložiť
                    </button>

                    <button type="submit" name="back" form="back" value="Späť" title="Spať" class="btn-light btn-labeled btn_ikony ">
                        <span class="btn-label"><i class="fa fa-arrow-left" style="font-size: 1.3rem;"></i></span>Späť
                    </button>
                </div>
                <?php else:?>
                    <button type="submit" name="back" form="back" value="Späť" title="Spať" class="btn-light btn-labeled btn_ikony">
                        <span class="btn-label "><i class="fa fa-arrow-left"></i></span>Späť
                    </button>
                <?php endif;?>
                <input type="hidden" name="akcia" value="<?php echo $akcia;?>">
                <input type="hidden" name="ZamestnanecID" value="<?php echo $ZamestnanecID;?>">
                <input type="hidden" name="PouzivatelID" value="<?php echo $PouzivatelID;?>">
            </td></tr>
    </table>
</form>

<form id="back" action="../zmena/zmena_zamestnanca.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    var app = new Vue({
        el: '#myapp_form',
        data: {
            searchInput: "<?php echo $Zam_pozicia?>",
            selectedIndex: 0,
            showResults: false,
            jobPositions: "",
            positions: "",
            oldname: '<?php echo $Pouz_meno; ?>',
            username: '<?php echo $Pouz_meno; ?>',
            message: '',
            usernameChecked: true,
            icons: { eye: 'fa-regular fa-eye', eyeSlash: 'fa-regular fa-eye-slash' },
            iconState: true,
            holdTimeout: null,
            mouseIndex: null,

        },
        mounted: function(){
            this.listPosition();
        },
        computed: {
            filteredJobPositions: function () {
                if (!this.searchInput) {
                    return this.jobPositions;
                }
                let self = this;
                return self.jobPositions.filter(function (job) {
                    return self.removeDiacritics(job.Zam_pozicia.toLowerCase())
                            .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });

            },
            currentIcon() {
                return this.iconState ? this.icons.eyeSlash : this.icons.eye;
            }
        },
        methods: {
            moveSelection(step) {
                this.selectedIndex += step;
                // Ošetrite pretečenie indexu
                if (this.selectedIndex >= this.filteredJobPositions.length) {
                    this.selectedIndex = 0;
                } else if (this.selectedIndex < 0) {
                    this.selectedIndex = this.filteredJobPositions.length - 1;
                }
                // Odznačte označenie myšou
                this.mouseIndex = null;
            },
            mouseover(index) {
                this.mouseIndex = index;
                // Odznačte označenie klávesnice
                this.selectedIndex = null;
            },
            selectJobPosition: function(position) {
                this.searchInput = position;
                this.showResults = false;
                this.selectedIndex = 0;
            },

            listPosition: function(){
                axios.get('../read/read_zamestnanci.php', {
                    params: {
                        list: "Zam_pozicia"
                    }
                })
                    .then(function (response) {
                        app.jobPositions = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            checkUsername: function() {
                this.usernameChecked = true;
                if(this.username!==this.oldname){ // zmenilli pouzivatelske  meno
                    axios.get(`../search/search_login.php?q=${this.username}`)
                        .then(function (response)  {
                            
                            if (response.data) {
                                app.message = 'Toto prihlasovacie meno sa už v systéme nachádza, skúste ho zmeniť.';
                                app.usernameChecked = false;
                            } else {
                                app.message = '';
                                app.usernameChecked = true;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app.message = 'Vyskytla sa chyba kontroly prihlasovacieho mena. Skúste ho zadať neskôr.';
                        });
                }
            },
            toggleIcon() {
                this.iconState = !this.iconState;
            },
            removeDiacritics(str) {
                const diacriticsMap = [
                    {base: 'a', chars: 'áäâàãåăą'},
                    {base: 'c', chars: 'çćč'},
                    {base: 'd', chars: 'ďđ'},
                    {base: 'e', chars: 'éěēèêëę'},
                    {base: 'i', chars: 'ìíîïĩīį'},
                    {base: 'l', chars: 'ľĺļł'},
                    {base: 'n', chars: 'ńñňņŋ'},
                    {base: 'o', chars: 'òóöôőõøō'},
                    {base: 'r', chars: 'řŗ'},
                    {base: 's', chars: 'šśș'},
                    {base: 't', chars: 'ťţț'},
                    {base: 'u', chars: 'ùúüûűūůų'},
                    {base: 'y', chars: 'ýÿŷ'},
                    {base: 'z', chars: 'žźż'}
                ];
                for (let i = 0; i < diacriticsMap.length; i++) {
                    for (let j = 0; j < diacriticsMap[i]["chars"].length; j++) {
                        str = str.replace(diacriticsMap[i]["chars"][j], diacriticsMap[i]["base"]);
                    }
                }
                return str;
            },
        }
    });

</script>

</body>

</html>