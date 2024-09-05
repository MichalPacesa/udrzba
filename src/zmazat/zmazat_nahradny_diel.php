<?php
// zmazanie zaznamu z nahradnych dielov
session_start();
include_once "../../config.php";
include_once "../../lib.php";

$chyba='';

if(ZistiPrava("editNahradneDiely",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu náhradnych dielov.</span>";
    exit;
}

if(isset($_GET['Nahradny_dielID']) and $_GET['Nahradny_dielID'] ){
    $Nahradny_dielID = mysqli_real_escape_string($dblink,trim($_GET['Nahradny_dielID']));
}
else exit;

if(isset($_GET['Diel_nazov']) and $_GET['Diel_nazov'] ){
    $Diel_nazov = mysqli_real_escape_string($dblink,trim($_GET['Diel_nazov']));
}
else exit;

$sql = "SELECT * FROM pouziva WHERE Nahradny_dielID=$Nahradny_dielID";
$vysledok_oprava = mysqli_query($dblink,$sql);
$row = mysqli_num_rows($vysledok_oprava);
if($row > 0) {

    $pouziva_sa = "Náhradný diel sa nepodarilo vymazať kvôli použitiu v opravách!";

}
else{

    $sql = "DELETE FROM nahradny_diel WHERE Nahradny_dielID=$Nahradny_dielID";
    $vysledok = mysqli_query($dblink, $sql);

    if (!$vysledok){
        $chyba="Chyba pri vymazávaní náhradného dielu! <br>";

    }
    else{
        $nazov =  $Diel_nazov;

    }

}

if($chyba)
    echo json_encode($chyba);
else{
    if ($nazov) {
        echo json_encode(["status" => "success", "nazov" => $nazov]);
    } else {
        echo json_encode(["status" => "error", "pouziva_sa" => $pouziva_sa]);
    }
}



exit;
?>