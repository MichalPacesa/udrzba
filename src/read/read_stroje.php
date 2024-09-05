<?php

session_start();
include_once "config.php";
include_once "lib.php";

if(ZistiPrava("editStroj",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu strojov.</span>";
    exit;
}

$condition  = "1";
if(isset($_GET['StrojID']) and $_GET['StrojID'] ){
	$id = mysqli_real_escape_string($dblink,trim($_GET['StrojID']));
	$condition  = " StrojID=".$id;
}

if(isset($_GET['pozicia']) and $_GET['pozicia']){ // Filter podla vybraneho umiestnenia
    $pozicia = mysqli_real_escape_string($dblink,trim($_GET['pozicia']));
	$condition  .= " AND Stroj_umiestnenie='".$pozicia."'";
}

if(isset($_GET['list']) and $_GET['list'] and !$_GET['porucha']){ // Obsah selectu - zobrazi zoznam umiestneni z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $condition  .= " GROUP BY ".$list;
}

if(isset($_GET['list']) and $_GET['list'] and $_GET['porucha']){ // Obsah selectu - zobrazi zoznam umiestneni z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $porucha = 1;
}

if(isset($_GET['search']) and $_GET['search'])
{
    $search = mysqli_real_escape_string($dblink,trim($_GET['search']));
	$condition .= " AND (Stroj_evidencne_cislo LIKE '%$search%' OR Stroj_nazov like '%$search%' OR Stroj_popis LIKE '%$search%'  OR Stroj_umiestnenie LIKE '%$search%')";
}

if(isset($list)){
    $sql="select ".$list.",StrojID from stroj WHERE ".$list."!='' AND ".$condition;
}
else{
    $sql="select * from stroj WHERE ".$condition." ORDER BY strojID DESC";//echo $sql;
}

if($porucha == 1 && $list){
    $sql="select s.Stroj_nazov, s.StrojID from stroj s LEFT JOIN porucha p ON p.StrojID = s.StrojID WHERE s.StrojID IN (SELECT StrojID FROM porucha) GROUP BY s.Stroj_nazov;";
}
//echo $sql."<br>";exit;

$userData = mysqli_query($dblink,$sql);

$response = array();

while($row = mysqli_fetch_assoc($userData)){

    $response[] = $row;

}

echo json_encode($response);
exit;
?>