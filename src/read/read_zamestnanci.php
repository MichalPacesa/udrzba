<?php
session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("zamestnanci",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu zamestnancov.</span>";
    exit;
}

$condition  = "1";
if(isset($_GET['ZamestnanecID']) and $_GET['ZamestnanecID'] ){
	$id = mysqli_real_escape_string($dblink,trim($_GET['ZamestnanecID']));
	$condition  = " ZamestnanecID=".$id;
}

if(isset($_GET['pozicia']) and $_GET['pozicia']){ // Filter podla vybranej pozicie
    $pozicia = mysqli_real_escape_string($dblink,trim($_GET['pozicia']));
	$condition  .= " AND Zam_pozicia='".$pozicia."'";
}

if(isset($_GET['list']) and $_GET['list']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $condition  .= " GROUP BY ".$list;
}

if(isset($_GET['search']) and $_GET['search'])
{
    $search = mysqli_real_escape_string($dblink,trim($_GET['search']));
	$condition .= " AND (Zam_meno like '%$search%' OR Zam_priezvisko LIKE '%$search%' OR CONCAT(Zam_Meno,' ',Zam_Priezvisko) LIKE '%$search%' OR Zam_pozicia LIKE '%$search%' OR Zam_Telefon LIKE '%$search%')";
}

if(isset($list)){
    if(ZistiPrava("rola",$dblink) == 0){
        $sql="select ".$list." from zamestnanec WHERE ".$list."!='Systémový administrátor' AND ".$list."!='Vedúci údržby' AND ".$list."!='Vedúci výroby' AND ".$condition;
    }
    else {
        $sql="select ".$list." from zamestnanec WHERE ".$list."!='' AND ".$condition;
    }

}
else{
    $sql="select * from zamestnanec WHERE ".$condition." ORDER BY ZamestnanecID DESC";
}

//echo $sql."<br>";
$userData = mysqli_query($dblink,$sql);

$response = array();

while($row = mysqli_fetch_assoc($userData)){

    $response[] = $row;

}

echo json_encode($response);
exit;
?>