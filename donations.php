<?php
/***************************************
 $Revision:: 94                         $: Revision of last commit
 $LastChangedBy::                       $: Author of last commit
 $LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
 ***************************************/
/*
 donations.php
 tjs 101119

 file version 1.00

 release version 1.00
 */
// memberId 1 is SteelDrum
//add
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=5&charityId=5&remove=false&id=0
//alter
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=15&charityId=5&remove=false&id=1
//delete
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=5&charityId=5&remove=true&id=1

//$account = $_GET['account'];
//echo "processdonation...";

// tjs 130726
date_default_timezone_set ( "America/New_York" );
$account = 0;

require_once( "Member.class.php" );
//require_once( "Donation.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
//$username=DB_USERNAME;
//$password=DB_PASSWORD;
//$database=DB_NAME;
session_start();
//if (isset($_SESSION['loginAccountNumber'])) {
//	$account = $_SESSION['loginAccountNumber'];
//}
//$account = 0;
//echo "processdonation account $account";
// e.g. processdonation account 0

if (isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	if ($member) {
		$account = $member->getValue( "id" );
	}
}
//tjs130726
if ($account == 0) {
	$account = $_GET['account'];
}
//echo "processdonation final account $account";

// tjs 130726
$id = $_GET['id'];
$remove = $_GET['remove'];
//$id = $_POST['id'];
//$remove = $_POST['remove'];

//tjs101011
//define("MYSQL_HOST", "localhost");
//$username="root";
//$password="root";
//$database="COLLORG";

//$con = mysql_connect('localhost',$username,$password);
//@mysql_select_db($database) or die( "Unable to select database");

// tjs 130726
try {
	// tjs 130719 - conversion to postgreSQL
	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
	//$conn = new PDO( DB_DSN, DB_HOST, DB_USERNAME, DB_PASSWORD );
	$conn->setAttribute( PDO::ATTR_PERSISTENT, true );
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch ( PDOException $e ) {
	die( "Connection failed: " . $e->getMessage() );
}

/* tjs 130725
 if ($id == 0) {
 // add the new donation
 //$date =   date( "Y-m-d" );
 $sql="INSERT INTO donations (charityId, memberId, amount)
 VALUES ('$_GET[charityId]','$account','$_GET[amount]')";
 } else {
 if ($remove == 'true') {
 $sql = "DELETE FROM donations WHERE id = $id";
 } else {
 $sql = "UPDATE donations SET amount = '$_GET[amount]', madeOn = '$_GET[date]' WHERE id = $id";
 }
 }
 */
//echo "processdonation id $id";
if ($id == 0) {
	//echo "adding donation...";
	// add the new donation
	/*
	 $donation = new Donation( array(
	 "charityid" => $_GET[charityId],
	 //"charityid" => isset( $_POST[charityId] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST[charityId] ) : "",
	 "memberid" => $account,
	 "amount" => $_GET[amount],
	 "madeon" => ''
	 //"amount" => isset( $_POST[amount] ) ? preg_replace( "/[^ \-\0-9]/", "", $_POST[amount] ) : "",
	 ) );
	 //echo "inserting donation...";
	 $donation.insert();*/
	$sql = "INSERT INTO " . TBL_DONATIONS . " (
              charityid,
              memberid,
              amount
            ) VALUES (
              :charityId,
              :memberId,
              :amount
             )";
	//echo "Donation insert sql $sql";

	//$charityId = $this->data["charityid"];
	//echo "Donation insert charityId $charityId";
	try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":charityId", $_GET[charityId], PDO::PARAM_STR );
		$st->bindValue( ":memberId", $account, PDO::PARAM_STR );
		$st->bindValue( ":amount", $_GET[amount], PDO::PARAM_STR );
		$st->execute();
		$conn = "";
	} catch ( PDOException $e ) {
		$conn = "";
		die( "Query failed: " . $e->getMessage() );
	}

} else {
	if ($remove == 'true') {
		/*$donation = new Donation( array(
		 "id" => $id
		 ) );
		 $donation.delete();*/
		$sql = "DELETE FROM " . TBL_DONATIONS . " WHERE id = :id";

		try {
			$st = $conn->prepare( $sql );
			$st->bindValue( ":id", $id, PDO::PARAM_INT );
			$st->execute();
			$conn = "";
		} catch ( PDOException $e ) {
			$conn = "";
			die( "Query failed: " . $e->getMessage() );
		}

	} else {
		/*$donation = new Donation( array(
		 "id" => $id,
		 "madeon" => isset( $_GET[date] ) ? preg_replace( "/[^ \-\0-9]/", "", $_GET[date] ) : "",
		 //"madeon" => isset( $_POST[date] ) ? preg_replace( "/[^ \-\0-9]/", "", $_POST[date] ) : "",
		 ) );
		 $donation.update();*/
		/* tjs 130902
		$sql = "UPDATE " . TBL_DONATIONS . " SET
              madeon = :madeOn
            WHERE id = :id";
*/
		
				$sql = "UPDATE " . TBL_DONATIONS . " SET
              madeon = :madeOn, amount = :amount
            WHERE id = :id";
		
		try {
			$st = $conn->prepare( $sql );
			$st->bindValue( ":id", $id, PDO::PARAM_INT );
			$st->bindValue( ":madeOn", $_GET[date], PDO::PARAM_STR );
			$st->bindValue( ":amount", $_GET[amount], PDO::PARAM_STR );
			$st->execute();
			$conn = "";
		} catch ( PDOException $e ) {
			$conn = "";
			die( "Query failed: " . $e->getMessage() );
		}

	}
}
/*
 if (!mysql_query($sql,$con))
 {
 die('Error: ' . mysql_error());
 }

 mysql_close();
 */
?>


