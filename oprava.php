<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>

    <?php include_once "head.php";

    include "config.php";
    include "lib.php";
    include 'login.php';

    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }

    if(ZistiPrava("editOpravy",$dblink) == 0){

       $zobrazit = 1;
    }

    ?>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function Zmazat_riadok(poradie,riadokID,cinnost){
            var c = confirm("Ste si istý, že chcete zmazať riadok č. "+poradie+" činnosť: "+cinnost+"?");
            if(c){
                var div_riadku='riadok_cinnost'+riadokID;
                var ikona_vymazat='ikona_vymazat'+riadokID;
                var input_riadku='Zmazat_opravu['+riadokID+']';
                var divElement = document.getElementById(div_riadku);
                var ikonaElement = document.getElementById(ikona_vymazat);
                var inputElement = document.getElementById(input_riadku);
                var zmazatOpravyElement = document.getElementById('Zmazat_opravy');
                var inputCinnost0 = document.getElementById('Cin_nazov[0]');
                var inputDatum0 = document.getElementById('Opr_datum_opravy[0]');
                var inputHod0 = document.getElementById('Opr_odpracovane_hodiny[0]');

               // console.log (input_riadku);


                if(inputElement) {
                    inputElement.value = "zmazat";
                }
                else {
                  //  console.log ("inputElement je null");
                  //  console.log (inputElement);
                }
                if(zmazatOpravyElement) {
                    zmazatOpravyElement.value="zmazat";
                }
                else {
                    //console.log ("zmazatOpravyElement je null");
                }

                if(divElement) {
                    divElement.className += " d-none";
                }
                if(ikonaElement) {
                    ikonaElement.className += " d-none";
                }
            }
        }

        function Zmazat_riadok_nahradny_diel(poradie,riadokID,nahradny_diel){
            var c = confirm("Ste si istý, že chcete zmazať riadok č. "+poradie+" náhradný diel: "+nahradny_diel+"?");
            if(c){
                var div_riadku='riadok_nahradny_diel'+riadokID;
                var ikona_vymazat='ikona_vymazat_nahradny_diel'+riadokID;
                var input_riadku='Zmazat_opravu_nahradny_diel['+riadokID+']';
                var divElement = document.getElementById(div_riadku);
                var ikonaElement = document.getElementById(ikona_vymazat);
                var inputElement = document.getElementById(input_riadku);
                var zmazatOpravyElement = document.getElementById('Zmazat_opravy_nahradny_diel');
                //console.log (input_riadku);

                if(inputElement) {
                    inputElement.value = "zmazat";
                }
                else {
                    console.log ("inputElement je null");
                    console.log (inputElement);
                }
                if(zmazatOpravyElement) {
                    zmazatOpravyElement.value="zmazat";
                }
                else {
                    console.log ("zmazatOpravyElement je null");
                }

                if(divElement) {
                    divElement.className += " d-none";
                }
                if(ikonaElement) {
                    ikonaElement.className += " d-none";
                }

            }
        }
    </script>
</head>
<body>

<?php
if (!$dblink) { // kontrola ci je pripojenie na db dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    die(); // ukonci vykonanie php
}

mysqli_set_charset($dblink, "utf8mb4");
$PoruchaID=mysqli_real_escape_string($dblink,strip_tags($_GET["PoruchaID"]));
$PoruchaID=intval($PoruchaID);

