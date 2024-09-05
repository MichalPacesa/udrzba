<?php

session_start();
include_once "../../config.php";
include_once "../../lib.php";

if(ZistiPrava("editCinnostiOpravy",$dblink) == 0){

    echo "<span class='oznam cervene'>Nemáte práva na úpravu činností opravy.</span>";
    exit;
}

$condition  = "1";

if(isset($_GET['list']) and $_GET['list']){ // Obsah selectu - zobrazi zoznam pozicii z databazy do filtru
    $list = mysqli_real_escape_string($dblink,trim($_GET['list']));
    $condition  .= " GROUP BY ".$list;
}

if(isset($_GET['search']) and $_GET['search']) {
    $search = strip_tags($_GET['search']);
	$condition .= " AND (Cin_nazov like '%$search%')";
}

if(isset($list)){
    $sql="select ".$list.",Cinnost_opravyID from cinnost_opravy WHERE ".$list."!='' AND ".$condition;
}
else{
    $sql="select * from cinnost_opravy WHERE ".$condition." ORDER BY Cinnost_opravyID DESC";
}

$userData = mysqli_query($dblink,$sql);

$response = array();

while($row = mysqli_fetch_assoc($userData)){

    $response[] = $row;

}

echo json_encode($response);
exit;
?>