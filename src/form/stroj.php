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

    if(ZistiPrava("editStroje",$dblink) == 0){
        include_once "src/partials/navbar.php"; // navigacia
        echo "<span class='oznam'>Nemáte práva na úpravu strojov.</p>";
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

        function zmazat_prilohu(poradie,nazovsuboru){
            var c = confirm("Ste si istý, že chcete zmazať prílohu "+nazovsuboru+" ?");
            if(c){
                var div_suboru='Subor_'+poradie;
                var input_suboru='Zmazat_prilohu_'+poradie;
                document.getElementById(div_suboru).style.display="none";
                document.getElementById(input_suboru).value="zmazat";
                document.getElementById('Zmazat_prilohy').value="zmazat";
            }
            return;
        }
    </script>

</head>
<body>



<?php
/*novy stoj a zmena */



// pripojenie na DB server a zapamatame si pripojenie do premennej $dblink
//$dblink = mysqli_connect($mysql_server, $mysql_user, $mysql_password, $mysql_db);

if (!$dblink) { // kontrola ci je pripojenie na db dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    die(); // ukonci vykonanie php
}

mysqli_set_charset($dblink, "utf8mb4");
$StrojID=mysqli_real_escape_string($dblink,strip_tags_html($_GET["StrojID"]));
$StrojID=intval($StrojID);

if($StrojID)
{
    $zobrazit=strip_tags_html($_GET["zobrazit"]);
    $sql="SELECT * FROM stroj  WHERE stroj.StrojID = $StrojID";

    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Stroj_nazov = strip_tags_html($riadok["Stroj_nazov"]);
    $Stroj_popis = strip_tags_html($riadok["Stroj_popis"]);
    $Stroj_datum_vyroby = strip_tags_html($riadok["Stroj_datum_vyroby"]);
    $Stroj_vyrobca = strip_tags_html($riadok["Stroj_vyrobca"]);
    $Stroj_umiestnenie = strip_tags_html($riadok["Stroj_umiestnenie"]);
    $Stroj_vyrobne_cislo = strip_tags_html($riadok["Stroj_vyrobne_cislo"]);
    $Stroj_evidencne_cislo = strip_tags_html($riadok["Stroj_evidencne_cislo"]);
    $Stroj_datum_prevzatia = strip_tags_html($riadok["Stroj_datum_prevzatia"]);
    $Stroj_zarucna_doba = strip_tags_html($riadok["Stroj_zarucna_doba"]);
    $DodavatelID = strip_tags_html($riadok["DodavatelID"]);


    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Stroj ".$Stroj_nazov;
    }
    else
    {
        // zisti prava
        $akcia = "update";
        $nadpis = "Editácia stroja ".$Stroj_nazov;
    }

    if($Stroj_datum_vyroby == "0000-00-00") $Stroj_datum_vyroby = "";
    else $Upraveny_datum_vyroby = date("d.m.Y", strtotime($Stroj_datum_vyroby));

    if($Stroj_datum_prevzatia == "0000-00-00") $Stroj_datum_prevzatia = "";
    else $Upraveny_datum_prevzatia = date("d.m.Y", strtotime($Stroj_datum_prevzatia));

}
else
{

    $akcia = "insert";
    $nadpis = "Nový stroj";
    $Stroj_nazov = "";
    $Stroj_popis = "";
    $Stroj_datum_vyroby = "";
    $Stroj_vyrobca = "";
    $Stroj_umiestnenie = "";
    $Stroj_vyrobne_cislo = "";
    $Stroj_evidencne_cislo = "";
    $Stroj_datum_prevzatia = "";
    $Stroj_zarucna_doba = "";
    $Upraveny_datum_vyroby = "";
    $Upraveny_datum_prevzatia = "";
}
?>

<h1><?php echo $nadpis; ?></h1>

