<?php
session_start();
include "../../config.php";  // konfiguracia
include "../../lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
	echo "Chyba pripojenia na DB!</br>";
	exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
	echo "Nemáte práva na zmenu cinnosti opravy.";
	exit;
}

if($_POST["back"] != "Späť" && ZistiPrava("editCinnostiOpravy",$dblink) == 0){

	echo "<span class='oznam cervene'>Nemáte práva na úpravu činností opravy.</span>";
	exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

$Cinnost_opravyID = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Cinnost_opravyID"])));
$Cin_nazov = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Cin_nazov"])));
$Cin_nazov_old = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Cin_nazov_old"])));

// ---------------- Idem insertovat zaznam do tabuluky ------------------
if($_POST["akcia"]=="insert" && $_POST["Cin_nazov"]!="" && $_POST["back"] != "Späť"):  // ak je akcia insert z hidden parametru formulara a vyplnené meno nesmie byt prazdne

	/*kontrola názvu*/
	$sql = "SELECT * FROM cinnost_opravy WHERE Cin_nazov='$Cin_nazov'";
	$vysledok = mysqli_query($dblink, $sql);
	$num_row = mysqli_num_rows($vysledok);
	if($num_row > 0){  /// Ak nasiel aspon 1 zaznam{
		header('Location: cinnost_opravy.php?vysledok=chyba');
		exit;
	}

		$sql = "INSERT INTO cinnost_opravy(`Cin_nazov`, `Cinnost_opravyID`) VALUES ('$Cin_nazov',NULL)";

	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz select a vysledok načítame do premennej $vysledok

	if (!$vysledok)
	{
		$hlaska .= "Chyba pri vkladani cinnosti opravy! </br>";

	}
	else
	{
		$hlaska = "<span class='oznam'>Činnosť opravy $Cin_nazov bola pridaná do databázy.</span>";
	}
	$_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

// ---------------- Idem Upravit zaznam do tabuluky ------------------
if ($_POST["akcia"]=="update" && $_POST["Cinnost_opravyID"]!="" && $_POST["back"] != "Späť"): // chcem akciu update z hidden parametru formulara a meno nesmie byt prazdne


	/*kontrola názvu*/
	if($Cin_nazov AND $Cin_nazov!=$Cin_nazov_old){
		$sql = "SELECT * FROM cinnost_opravy WHERE Cin_nazov='$Cin_nazov'";
		$vysledok = mysqli_query($dblink, $sql);
		$num_row = mysqli_num_rows($vysledok);
		if($num_row > 0){  /// Ak nasiel aspon 1 zaznam
				header('Location: cinnost_opravy.php?vysledok=chyba');
				exit;
		}
	}

	$sql = "UPDATE cinnost_opravy SET Cin_nazov='$Cin_nazov' WHERE Cinnost_opravyID=$Cinnost_opravyID";
//	echo "aadsfsdfa";exit;

	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
	if (!$vysledok)
	{
		$hlaska .= "Chyba updatu činnosti opravy! </br>";
	}
	else
	{
		$hlaska .= "<span class='oznam'> Činnosť opravy $Cin_nazov bola upravená.</span>";
	}


	$_SESSION["hlaska"]=$hlaska;

endif;
//-----------------------------------------------------------------------

header('Location: ../../index_cinnosti_opravy.php');
?>
