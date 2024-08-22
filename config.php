<?php

$host = "localhost";    /* Host name */
$user = "root";         /* User */
$password = "";         /* Password */
$dbname = "udrzba";   /* Database name */

//$host = "db.www-stranky.sk";    /* Host name */
//$user = "pacesa";         /* User */
//$password = "Majcichov91922";         /* Password */
//$dbname = "udrzba";   /* Database name */

$dir_name="udrzba";

// Create connection
$dblink = mysqli_connect($host, $user, $password,$dbname);

// Check connection
if (!$dblink) {
    die("Nepodarilo sa nadviazať spojenie s databázou: " . mysqli_connect_error());
}

/* dfsfsfd */

$stav = 0;// ci vypisovat php errory
@ini_set('display_errors', $stav);
@ini_set('display_startup_errors;',$stav);
if($stav){
    @ini_set('error_reporting;',E_ALL);
}


