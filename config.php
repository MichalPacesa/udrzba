<?php

$host = "localhost";    /* Host name */
$user = "root";         /* User */
$password = "";         /* Password */
$dbname = "udrzba";   /* Database name */

$dir_name="udrzba";

// Create connection
$dblink = mysqli_connect($host, $user, $password,$dbname);

// Check connection
if (!$dblink) {
    die("Nepodarilo sa nadviazať spojenie s databázou: " . mysqli_connect_error());
}

$stav = 0;  // stav = 1 znamena, ze chceme vidiet chyby 
@ini_set('display_errors', $stav);
@ini_set('display_startup_errors;',$stav);
if($stav){
    @ini_set('error_reporting;',E_ALL);
}


