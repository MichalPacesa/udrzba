<?php
session_start();

unset($_SESSION['Login_ID_pouzivatela']);
unset($_SESSION['Login_Prihlasovacie_meno']); 
unset($_SESSION['Login_Meno_Priezvisko']);
unset($_SESSION['Login_RolaID']);
header('Location: ../../index.php');
?>

