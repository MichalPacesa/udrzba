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
    if(!strip_tags_html($_GET["zobrazit"])){
        if(ZistiPrava("editNahradneDiely",$dblink) == 0){
            include_once "src/partials/navbar.php"; // navigacia
            echo "<span class='oznam cervene'>Nemáte práva na úpravu náhradnych dielov.</span>";
            exit;
        }
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
$Nahradny_dielID=mysqli_real_escape_string($dblink,strip_tags_html($_GET["Nahradny_dielID"]));
$Nahradny_dielID=intval($Nahradny_dielID);

if($Nahradny_dielID)
{
    $zobrazit=strip_tags_html($_GET["zobrazit"]);
    $sql="SELECT * FROM nahradny_diel n LEFT JOIN kategoria k ON n.KategoriaID = k.KategoriaID LEFT JOIN stroj s ON n.StrojID = s.StrojID WHERE n.Nahradny_dielID = $Nahradny_dielID";

    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Diel_evidencne_cislo = strip_tags_html($riadok["Diel_evidencne_cislo"]);
    $Diel_nazov = strip_tags_html($riadok["Diel_nazov"]);
    $Diel_popis = strip_tags_html($riadok["Diel_popis"]);
    $Diel_jednotka = strip_tags_html($riadok["Diel_jednotka"]);
    $Diel_mnozstvo = strip_tags_html($riadok["Diel_mnozstvo"]);
    $Diel_umiestnenie = strip_tags_html($riadok["Diel_umiestnenie"]);
    $Diel_datum_prevzatia = strip_tags_html($riadok["Diel_datum_prevzatia"]);
    $Diel_zarucna_doba = strip_tags_html($riadok["Diel_zarucna_doba"]);
    if($Diel_zarucna_doba == 0){
        $Diel_zarucna_doba = "";
    }

    $Kat_nazov = strip_tags_html($riadok["Kat_nazov"]);
    $KategoriaID = strip_tags_html($riadok["KategoriaID"]);

    $Stroj_nazov = strip_tags_html($riadok["Stroj_nazov"]);
    $StrojID = strip_tags_html($riadok["StrojID"]);



    if($Diel_datum_prevzatia == "0000-00-00") $Diel_datum_prevzatia = "";
    else $Diel_datum_prevzatia = date("d.m.Y", strtotime($Diel_datum_prevzatia));

    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Náhradný diel č. ".$Diel_evidencne_cislo;
    }
    else
    {
        // zisti prava
        $akcia = "update";
        $nadpis = "Editácia náhradného dielu č. ".$Diel_evidencne_cislo;
    }


}
else
{

    $akcia = "insert";
    $nadpis = "Nový náhradný diel";
    $Nahradny_dielID = "";
    $Diel_evidencne_cislo = "";
    $Diel_nazov = "";
    $Diel_popis = "";
    $Diel_jednotka = "";
    $Diel_mnozstvo = "";
    $Diel_umiestnenie = "";
    $Diel_datum_prevzatia = "";
    $Diel_zarucna_doba = "";

    $KategoriaID = "";
    $Kat_nazov = "";

    $Stroj_nazov = "";
    $StrojID = "";

}
?>

<h1><?php echo $nadpis; ?></h1>