if($PoruchaID)
{
    if($zobrazit)
    {
        $akcia="preview";
        $nadpis = "Oprava k poruche č. ".$PoruchaID;
    }
    else
    {
        $akcia = "insert";
        $nadpis = "Oprava k poruche č. ".$PoruchaID;
    }
    $zobrazit=strip_tags_html($_GET["zobrazit"]);

    /* INFO O PORUCHE */
    $sql="select * from porucha p LEFT JOIN zamestnanec z ON p.PouzivatelID = z.PouzivatelID LEFT JOIN stroj s ON p.StrojID = s.StrojID WHERE p.PoruchaID = $PoruchaID";

    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);

    $Por_nazov = strip_tags_html($riadok["Por_nazov"]);
    $Por_popis = strip_tags_html($riadok["Por_popis"]);
    $Por_stav = strip_tags_html($riadok["Por_stav"]);
    $Por_datum_vzniku = strip_tags_html($riadok["Por_datum_vzniku"]);
    $Por_datum_pridelenia = strip_tags_html($riadok["Por_datum_pridelenia"]);
    $Stroj_nazov = strip_tags_html($riadok["Stroj_nazov"]);
    $StrojID = strip_tags_html($riadok["StrojID"]);
    $PouzivatelID = strip_tags_html($_SESSION["Login_ID_pouzivatela"]);
    $PouzivatelID_porucha = strip_tags_html($riadok["PouzivatelID"]);
    $RolaID = strip_tags_html($_SESSION["Login_RolaID"]);
    $MenoPrihlaseneho=strip_tags_html($_SESSION['Login_Meno_Priezvisko']);

    $dokoncenaoprava = 0;
    if($Por_stav == 3 || $Por_stav == 4){
        $dokoncenaoprava = 1;
    }

    if($Por_datum_vzniku == "0000-00-00 00:00:00") $Por_datum_vzniku = "";
    else $Por_datum_vzniku = date("Y-m-d\TH:i", strtotime($Por_datum_vzniku));

    if($Por_datum_pridelenia == "0000-00-00 00:00:00") $Por_datum_pridelenia = "";
    else $Por_datum_pridelenia = date("d.m.Y H:i", strtotime($Por_datum_pridelenia));

    /* OPRAVY */
    $Opr_datum_opravy = date("Y-m-d H:i");

    /*  starsie opravy  */
    $sql="select * from oprava o LEFT JOIN zamestnanec z ON o.PouzivatelID = z.PouzivatelID LEFT JOIN cinnost_opravy c ON o.Cinnost_opravyID = c.Cinnost_opravyID   WHERE o.PoruchaID = $PoruchaID";
    //echo $sql;
    $vysledok = mysqli_query($dblink,$sql);
    if (!$vysledok){
        $hlaska = "Chyba pri načítaní opráv z databázy! </br>";
    }
    $num_rows = mysqli_num_rows($vysledok);
    for ($i = 0; $i < $num_rows; $i++) {
            $riadok = mysqli_fetch_assoc($vysledok);
            $Zapisane_Opr_popis[$i] = strip_tags_html($riadok["Opr_popis"]);
            $Zapisane_Opr_datum_opravy[$i] = strip_tags_html($riadok["Opr_datum_opravy"]);
            if ($Zapisane_Opr_datum_opravy[$i] == "0000-00-00 00:00") $Zapisane_Opr_datum_opravy[$i] = "";
            else $Zapisane_Opr_datum_opravy[$i] = date("d.m.Y H:i", strtotime($Zapisane_Opr_datum_opravy[$i]));
            $Zapisane_Opr_odpracovane_hodiny[$i] = strip_tags_html($riadok["Opr_odpracovane_hodiny"]);
            $Zapisane_MenoPriezvisko[$i] = strip_tags_html($riadok["Zam_meno"]) . '&nbsp;' . strip_tags_html($riadok["Zam_priezvisko"]);
            $Zapisane_PouzivatelID[$i] = strip_tags_html($riadok["PouzivatelID"]);
            $Zapisane_Cin_nazov[$i] = strip_tags_html($riadok["Cin_nazov"]);
            $OpravaID[$i] = strip_tags_html($riadok["OpravaID"]);
    }

    /*  starsie pouzite nahradne diely  */
    $sql="select * from pouziva p LEFT JOIN nahradny_diel n ON p.Nahradny_dielID = n.Nahradny_dielID   WHERE p.PoruchaID = $PoruchaID";
    //echo $sql;
    $vysledok = mysqli_query($dblink,$sql);
    if (!$vysledok){
        $hlaska = "Chyba pri načítaní nahradných dielov na oprave z databázy! </br>";
    }
    $num_rows_nd = mysqli_num_rows($vysledok);
    for ($i = 0; $i < $num_rows_nd; $i++) {
        $riadok = mysqli_fetch_assoc($vysledok);
        $Zapisane_Opr_nazov_dielu[$i] = strip_tags_html($riadok["Opr_nazov_dielu"]);
        $Zapisane_Opr_jednotka[$i] = strip_tags_html($riadok["Opr_jednotka"]);
        $Zapisane_Opr_mnozstvo[$i] = strip_tags_html($riadok["Opr_mnozstvo"]);
        $Nahradny_dielID[$i] = strip_tags_html($riadok["Nahradny_dielID"]);
        $PouzivaID[$i] = strip_tags_html($riadok["PouzivaID"]);
        if(!$Zapisane_Opr_nazov_dielu[$i]){
            $Zapisane_Opr_nazov_dielu[$i]=strip_tags_html($riadok["Diel_nazov"]);// nazov, co najde v tabulke nahradne_diely
        }
        if(!$Zapisane_Opr_jednotka[$i]){
            $Zapisane_Opr_jednotka[$i]=strip_tags_html($riadok["Diel_jednotka"]);// jednotka, co najde v tabulke nahradne_diely
        }
    }
}
else
{
    include_once "navbar.php";
    echo '<span class="oznam cervene text-start">Nebola nájdená porucha ku oprave</span>';
    exit;
}
?>

