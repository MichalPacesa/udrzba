<?php
// program na kontrolu existencie evidencneho cisla. Vysledok posle ako json a hlaska sa zobrazi sa v message
include "config.php";
include "lib.php";
$hladaj=$_GET["q"];
$sql = "SELECT Diel_evidencne_cislo FROM nahradny_diel WHERE Diel_evidencne_cislo='$hladaj'";
$vysledok = mysqli_query($dblink, $sql);
if (!$vysledok){
    $chyba= "Chyba pri vyhladani evidencneho cisla <br>";
    echo json_encode($chyba);
}
else {
    $row = mysqli_fetch_row($vysledok); // nacita 1 riadok do pola
    echo json_encode($row);
}
exit;
