<?php
session_start();//phpinfo();
include "config.php";  // konfiguracia
include "lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
    echo "Nemáte práva na zmenu poruchy.";
    exit;
}

if($_POST["back"] != "Späť" && ZistiPrava("editOpravy",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu opráv.</span>";
    exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

$PoruchaID = mysqli_real_escape_string($dblink, strip_tags($_POST["PoruchaID"]));
$Por_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_nazov"])));
$Por_stav = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_stav"])));
for ($i = 0; $i <3 ; $i++) {
    

    $Opr_datum_opravy = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_datum_opravy"][$i])));
    $Opr_odpracovane_hodiny = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_odpracovane_hodiny"][$i])));
    $Opr_popis = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_popis"][$i])));
    $Opr_odpracovane_hodiny = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_odpracovane_hodiny"][$i])));
    $Cinnost_opravyID = mysqli_real_escape_string($dblink, strip_tags($_POST["Cinnost_opravyID"][$i]));
    $Cin_nazov = mysqli_real_escape_string($dblink, strip_tags($_POST["Cin_nazov"][$i]));
    $PouzivatelID = mysqli_real_escape_string($dblink, strip_tags($_POST["PouzivatelID"]));
    $PouzivatelID_porucha = mysqli_real_escape_string($dblink, strip_tags($_POST["PouzivatelID_porucha"]));
    $dokoncenaoprava = mysqli_real_escape_string($dblink, strip_tags($_POST["dokoncenaoprava"]));



    if($Opr_datum_opravy == "0000-00-00 00:00:00") $Upraveny_datum_opravy = date("Y-m-d H:i:s");
        else $Upraveny_datum_opravy = date("Y-m-d H:i:s", strtotime($Opr_datum_opravy));

    // ---------------- Idem insertovat zaznam do tabulky ------------------
if($_POST["akcia"]=="insert"  && $_POST["PoruchaID"]!="" && ($_POST["Cinnost_opravyID"]!="" OR $_POST["Cin_nazov"]!="")  && $_POST["back"] != "Späť" ):  // ak je akcia insert z hidden parametru formulara
    if(($Cinnost_opravyID  or $Cin_nazov) and (!$Opr_odpracovane_hodiny)){
        // to je v pripade ze pridali len cinnost opravy a nic ine
        header('Location:oprava.php?&PoruchaID='.$PoruchaID.'&vysledok=chyba');
        exit;

    }
    if(!$Cinnost_opravyID and $Cin_nazov){
        // ciselnik cinnosti oprav
        $sql = "INSERT INTO `cinnost_opravy` (`Cin_nazov`) VALUES ('$Cin_nazov')";
        //echo $sql;//exit;
        $vysledok = mysqli_query($dblink, $sql); /* vykonam sql prikaz select a vysledok načítame do premennej vysledok*/
        if (!$vysledok){
            $hlaska = "Chyba pri vkladani opravy! </br>";
        }
        $Cinnost_opravyID = mysqli_insert_id($dblink);

    }

    if($Cinnost_opravyID) {

        $sql = "INSERT INTO `oprava`(`Opr_popis`, `Opr_datum_opravy`,`Opr_odpracovane_hodiny`,`Cinnost_opravyID`, `PoruchaID`, `PouzivatelID`)
                VALUES ('$Opr_popis','$Upraveny_datum_opravy',$Opr_odpracovane_hodiny,$Cinnost_opravyID,$PoruchaID,$PouzivatelID)";

        $vysledok = mysqli_query($dblink, $sql); /* vykonam sql prikaz select a vysledok načítame do premennej vysledok*/

        if (!$vysledok){
            $hlaska = "Chyba pri vkladani opravy! </br>";
        }
        else
        {
            $hlaska = "<span class='oznam'>Oprava k poruche $Por_nazov bola pridaná do databázy.</span>";
        }
    }

endif;
} // koniec for cyklu
//-----------------------------------------------------------------------

if($Por_stav != 4 && $_POST["back"] != "Späť" && $_POST["PoruchaID"]!=""){ /* ZMENA STAVU */

    if($dokoncenaoprava){
        $sql = "UPDATE porucha SET Por_stav=3 WHERE PoruchaID=".$PoruchaID;

    } elseif($PouzivatelID_porucha){
        $sql = "UPDATE porucha SET Por_stav=2 WHERE PoruchaID=".$PoruchaID;
    }
    else{
        $sql = "UPDATE porucha SET Por_stav=1 WHERE PoruchaID=".$PoruchaID;
    }

    $vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
    if (!$vysledok)
    {
        $hlaska .= "Chyba updatu poruchy! </br>";
    }

}

// ---------------- Idem vymazat zaznamy do tabuluky ------------------
//phpinfo();
if($_POST['Zmazat_opravy']){
    $i=0;
    foreach($_POST['Zmazat_opravu'] as $zmazat ){
        //echo 'som tu'; echo 'zmazat premenna'.$i.'---'.$zmazat;
        if($zmazat=="zmazat" and $_POST['Oprava_ID'][$i]) {
            $OpravaID = mysqli_real_escape_string($dblink,strip_tags($_POST['Oprava_ID'][$i]));
         //   echo 'opravaID:'.$OpravaID;
            if($OpravaID){
                $sql="DELETE FROM `oprava` WHERE `OpravaID`=$OpravaID";//echo $sql; //exit;
                $vysledok = mysqli_query($dblink, $sql);
                if(!$vysledok){
                    $hlaska .= "Chyba pri vymazávaní opravy! </br>";
                }
                else $hlaska .= "&nbsp;Oprava k poruche $Por_nazov bola upravená </br>";
            }
        }
        $i++;
    }
}

/* NAHRADNE DIELY */

for ($i = 0; $i <3 ; $i++) {

    $Opr_nazov_dielu = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_nazov_dielu"][$i])));
    $Opr_mnozstvo = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Opr_mnozstvo"][$i])));
    $Opr_jednotka = mysqli_real_escape_string($dblink, strip_tags($_POST["Opr_jednotka"][$i]));
    $Nahradny_dielID = mysqli_real_escape_string($dblink, strip_tags($_POST["Nahradny_dielID"][$i]));
    $PoruchaID = mysqli_real_escape_string($dblink, strip_tags($_POST["PoruchaID"]));

    if(!$Nahradny_dielID){
        $Nahradny_dielID = NULL;
    }
//    echo "Nazov: ".$Opr_nazov_dielu."<br>";
//    echo "ID:".$Nahradny_dielID;
////    exit;

    // ---------------- Idem insertovat zaznam do tabulky ------------------
    if($_POST["akcia"]=="insert"  && $_POST["PoruchaID"]!="" && ($_POST["Nahradny_dielID"]!="" || $_POST["Opr_nazov_dielu"]!="")  && $_POST["back"] != "Späť" ):  // ak je akcia insert z hidden parametru formulara
       // phpinfo();
        if(($Nahradny_dielID  || $Opr_nazov_dielu) && (!$Opr_mnozstvo || !$Opr_jednotka)){
            // to je v pripade ze pridali len nazov dielu  a nic ine
            header('Location:oprava.php?&PoruchaID='.$PoruchaID.'&vysledok=chyba2');
            exit;
        }
        if($Nahradny_dielID){  // nepotrebujeme zapisat nazov dielu ked je v ciselniku
            $Opr_nazov_dieLu="";
        }
        if($Nahradny_dielID || $Opr_nazov_dielu) {
            $sql = "INSERT INTO `pouziva`(`Opr_nazov_dielu`, `Opr_mnozstvo`,`Opr_jednotka`,`Nahradny_dielID`, `PoruchaID`)
                VALUES ('$Opr_nazov_dielu',$Opr_mnozstvo,'$Opr_jednotka',".(is_null($Nahradny_dielID) ? "NULL" : $Nahradny_dielID).",".$PoruchaID.")";
                    //echo $sql;exit;
            $vysledok = mysqli_query($dblink, $sql); /* vykonam sql prikaz select a vysledok načítame do premennej vysledok*/
            if (!$vysledok) {
                $hlaska = "&nbsp;Chyba pri vkladani náhradneho dielu do opravy! </br>";
            } else {
                $hlaska .= "&nbsp;<span class='oznam'>Nahradné diely k poruche $Por_nazov boli pridané do databázy.</span>";
            }
        }
   endif;

} // koniec for cyklu
//-----------------------------------------------------------------------


// ---------------- Idem vymazat zaznamy nahradnych dielov  ------------------
//phpinfo();
if($_POST['Zmazat_opravy_nahradny_diel']){
    $i=0;
    foreach($_POST['Zmazat_opravu_nahradny_diel'] as $zmazat ){
        if($zmazat=="zmazat" and $_POST['PouzivaID'][$i]) {
            $PouzivaID = mysqli_real_escape_string($dblink,strip_tags($_POST['PouzivaID'][$i]));
            if($PouzivaID){
                $sql="DELETE FROM `pouziva` WHERE `PouzivaID`=$PouzivaID";//echo $sql; //exit;
                $vysledok = mysqli_query($dblink, $sql);
                if(!$vysledok){
                    $hlaska .= "Chyba pri vymazávaní nahradného dielu opravy! </br>";
                }
                else $hlaska .= "&nbsp;Náhradné diely k poruche $Por_nazov boli vymazané</br>";
            }
        }
        $i++;
    }
}

$_SESSION["hlaska"]=$hlaska;
header('Location: index.php');
?>