<p>
<form id ="myapp_form" action="../zmena/zmena_stroja.php" method="POST" enctype="multipart/form-data" onsubmit="return app.evidencneCisloChecked" >

    <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>
     <?php
         if($_GET["vysledok"] == "chyba")
             $hlaska = "Zadané evidenčné číslo už existuje.";
     ?>
    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <tr><td colspan="2"><b>Základné údaje: </b><span class="cervene" v-if="message" >{{ message }}</span></td></tr>
        <tr><td>Názov: <span class="hviezdicka">*</span></td><td style="width:50%;"><input required type="text" class="form-control" name="Stroj_nazov" value="<?php echo $Stroj_nazov;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Popis:</td><td> <textarea type="text" class="form-control w-responsive" name="Stroj_popis"  <?php echo disabled($akcia);?> ><?php echo $Stroj_popis;?></textarea></br></td></tr>
        <tr><td>Evidenčné číslo: <span class="hviezdicka">*</span></td><td style="width:50%;"><input required type="text" class="form-control" v-model="evidencneCislo" name="Stroj_evidencne_cislo" @change="checkEvidencneCislo()" value="<?php echo $Stroj_evidencne_cislo;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Umiestnenie: <span class="hviezdicka">*</span></td>
            <td>
                <ul>
                    <div>
                        <input
                                class="form-control"
                                type="text"
                                name="Stroj_umiestnenie"
                                v-model="searchInput"
                                placeholder="Zadajte umiestnenie"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectMachinePosition(filteredMachinePositions[selectedIndex].Stroj_umiestnenie)"
                                value="<?php echo $Stroj_umiestnenie;?>"
                            <?php echo disabled($akcia);?>
                                required
                        />
                        <div class="results zoznam_pozicii" v-if="showResults && filteredMachinePositions.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredMachinePositions"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectMachinePosition(result.Stroj_umiestnenie)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Stroj_umiestnenie }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </td>
        </tr>
        <tr><td>Výrobca: <span class="hviezdicka">*</span></td><td style="width:50%;"><input required type="text" class="form-control" name="Stroj_vyrobca"   value="<?php echo $Stroj_vyrobca;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Výrobné číslo: <span class="hviezdicka">*</span></td><td> <input required type="text" class="form-control" name="Stroj_vyrobne_cislo" placeholder="" value="<?php echo $Stroj_vyrobne_cislo;?>" <?php echo disabled($akcia);?>/></br></td></tr>
        <tr><td>Dátum výroby:</td><td> <input type="text" class="form-control" id="datepicker" name="Stroj_datum_vyroby"   autocomplete="off" value="<?php echo $Upraveny_datum_vyroby;?>" <?php echo disabled($akcia);?> /></br></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Údaje o záruke:</b></td></tr>

        <tr><td style = "vertical-align:middle !important;">Dodávateľ:</td>
            <td style = "vertical-align:middle !important;">
                <?php $sql = "Select * FROM dodavatel";
                $vysledok=mysqli_query($dblink,$sql);
                if (!$vysledok):
                    echo "Doslo k chybe pri vytvarani SQL dotazu !";
                else:
                    ?>
                    <select class = "form-select" name="DodavatelID" <?php echo disabled($akcia);?>>
                            <option value="0"> </option>
                            <?php while($riadok=mysqli_fetch_assoc($vysledok)): ?>
                                <option value="<?php echo $riadok["DodavatelID"];?>" <?php echo selected($DodavatelID,$riadok["DodavatelID"]); ?>><?php echo $riadok["Dod_nazov"]; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                <?php endif; /* end Rola  */?>
            </td></tr>

        <tr><td>Dátum prevzatia:</td><td> <input type="text" class="form-control" id="datepicker2" name="Stroj_datum_prevzatia"  autocomplete="off" value="<?php echo $Upraveny_datum_prevzatia;?>" <?php echo disabled($akcia);?>/></br></td></tr>
        <tr><td>Záručná doba (počet mesiacov):</td><td> <input type="number" class="form-control" name="Stroj_zarucna_doba" value="<?php echo $Stroj_zarucna_doba;?>" <?php echo disabled($akcia);?>/></br></td></tr>
        <tr><td colspan="2"><br></td></tr>
        <?php if($StrojID): ?>
        <tr><td colspan="2"><b>Vložené prílohy:</b>
        <!-- PRILOHY ZOZNAM-->
        <?php	$sql = "Select * FROM priloha where StrojID=$StrojID";
        $vysledok=mysqli_query($dblink,$sql);
        $num_rows = zisti_pocet_riadkov($dblink,$sql);
        if (!$vysledok):
            echo "Doslo k chybe pri vyhladani priloh !";

        elseif($num_rows!=0):	?>
            <div class="mt-2 mb-2 w-100">
                <?php
                $s=0; // poradie prilohy
                while($riadok=mysqli_fetch_assoc($vysledok)):
                        $nazov_suboru=$riadok["Nazov_suboru"];
                        $nazov_suboru_skrateny = getShortFileName($nazov_suboru, 15, 5);
                        ?>
                        <div class="col-md-4" id="Subor_<?php echo $s;?>" style="display:block; float:left;">
                            <?php echo '<a href="prilohy/'.$nazov_suboru.'" title="Otvoriť" target="_blank">'.'<div style="float:left">'.$nazov_suboru_skrateny.'</div></a>';

                            if($akcia!="preview")
                                echo '<button type="button" id="prilohaikona" class="btn" title="Vymazať prílohu" style="padding-bottom: 1rem;" onclick="zmazat_prilohu('.$s.',\''.$nazov_suboru.'\')"><i class="fa-regular fa-trash-can"></i></button>';

                            ?>
                        </div>
                        <input type="text" value="" id="Zmazat_prilohu_<?php echo $s;?>" name="Zmazat_prilohu_<?php echo $s;?>" style="display:none"/>
                        <?php $s++; endwhile; ?>
                    <input type="text" value="" id="Zmazat_prilohy" name="Zmazat_prilohy" style="display:none"/>
                </div>

                <?php endif; /* end hladania */ ?>
                <!-- END PRILOHY ZOZNAM-->
                </td></tr>
        <tr><td colspan="2"><br></td></tr>
        <?php endif; ?>
        <?php if($akcia !='preview'):?>
        <tr><td colspan="2"><label for="files" class="mb-2"><b>Prílohy:</b></label><br>Vyberte jeden alebo viac súborov:<br><br>
                <input class="mb-2 form-control" type="file" id="userfile" name="userfile1" ><br>
                <input class="mb-2 form-control" type="file" id="userfile" name="userfile2" ><br>
                <input class="mb-2 form-control" type="file" id="userfile" name="userfile3" ><br>
                <input class="mb-2 form-control" type="file" id="userfile" name="userfile4" ><br>
                <input class="mb-2 form-control" type="file" id="userfile" name="userfile5" >
                <br><br></td></tr><br><br>
        <tr><td colspan="2"><br></td></tr>
        <?php endif; ?>

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
                        <input type="hidden" name="akcia" value="<?php echo $akcia;?>"/>
                        <input type="hidden" name="StrojID" value="<?php echo $StrojID;?>"/>
                        <input type="hidden" name="Stroj_evidencne_cislo_old" value="<?php echo $Stroj_evidencne_cislo;?>"/>
                    </td></tr>
    </table>
