<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
getCustomerInfoXML.php
tjs 110106

file version 1.00 

release version 1.00
*/
//http://localhost/getCustomerInfoXML.php
//http://localhost/getCustomerInfoXML.php?account=0

$account = $_GET['account'];
//$account = 0
require_once( "Member.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
$username=DB_USERNAME;
$password=DB_PASSWORD;
$database=DB_NAME;
//echo "username ".$username." password ".$password." database ".$database;
//e.g. username root password root database mysql:dbname=COLLORG
session_start();

//$account = 0;
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	$account = $member->getValue( "id" );
} 

//$charityId = $_GET['charityId'];
//$account = 0;

define("MYSQL_HOST", "localhost");

//$username="root";
//$password="root";
//$database="COLLORG";

//tjs 110511
mysql_connect("".MYSQL_HOST."",$username,$password);
//mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" ?><customers>";

if ($account > 0) {
	$query="SELECT * FROM customers where account = ".$account." and isInactive is null";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	while ($i < $num) {
		$id=mysql_result($result,$i,"id");
		//$charityId=mysql_result($result,$i,"charityId");
		
		$last=mysql_result($result,$i,"last");
		$first=mysql_result($result,$i,"first");
		$customer= '<customer id="'.$id.'"><account>'.$account.'</account><last>'.$last.'</last><first>'.$first.'</first></customer>';
		echo $customer;
		
		$i++;
	}
	
	mysql_close();
}
echo "</customers>";

?> 