<h1 id="nadpisoprava"><?php echo $nadpis; ?></h1>
<p>
<form id ="myapp_form_oprava" action="zmena_opravy.php" method="POST" onsubmit="">

    <?php if($Por_datum_pridelenia): ?>
        <div class="container d-flex align-items-center">
            <p class="oznam text-grey text-start" style="margin-left:-0.625rem!important;">Položky označené <span class="red bold">*</span> sú povinné</p>
            <p class="oznam text-grey text-end " style="margin-left:2rem !important;">Dátum posledného pridelenia: <?php echo $Por_datum_pridelenia;?></p>
        </div>
    <?php else: ?>
        <p class="oznam text-grey text-start">Položky označené <span class="red bold">*</span> sú povinné</p>
    <?php endif; ?>

    <?php
    if(strip_tags_html($_GET["vysledok"]) == "chyba")
        $hlaska = "Chyba pri uložení opravy. Je potrebné vyplniť počet odpracovaných hodín.";
    if(strip_tags_html($_GET["vysledok"]) == "chyba2")
        $hlaska = "Chyba pri uložení opravy. Je potrebné vyplniť množstvo a jednotku na náhradných dieloch.";
    ?>
    <p class="oznam red text-start"><?php echo $hlaska;?></p>

    <table class="zoznam">
        <tr><td colspan="2"><b>Základné údaje: </b></td></tr>
        <tr><td>Názov:</td><td style="width:50%;"><input disabled  type="text" class="form-control" name="Por_nazov" value="<?php echo $Por_nazov;?>" <?php echo disabled($akcia);?>/></td></tr>
        <tr><td>Stroj:</td>
            <td>

                    <div>
                        <input
                                disabled
                                class="form-control"
                                type="text"
                                id="$Stroj_nazov"
                                value="<?php echo $Stroj_nazov;?>"
                        />
                    </div>

            </td>
        </tr>
        <tr><td>Dátum vzniku:</td><td> <input disabled type="datetime-local" class="form-control" name="Por_datum_vzniku" value="<?php echo $Por_datum_vzniku;?>" <?php echo disabled($akcia);?>></br></td></tr>
        <tr><td style = "vertical-align:top !important;">Popis:</td><td><textarea disabled type="text" class="form-control" name="Por_popis" <?php echo disabled($akcia); ?>><?php echo $Por_popis;?></textarea></td></tr>
        <tr><td colspan="2"><b>Údaje o oprave:</b><br></td><tr></tr>

        <tr><td colspan="2" ><div class="zoznam_div">
                <div class="container-fluid d-flex col-md-12 col-12 flex-column justify-content-center">
                    <div class="row riadokopravy align-items-center">
                        <div class="col-md-3 col-6 text-center" >Činnosť opravy</div>
                        <div class="col-md-2 col-6 text-center">Popis</div>
                        <div class="col-md-2 col-6 text-center" >Odpracované hodiny</div>
                        <div class="col-md-2 col-6 text-center" >Dátum a čas opravy</div>
                        <div class="col-md-2 col-6 text-center">Zamestnanec</div>
                        <div class="col-md-1 col-6 text-center"></div>
                    </div>

                    <?php for ($i = 0; $i < $num_rows; $i++) { ?>
                       <div  id="riadok_cinnost<?php echo $i?>" style="display:block;" class="row riadokopravy d-flex  col-md-12 align-items-center justify-content-center" >
                            <div  class="col-md-3 col-6 d-flex justify-content-center align-items-center" >
                                <?php echo $Zapisane_Cin_nazov[$i]; ?>
                            </div>

                            <div class="col-md-2 col-6 text-center d-flex justify-content-center align-items-center" >
                                <?php echo $Zapisane_Opr_popis[$i]; ?>
                            </div>
                           <div class="col-md-2 col-6 text-center d-flex justify-content-center align-items-center">
                               <?php echo $Zapisane_Opr_odpracovane_hodiny[$i]; ?>
                           </div>

                           <div class="col-md-2 col-6 d-flex justify-content-center align-items-center">
                               <?php echo $Zapisane_Opr_datum_opravy[$i]; ?>
                           </div>

                           <div class="col-md-2 col-6 text-center d-flex justify-content-center align-items-center" >
                                <?php echo $Zapisane_MenoPriezvisko[$i]; ?>
                           </div>
                           <div class="col-md-1 col-6 col-sm-6 text-center">
                                 <?php
                                if($akcia!="preview" and ($RolaID==1 or $PouzivatelID==$Zapisane_PouzivatelID[$i])){
                                    ?>
                                    <button <?php echo disabled($akcia);?> type="button" id="ikona_vymazat<?php echo $i;?>" class="btn" title="Vymazať riadok" style="padding-bottom: 1rem;" onclick="Zmazat_riadok('<?php echo $i+1;?>','<?php echo $i;?>','<?php echo $Zapisane_Cin_nazov[$i];?>')" >
                                        <i class="fa-regular fa-trash-can"></i></button>
                                    <input type="text" value="" id="Zmazat_opravu[<?php echo $i;?>]" name="Zmazat_opravu[<?php echo $i;?>]" style="display:none"/>
                                    <input type="hidden" name="Oprava_ID[<?php echo $i; ?>]" value="<?php echo $OpravaID[$i];?>" />
                                <?php }?>
                           </div>
                        </div>

                    <?php } /* starsie opravy koniec  */  ?>
                </div>
                    <br><br>
                <div class="container-fluid d-flex col-md-12 col-12 flex-column justify-content-center">
                 <input type="text" value="" id="Zmazat_opravy" name="Zmazat_opravy" style="display:none"/>
                <!--riadok novej opravy-->
                <div id ="row0" class="row riadokopravy d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >

                <div class="col-md-3 col-6 col-sm-12 d-flex justify-content-center align-items-center" >
                    <div>
                        <input
                                class="form-control"
                                type="text"
                                name="Cin_nazov[0]"
                                id="Cin_nazov[0]"
                                v-model="searchInput"
                                placeholder="Zadajte činnosť opravy"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"

                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Cin_nazov,filteredMachine[selectedIndex].Cinnost_opravyID)"
                                value="<?php echo $Cin_nazov[0];?>"
                            <?php echo disabled($akcia);?>
                        />
                        <input type="hidden" name="Cinnost_opravyID[0]" v-model="Cinnost_opravyID">
                        <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredMachine"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectMachine(result.Cin_nazov,result.Cinnost_opravyID)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Cin_nazov }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                   <textarea  type="text" placeholder="Zadajte popis" class="popisoprava form-control " name="Opr_popis[]" <?php echo disabled($akcia); ?>><?php echo $Opr_popis;?></textarea>
                </div>
                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                    <input   placeholder="hod" type="number" min="1" max="999" class="odpracovanehodiny form-control" id="Opr_odpracovane_hodiny[0]" name="Opr_odpracovane_hodiny[]" value="<?php echo $Opr_odpracovane_hodiny;?>" <?php echo disabled($akcia);?>/>
                </div>

                <div class="col-md-2 col-6 col-sm-12 d-flex justify-content-center align-items-center">
                    <input type="datetime-local" class="datumoprava form-control" id="Opr_datum_opravy[0]" name="Opr_datum_opravy[]" value="<?php echo $Opr_datum_opravy;?>" <?php echo disabled($akcia);?>>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                    <?php echo $MenoPrihlaseneho; ?>
                </div>

                <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >

                </div>

            </div>
            <!--riadok novej opravy koniec -->

            <!--riadok 2 novej opravy -->
            <div id ="row1" class="row riadokopravy  d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >

                <div class="col-md-3 col-6 d-flex justify-content-center align-items-center" >
                    <div>
                        <input
                                class="form-control"
                                type="text"
                                name="Cin_nazov[1]"
                                v-model="searchInput"
                                placeholder="Zadajte činnosť opravy"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"
                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Cin_nazov,filteredMachine[selectedIndex].Cinnost_opravyID)"
                                value="<?php echo $Cin_nazov[1];?>"
                            <?php echo disabled($akcia);?>
                        />
                        <input type="hidden" name="Cinnost_opravyID[1]" v-model="Cinnost_opravyID">
                        <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredMachine"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectMachine(result.Cin_nazov,result.Cinnost_opravyID)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Cin_nazov }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                    <textarea type="text" placeholder="Zadajte popis" class="popisoprava form-control " name="Opr_popis[]" <?php echo disabled($akcia); ?>><?php echo $Opr_popis;?></textarea>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                     <input  placeholder="hod"  type="number" min="1" max="999" class="odpracovanehodiny form-control" name="Opr_odpracovane_hodiny[]" value="<?php echo $Opr_odpracovane_hodiny;?>" <?php echo disabled($akcia);?>/>
                </div>

                <div class="col-md-2 col-6 col-sm-12 d-flex justify-content-center align-items-center">
                     <input type="datetime-local"  class=" datumoprava form-control" name="Opr_datum_opravy[]" value="<?php echo $Opr_datum_opravy;?>" <?php echo disabled($akcia);?>>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                      <?php echo $MenoPrihlaseneho; ?>
                </div>
                <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >

                </div>
            </div>
            <!--riadok 2 novej opravy koniec -->
            <!--riadok 3 novej opravy-->
            <div id ="row2" class="row riadokopravy  d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >

                <div class="col-md-3 col-6 col-sm-12 d-flex justify-content-center align-items-center" >
                    <div>
                        <input
                                class="form-control"
                                type="text"
                                name="Cin_nazov[2]"
                                v-model="searchInput"
                                placeholder="Zadajte činnosť opravy"
                                autocomplete="off"
                                @focus="showResults = true"
                                @blur="showResults = false"

                                @keydown.down.prevent="moveSelection(1)"
                                @keydown.up.prevent="moveSelection(-1)"
                                @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Cin_nazov,filteredMachine[selectedIndex].Cinnost_opravyID)"
                                value="<?php echo $Cin_nazov[2];?>"
                            <?php echo disabled($akcia);?>
                        />
                        <input type="hidden" name="Cinnost_opravyID[2]" v-model="Cinnost_opravyID">
                        <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                            <ul>
                                <li
                                        class="pozicie"
                                        v-for="(result, index) in filteredMachine"
                                        :key="index"
                                        v-if="index < 5"
                                        @mouseover="mouseover(index)"
                                        @click="selectMachine(result.Cin_nazov,result.Cinnost_opravyID)"
                                        @mousedown.prevent
                                        :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                >
                                    {{ result.Cin_nazov }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                    <textarea type="text" placeholder="Zadajte popis" class="popisoprava form-control " name="Opr_popis[]" <?php echo disabled($akcia); ?>><?php echo $Opr_popis;?></textarea>
                </div>
                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                    <input placeholder="hod"  type="number" min="1" max="999" class="odpracovanehodiny form-control" name="Opr_odpracovane_hodiny[]" value="<?php echo $Opr_odpracovane_hodiny;?>" <?php echo disabled($akcia);?>/>
                </div>

                <div class="col-md-2 col-6 col-sm-12 d-flex justify-content-center align-items-center">
                    <input  type="datetime-local"  class=" datumoprava form-control" name="Opr_datum_opravy[]" value="<?php echo $Opr_datum_opravy;?>" <?php echo disabled($akcia);?>>
                </div>
                <div class="col-md-2 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                    <?php echo $MenoPrihlaseneho;?>
                </div>
                <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center" >

                </div>
            </div>
        </div>
            <!--riadok 3 novej opravy koniec -->

        </td></tr>
        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2"><b>Údaje o použitých náhradných dieloch :</b><br></td><tr></tr>
        <tr><td colspan="2">
        <div class="zoznam_div">
            <div class="container-fluid d-flex col-md-12 col-12 flex-column justify-content-center">
                      <div class="row align-items-center">
                        <div class="col-md-3 col-12 text-center">Názov dielu</div>
                        <div class="col-md-3 col-12 text-center">Množstvo a jednotka</div>
                        <div class="col-md-1 col-6 text-center"></div>
                        <div class="col-md-5 col-6 text-center"></div>
                      </div><br>
                <?php for ($i = 0; $i < $num_rows_nd; $i++) { ?>
                    <div  id="riadok_nahradny_diel<?php echo $i?>" style="display:block;" class="row riadokopravy d-flex col-md-12 align-items-center justify-content-center" >
                        <div  class="col-md-3 col-6 d-flex text-center justify-content-center align-items-center" >
                            <?php echo $Zapisane_Opr_nazov_dielu[$i]; ?>
                        </div>
                        <div class="col-md-3 col-6 text-center d-flex justify-content-center align-items-center" >
                            <?php echo $Zapisane_Opr_mnozstvo[$i]; echo " "; echo $Zapisane_Opr_jednotka[$i]; ?>
                        </div>
                        <div class="col-md-1 col-12 text-center d-flex justify-content-center align-items-center">
                        <?php
                        if($akcia!="preview"){
                         ?>
                           <button type="button" id="ikona_vymazat_nahradny_diel<?php echo $i;?>" class="btn" title="Vymazať riadok" style="padding-bottom: 1rem;" onclick="Zmazat_riadok_nahradny_diel('<?php echo $i+1;?>','<?php echo $i;?>','<?php echo $Zapisane_Opr_nazov_dielu[$i];?>')">
                                <i class="fa-regular fa-trash-can"></i></button>
                                <input type="text" value="" id="Zmazat_opravu_nahradny_diel[<?php echo $i;?>]" name="Zmazat_opravu_nahradny_diel[<?php echo $i;?>]" style="display:none"/>
                                <input type="hidden" name="PouzivaID[<?php echo $i; ?>]" value="<?php echo $PouzivaID[$i];?>" />
                         <?php }?>
                        </div>
                        <div class="col-md-5 col-6 text-center d-flex justify-content-center align-items-center">
                        <!-- Prazdny -->
                        </div>
                    </div> <!-- riadok koniec -->
            </div><!-- container koniec -->

                <?php } /* starsie nahradne diely koniec  */  ?>
                 <br><br>
                <input type="text" value="" id="Zmazat_opravy_nahradny_diel" name="Zmazat_opravy_nahradny_diel" style="display:none"/>

                <div class="container-fluid d-flex col-md-12 col-12 flex-column justify-content-center">
                <!-- riadok 1 nahradneho dielu -->
                      <div id ="row0_nahradny_diel" class="row riadokopravy d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >
                            <div class="col-md-3 col-12 col-sm-12 d-flex justify-content-center align-items-center" >
                                <div>
                                    <input
                                         class="form-control"
                                         type="text"
                                         name="Opr_nazov_dielu[]"
                                         id="Opr_nazov_dielu[0]"
                                         v-model="searchInput"
                                         placeholder="Zadajte náhradný diel"
                                         autocomplete="off"
                                         @focus="showResults = true"
                                         @blur="showResults = false"
                                         @keydown.down.prevent="moveSelection(1)"
                                         @keydown.up.prevent="moveSelection(-1)"
                                         @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Diel_nazov,filteredMachine[selectedIndex].Nahradny_dielID,filteredMachine[selectedIndex].Diel_jednotka)"
                                         value="<?php echo $Opr_nazov_dielu[0];?>"
                                         <?php echo disabled($akcia);?>
                                    />
                                    <input type="hidden" name="Nahradny_dielID[0]" v-model="Nahradny_dielID">
                                         <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                                             <ul>
                                                 <li
                                                    class="pozicie"
                                                    v-for="(result, index) in filteredMachine"
                                                    :key="index"
                                                    v-if="index < 5"
                                                    @mouseover="mouseover(index)"
                                                    @click="selectMachine(result.Diel_nazov,result.Nahradny_dielID,result.Diel_jednotka)"
                                                    @mousedown.prevent
                                                    :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                                  >
                                                  {{ result.Diel_nazov }}
                                                  </li>
                                             </ul>
                                         </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                                 <input  type="number" min="1" max="999" placeholder="" class="form-control mnozstvo" id="Opr_mnozstvo[0]" name="Opr_mnozstvo[]"  value="<?php echo $Opr_mnozstvo;?>"  <?php echo disabled($akcia); ?> />
                                 &nbsp;<input v-model="jednotka" placeholder="Napr.ks" type="text" class="form-control mnozstvo" id="Opr_jednotka[0]" name="Opr_jednotka[]" value="<?php echo $Opr_jednotka;?>" <?php echo disabled($akcia);?> />
                            </div>
                            <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                                <!--kos-->
                            </div>
                            <div class="col-md-5 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                                <!--prazdny -->
                            </div>

                      </div>
                    <!--riadok 1 nahradneho dielu koniec -->

                    <!-- riadok 2 nahradneho dielu -->
                    <div id ="row1_nahradny_diel" class="row riadokopravy  d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >

                        <div class="col-md-3 col-12 col-sm-12 d-flex justify-content-center align-items-center" >
                              <div>
                                   <input
                                         class="form-control"
                                         type="text"
                                         name="Opr_nazov_dielu[]"
                                         id="Opr_nazov_dielu[1]"
                                         v-model="searchInput"
                                         placeholder="Zadajte náhradný diel"
                                         autocomplete="off"
                                         @focus="showResults = true"
                                         @blur="showResults = false"
                                         @keydown.down.prevent="moveSelection(1)"
                                         @keydown.up.prevent="moveSelection(-1)"
                                         @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Diel_nazov,filteredMachine[selectedIndex].Nahradny_dielID,filteredMachine[selectedIndex].Diel_jednotka)"
                                         value="<?php echo $Opr_nazov_dielu[0];?>"
                                         <?php echo disabled($akcia);?>
                                   />
                                    <input type="hidden" name="Nahradny_dielID[1]" v-model="Nahradny_dielID">
                                    <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                                        <ul>
                                             <li
                                                class="pozicie"
                                                v-for="(result, index) in filteredMachine"
                                                :key="index"
                                                v-if="index < 5"
                                                @mouseover="mouseover(index)"
                                                @click="selectMachine(result.Diel_nazov,result.Nahradny_dielID,result.Diel_jednotka)"
                                                @mousedown.prevent
                                                :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                             >
                                               {{ result.Diel_nazov }}
                                            </li>
                                        </ul>
                                    </div>
                              </div>
                        </div>

                        <div class="col-md-3 col-12 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                               <input type="number" min="1" max="999" placeholder="" class="form-control mnozstvo"  name="Opr_mnozstvo[]"  value="<?php echo $Opr_mnozstvo[$i];?>"  <?php echo disabled($akcia); ?> />
                               &nbsp;<input v-model="jednotka" placeholder="Napr.ks" type="text" class="form-control mnozstvo"  name="Opr_jednotka[]" value="<?php echo $Opr_jednotka[$i];?>" <?php echo disabled($akcia);?> />
                        </div>
                        <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                            <!--kos -->
                        </div>
                        <div class="col-md-5 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                            <!--prazdny -->
                        </div>
                    </div>
                    <!--riadok 2 noveho nahradneho dielu koniec -->
                    <!-- riadok 3 nahradneho dielu -->
                    <div id ="row2_nahradny_diel" class="row riadokopravy  d-flex col-sm-12 col-md-12 align-items-center justify-content-center" >
                        <div class="col-md-3 col-12  col-sm-12 d-flex justify-content-center align-items-center" >
                            <div>
                                <input
                                   class="form-control"
                                   type="text"
                                   name="Opr_nazov_dielu[]"
                                   v-model="searchInput"
                                   placeholder="Zadajte náhradný diel"
                                   autocomplete="off"
                                   @focus="showResults = true"
                                   @blur="showResults = false"
                                   @keydown.down.prevent="moveSelection(1)"
                                   @keydown.up.prevent="moveSelection(-1)"
                                   @keydown.enter.prevent="selectMachine(filteredMachine[selectedIndex].Diel_nazov,filteredMachine[selectedIndex].Nahradny_dielID,filteredMachine[selectedIndex].Diel_jednotka)"
                                   value="<?php echo $Opr_nazov_dielu[2];?>"
                                   <?php echo disabled($akcia);?>
                                            />
                                  <input type="hidden" name="Nahradny_dielID[2]" v-model="Nahradny_dielID">
                                      <div class="results zoznam_pozicii" v-if="showResults && filteredMachine.length > 0">
                                          <ul>
                                              <li
                                                class="pozicie"
                                                v-for="(result, index) in filteredMachine"
                                                :key="index"
                                                v-if="index < 5"
                                                @mouseover="mouseover(index)"
                                                @click="selectMachine(result.Diel_nazov,result.Nahradny_dielID,result.Diel_jednotka)"
                                                @mousedown.prevent
                                                :class="{ selected: selectedIndex === index || mouseIndex === index }"
                                              >
                                               {{ result.Diel_nazov }}
                                              </li>
                                          </ul>
                                      </div>
                                </div>
                        </div>

                        <div class="col-md-3 col-12 col-sm-12 text-center d-flex justify-content-center align-items-center" >
                              <input  type="number" min="1" max="999" placeholder="" class="form-control mnozstvo"   name="Opr_mnozstvo[]"  value="<?php echo $Opr_mnozstvo;?>"  <?php echo disabled($akcia); ?> />
                              &nbsp;<input v-model="jednotka" placeholder="Napr.ks" type="text" class="form-control mnozstvo"  name="Opr_jednotka[]" value="<?php echo $Opr_jednotka;?>" <?php echo disabled($akcia);?> />
                        </div>
                        <div class="col-md-1 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                        </div>
                        <div class="col-md-5 col-6 col-sm-12 text-center d-flex justify-content-center align-items-center">
                        </div>
                    </div>
                    <!--riadok 3 noveho nahradneho dielu koniec -->
                </div> <!-- KONIEC CONTAINER  -->


        </div>
        <!--KONIEC DIV ZOZNAM-->
        </td></tr>
        <tr><td colspan="2"><br></td></tr>

        <tr><td colspan="2">
                <div class="form-check ms-4">
                    <input class="form-check-input" type="checkbox" id="dokoncenaoprava" name="dokoncenaoprava" <?php if($dokoncenaoprava) echo "checked" ?> <?php echo disabled($akcia);?>>
                    <label class="form-check-label" for="flexCheckDefault">
                        Oprava je dokončená
                    </label>
                </div>
        </td></tr

        <tr><td colspan="2"><br></td></tr>
        <tr><td colspan="2">
        <div>
            <!-- SUBMIT  -->
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
            <input type="hidden" name="Por_nazov" value="<?php echo $Por_nazov;?>">
            <input type="hidden" name="Por_stav" value="<?php echo $Por_stav;?>">
            <input type="hidden" name="PouzivatelID" value="<?php echo $PouzivatelID;?>">
            <input type="hidden" name="PouzivatelID_porucha" value="<?php echo $PouzivatelID_porucha;?>">
        </div>
        </td></tr>
    </table>