<p>
<form id="myapp_form" action="../zmena/zmena_nahradneho_dielu.php" method="POST" onsubmit="return app.strojChecked && app.evidencneCisloChecked">

    <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>

    <?php
    if($_GET["vysledok"] == "chyba")
        $hlaska = "Zadané prihlasovacie meno už existuje.";
    ?>

    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <tr><td colspan="2"><b>Základné údaje:</b></td></tr>
        <tr><td>Evidenčné číslo: <span class="hviezdicka">*</span></td>
            <td style="width:50%;">
                <div id="evidencne">
                    <span class="cervene" v-if="message">{{ message }}</span>
                    <input required type="text" class="form-control" v-model="evidencneCislo" @change="checkEvidencneCislo()" name="Diel_evidencne_cislo" value="<?php echo $Diel_evidencne_cislo;?>" <?php echo disabled($akcia);?>/>
                    <button style="width: 8rem !important;" type="button" class="form-control" @click="generatePartNumber" <?php echo disabled($akcia);?>>Vygenerovať</button>
                </div>
            </td></tr>
        <tr><td>Názov: <span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Diel_nazov" value="<?php echo $Diel_nazov;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Kategória: <span class="hviezdicka">*</span></td>
            <td>
                <ul>
                    <div id="kategoria">
                        <input
                                required
                                class="form-control"
                                type="text"
                                name="Kat_nazov"
                                v-model="searchInput"
                                placeholder="Vyberte kategóriu"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @change="checkKategoria()"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectCategory(filteredCategory[selectedIndex].Kat_nazov,filteredCategory[selectedIndex].KategoriaID)"
                                value="<?php echo $Kat_nazov;?>"
                            <?php echo disabled($akcia);?>
                        />
                        <input type="hidden" name="KategoriaID" v-model="KategoriaID">
                        <div class="results zoznam_pozicii" v-if="showResults && filteredCategory.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredCategory"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectCategory(result.Kat_nazov,result.KategoriaID)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Kat_nazov }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </td>
        </tr>

        <tr><td>Stroj:</td>
                <td>
                    <ul>
                        <div id="stroj">
                            <span class="cervene" v-if="message" >{{ message }}</span>
                            <input
                                    class="form-control"
                                    type="text"
                                    name="Stroj_nazov"
                                    v-model="searchInput"
                                    placeholder="Vyberte stroj"
                                    autocomplete="off"
                                    @focus="showResults = true"
                                    @blur="showResults = false"
                                    @change="checkStroj()"
                                    @keydown.down.prevent="moveSelection(1)"
                                    @keydown.up.prevent="moveSelection(-1)"
                                    @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Stroj_nazov,filteredMachine[selectedIndex].StrojID)"
                                    value="<?php echo $Stroj_nazov;?>"
                                <?php echo disabled($akcia);?>
                            />
                            <input type="hidden" name="StrojID" v-model="StrojID">
                            <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                                <ul>
                                    <li
                                            class="pozicie"
                                            v-for="(result, index) in filteredMachine"
                                            :key="index"
                                            v-if="index < 5"
                                            @mouseover="mouseover(index)"
                                            @click="selectMachine(result.Stroj_nazov,result.StrojID)"
                                            @mousedown.prevent
                                            :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                    >
                                        {{ result.Stroj_nazov }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ul>
                </td>
        </tr>

        <tr><td style = "vertical-align:top !important;">Popis:</td><td><textarea type="text" class="form-control" name="Diel_popis" <?php echo disabled($akcia); ?>><?php echo $Diel_popis;?></textarea></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Údaje o skladovaní: </b></td></tr>
        <tr><td>Mnozstvo a jednotka: <span class="hviezdicka">*</span></td><td>
                <input required type="number" class="form-control mnozstvo" name="Diel_mnozstvo" value="<?php echo $Diel_mnozstvo;?>" <?php echo disabled($akcia);?>>
                <input required type="text" class="form-control mnozstvo" name="Diel_jednotka" placeholder="Napr. ks" value="<?php echo $Diel_jednotka;?>" <?php echo disabled($akcia);?>>
        </td></tr>
        <tr><td>Umiestnenie: <span class="hviezdicka">*</span></td>
            <td>
                <ul>
                    <div id="umiestnenie">
                        <input
                                class="form-control"
                                type="text"
                                name="Diel_umiestnenie"
                                v-model="searchInput"
                                placeholder="Zadajte umiestnenie"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectPartPosition(filteredPartPositions[selectedIndex].Diel_umiestnenie)"
                                value="<?php echo $Diel_umiestnenie;?>"
                                <?php echo disabled($akcia);?>
                                required
                        />
                        <div class="results zoznam_pozicii" v-if="showResults && filteredPartPositions.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredPartPositions"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectPartPosition(result.Diel_umiestnenie)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Diel_umiestnenie }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </td>
        </tr>

        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Údaje o záruke:</b></td></tr>
        <tr><td>Dátum prevzatia:</td><td><input type="text" class="form-control" id="datepicker2" name="Diel_datum_prevzatia" value="<?php echo $Diel_datum_prevzatia;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td>Záručná doba (počet mesiacov):</td><td> <input type="number" class="form-control" name="Diel_zarucna_doba" value="<?php echo $Diel_zarucna_doba;?>" <?php echo disabled($akcia);?>/></br></td></tr>
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
                <input type="hidden" name="Nahradny_dielID" value="<?php echo $Nahradny_dielID;?>">
                <input type="hidden" name="StrojID_old" value="<?php echo $StrojID;?>">
                <input type="hidden" name="Stroj_nazov_old" value="<?php echo $Stroj_nazov;?>">
                <input type="hidden" name="KategoriaID_old" value="<?php echo $KategoriaID;?>">
                <input type="hidden" name="Kat_nazov_old" value="<?php echo $Kat_nazov;?>">
                <input type="hidden" name="Diel_evidencne_cislo_old" value="<?php echo $Diel_evidencne_cislo;?>">
            </td></tr>
    </table>
