<?php
session_start();
include "../../config.php";  // konfiguracia
include "../../lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
    echo "Nemáte práva na zmenu poruchy.";
    exit;
}

if($_POST["back"] != "Späť" && ZistiPrava("editPorucha",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu porúch.</span>";
    exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

$PoruchaID = mysqli_real_escape_string($dblink, strip_tags($_POST["PoruchaID"]));

$Por_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_nazov"])));
$Por_popis = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_popis"])));
$Por_stav = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_stav"])));
$Por_datum_vzniku = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_datum_vzniku"])));
$Por_datum_pridelenia = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Por_datum_pridelenia"])));

$StrojID = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["StrojID"])));
$StrojID_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["StrojID_old"])));
$Stroj_nazov_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Stroj_nazov_old"])));
$Stroj_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Stroj_nazov"])));

$PouzivatelID = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["PouzivatelID"])));
$PouzivatelID_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["PouzivatelID_old"])));

if($Stroj_nazov == $Stroj_nazov_old && $Stroj_nazov){
    $StrojID = $StrojID_old;
}
if(!$StrojID){
    $StrojID = NULL;
}
if(!$PouzivatelID){
    $PouzivatelID = NULL;
}
//phpinfo();
//exit;

if($Por_datum_vzniku == "") {
    $Upraveny_datum_vzniku = "0000-00-00 00:00:00";
}
else $Upraveny_datum_vzniku = date("y-m-d H:i:s", strtotime($Por_datum_vzniku));

if($PouzivatelID && $PouzivatelID !== $PouzivatelID_old) {
    $Upraveny_datum_pridelenia = date("y-m-d H:i:s");
} elseif($PouzivatelID && $PouzivatelID == $PouzivatelID_old){
    $Upraveny_datum_pridelenia = $Por_datum_pridelenia;
}
else $Upraveny_datum_pridelenia = "0000-00-00 00:00:00";

// ---------------- Idem insertovat zaznam do tabuluky ------------------
if($_POST["akcia"]=="insert" && $_POST["Por_nazov"]!="" && $_POST["back"] != "Späť"):  // ak je akcia insert z hidden parametru formulara a vyplnené meno nesmie byt prazdne

    if($PouzivatelID && $Por_stav == 1){
        $Por_stav = 2;
    }
    elseif(!$PouzivatelID && $Por_stav == 2){
        $Por_stav = 1;
    }

    if(!$Por_stav) $Por_stav = 1;

    $sql = "INSERT INTO `porucha` (`Por_nazov`, `Por_popis`, `Por_stav`, `Por_datum_vzniku`, `Por_datum_pridelenia`, `PoruchaID`, `StrojID`, `PouzivatelID`)
            VALUES ('$Por_nazov','$Por_popis','$Por_stav','$Upraveny_datum_vzniku','$Upraveny_datum_pridelenia',NULL,".
            (is_null($StrojID) ? "NULL" : $StrojID).",".(is_null($PouzivatelID) ? "NULL" : $PouzivatelID).")";
//    echo "SQL: ".$sql;exit;

        $vysledok = mysqli_query($dblink, $sql); /* vykonam sql prikaz select a vysledok načítame do premennej vysledok*/

    if (!$vysledok){
        $hlaska .= "Chyba pri vkladani poruchy! </br>";
    }
    else
    {
        $hlaska = "<span class='oznam'>Porucha $Por_nazov bola pridaná do databázy.</span>";
    }
    $_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

// ---------------- Idem Upravit zaznam do tabuluky ------------------
if ($_POST["akcia"]=="update" && $_POST["PoruchaID"]!="" && $_POST["back"] != "Späť"): // chcem akciu update z hidden parametru formulara a meno nesmie byt prazdne

    if(!$Por_stav) $Por_stav = 1;

    if($PouzivatelID && $Por_stav == 1){
        $Por_stav = 2;
    }
    elseif(!$PouzivatelID && $Por_stav == 2){
        $Por_stav = 1;
    }

    $sql = "UPDATE porucha SET Por_nazov='$Por_nazov', Por_popis='$Por_popis', Por_stav=$Por_stav, Por_datum_vzniku='$Upraveny_datum_vzniku', Por_datum_pridelenia='$Upraveny_datum_pridelenia',";

    if($StrojID){
        $sql .= " StrojID='$StrojID',";
    }
    else{
        $sql .= " StrojID=NULL,";
    }

    if($PouzivatelID){
        $sql .= " PouzivatelID='$PouzivatelID'";
    }
    else{
        $sql .= " PouzivatelID=NULL";
    }
    $sql .= " WHERE PoruchaID='$PoruchaID'";
//
//    echo $sql;
//    exit;

    $vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
    if (!$vysledok)
    {
        $hlaska .= "Chyba updatu poruchy! </br>";
    }
    else
    {
        $hlaska .= "<span class='oznam'>Porucha $Por_nazov bola upravená.</span>";
    }

    $_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

header('Location: ../../index.php');
?>
