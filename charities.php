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
//echo "charities...";

//$account = $_GET['account'];
require_once( "Member.class.php" );
require_once( "Charity.class.php" );

// tjs 140203
//tjs 110511 above ensures that config.php has been loaded as well
//$username=DB_USERNAME;
//$password=DB_PASSWORD;
//$database=DB_NAME;

session_start();
//if (isset($_SESSION['loginAccountNumber'])) {
//	$account = $_SESSION['loginAccountNumber'];
//}
$account = 0;
//echo "account $account";
if (1 == 1) { // production
	if (isset($_SESSION['member'])) {
		$member = $_SESSION['member'];
		$account = $member->getValue( "id" );
	} 
} else {
	$account = $_GET['account'];
}
//echo "account $account";

$id = $_GET['id'];
$remove = $_GET['remove'];
// tjs 140213
$detail = $_GET['detail'];
//echo "account $account remove $remove id $id charityName $_GET[charityName] shortName $_GET[shortName]";
//tjs101011
//define("MYSQL_HOST", "localhost");
//$username="root";
//$password="root";
//$database="COLLORG";

// 140203
//$con = mysql_connect('localhost',$username,$password);
//@mysql_select_db($database) or die( "Unable to select database");

/*
 "id" => "",
 "memberid" => "",
 "charityname" => "",
 "shortname" => "",
 "dunns" => "",
 "url" => "",
 "description" => "",
 "numstars" => "",
 "createddate" => "",
 "isinactive" => "",
 "baseid" => "",
 "isforprofit" => "",
 */
if ($id == 0) {
	$url = "";
	$description == "";
	$numStars = 0;
	$isInactive = 0;
	$isForProfit = 0;
	if ($detail == 'true') {
		$url = $_GET['url'];;
		$description == $_GET['description'];;
		$numStars = $_GET['numStars'];;
		$isInactive = $_GET['isInactive'];;
		$isForProfit = $_GET['isForProfit'];;
	}
	// add the new charity
	$charity = new Charity( array(
    "memberid" => $account,
    "charityname" => $_GET[charityName],
    "shortname" => $_GET[shortName],
    "url" => $url,
    "description" => $description,
    "numstars" =>  $numStars,
    "createddate" => date("Y-m-d"),
    "isinactive" =>  $isInactive,
    "baseid" =>  0,
     "isforprofit" => $isForProfit     
	) );
	$charityName = $charity -> getValue('charityname');
	//echo "inserting new charity named $charityName";
	$charity -> insert();
	//echo "inserted new charity named $charityName";
	$charityId = $charity -> getValue('id');
	//echo "inserted id $charityId";
	// tjs 140203
	//$sql="INSERT INTO charities (memberId, charityName, shortName)
	//VALUES
	//('$account','$_GET[charityName]','$_GET[shortName]')";
} else {
	$charity = Charity::getCharity($id);
	if ($remove == 'true') {
		// tjs 140203
		//$sql = "UPDATE charities SET isinactive = 1
		//WHERE id = $id";
		$charity -> delete();
	} else {
		// tjs 140203
		$charity -> setCharityName($_GET[charityName]);
		$charity -> setShortName($_GET[shortName]);
		//$url = "";
		//$description == "";
		//$numStars = 0;
		//$isInactive = 0;
		//$isForProfit = 0;
		if ($detail == 'true') {
			$charity -> setUrl($_GET['url']);
			$charity -> setDescription($_GET['description']);
			$charity -> setNumStars($_GET['numStars']);
			$charity -> setIsInactive($_GET['isInactive']);
			$charity -> setIsForProfit($_GET['isForProfit']);
		}
		
		$charity -> update();
		//$sql = "UPDATE charities SET charityName = '$_GET[charityName]', shortName = '$_GET[shortName]'
		//WHERE id = $id";
	}
}

/*
 * tjs 140203
 if (!mysql_query($sql,$con))
 {
 die('Error: ' . mysql_error());
 }

 mysql_close();
 */
?>