</form>

<form id="back" action="zmena_opravy.php" method="POST"></form>

<?php
mysqli_close($dblink); // odpojit sa z DB
?>

<script>
    /* vue objekt */
    var app0 = new Vue({
        el: '#row0',
        data: {
            searchInput: "<?php //echo $Cin_nazov[0]?>",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Cinnost_opravyID: "",

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
                    return self.removeDiacritics(machine.Cin_nazov.toLowerCase())
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
            selectMachine: function(machine,CinnostID_selected) {
                this.searchInput = machine;
                this.showResults = false;
                app0.Cinnost_opravyID = CinnostID_selected;
                this.selectedIndex = 0;
            },

            listMachine: function(){
                axios.get('read_cinnosti.php', {
                    params: {
                        list: "Cin_nazov"
                    }
                })
                    .then(function (response) {
                        app0.machines = response.data;

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
        }
    });
    /* koniec vue objekt */

    /* vue objekt */
    var app1 = new Vue({
        el: '#row1',
        data: {
            searchInput: "",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Cinnost_opravyID:"",

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
                    return self.removeDiacritics(machine.Cin_nazov.toLowerCase())
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
            selectMachine: function(machine,CinnostID_selected) {
                this.searchInput = machine;
                this.showResults = false;
                app1.Cinnost_opravyID = CinnostID_selected;
                this.selectedIndex = 0;
            },

            listMachine: function(){
                axios.get('read_cinnosti.php', {
                    params: {
                        list: "Cin_nazov"
                    }
                })
                    .then(function (response) {
                        app1.machines = response.data;

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
        }
    });
    /* koniec vue objekt */

    var app2 = new Vue({
        el: '#row2',
        data: {
            searchInput: "",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Cinnost_opravyID:"",

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
                    return self.removeDiacritics(machine.Cin_nazov.toLowerCase())
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
            selectMachine: function(machine,CinnostID_selected) {
                this.searchInput = machine;
                this.showResults = false;
                app2.Cinnost_opravyID = CinnostID_selected;
                this.selectedIndex = 0;
            },

            listMachine: function(){
                axios.get('read_cinnosti.php', {
                    params: {
                        list: "Cin_nazov"
                    }
                })
                    .then(function (response) {
                        app2.machines = response.data;

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
        }
    });
    /* koniec vue objekt */


    /* vue objekt */
    var app0nd = new Vue({
        el: '#row0_nahradny_diel',
        data: {
            searchInput: "<?php //echo $Opr_nazov_dielu[0]?>",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Nahradny_dielID:"<?php //echo $Nahradny_dielID[0]?>",
            jednotka: "",
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
                    return self.removeDiacritics(machine.Diel_nazov.toLowerCase())
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
            selectMachine: function(machine,Nahradny_dielID_selected,Diel_jednotka) {
                this.searchInput = machine;
                this.showResults = false;
                app0nd.Nahradny_dielID = Nahradny_dielID_selected;
                //alert(app0nd.Nahradny_dielID);
                this.selectedIndex = 0;
                app0nd.jednotka = Diel_jednotka;
            },

            listMachine: function(){
                axios.get('read_nahradne_diely.php', {
                    params: {
                        list_nahradne_diely: "Diel_nazov"
                    }
                })
                .then(function (response) {
                app0nd.machines = response.data;

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
        }
    });
    /* koniec vue objekt 0nd */
    /* vue objekt app1nd */
    var app1nd = new Vue({
        el: '#row1_nahradny_diel',
        data: {
            searchInput: "<?php //echo $Opr_nazov_dielu[1]?>",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Nahradny_dielID:"<?php //echo $Nahradny_dielID[1]?>",
            jednotka: "",
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
                    return self.removeDiacritics(machine.Diel_nazov.toLowerCase())
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
            selectMachine: function(machine,Nahradny_dielID_selected,Diel_jednotka) {
                this.searchInput = machine;
                this.showResults = false;
                app1nd.Nahradny_dielID = Nahradny_dielID_selected;
                this.selectedIndex = 0;
                app1nd.jednotka = Diel_jednotka;
            },

            listMachine: function(){
                axios.get('read_nahradne_diely.php', {
                    params: {
                        list_nahradne_diely: "Diel_nazov"
                    }
                })
                    .then(function (response) {
                        app1nd.machines = response.data;

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
        }
    });
    /* koniec vue objekt app1nd*/
    /* vue objekt app2nd */
    var app2nd = new Vue({
        el: '#row2_nahradny_diel',
        data: {
            searchInput: "<?php //echo $Opr_nazov_dielu[1]?>",
            selectedIndex: 0,
            showResults: false,
            machines: "",
            message: '',
            holdTimeout: null,
            mouseIndex: null,
            Nahradny_dielID:"<?php //echo $Nahradny_dielID[1]?>",
            jednotka: "",
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
                    return self.removeDiacritics(machine.Diel_nazov.toLowerCase())
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
            selectMachine: function(machine,Nahradny_dielID_selected,Diel_jednotka) {
                this.searchInput = machine;
                this.showResults = false;
                app2nd.Nahradny_dielID = Nahradny_dielID_selected;
                this.selectedIndex = 0;
                app2nd.jednotka = Diel_jednotka;
            },

            listMachine: function(){
                axios.get('read_nahradne_diely.php', {
                    params: {
                        list_nahradne_diely: "Diel_nazov"
                    }
                })
                    .then(function (response) {
                        app2nd.machines = response.data;

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
        }
    });
    /* koniec vue objekt app2nd*/





</script>

</body>

</html>