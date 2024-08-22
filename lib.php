<?php

function zisti_pocet_riadkov($db_link,$sql)
 {
 	$vysledok = mysqli_query($db_link, $sql);
 	$riadok = mysqli_fetch_row($vysledok);
 	return $riadok[0];
 }


function generatePartNumber()
{
	list($usec, $sec) = explode(" ", microtime());

	// Convert both seconds and microseconds to string, concatenate them, and remove any non-digit characters
	$timestamp = preg_replace("/\D/", "", $sec . $usec);

	// Cut the number to the last 9 digits
	$partNumber = substr($timestamp, -9);

	// Format the 9-digit number with spaces for every three digits
	$formattedNumber = implode(' ', str_split($partNumber, 3));

	return $formattedNumber;
}

function strip_tags_html($input) {
	// Strip HTML tags
	$input = strip_tags($input);
	// Convert special characters to HTML entities
	$input = htmlspecialchars($input);
	// Return sanitized input
	return $input;
}


function selected($nazovprem,$hodnota)
{
 // funkcia pre select vo formulari
 if ($nazovprem == $hodnota)
 return 'selected';
}

function disabled($nazovprem)
{
 // funkcia pre preview vo formulari
 // vracia slovo disabled ak ide o preview
 if ($nazovprem == 'preview' OR $nazovprem == 'vyber')
	return 'disabled';
}

function disabled_vyber($nazovprem) 
{
 if ($nazovprem == 'preview')
	return 'disabled';

 if ($nazovprem == 'vyber')
	return '';
}

function checked($premenna,$hodnota)
{
// funkcia pre checkbox vo formulari
if($premenna==$hodnota)
 return 'checked';
else 
 return ' ';
 
}

function zrus_diakritiku($text)
    {
		//echo 'zrus diakritiku text: '.$text;
		$return = Str_Replace(
		Array("á","č","ď","é","ě","í","ľ","ň","ó","ř","š","ť","ú","ů","ý","ž","ô","ú","ä","Á","Č","Ď","É","Ě","Í","Ľ","Ň","Ó","Ř","Š","Ť","Ú","Ů","Ý","Ž") ,
		Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","o","u","a","A","C","D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
		$text);
		$return = Str_Replace(Array(" ", "_"), "-", $return); //nahradí mezery a podtržítka pomlčkami
		$return = Str_Replace(Array("(",")","!",",","\"","'"), "", $return); //odstraní ()!,"'
		$return = StrToLower($return); //velké písmena nahradí malými.
		return $return;
	}



function kontrolasuboru($typsuboru,$nazovsuboru)	 
{
    if($typsuboru=='application/msword' or $typsuboru=='application/vnd.ms-excel' or $typsuboru=='application/pdf' or $typsuboru=='text/html' or $typsuboru=="image/jpeg"
		or $typsuboru=='image/jpeg' or $typsuboru=='image/gif' or $typsuboru=='image/png' or $typsuboru=='image/bmp' )
	   {
	   return 1; 
       }
   	else 
             
	   return 0;
  
}

function ZistiPrava($nazovPrava,$dblink)
{
	$ID_role = strip_tags_html($_SESSION['Login_RolaID']);
	if(!$ID_role or !$dblink or !$nazovPrava){
		return 0;
	}
	$vratit=0;

	switch ($ID_role)  {
		case 1:
			$vratit=1;                // admim
			break;

		case 2:                       //veduci udrzby
			if($nazovPrava=="rola" )  //  nemoze menit rolu uzivatela
				$vratit=0;
			else $vratit=1;
			break;

		case 3:                      // veduci vyroby
			if($nazovPrava=="zamestnanci")
				$vratit=0;
			elseif($nazovPrava=="zobrazNahradneDiely")
				$vratit=0;
			elseif($nazovPrava=="editNahradneDiely")
				$vratit=0;
			elseif($nazovPrava=="zobrazCinnostiOpravy")
				$vratit=0;
			elseif($nazovPrava=="editCinnostiOpravy")
				$vratit=0;
			elseif($nazovPrava=="editStavAZamestnanecNaPoruche")
				$vratit=0;
			elseif($nazovPrava=="cinnostiOpravy")
				$vratit=0;
			elseif($nazovPrava=="editOpravy")
				$vratit=0;
			else $vratit=1;      // ostatne veduci vyroby moze
			break;

		case 4:                // servisny technik
			if($nazovPrava=="zamestnanci")
				$vratit=0;
			elseif($nazovPrava=="editStavAZamestnanecNaPoruche")
				$vratit=0;
			elseif($nazovPrava=="editNahradneDiely")
				$vratit=0;

			else $vratit=1;      // ostatne moze
			break;
	}
	return $vratit;
}

// Generovanie  tokenu a ulozenie do $_SESSION
function generateToken() {
	if (empty($_SESSION['token'])) {
		$_SESSION['token'] = bin2hex(random_bytes(32));
	}
}

// Kontrola  tokenu
function checkToken($tokenFromForm) {
	if (!hash_equals($_SESSION['token'], $tokenFromForm)) {
		// Tokeny sa nezhodujú, je možný útok CSRF
		return false;
	}
	else return true;
}

function getShortFileName($fileName, $startLength, $endLength) {
	$fileLength = strlen($fileName);

	if ($fileLength > ($startLength + $endLength)) {
		$start = substr($fileName, 0, $startLength);
		$end = substr($fileName, -$endLength);
		return $start . "..." . $end;
	} else {
		return $fileName;
	}
}


?>
