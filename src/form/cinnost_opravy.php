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

    if(ZistiPrava("editCinnostiOpravy",$dblink) == 0){
        include_once "src/partials/navbar.php"; // navigacia
        echo "<span class='oznam cervene'>Nemáte práva na úpravu činností opravy.</span>";
        exit;
    }
    ?>

    <meta name="viewport" content="width=device-width, initial-scale=1">

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
$Cinnost_opravyID=mysqli_real_escape_string($dblink,strip_tags_html($_GET["Cinnost_opravyID"]));
$Cinnost_opravyID=intval($Cinnost_opravyID);

if($Cinnost_opravyID)
{
    $zobrazit=strip_tags_html($_GET["zobrazit"]);
    $sql="SELECT * FROM cinnost_opravy WHERE Cinnost_opravyID = $Cinnost_opravyID";
    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Činnosť opravy č. ".$Cinnost_opravyID;
    }
    else
    {
        // zisti prava
        $akcia = "update";
        $nadpis = "Editácia činnosti opravy č. ".$Cinnost_opravyID;
    }

    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Cin_nazov = strip_tags_html($riadok["Cin_nazov"]);

}
else {
    $akcia = "insert";
    $nadpis = "Nová činnosť opravy";
    $Cinnost_opravyID = "";
    $Cin_nazov = "";
}
?>

<h1><?php echo $nadpis; ?></h1>

<p>
<form id="myapp_form" action="../zmena/zmena_cinnosti_opravy.php" method="POST" onsubmit="return app.nazovChecked">

    <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>

    <?php
    if($_GET["vysledok"] == "chyba")
        $hlaska = "Zadaný názov činnosti opravy už existuje.";
    ?>
    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <tr><td colspan="2"><b>Údaje o činnosti opravy: </b><span class="cervene" v-if="message" >{{ message }}</span></td></tr>
        <tr><td>Názov: <span class="hviezdicka">*</span></td>
            <td style="width:50%;">
                <ul>
                    <div>
                        <input
                                required
                                class="form-control"
                                type="text"
                                name="Cin_nazov"
                                v-model="searchInput"
                                placeholder="Zadajte názov činnosti"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectJobPosition(filteredJobPositions[selectedIndex].Cin_nazov)"
                                @change="checkNazov()"
                                style="width: 20rem !important;"
                                value="<?php echo $Cin_nazov;?>"
                            <?php echo disabled($akcia);?>
                        />
                        <div class="results zoznam_pozicii" v-if="showResults && filteredJobPositions.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredJobPositions"
                                        :key="index"
                                        @mouseover="mouseover(index)"
                                        v-if="index < 5"
                                        @click="selectJobPosition(result.Cin_nazov);checkNazov(result.Cin_nazov)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                     Už existuje: {{ result.Cin_nazov }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </td>
        </tr>

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
                <input type="hidden" name="Cinnost_opravyID" value="<?php echo $Cinnost_opravyID;?>">
                <input type="hidden" name="Cin_nazov_old" value="<?php echo $Cin_nazov;?>">
            </td></tr>
    </table>
</form>

<form id="back" action="../zmena/zmena_cinnosti_opravy.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>


<script>
    var app = new Vue({
        el: '#myapp_form',
        data: {
            searchInput: "<?php echo $Cin_nazov?>",
            selectedIndex: 0,
            showResults: false,
            jobPositions: "",
            positions: "",
            oldnazov: '<?php echo $Cin_nazov; ?>',
            message: '',
            usernameChecked: true,
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
                    return self.removeDiacritics(job.Cin_nazov.toLowerCase())
                        .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });

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
                axios.get('../read/read_cinnosti.php', {
                    params: {
                        list: "Cin_nazov"
                    }
                })
                    .then(function (response) {
                        app.jobPositions = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            checkNazov: function(Nazov_selected) {
                this.nazovChecked = true;
                if(this.searchInput!==this.oldnazov  || Nazov_selected!==this.oldnazov){
                    axios.get(`src/search/search_nazov_cinnosti.php?q=${this.searchInput}`)
                        .then(function (response)  {
                            console.log(response.data);

                            if (response.data) {
                                app.message = 'Tento názov činnosti opravy sa už v systéme nachádza, skúste ho zmeniť.';
                                app.nazovChecked = false;
                            } else {
                                app.message = '';
                                app.nazovChecked = true;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app.message = 'Vyskytla sa chyba kontroly názvu činnosti opravy. Skúste ho zadať neskôr.';
                        });
                }
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