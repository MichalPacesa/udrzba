<?php
session_start();
include "config.php";  // konfiguracia
include "lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
    echo "Chyba pripojenia na DB!</br>";
    exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
    echo "Nemáte práva na zmenu nahradneho dielu.";
    exit;
}

if($_POST["back"] != "Späť"){
    if(ZistiPrava("editNahradneDiely",$dblink) == 0){

        echo "<span class='oznam cervene'>Nemáte práva na úpravu náhradnych dielov.</span>";
        exit;
    }
}

$hlaska="";
$_SESSION["hlaska"] = "";

$Nahradny_dielID = mysqli_real_escape_string($dblink, strip_tags($_POST["Nahradny_dielID"]));

$Diel_evidencne_cislo = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_evidencne_cislo"])));
$Diel_evidencne_cislo_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_evidencne_cislo_old"])));
$Diel_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_nazov"])));
$Diel_popis = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_popis"])));
$Diel_jednotka = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_jednotka"])));
$Diel_mnozstvo = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_mnozstvo"])));
$Diel_umiestnenie = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_umiestnenie"])));
$Diel_datum_prevzatia = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_datum_prevzatia"])));
$Diel_zarucna_doba = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Diel_zarucna_doba"])));

$KategoriaID = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["KategoriaID"])));
$Kat_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Kat_nazov"])));
$KategoriaID_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["KategoriaID_old"])));
$Kat_nazov_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Kat_nazov_old"])));

$StrojID = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["StrojID"])));
$Stroj_nazov = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Stroj_nazov"])));
$StrojID_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["StrojID_old"])));
$Stroj_nazov_old = mysqli_real_escape_string($dblink, trim(strip_tags($_POST["Stroj_nazov_old"])));

if($Stroj_nazov == $Stroj_nazov_old && $Stroj_nazov){ /* Ked sa nazov stroja nezmenil */
    $StrojID = $StrojID_old;
}

if($Kat_nazov == $Kat_nazov_old && $Kat_nazov){ /* Ked sa nazov stroja nezmenil */
    $KategoriaID = $KategoriaID_old;
}


if(!$StrojID){
    $StrojID = NULL;
}

if($Diel_datum_prevzatia == "")
    $Upraveny_datum_prevzatia = "0000-00-00";
else
    $Upraveny_datum_prevzatia = date("y-m-d", strtotime($Diel_datum_prevzatia));

//echo "Diel_evidencne_cislo: " . $Diel_evidencne_cislo . "<br>";
//echo "Diel_evidencne_cislo stare: " . $Diel_evidencne_cislo_old . "<br>";
//echo "Diel_nazov: " . $Diel_nazov . "<br>";
//echo "Diel_popis: " . $Diel_popis . "<br>";
//echo "Diel_jednotka: " . $Diel_jednotka . "<br>";
//echo "Diel_mnozstvo: " . $Diel_mnozstvo . "<br>";
//echo "Diel_umiestnenie: " . $Diel_umiestnenie . "<br>";
//echo "Diel_datum_prevzatia: " . $Diel_datum_prevzatia . "<br>";
//echo "Diel_datum_prevzatia upraveny: " . $Upraveny_datum_prevzatia . "<br>";
//echo "Diel_zarucna_doba: " . $Diel_zarucna_doba . "<br>";
//
//echo "KategoriaID: " . $KategoriaID . "<br>";
//echo "Kat_nazov: " . $Kat_nazov . "<br>";
//
//echo "StrojID: " . $StrojID . "<br>";
//echo "StrojID_old: " . $StrojID_old . "<br>";
//echo "Stroj_nazov_old: " . $Stroj_nazov_old . "<br>";
//echo "Stroj_nazov: " . $Stroj_nazov . "<br>";
//exit;



