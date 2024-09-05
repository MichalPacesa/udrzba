<?php
// zmazanie zaznamu z činnosti oprav
session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("editCinnostiOpravy",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu činností opravy.</span>";
    exit;
}

$chyba='';

if(isset($_GET['Cinnost_opravyID']) and $_GET['Cinnost_opravyID'] ){
    $Cinnost_opravyID = mysqli_real_escape_string($dblink,trim($_GET['Cinnost_opravyID']));
}
else exit;

if(isset($_GET['Cin_nazov']) and $_GET['Cin_nazov'] ){
    $Cin_nazov = mysqli_real_escape_string($dblink,trim($_GET['Cin_nazov']));
}
else exit;

$sql = "SELECT * FROM oprava WHERE Cinnost_opravyID=$Cinnost_opravyID";
$vysledok_oprava = mysqli_query($dblink,$sql);
$row = mysqli_num_rows($vysledok_oprava);
if($row > 0) {

    $pouziva_sa = "Činnosť opravy sa nepodarilo vymazať kvôli použitiu v opravách!";

}
else{

    $sql = "DELETE FROM cinnost_opravy WHERE Cinnost_opravyID=$Cinnost_opravyID";
    $vysledok = mysqli_query($dblink, $sql); // vymazanie zamestnanca

    if (!$vysledok){
        $chyba="Chyba pri vymazávaní činnosti opravy! <br>";

    }
    else{
        $nazov =  $Cin_nazov;

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