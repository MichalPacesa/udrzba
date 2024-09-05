<?php

session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("editPorucha",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu porúch.</span>";
    exit;
}

$condition  = "1";
if(isset($_GET['ZamestnanecID']) and $_GET['ZamestnanecID'] ){
	$id = mysqli_real_escape_string($dblink,trim($_GET['ZamestnanecID']));
	$condition  = " ZamestnanecID=".$id;
}

if(isset($_GET['StrojID']) and $_GET['StrojID']){ // Filter podla vybranej stroja
    $StrojID = mysqli_real_escape_string($dblink,trim($_GET['StrojID']));
    $Stroj_nazov = mysqli_real_escape_string($dblink,trim($_GET['$Stroj_nazov']));
	$condition  .= " AND p.StrojID =".$StrojID;
}

if(isset($_GET['Por_stav']) and $_GET['Por_stav']){ // Filter podla vybranej stroja
    $Por_stav = mysqli_real_escape_string($dblink,trim($_GET['Por_stav']));
    $condition  .= " AND p.Por_stav =".$Por_stav;
}

if(isset($_GET['list']) and $_GET['list']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $condition  .= " GROUP BY ".$list;
}

if(isset($_GET['search']) and $_GET['search'])
{
    $search = mysqli_real_escape_string($dblink,trim($_GET['search']));
	$condition .= " AND (Por_nazov like '%$search%' OR Por_datum_vzniku LIKE '%$search%' OR Stroj_nazov LIKE '%$search%' OR
	Zam_meno like '%$search%' OR Zam_priezvisko LIKE '%$search%' OR CONCAT(Zam_Meno,' ',Zam_Priezvisko) LIKE '%$search%' OR Por_stav LIKE '%$search%')";
}


if(isset($list)){
    $sql="select ".$list." from porucha WHERE ".$list."!='' AND ".$condition;
}
else{
    $sql="select * from porucha p LEFT JOIN zamestnanec z ON p.PouzivatelID = z.PouzivatelID LEFT JOIN stroj s ON p.StrojID = s.StrojID WHERE ".$condition." ORDER BY Por_datum_vzniku DESC";
}

$userData = mysqli_query($dblink,$sql );

$response = array();

while($row = mysqli_fetch_assoc($userData)){

    $response[] = $row;

}

echo json_encode($response);
exit;
?>