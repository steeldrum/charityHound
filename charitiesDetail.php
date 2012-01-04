<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
charitiesDetail.php
tjs 101213

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
/*
$dunns = $_GET['dunns'];
$url = $_GET['url'];
$description = $_GET['description'];
$numStars = $_GET['numStars'];
*/
//echo "id ".$id." dunns ".$dunns." url ".$url." description ".$description." numStars ".$numStars; 

//tjs101011
//define("MYSQL_HOST", "localhost");
//$username="root";
//$password="root";
//$database="COLLORG";

$con = mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

if ($id == 0) {
	// add the new charity
	/*
	$sql="INSERT INTO charities (memberId, charityName, shortName, dunns, url, description, numStars)
	VALUES
	('$account','$_GET[charityName]','$_GET[shortName]','$_GET[dunns]','$_GET[url]','$_GET[description]','$_GET[numStars]')";
	*/
	$sql="INSERT INTO charities (memberId, charityName, shortName, dunns, isForProfit, isInactive, url, description, numStars)
	VALUES
	('$account','$_GET[charityName]','$_GET[shortName]','$_GET[dunns]','$_GET[isForProfit]','$_GET[isInactive]','$_GET[url]','$_GET[description]','$_GET[numStars]')";
} else {
	if ($remove == 'true') {
		$sql = "UPDATE charities SET isinactive = 1
		WHERE id = $id";
	} else {
	/*
		$sql = "UPDATE charities SET charityName = '$_GET[charityName]', shortName = '$_GET[shortName]', dunns = '$_GET[dunns]', url = '$_GET[url]', description = '$_GET[description]', numStars = '$_GET[numStars]'
		WHERE id = $id";
		//echo "sql: ".$sql;
		*/
		$sql = "UPDATE charities SET charityName = '$_GET[charityName]', shortName = '$_GET[shortName]', dunns = '$_GET[dunns]', isForProfit = '$_GET[isForProfit]', isInactive = '$_GET[isInactive]', url = '$_GET[url]', description = '$_GET[description]', numStars = '$_GET[numStars]'
		WHERE id = $id";
		//echo "sql: ".$sql;
	}
}

if (!mysql_query($sql,$con))
  {
  die('Error: ' . mysql_error());
  }

mysql_close();

?> 


