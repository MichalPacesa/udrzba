<?php
// zmazanie zaznamu zamestnanca z tabulky zamestnanec
session_start();
include_once "config.php";
include_once "lib.php";

if(ZistiPrava("zamestnanci",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu zamestnancov.</span>";
    exit;
}
$chyba='';

if(isset($_GET['ZamestnanecID']) and $_GET['ZamestnanecID'] ){
    $ZamestnanecID = mysqli_real_escape_string($dblink,trim($_GET['ZamestnanecID']));
}
else exit;
if(isset($_GET['PouzivatelID']) and $_GET['PouzivatelID'] ){
    $PouzivatelID = mysqli_real_escape_string($dblink,trim($_GET['PouzivatelID']));
}
else exit;
if(isset($_GET['Zam_meno']) and $_GET['Zam_meno'] ){
    $Zam_meno = mysqli_real_escape_string($dblink,trim($_GET['Zam_meno']));
}
else exit;
if(isset($_GET['Zam_priezvisko']) and $_GET['Zam_priezvisko'] ){
    $Zam_priezvisko = mysqli_real_escape_string($dblink,trim($_GET['Zam_priezvisko']));
}
else exit;

$sql = "SELECT * FROM oprava WHERE PouzivatelID=$PouzivatelID";
$vysledok_oprava = mysqli_query($dblink,$sql);
$row = mysqli_num_rows($vysledok_oprava);
if($row > 0) {
    $pouziva_sa = "Zamestnanca sa nepodarilo vymazať kvôli použitiu v opravách!";
}
else{
    $sql="select RolaID from pouzivatel WHERE PouzivatelID=$PouzivatelID";
    $vysledok = mysqli_query($dblink,$sql);
    $riadok = mysqli_fetch_assoc($vysledok);
    if(ZistiPrava("rola",$dblink) == 0 AND strip_tags_html($riadok["RolaID"]) == 1){ // Veduci udrzby nemoze zmazat administratora
        $pouziva_sa = "Nemáta práva vymazať vybraného zamestnanca!";
    }
    else{
        /*pouzivateID je totozne s zamestnanecID*/
        $sql = "DELETE FROM zamestnanec WHERE ZamestnanecID=$ZamestnanecID";
        $vysledok = mysqli_query($dblink, $sql); // vymazanie zamestnanca
        if (!$vysledok){
            $chyba="Chyba pri vymazávaní zamestnanca! <br>";
        }
        else {
            $sql="DELETE  FROM pouzivatel WHERE PouzivatelID = $PouzivatelID";
            $vysledok = mysqli_query($dblink, $sql); // vykonam sql prikaz
            if (!$vysledok){
                $chyba= "Chyba pri vymazávaní  pouzivatela! <br>";
            }
        }

        $menopriezvisko = $Zam_meno." ".$Zam_priezvisko;
    }

}

if($chyba)
    echo json_encode($chyba);
else
    if ($menopriezvisko) {
        echo json_encode(["status" => "success", "menopriezvisko" => $menopriezvisko]);
    } else {
        echo json_encode(["status" => "error", "pouziva_sa" => $pouziva_sa]);
    }


exit;
?>