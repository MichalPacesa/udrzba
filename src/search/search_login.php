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

