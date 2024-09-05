<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>

    <?php include_once "head.php";

    include "config.php";
    include_once "lib.php";
    include 'login.php';

    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }

    if(ZistiPrava("Uprav_poruchy",$dblink) == 0){
        include_once "src/partials/navbar.php"; // navigacia
        echo "<p class='oznam'>Nemáte práva na upravenie porúch.</p>";
        exit;
    }

    ?>



    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<?php



if (!$dblink) { // kontrola ci je pripojenie na db dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    die(); // ukonci vykonanie php
}

mysqli_set_charset($dblink, "utf8mb4");
$PoruchaID=mysqli_real_escape_string($dblink,strip_tags_html($_GET["PoruchaID"]));
$PoruchaID=intval($PoruchaID);

if($PoruchaID)
{
    $zobrazit=strip_tags_html($_GET["zobrazit"]);
    $sql="select * from porucha p LEFT JOIN zamestnanec z ON p.PouzivatelID = z.PouzivatelID LEFT JOIN stroj s ON p.StrojID = s.StrojID WHERE p.PoruchaID = $PoruchaID";
    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Porucha č. ".$PoruchaID;
    }
    else
    {
        // zisti prava
        $akcia = "update";
        $nadpis = "Editácia poruchy č. ".$PoruchaID;
    }
    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Por_nazov = strip_tags_html($riadok["Por_nazov"]);
    $Por_popis = strip_tags_html($riadok["Por_popis"]);
    $Por_stav = strip_tags_html($riadok["Por_stav"]);
    $Por_datum_vzniku = strip_tags_html($riadok["Por_datum_vzniku"]);
    $Por_datum_pridelenia = strip_tags_html($riadok["Por_datum_pridelenia"]);
    $Stroj_nazov = strip_tags_html($riadok["Stroj_nazov"]);
    $StrojID = strip_tags_html($riadok["StrojID"]);
    $PouzivatelID = strip_tags_html($riadok["PouzivatelID"]);


    if($Por_datum_vzniku == "0000-00-00 00:00:00") $Por_datum_vzniku = "";
    else $Por_datum_vzniku = date("Y-m-d\TH:i", strtotime($Por_datum_vzniku));

    if($Por_datum_pridelenia == "0000-00-00 00:00:00") $Por_datum_pridelenia = "";
    else $Por_datum_pridelenia = date("d.m.Y H:i", strtotime($Por_datum_pridelenia));

}
else
{

    $akcia = "insert";
    $nadpis = "Nová porucha";
    $Por_nazov = "";
    $Por_popis = "";
    $Por_stav = "";
    $Por_datum_vzniku = date("Y-m-d H:i");
    $Por_datum_pridelenia = "";
    $Stroj_nazov = "";
    $Zam_meno = "";
    $Zam_priezvisko = "";

}
?>

