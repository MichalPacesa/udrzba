<?php
// program na kontrolu existencie názvu činnosti údržby. Vysledok posle ako json a hlaska sa zobrazi sa v message
include "../../config.php";
include "../../lib.php";
$hladaj=$_GET["q"];
$sql = "SELECT Cin_nazov FROM cinnost_opravy WHERE Cin_nazov='$hladaj'";
//echo $sql;exit;
$vysledok = mysqli_query($dblink, $sql);
if (!$vysledok){
    $chyba= "Chyba pri vyhladani názvu činnosti údržby <br>";
    echo json_encode($chyba);
}
else {
    $row = mysqli_fetch_row($vysledok); // nacita 1 riadok do pola
    echo json_encode($row);
}
exit;
