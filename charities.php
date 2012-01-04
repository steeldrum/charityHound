<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
charities.php
tjs 101119

file version 1.00 

release version 1.00
*/
//http://localhost/ccCustomers.php?account=0&last=lc8&first=fc8&id=5&remove=false

//$account = $_GET['account'];
require_once( "Member.class.php" );

//tjs 110511 above ensures that config.php has been loaded as well
$username=DB_USERNAME;
$password=DB_PASSWORD;
$database=DB_NAME;

session_start();
//if (isset($_SESSION['loginAccountNumber'])) {
//	$account = $_SESSION['loginAccountNumber'];
//}
$account = 0;
if (isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	$account = $member->getValue( "id" );
} 
$id = $_GET['id'];
$remove = $_GET['remove'];

//tjs101011
//define("MYSQL_HOST", "localhost");
//$username="root";
//$password="root";
//$database="COLLORG";

$con = mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

if ($id == 0) {
// add the new charity
$sql="INSERT INTO charities (memberId, charityName, shortName)
VALUES
('$account','$_GET[charityName]','$_GET[shortName]')";
} else {
	if ($remove == 'true') {
$sql = "UPDATE charities SET isinactive = 1
WHERE id = $id";
	} else {
$sql = "UPDATE charities SET charityName = '$_GET[charityName]', shortName = '$_GET[shortName]'
WHERE id = $id";
	}
}

if (!mysql_query($sql,$con))
  {
  die('Error: ' . mysql_error());
  }

mysql_close();

?> 


