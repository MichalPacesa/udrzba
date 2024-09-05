<?php
session_start();
include "../../config.php";  // konfiguracia
include "../../lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
	echo "Chyba pripojenia na DB!</br>";
	exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
	echo "Nemáte práva na zmenu zamestnanca.";
	exit;
}

if($_POST["back"] != "Späť" && ZistiPrava("zamestnanci",$dblink) == 0){

	echo "<span class='oznam cervene'>Nemáte práva na úpravu zamestnancov.</span>";
	exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

$ZamestnanecID = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["ZamestnanecID"])));

$Zam_meno = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_meno"])));
$Zam_priezvisko = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_priezvisko"])));
$Zam_datum_narodenia = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_datum_narodenia"])));
$Zam_email = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_email"])));
$Zam_telefon = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_telefon"])));
$Zam_ulica_a_cislo = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_ulica_a_cislo"])));
$Zam_mesto = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_mesto"])));
$Zam_psc = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_psc"])));
$Zam_psc = str_replace(' ', '', $Zam_psc);
$Zam_pozicia = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_pozicia"])));
$Zam_datum_nastupu = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_datum_nastupu"])));
$Zam_poznamka = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Zam_poznamka"])));

$Pouz_meno = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Pouz_meno"])));
$Pouz_meno_old = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Pouz_meno_old"])));
$Pouz_heslo = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Pouz_heslo"])));
$Pouz_heslo_crypt = password_hash($Pouz_heslo, PASSWORD_BCRYPT);
$RolaID = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["RolaID"])));

if($Zam_datum_narodenia == "")
{
	$Zam_datum_narodenia = "0000-00-00";
}
else $Upraveny_datum_narodenia = date("y-m-d", strtotime($Zam_datum_narodenia));

if($Zam_datum_nastupu == "")
{
	$Upraveny_datum_nastupu = "0000-00-00";
}
else $Upraveny_datum_nastupu = date("y-m-d", strtotime($Zam_datum_nastupu));

// ---------------- Idem insertovat zaznam do tabuluky ------------------
if($_POST["akcia"]=="insert" && $_POST["Zam_meno"]!="" && $_POST["back"] != "Späť"):  // ak je akcia insert z hidden parametru formulara a vyplnené meno nesmie byt prazdne

	/*kontrola loginu*/
	$sql = "SELECT * FROM pouzivatel WHERE Pouz_meno='$Pouz_meno'";
	$vysledok = mysqli_query($dblink, $sql);
	$num_row = mysqli_num_rows($vysledok);
	if($num_row > 0){  /// Ak nasiel aspon 1 zaznam{
		header('Location: zamestnanec.php?vysledok=chyba');
		exit;
	}

	// INSERT POUZIVATELA
	$sql = "INSERT INTO `pouzivatel` (`Pouz_meno`, `Pouz_heslo`, `PouzivatelID`, `RolaID`) VALUES
			('$Pouz_meno','$Pouz_heslo_crypt',NULL,'$RolaID')";

	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz select a vysledok načítame do premennej $vysledok
	if (!$vysledok)
	{
		$hlaska = "Chyba pri vkladani pouzivatela! </br>";
	}
	$PouzivatelID = mysqli_insert_id($dblink);

		$sql = "INSERT INTO zamestnanec(`Zam_meno`, `Zam_priezvisko`, `Zam_datum_narodenia`, `Zam_email`, 
                                       `Zam_telefon`, `Zam_ulica_a_cislo`, `Zam_mesto`, `Zam_psc`,
                        				`Zam_pozicia`, `Zam_datum_nastupu`, `Zam_poznamka`, `ZamestnanecID`,`PouzivatelID`)
										VALUES ('$Zam_meno','$Zam_priezvisko','$Upraveny_datum_narodenia','$Zam_email',
										'$Zam_telefon','$Zam_ulica_a_cislo','$Zam_mesto','$Zam_psc',
										'$Zam_pozicia','$Upraveny_datum_nastupu','$Zam_poznamka',NULL,'$PouzivatelID')";

	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz select a vysledok načítame do premennej $vysledok

	if (!$vysledok)
	{
		$hlaska .= "Chyba pri vkladani zamestnanca! </br>";

	}
	else
	{
		$hlaska = "<span class='oznam'>Zamestnanec  $Zam_meno $Zam_priezvisko bol pridaný do databázy.</span>";
	}
	$_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

// ---------------- Idem Upravit zaznam do tabuluky ------------------
if ($_POST["akcia"]=="update" && $_POST["ZamestnanecID"]!="" && $_POST["back"] != "Späť"): // chcem akciu update z hidden parametru formulara a meno nesmie byt prazdne

	$PouzivatelID = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["PouzivatelID"])));

	/*kontrola loginu*/
	if($Pouz_meno AND $Pouz_meno!=$Pouz_meno_old){
		$sql = "SELECT * FROM pouzivatel WHERE Pouz_meno='$Pouz_meno'";
		$vysledok = mysqli_query($dblink, $sql);
		$num_row = mysqli_num_rows($vysledok);
		if($num_row > 0)  /// Ak nasiel aspon 1 zaznam
			{
			header('Location: zamestnanec.php?vysledok=chyba');exit;
		}
	}

	if($Pouz_heslo!=""){ // zmenili heslo
		$sql_pouz = "UPDATE pouzivatel SET Pouz_meno='$Pouz_meno',
                      Pouz_heslo='$Pouz_heslo_crypt', RolaID='$RolaID' WHERE PouzivatelID=$PouzivatelID";
	}
	else {// bez zmeny hesla
		$sql_pouz = "UPDATE pouzivatel SET Pouz_meno='$Pouz_meno',
                      RolaID='$RolaID' WHERE PouzivatelID=$PouzivatelID";
	}
	$vysledok = mysqli_query($dblink, $sql_pouz); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
	if (!$vysledok)
	{
		$hlaska .= "Chyba updatu registrovaného použivateľa! </br>";
	}

	$sql = "UPDATE zamestnanec SET Zam_meno='$Zam_meno', Zam_priezvisko='$Zam_priezvisko', Zam_datum_narodenia='$Upraveny_datum_narodenia', Zam_email='$Zam_email', Zam_telefon='$Zam_telefon', 
	Zam_ulica_a_cislo='$Zam_ulica_a_cislo', Zam_mesto='$Zam_mesto', Zam_psc='$Zam_psc',  Zam_pozicia='$Zam_pozicia', Zam_datum_nastupu='$Upraveny_datum_nastupu',
	Zam_poznamka='$Zam_poznamka' WHERE ZamestnanecID=$ZamestnanecID";
	//echo $sql;exit;
	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
	if (!$vysledok)
	{
		$hlaska .= "Chyba updatu zamestnanca! </br>";
	}
	else
	{
		$hlaska .= "<span class='oznam'>Zamestnanec $Zam_meno $Zam_priezvisko bol upravený.</span>";
	}



	$_SESSION["hlaska"]=$hlaska;
//	echo $_SESSION["hlaska"];

endif;
//-----------------------------------------------------------------------

header('Location: ../../index_zamestnanci.php');
?>