</form>

<form id="back" action="../zmena/zmena_nahradneho_dielu.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    var app = new Vue({
        el: '#evidencne',
        data: {
            evidencneCislo: '',
            evidencneCisloChecked: true,
            oldEvidencneCislo: '<?php echo $Diel_evidencne_cislo; ?>',
            evidencneCislo: '<?php echo $Diel_evidencne_cislo; ?>',
            message: '',
        },
        mounted: function(){
        },
        methods: {
            checkEvidencneCislo: function() {
                this.evidencneCisloChecked = true;
                if(this.evidencneCislo!==this.oldEvidencneCislo || this.evidencneCislo!==""){ // zmenilli pouzivatelske  meno

                    axios.get(`../search/search_diel_evidencne_cislo.php?q=${this.evidencneCislo}`)
                        .then(function (response)  {
                            // console.log(response.data);
                            if (response.data) {
                                app.evidencneCisloChecked = false;
                                app.message = 'Toto evidenčné číslo sa už v systéme nachádza, skúste ho zmeniť.';

                            } else {

                                app.message = '';
                                app.evidencneCisloChecked = true;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app.message = 'Vyskytla sa chyba kontroly evidenčného čísla. Skúste ho zadať neskôr.';
                        });
                }
            },
            generatePartNumber: function() {
                var timestamp = new Date().getTime().toString();  // Don't need to replace non-digits in JS
                var partNumber = timestamp.slice(-9);  // Cut the number to the last 9 digits
                this.evidencneCislo = partNumber.match(/.{1,3}/g).join(' ');  // Split into every three digits

                return evidencneCislo;
            }

        }
    });

</script>



