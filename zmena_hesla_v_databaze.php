<?php
session_start();
include "config.php";  // konfiguracia
include "lib.php";	//	funkcie

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
	echo "Chyba pripojenia na DB!</br>";
	exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
	echo "Nemáte práva na zmenu hesla.";
	exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

// ---------------- Idem Upravit zaznam do tabuluky ------------------
if ($_POST["akcia"]=="update" && $_POST["PouzivatelID"]!="" && $_POST["back"] != "Späť"): // chcem akciu update z hidden parametru formulara a meno nesmie byt prazdne

	$PouzivatelID = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["PouzivatelID"])));

	$Pouz_heslo = mysqli_real_escape_string($dblink,strip_tags(trim($_POST["Pouz_heslo"])));
	$Pouz_heslo_crypt = password_hash($Pouz_heslo, PASSWORD_BCRYPT);

	if($Pouz_heslo!=""){ // zmenili heslo
		$sql_pouz = "UPDATE pouzivatel SET Pouz_heslo='$Pouz_heslo_crypt' WHERE PouzivatelID=$PouzivatelID";
	}
	$vysledok = mysqli_query($dblink, $sql_pouz); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
	if (!$vysledok)
	{
		$hlaska .= "Chyba updatu registrovaného použivateľa! </br>";
	}
	else{
		$hlaska .= "<span class='oznam'>Vaše heslo bolo upravené.</span>";
	}

	$_SESSION["hlaska"]=$hlaska;
	//echo $_SESSION["hlaska"];exit;

endif;
//-----------------------------------------------------------------------

header('Location: index.php');
?>
