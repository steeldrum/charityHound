<?php
/***************************************
$Revision:: 91                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 11:31:55#$: Date of last commit
***************************************/
/*
getLoginXML.php
tjs 100928

file version 1.00 

release version 1.00
*/
//http://localhost/ccGetLoginXML.php
session_start();

if (isset($_SESSION['loginAccountNumber'])) {
	$account = $_SESSION['loginAccountNumber'];
	$lastName=$_SESSION['last'];
	$firstName=$_SESSION['first'];
} else {
	$account = 0;
	$lastName = 'collaborator';
	$firstName = 'demo';
}


header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" ?><login>";
echo '<account id="'.$account.'"><last>'.$lastName.'</last><first>'.$firstName.'</first></account>';
echo "</login>";

?> 


