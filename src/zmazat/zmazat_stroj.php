<?php
// zmazanie zaznamu zamestnanca z tabulky stroj
session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("editStroj",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu strojov.</span>";
    exit;
}
    $chyba='';

    if(isset($_GET['StrojID']) and $_GET['StrojID'] ){
    	$StrojID = mysqli_real_escape_string($dblink,trim($_GET['StrojID']));
    }
    else exit;

    if(isset($_GET['Stroj_nazov']) and $_GET['Stroj_nazov'] ){
        $Stroj_nazov = mysqli_real_escape_string($dblink,trim($_GET['Stroj_nazov']));
    }
    else exit;



    $sql = "DELETE FROM stroj WHERE StrojID=$StrojID";
    $vysledok = mysqli_query($dblink, $sql); // vymazanie zamestnanca
    if (!$vysledok){
        $chyba="Chyba pri vymazávaní stroja! <br>";
    }

    if($chyba)
        echo json_encode($chyba);
    else
        $odoslat = [

            "Stroj_nazov" => $Stroj_nazov
        ];
        echo json_encode($odoslat);
    exit;
?>