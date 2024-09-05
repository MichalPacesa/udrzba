<?php
session_start();
include "../../config.php";  // konfiguracia
include "../../lib.php";	//	funkcie    

if (!$dblink) { // kontrola ci je pripojenie na databazu dobre ak nie tak napise chybu
	echo "Chyba pripojenia na DB!</br>";
	exit;
}

if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  // nie je prihlaseny
	echo "Nemáte práva na zmenu stroja.";
	exit;
}

if($_POST["back"] != "Späť" && ZistiPrava("editStroj",$dblink) == 0){

	echo "<span class='oznam cervene'>Nemáte práva na úpravu strojov.</span>";
	exit;
}

$hlaska="";
$_SESSION["hlaska"] = "";

// ---------------- Idem insertovat zaznam do tabuluky ------------------

	$StrojID = mysqli_real_escape_string($dblink,strip_tags($_POST["StrojID"]));
	$Stroj_nazov = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_nazov"])));
	$Stroj_popis = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_popis"])));
	$Stroj_umiestnenie = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_umiestnenie"])));

	$Stroj_vyrobca = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_vyrobca"])));
	$Stroj_datum_vyroby = mysqli_real_escape_string($dblink,strip_tags($_POST["Stroj_datum_vyroby"]));

	$Stroj_vyrobne_cislo = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_vyrobne_cislo"])));
	$Stroj_evidencne_cislo = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_evidencne_cislo"])));
	$Stroj_evidencne_cislo_old = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_evidencne_cislo_old"])));

	$DodavatelID = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["DodavatelID"])));

	$Stroj_datum_prevzatia = mysqli_real_escape_string($dblink,strip_tags($_POST["Stroj_datum_prevzatia"]));
	$Stroj_zarucna_doba = mysqli_real_escape_string($dblink,strip_tags(Trim($_POST["Stroj_zarucna_doba"])));

	if($Stroj_datum_vyroby == "")
		$Upraveny_datum_vyroby = "0000-00-00";
	else $Upraveny_datum_vyroby = date("y-m-d", strtotime($Stroj_datum_vyroby));

	if($Stroj_datum_prevzatia == "")
		$Upraveny_datum_prevzatia = "0000-00-00";
	else
		$Upraveny_datum_prevzatia = date("y-m-d", strtotime($Stroj_datum_prevzatia));

	if(!$DodavatelID){
		$DodavatelID = NULL;
	}
	if(!$Stroj_zarucna_doba){
		$Stroj_zarucna_doba = NULL;
	}