<script>
    var app1 = new Vue({
        el: '#kategoria',
        data: {
            searchInput: "<?php echo $Kat_nazov?>",
            selectedIndex: 0,
            showResults: false,
            categories: "",
            message: '',
            mouseIndex: null,
            KategoriaID: "",
            oldKategoria: '<?php echo $Kat_nazov; ?>',
        },
        mounted: function(){
            this.listCategory();
        },
        computed: {
            filteredCategory: function () {
                if (!this.searchInput) {
                    return this.categories;
                }
                let self = this;
                return self.categories.filter(function (category) {
                    return self.removeDiacritics(category.Kat_nazov.toLowerCase())
                        .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });

            },
        },
        methods: {
            moveSelection(step) {
                this.selectedIndex += step;
                // Ošetrite pretečenie indexu
                if (this.selectedIndex >= this.filteredCategory.length) {
                    this.selectedIndex = 0;
                } else if (this.selectedIndex < 0) {
                    this.selectedIndex = this.filteredCategory.length - 1;
                }
                // Odznačte označenie myšou
                this.mouseIndex = null;
            },
            mouseover(index) {
                this.mouseIndex = index;
                // Odznačte označenie klávesnice
                this.selectedIndex = null;
            },
            selectCategory: function(category,KategoriaID_selected) {
                this.searchInput = category;
                this.showResults = false;
                app1.KategoriaID = KategoriaID_selected;
                this.selectedIndex = 0;
            },

            listCategory: function(){
                axios.get('../read/read_nahradne_diely.php', {
                    params: {
                        list_kategoria: "Kat_nazov"
                    }
                })
                    .then(function (response) {
                        app1.categories = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            checkKategoria: function() {

                if (!this.searchInput){
                    app1.KategoriaID = "";
                }

                if(this.searchInput!==this.oldKategoria){ // zmenilli pouzivatelske  meno
                    axios.get(`../search/search_kategoria.php?q=${this.searchInput}`)
                        .then(function (response)  {
                            // console.log(response.data);
                            if (response.data) {
                                app1.KategoriaID = response.data;
                            } else {
                                app1.KategoriaID = "";
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app.message = 'Vyskytla sa chyba kontroly kategorie. Skúste ho zadať neskôr.';
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

<script>
    var app2 = new Vue({
        el: '#stroj',
        data: {
            searchInput: "<?php echo $Stroj_nazov?>",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            oldstroj: '<?php echo $Stroj_nazov; ?>',
            message: '',
            strojChecked: true,
            holdTimeout: null,
            mouseIndex: null,
            StrojID: "",

        },
        mounted: function(){
            this.listMachine();
        },
        computed: {
            filteredMachine: function () {
                if (!this.searchInput) {
                    return this.machines;
                }
                let self = this;
                return self.machines.filter(function (machine) {
                    return self.removeDiacritics(machine.Stroj_nazov.toLowerCase())
                        .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });

            },
        },
        methods: {
            moveSelection(step) {
                this.selectedIndex += step;
                // Ošetrite pretečenie indexu
                if (this.selectedIndex >= this.filteredMachine.length) {
                    this.selectedIndex = 0;
                } else if (this.selectedIndex < 0) {
                    this.selectedIndex = this.filteredMachine.length - 1;
                }
                // Odznačte označenie myšou
                this.mouseIndex = null;
            },
            mouseover(index) {
                this.mouseIndex = index;
                // Odznačte označenie klávesnice
                this.selectedIndex = null;
            },
            selectMachine: function(machine,StrojID_selected) {
                this.searchInput = machine;
                this.showResults = false;
                this.checkStroj();
                app2.StrojID = StrojID_selected;
                this.selectedIndex = 0;
            },

            listMachine: function(){
                axios.get('../read/read_stroje.php', {
                    params: {
                        list: "Stroj_nazov"
                    }
                })
                    .then(function (response) {
                        app2.machines = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            checkStroj: function() {
                this.strojChecked = true;

                if (!this.searchInput){
                    app2.StrojID = "";
                }

                if(this.searchInput!==this.oldstroj && this.searchInput!==""){ // zmenilli pouzivatelske  meno
                    axios.get(`../search/search_stroj.php?q=${this.searchInput}`)
                        .then(function (response)  {
                            // console.log(response.data);
                            if (response.data) {
                                app2.message = '';
                                app.strojChecked = true;
                                app2.StrojID = response.data;

                            } else {
                                app2.message = 'Zadaný stroj nie je v evidencií strojov.';
                                app.strojChecked = false;
                                app2.StrojID = "";
                            }

                        })
                        .catch(error => {
                            console.error(error);
                            app2.message = 'Vyskytla sa chyba kontroly stroja. Skúste ho zadať neskôr.';
                        });
                }else if(this.searchInput==""){
                    app2.message = '';
                    app2.strojChecked = true;
                    app.strojChecked = true;
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

<script>
    var app3 = new Vue({
        el: '#umiestnenie',
        data: {
            searchInput: "<?php echo $Diel_umiestnenie?>",
            selectedIndex: 0,
            showResults: false,
            partPositions: "",
            message: '',
            mouseIndex: null,
            evidencneCisloChecked: true,
            oldEvidencneCislo: '<?php echo $Diel_evidencne_cislo; ?>',
            evidencneCislo: '<?php echo $Diel_evidencne_cislo; ?>',
        },
        mounted: function(){
            this.listPartPosition();
        },
        computed: {
            filteredPartPositions: function () {
                if (!this.searchInput) {
                    return this.partPositions;
                }
                let self = this;
                return self.partPositions.filter(function (machine) {
                    return self.removeDiacritics(machine.Diel_umiestnenie.toLowerCase())
                        .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });
            }
        },
        methods: {
            checkEvidencneCislo: function() {
                this.evidencneCisloChecked = true;
                if(this.evidencneCislo!==this.oldEvidencneCislo){ // zmenilli pouzivatelske  meno
                    axios.get(`../search/search_evidencne_cislo.php?q=${this.evidencneCislo}`)
                        .then(function (response)  {
                            console.log(response.data);

                            if (response.data) {
                                app3.message = 'Toto evidenčné číslo sa už v systéme nachádza, skúste ho zmeniť.';
                                app3.evidencneCisloChecked = false;
                            } else {
                                app3.message = '';
                                app3.evidencneCisloChecked = true;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app3.message = 'Vyskytla sa chyba kontroly evidenčného čísla. Skúste ho zadať neskôr.';
                        });
                }
            },

            moveSelection(step) {
                this.selectedIndex += step;
                // Ošetrite pretečenie indexu
                if (this.selectedIndex >= this.filteredPartPositions.length) {
                    this.selectedIndex = 0;
                } else if (this.selectedIndex < 0) {
                    this.selectedIndex = this.filteredPartPositions.length - 1;
                }
                // Odznačte označenie myšou
                this.mouseIndex = null;
            },

            mouseover(index) {
                this.mouseIndex = index;
                // Odznačte označenie klávesnice
                this.selectedIndex = null;
            },
            selectPartPosition: function(position) {
                this.searchInput = position;
                this.showResults = false;
                this.selectedIndex = 0;
            },

            listPartPosition: function(){
                axios.get('../read/read_nahradne_diely.php', {
                    params: {
                        list_umiestnenie: "Diel_umiestnenie"
                    }
                })
                    .then(function (response) {
                        app3.partPositions = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

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
            }

        }
    });

</script>



</body>

</html>