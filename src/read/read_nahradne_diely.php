<?php

session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("zobrazNahradneDiely",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu náhradnych dielov.</span>";
    exit;
}

$condition  = "1";
if(isset($_GET['Nahradny_dielID']) and $_GET['Nahradny_dielID'] ){
	$id = mysqli_real_escape_string($dblink,trim($_GET['Nahradny_dielID']));
	$condition  = " Nahradny_dielID=".$id;
}

if(isset($_GET['kategoria']) and $_GET['kategoria']){ // Filter podla vybranej pozicie
    $kategoria = mysqli_real_escape_string($dblink,trim($_GET['kategoria']));
	$condition  .= " AND Kat_nazov='".$kategoria."'";
}

if(isset($_GET['list']) and $_GET['list']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $condition  .= " GROUP BY ".$list;
}

if(isset($_GET['list_umiestnenie']) and $_GET['list_umiestnenie']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list_umiestnenie = mysqli_real_escape_string($dblink,trim($_GET['list_umiestnenie']));
    $condition  .= " GROUP BY ".$list_umiestnenie;
}
if(isset($_GET['list_nahradne_diely']) and $_GET['list_nahradne_diely']){
    $list_nahradne_diely = mysqli_real_escape_string($dblink,trim($_GET['list_nahradne_diely']));
    $condition  .= " GROUP BY ".$list_nahradne_diely;

}


if(isset($_GET['list_kategoria']) and $_GET['list_kategoria']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list_kategoria = mysqli_real_escape_string($dblink,trim($_GET['list_kategoria']));
    $condition  .= " GROUP BY ".$list_kategoria;
}

if(isset($_GET['search']) and $_GET['search'])
{
    $search = mysqli_real_escape_string($dblink,trim($_GET['search']));
	$condition .= " AND (Diel_evidencne_cislo like '%$search%' OR Diel_nazov LIKE '%$search%' OR Kat_nazov LIKE '%$search%' OR Diel_umiestnenie LIKE '%$search%')";
}

if(isset($list)){
    $sql="select ".$list." from kategoria WHERE ".$list."!='' AND KategoriaID IN (SELECT KategoriaID FROM nahradny_diel) AND ".$condition;
    //echo $sql;exit;
}
elseif(isset($list_umiestnenie)){
    $sql="select ".$list_umiestnenie." from nahradny_diel WHERE ".$list_umiestnenie."!='' AND ".$condition;
    //echo $sql;exit;
}
elseif(isset($list_nahradne_diely)){
    $sql="select * from nahradny_diel WHERE ".$list_nahradne_diely."!='' GROUP BY ".$list_nahradne_diely;
    //echo $sql;exit;
}
elseif(isset($list_kategoria)){
    $sql="select ".$list_kategoria.",KategoriaID from kategoria WHERE ".$list_kategoria."!='' AND ".$condition;
    //echo $sql;exit;
}
else{
    $sql="select * from nahradny_diel n LEFT JOIN kategoria k ON n.KategoriaID = k.KategoriaID WHERE ".$condition." ORDER BY Nahradny_dielID DESC";
}

//echo $sql."<br>";exit;
$userData = mysqli_query($dblink,$sql );

$response = array();

while($row = mysqli_fetch_assoc($userData)){

    $response[] = $row;

}

echo json_encode($response);
exit;
?>