if($_POST["akcia"]=="insert" && $_POST["Stroj_nazov"]!="" && $_POST["back"] != "Späť"):  // ak je akcia insert z hidden parametru formulara a vyplnené meno nesmie byt prazdne
	/*kontrola evidencneho cisla*/
	$sql = "SELECT * FROM stroj WHERE Stroj_evidencne_cislo='$Stroj_evidencne_cislo'";
	$vysledok = mysqli_query($dblink, $sql);
	$num_row = mysqli_num_rows($vysledok);
	if($num_row > 0){  /// Ak nasiel aspon 1 zaznam{
		header('Location:src/form/stroj.php?vysledok=chyba');
		exit;
	}
	// INSERT STROJA
	$sql = "INSERT INTO `stroj` (`Stroj_nazov`, `Stroj_popis`, `Stroj_umiestnenie`,`Stroj_vyrobca`,`Stroj_datum_vyroby`,`Stroj_vyrobne_cislo`,`Stroj_evidencne_cislo`, `DodavatelID`,`Stroj_datum_prevzatia`,`Stroj_zarucna_doba`, `StrojID`) VALUES
			('$Stroj_nazov','$Stroj_popis','$Stroj_umiestnenie','$Stroj_vyrobca','$Upraveny_datum_vyroby','$Stroj_vyrobne_cislo','$Stroj_evidencne_cislo',".(is_null($DodavatelID) ? "NULL" : $DodavatelID).",'$Upraveny_datum_prevzatia',".(is_null($Stroj_zarucna_doba) ? "NULL" : $Stroj_zarucna_doba).", NULL)";
	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz select a vysledok načítame do premennej $vysledok
	if (!$vysledok)
	{ 
		$hlaska = "Chyba pri vkladani stroja! </br>";
	}
	else
	{
		$ID_posledneho_zaznamu = mysqli_insert_id($dblink);
		$hlaska = "<span class='oznam'>Stroj $Stroj_nazov bol pridany do databázy</span>";
	}

	/* PRILOHY */
	$j=1;
	$pocet_priloh = 0;
	if ($_FILES["userfile1"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=2;
	if ($_FILES["userfile2"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=3;
	if ($_FILES["userfile3"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=4;
	if ($_FILES["userfile4"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=5;
	if ($_FILES["userfile5"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}

	if($pocet_priloh == 1)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbsppríloha bola úspešne odoslaná.</span>";

	if($pocet_priloh >= 2 AND $pocet_priloh <= 4)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbspprílohy boli úspešne odoslané.</span>";

	if($pocet_priloh >= 5)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbsppríloh bolo úspešne odoslaných.</span>";


/* END PRILOHY */



	$_SESSION["hlaska"]=$hlaska; 
	
endif;
//-----------------------------------------------------------------------

// ---------------- Idem Upravit zaznam do tabulky ------------------
if ($_POST["akcia"]=="update" && $_POST["StrojID"]!="" && $_POST["back"] != "Späť"):

	  /*kontrola evidencneho cisla*/
	if($Stroj_evidencne_cislo AND $Stroj_evidencne_cislo!=$Stroj_evidencne_cislo_old) {
		$sql = "SELECT * FROM stroj WHERE Stroj_evidencne_cislo='$Stroj_evidencne_cislo'";
		$vysledok = mysqli_query($dblink, $sql);
		$num_row = mysqli_num_rows($vysledok);
		if ($num_row > 0) {  /// Ak nasiel aspon 1 zaznam{
			header('Location:src/form/stroj.php?vysledok=chyba');
			exit;
		}
	}
	


	$sql = "UPDATE stroj SET Stroj_nazov='$Stroj_nazov', Stroj_popis='$Stroj_popis', Stroj_umiestnenie='$Stroj_umiestnenie',Stroj_datum_vyroby='$Upraveny_datum_vyroby', Stroj_vyrobca='$Stroj_vyrobca', Stroj_vyrobne_cislo='$Stroj_vyrobne_cislo', 
	Stroj_evidencne_cislo='$Stroj_evidencne_cislo',  Stroj_datum_prevzatia='$Upraveny_datum_prevzatia', Stroj_zarucna_doba=".(is_null($Stroj_zarucna_doba) ? "NULL" : $Stroj_zarucna_doba).",";
	if($DodavatelID)
		$sql.=" DodavatelID=$DodavatelID";
	else
		$sql.=" DodavatelID=NULL";
	$sql.=" WHERE StrojID=$StrojID";

	$vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz update a vysledky nacitame do premennej $vysledok
	if (!$vysledok)	{
			$hlaska .= "Chyba updatu stroja! </br>";
	}
	else {
		$hlaska .= "<span class='oznam'>Stroj $Stroj_nazov bol upravený.</span>&nbsp";
	}

	/* PRILOHY */
	$j=1;
	$pocet_priloh = 0;
	if ($_FILES["userfile1"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=2;
	if ($_FILES["userfile2"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=3;
	if ($_FILES["userfile3"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=4;
	if ($_FILES["userfile4"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}
	$j=5;
	if ($_FILES["userfile5"]['name'])  //uzivatel vybral novy subor
	{include 'upload.php';}

	if($pocet_priloh == 1)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbsppríloha bola úspešne odoslaná.</span>";

	if($pocet_priloh >= 2 AND $pocet_priloh <= 4)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbspprílohy boli úspešne odoslané.</span>";

	if($pocet_priloh >= 5)
		$hlaska.="<span class='oznam'> ".$pocet_priloh."&nbsppríloh bolo úspešne odoslaných.</span>";

	if($_POST['Zmazat_prilohy'] and $_POST['Zmazat_prilohy']=="zmazat"){
		$sql="SELECT count(1) as pocet_riadkov from priloha where StrojID=$StrojID";
		$num_rows = zisti_pocet_riadkov($dblink,$sql);
		$sql = "Select * FROM priloha where StrojID=$StrojID";
		$vysledok_prilohy=mysqli_query($dblink,$sql);
		if (!$vysledok_prilohy)
		{$hlaska .="Doslo k chybe pri vyhladani príloh !";}
		elseif($num_rows!=0){
			for ($s=0; $s < $num_rows; $s++){ // poradie priloh
				$row=mysqli_fetch_assoc($vysledok_prilohy);
				if($_POST['Zmazat_prilohu_'.$s] and ($_POST['Zmazat_prilohu_'.$s]=="zmazat")){
					$cesta='prilohy'.$row['Nazov_suboru'];
					$PrilohaID=$row['PrilohaID'];
					if (file_exists($cesta)){     // Ak existuje taky subor na disku
						@unlink($cesta);     	  // Tak 
					}
					$sql = "Delete FROM priloha where PrilohaID=$PrilohaID"; // vymazeme aj v db
					$vysledok=mysqli_query($dblink,$sql);
					if(!$vysledok)
						$hlaska .= "Chyba pri vymazani prilohy. ";

					else{
						$cesta=getShortFileName($cesta, 15, 5);
						$hlaska .= "&nbspPríloha ".$cesta." bola vymazaná. ";
					}
				}
			}
		}
		else{ 	$hlaska .="Chyba pri vymazani priloh !";}

	}
	/* END PRILOHY */

	
	$_SESSION["hlaska"]=$hlaska;
	
	endif;
//-----------------------------------------------------------------------	

header('Location: ../../index_stroje.php');
?>