// ---------------- Idem insertovat zaznam do tabuluky ------------------
if($_POST["akcia"]=="insert" && $_POST["Diel_evidencne_cislo"]!="" && $_POST["back"] != "Späť"):  // ak je akcia insert z hidden parametru formulara a vyplnené meno nesmie byt prazdne

    if(!$KategoriaID){
        $sql_kat = "INSERT INTO `kategoria` (`Kat_nazov`,`KategoriaID`) VALUES ('$Kat_nazov',NULL)";
        $vysledok = mysqli_query($dblink, $sql_kat);
        if (!$vysledok){
            $hlaska .= "Chyba pri vkladani kategorie! </br>";
        }
        $KategoriaID = mysqli_insert_id($dblink);
    }

    /*kontrola evidencneho cisla*/
    $sql = "SELECT * FROM nahradny_diel WHERE Diel_evidencne_cislo='$Diel_evidencne_cislo'";
    $vysledok = mysqli_query($dblink, $sql);
    $num_row = mysqli_num_rows($vysledok);
    if($num_row > 0){  /// Ak nasiel aspon 1 zaznam{
        header('Location:stroj.php?vysledok=chyba');
        exit;
    }

    $sql = "INSERT INTO `nahradny_diel` 
    (`Diel_evidencne_cislo`, `Diel_nazov`, `Diel_popis`, `Diel_jednotka`, `Diel_mnozstvo`, `Diel_umiestnenie`, `Diel_datum_prevzatia`, 
                             `Diel_zarucna_doba`,`Nahradny_dielID`,`KategoriaID`,`StrojID`)
            VALUES ('$Diel_evidencne_cislo','$Diel_nazov','$Diel_popis','$Diel_jednotka','$Diel_mnozstvo','$Diel_umiestnenie','$Upraveny_datum_prevzatia',
                    '$Diel_zarucna_doba',NULL,$KategoriaID,".(is_null($StrojID) ? "NULL" : $StrojID).")";

    //echo "SQL: ".$sql;exit;

        $vysledok = mysqli_query($dblink, $sql); /* vykonam sql prikaz select a vysledok načítame do premennej vysledok*/

    if (!$vysledok){
        $hlaska .= "Chyba pri vkladani poruchy! </br>";
    }
    else
    {
        $hlaska = "<span class='oznam'>Náhradný diel $Diel_nazov bol pridaný do databázy.</span>";
    }
    $_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

// ---------------- Idem Upravit zaznam do tabuluky ------------------
if ($_POST["akcia"]=="update" && $_POST["Nahradny_dielID"]!="" && $_POST["back"] != "Späť"): // chcem akciu update z hidden parametru formulara a meno nesmie byt prazdne

    if(!$KategoriaID){
        $sql_kat = "INSERT INTO `kategoria` (`Kat_nazov`,`KategoriaID`) VALUES ('$Kat_nazov',NULL)";
        $vysledok = mysqli_query($dblink, $sql_kat);
        if (!$vysledok){
            $hlaska .= "Chyba pri vkladani kategorie! </br>";
        }
        $KategoriaID = mysqli_insert_id($dblink);
    }

    /*kontrola evidencneho cisla*/
    if($Diel_evidencne_cislo AND $Diel_evidencne_cislo!=$Diel_evidencne_cislo_old) {
        $sql = "SELECT * FROM nahradny_diel WHERE Diel_evidencne_cislo='$Diel_evidencne_cislo'";
        $vysledok = mysqli_query($dblink, $sql);
        $num_row = mysqli_num_rows($vysledok);
        if ($num_row > 0) {  /// Ak nasiel aspon 1 zaznam{
            header('Location:stroj.php?vysledok=chyba');
            exit;
        }
    }

    $sql = "UPDATE `nahradny_diel` 
        SET `Diel_evidencne_cislo`='$Diel_evidencne_cislo',
            `Diel_nazov`='$Diel_nazov', 
            `Diel_popis`='$Diel_popis', 
            `Diel_jednotka`='$Diel_jednotka', 
            `Diel_mnozstvo`='$Diel_mnozstvo', 
            `Diel_umiestnenie`='$Diel_umiestnenie', 
            `Diel_datum_prevzatia`='$Upraveny_datum_prevzatia', 
            `Diel_zarucna_doba`='$Diel_zarucna_doba', 
            `KategoriaID`='$KategoriaID', 
            `StrojID`=" . (is_null($StrojID) ? "NULL" : "'$StrojID'") . "
        WHERE Nahradny_dielID = $Nahradny_dielID";

//    echo $sql; exit;


    $vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
    if (!$vysledok)
    {
        $hlaska .= "Chyba updatu náhradného dielu! </br>";
    }
    else
    {
        $hlaska .= "<span class='oznam'>Náhradný diel $Diel_nazov bol upravený.</span>";
    }

    $_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

header('Location: index_nahradne_diely.php');
?>
