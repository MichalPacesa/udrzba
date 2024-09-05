<?php
// zmazanie zaznamu poruchy z tabulky porucha
session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("editPorucha",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu porúch.</span>";
    exit;
}

$chyba='';

if(isset($_GET['PoruchaID']) and $_GET['PoruchaID'] ){
    $PoruchaID = mysqli_real_escape_string($dblink,trim($_GET['PoruchaID']));
}
else exit;
if(isset($_GET['Por_nazov']) and $_GET['Por_nazov'] ){
    $Por_nazov = mysqli_real_escape_string($dblink,trim($_GET['Por_nazov']));
}
else exit;

$sql = "DELETE FROM porucha WHERE PoruchaID=$PoruchaID";
$vysledok = mysqli_query($dblink, $sql); // vymazanie poruchy
if (!$vysledok){
    $chyba="Chyba pri vymazávaní poruchy! <br>";
}

if($chyba)
    echo json_encode($chyba);
else
    $nazovporuchy = $Por_nazov;

echo json_encode($nazovporuchy);
exit;
?>