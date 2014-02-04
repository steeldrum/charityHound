<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
getDonationsXML.php
tjs 101120

file version 1.00 

release version 1.00
*/
//tests
//http://localhost/getDonationsXML.php
//http://localhost/getDonationsXML.php?account=0
//http://localhost/~thomassoucy/charityhound/getDonationsXML.php?account=1&charityId=537

require_once( "Member.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
// tjs 130902
//$username=DB_USERNAME;
//$password=DB_PASSWORD;
//$database=DB_NAME;
require_once( "Donation.class.php" );
session_start();
$account = 0;
if (isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	if ($member) {
		$account = $member->getValue( "id" );
	}
} 
//tjs110225
if ($account == 0) {
	$account = $_GET['account'];
}

//tjs101011
$charityId = $_GET['charityId'];

// tjs 130902
header('Content-Type: application/xml, charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "<donations>";
printf("\n");
list( $donations, $totalRows ) = Donation::getDonationsByCharityId( $account, $charityId);
	//echo "totalRows $totalRows";
$i=0;
foreach ( $donations as $donation ) {
	//echo "index counter $i";
	// e.g. index counter 0
//while ($i < $totalRows) {
	//$charity = $charities[$i];
	//$charityId=$charity.getValue("id");
	$id=$donation->getValue("id");
	$amount=$donation->getValue("amount");
	$date=$donation->getValue("madeon");
	$donation= '<donation id="'.$id.'"><memberId>'.$account.'</memberId><charityId>'.$charityId.'</charityId><amount>'.$amount.'</amount><date>'.$date.'</date></donation>';
	echo $donation;
	
	//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
	
	$i++;
}
	
/* tjs 130902
define("MYSQL_HOST", "localhost");

//$username="root";
//$password="root";
//$database="COLLORG";

mysql_connect("".MYSQL_HOST."",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" ?><donations>";

$query="SELECT * FROM donations where memberId = ".$account." and charityId = ".$charityId." order by madeOn desc";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while ($i < $num) {
	$id=mysql_result($result,$i,"id");
	$amount=mysql_result($result,$i,"amount");
	$date=mysql_result($result,$i,"madeOn");
	$donation= '<donation id="'.$id.'"><memberId>'.$account.'</memberId><charityId>'.$charityId.'</charityId><amount>'.$amount.'</amount><date>'.$date.'</date></donation>';
	echo $donation;
	
	$i++;
}

mysql_close();
*/
echo "</donations>";

?> 