<h1><?php echo $nadpis; ?></h1>
<p>
<form id ="myapp_form" action="zmena_poruchy.php" method="POST" onsubmit="return app.strojChecked">

    <?php if($Por_datum_pridelenia): ?>
        <div class="container d-flex align-items-center">
            <p class="oznam text-grey text-start" style="margin-left:-0.625rem!important;">Položky označené <span class="red bold">*</span> sú povinné</p>
            <p class="oznam text-grey text-end " style="margin-left:2rem !important;">Dátum posledného pridelenia: <?php echo $Por_datum_pridelenia;?></p>
        </div>
    <?php else: ?>
        <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>
    <?php endif; ?>

    <?php
    if($_GET["vysledok"] == "chyba")
        $hlaska = "Chyba pri uložení poruchy.";
    ?>
    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <?php if(ZistiPrava("editStavAZamestnanecNaPoruche",$dblink) == 1){ ?>
        <tr><td style = "vertical-align:middle !important;">Stav poruchy</td>
            <td style = "vertical-align:middle !important;">
                <select class="form-select" name="Por_stav" <?php echo disabled($akcia);?>>
                    <option value="1" <?php echo selected($Por_stav,'1') ?> > Nahlásená </option>
                    <option value="2" <?php echo selected($Por_stav,'2') ?> > Pridelená </option>
                    <option value="3" <?php echo selected($Por_stav,'3') ?> > Na kontrolu </option>
                    <option value="4" <?php echo selected($Por_stav,'4') ?> > Vybavená </option>
                </select>
            </td></tr>

        <tr><td style = "vertical-align:middle !important;">Pridelený zamestnanec:</td>
            <td style = "vertical-align:middle !important;">
                <?php $sql = "Select * FROM zamestnanec z LEFT JOIN pouzivatel p ON z.PouzivatelID = p.PouzivatelID WHERE RolaID = 4";
                $vysledok=mysqli_query($dblink,$sql);
                if (!$vysledok):
                    echo "Doslo k chybe pri vytvarani SQL dotazu !";
                else:
                    ?>

                    <select class = "form-select" name="PouzivatelID"  <?php echo disabled($akcia);?>>
                        <option value="0"> </option>
                        <?php while($riadok=mysqli_fetch_assoc($vysledok)): ?>
                            <option value="<?php echo $riadok["PouzivatelID"];?>" <?php echo selected($PouzivatelID,$riadok["PouzivatelID"]); ?>><?php echo $riadok["Zam_meno"]." ".$riadok["Zam_priezvisko"]; ?></option>
                        <?php endwhile; ?>
                    </select>

                <?php endif; /* end Prideleny zamestnanec  */?>
            </td></tr>
        <tr><td colspan="2"><br></td></tr>
        <?php } ?>
        <tr><td colspan="2"><b>Základné údaje: </b><span class="cervene" v-if="message" >{{ message }}</span></td></tr>
        <tr><td>Názov: <span class="hviezdicka">*</span></td><td style="width:50%;"><input required type="text" class="form-control" name="Por_nazov" value="<?php echo $Por_nazov;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Stroj:</td>
            <td>
                <ul>
                    <div>
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
        <tr><td>Dátum vzniku:<span class="hviezdicka">*</span></td><td> <input required type="datetime-local" class="form-control" name="Por_datum_vzniku" value="<?php echo $Por_datum_vzniku;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td style = "vertical-align:top !important;">Popis:</td><td><textarea type="text" class="form-control" name="Por_popis" <?php echo disabled($akcia); ?>><?php echo $Por_popis;?></textarea></td></tr>
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
                <input type="hidden" name="PoruchaID" value="<?php echo $PoruchaID;?>">
                <input type="hidden" name="PouzivatelID_old" value="<?php echo $PouzivatelID;?>">
                <input type="hidden" name="Por_datum_pridelenia" value="<?php echo $Por_datum_pridelenia;?>">
                <input type="hidden" name="StrojID_old" value="<?php echo $StrojID;?>">
                <input type="hidden" name="Stroj_nazov_old" value="<?php echo $Stroj_nazov;?>">
                </td></tr>
    </table>
</form>

<form id="back" action="zmena_poruchy.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    var app = new Vue({
        el: '#myapp_form',
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
                    return this.machines.slice(0, 10);
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
                app.StrojID = StrojID_selected;
                this.selectedIndex = 0;
            },

            listMachine: function(){
                axios.get('read_stroje.php', {
                    params: {
                        list: "Stroj_nazov"
                    }
                })
                    .then(function (response) {
                        app.machines = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            checkStroj: function() {
                this.strojChecked = true;

                if (!this.searchInput){
                    app.StrojID = "";
                }

                if(this.searchInput!==this.oldstroj && this.searchInput!==""){ // zmenilli pouzivatelske  meno
                    axios.get(`src/search/search_stroj.php?q=${this.searchInput}`)
                        .then(function (response)  {
                            // console.log(response.data);
                            if (response.data) {
                                app.message = '';
                                app.strojChecked = true;
                                app.StrojID = response.data;
                            } else {
                                app.message = 'Zadaný stroj nie je v evidencií strojov.';
                                app.strojChecked = false;
                                app.StrojID = "";
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            app.message = 'Vyskytla sa chyba kontroly stroja. Skúste ho zadať neskôr.';
                        });
                }else if(this.searchInput==""){
                    app.message = '';
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

</body>

</html>