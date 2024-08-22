<?php
// program na kontrolu existencie loginu. Vysledok posle ako json a hlaska sa zobrazi sa v message
include "config.php";
include "lib.php";
$hladaj=$_GET["q"];
$sql = "SELECT Pouz_meno FROM pouzivatel WHERE Pouz_meno='$hladaj'";
$vysledok = mysqli_query($dblink, $sql);
if (!$vysledok){
    $chyba= "Chyba pri vyhladani pouzivatela <br>";
    echo json_encode($chyba);
}
else {
    $row = mysqli_fetch_row($vysledok); // nacita 1 riadok do pola
    echo json_encode($row);
}
exit;

/*$Result="ERR";
$vysledok_hladaj = mysqli_query($dblink, $sql); 
if(!$vysledok_hladaj) echo 'Nepodarila sa kontrola užívateľského mena.';
else
	$num_row = mysqli_num_rows($vysledok_hladaj);
	if($num_row > 0)  /// nasiel aspon 1 zaznam
		$Result=0;
	else $Result="OK";		
?>
<html>
<form  method="get">
	<input type="hidden" id="Result" name="Result" value="<?php echo $Result;?>" />
</form>
<?php
		mysqli_close($dblink); //odpojim sa od databazy


?>
</html>*/

