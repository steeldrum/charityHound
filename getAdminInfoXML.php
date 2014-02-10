<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
getAdminInfoXML.php
tjs 140206

file version 1.00 

release version 1.00
*/
//http://localhost/getAdminInfoXML.php
//http://localhost/getAdminInfoXML.php?account=0

$account = $_GET['account'];
//$account = 0
require_once( "Member.class.php" );
//e.g. username root password root database mysql:dbname=COLLORG
session_start();

$isAdmin = false;
		$last='unknownTesterLastName';
		$first='unknownTesterFirstName';
//$account = 0;
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	$account = $member->getValue( "id" );
	$isAdmin = Member::isMemberAdmin( $account );
		$last=$member->getValue( "lastname" );
		$first=$member->getValue( "firstname" );
} else {
	//$isAdmin = $_GET['torf'];
	// hack for test
	$isAdmin = Member::isMemberAdmin( $account );
}

header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" ?><administrators>";
//echo "account $account isAdmin $isAdmin";
if ($account > 0) {

	if ($isAdmin) {		
		$administrator= '<administrator id="'.$account.'"><account>'.$account.'</account><last>'.$last.'</last><first>'.$first.'</first></administrator>';
		echo $administrator;

	}
}
echo "</administrators>";

?> 