</form>

<form id="back" action="../zmena/zmena_stroja.php" method="POST"></form>
</p>
<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    var app = new Vue({
        el: '#myapp_form',
        data: {
            searchInput: "<?php echo $Stroj_umiestnenie?>",
            selectedIndex: 0,
            showResults: false,
            machinePositions: "",
            positions: "",
            message: '',
            icons: { eye: 'fa-regular fa-eye', eyeSlash: 'fa-regular fa-eye-slash' },
            iconState: true,
            holdTimeout: null,
            mouseIndex: null,
            evidencneCisloChecked: true,
            oldEvidencneCislo: '<?php echo $Stroj_evidencne_cislo; ?>',
            evidencneCislo: '<?php echo $Stroj_evidencne_cislo; ?>',
        },
        mounted: function(){
            this.listPosition();
        },
        computed: {
            filteredMachinePositions: function () {
                if (!this.searchInput) {
                    return this.machinePositions.slice(0, 10);;
                }
                let self = this;
                return self.machinePositions.filter(function (machine) {
                    return self.removeDiacritics(machine.Stroj_umiestnenie.toLowerCase())
                        .includes(self.removeDiacritics(self.searchInput.toLowerCase()))
                });
            },
            currentIcon() {
                return this.iconState ? this.icons.eyeSlash : this.icons.eye;
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
                                app.message = 'Toto evidenčné číslo sa už v systéme nachádza, skúste ho zmeniť.';
                                app.evidencneCisloChecked = false;
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
            moveSelection(step) {
                this.selectedIndex += step;
                // Ošetrite pretečenie indexu
                if (this.selectedIndex >= this.filteredMachinePositions.length) {
                    this.selectedIndex = 0;
                } else if (this.selectedIndex < 0) {
                    this.selectedIndex = this.filteredMachinePositions.length - 1;
                }
                // Odznačte označenie myšou
                this.mouseIndex = null;
            },
            mouseover(index) {
                this.mouseIndex = index;
                // Odznačte označenie klávesnice
                this.selectedIndex = null;
            },
            selectMachinePosition: function(position) {
                this.searchInput = position;
                this.showResults = false;
                this.selectedIndex = 0;
            },

            listPosition: function(){
                axios.get('../read/read_stroje.php', {
                    params: {
                        list: "Stroj_umiestnenie"
                    }
                })
                    .then(function (response) {
                        app.machinePositions = response.data;
                        console.log(machine);
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
            },

            toggleIcon() {
                this.iconState = !this.iconState;
            }
        }
    });

</script>

</body>

</html>