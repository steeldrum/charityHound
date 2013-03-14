<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
getBaseCharityXML.php
tjs 130226

file version 1.00 

release version 1.00
*/
//http://new-host-8.home:8081/getBaseCharityXML.php?account=1&memberCharityId=545

date_default_timezone_set ( "America/New_York" );

$account = 0;
require_once( "Member.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
$username=DB_USERNAME;
$password=DB_PASSWORD;
$database=DB_NAME;
// tjs 130226
$aggregateDSN=AGGREGATE_DSN;
$aggregateDatabase=AGGREGATE_DB_NAME;
session_start();
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
$memberCharityId = $_GET['memberCharityId'];
//echo "account $account member charity id $memberCharityId";

//tjs101011
define("MYSQL_HOST", "localhost");

$con = mysql_connect("".MYSQL_HOST."",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

/*
e.g. insert
INSERT INTO `charities` (`id`, `memberId`, `charityName`, `shortName`, `dunns`, `url`, `numStars`, `createdDate`, `isInactive`) VALUES
(1, 0, 'A Wider Circle', NULL, NULL, NULL, 0, '2010-11-17 11:05:01', NULL),
(501, 0, 'Yosemite Fund', NULL, NULL, NULL, 0, '2010-11-17 11:05:01', NULL);
*/

$query="SELECT baseId FROM charities where memberId = ".$account." and id = ".$memberCharityId;
//echo $query;
$result=mysql_query($query);
$num=mysql_numrows($result);
// e.g. 489
//echo $num;
if ($num == 1) {
	$baseId=mysql_result($result,0,"baseId");
	//echo $baseId;
	header('Content-Type: application/xml, charset=utf-8');
	echo '<?xml version="1.0" encoding="utf-8"?>';
	//echo "<charities>";
	printf("<charities provider=\"%s\" database=\"%s\" >", $aggregateDSN, $aggregateDatabase);
	printf("\n");
	
	//$query="SELECT * FROM charities where memberId = ".$account." and id = ".$baseId;
	$query="SELECT * FROM charities where id = ".$baseId;
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	// e.g 488
	//echo "num for account".$num;
	$i=0;
	while ($i < $num) {
		$charityId=mysql_result($result,$i,"id");
		
		$charityName=mysql_result($result,$i,"charityName");
		$shortName=mysql_result($result,$i,"shortName");
		//if ($i == 0) {
			// e.g. id 2 name AARP Foundation short 
			//echo "id ".$charityId." name ".$charityName." short ".$shortName;
		//}
		
		// tjs 111027
		//list( $solicitations, $rate, $donations, $average ) = Donation::deriveDonationInfo4Charity( $account, $charityId );
		//tjs 111115
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId );
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId, 2000, 2100 );
		// tjs 120618
		//if ($i == 0) {
		//	echo "id ".$charityId." sols ".$solicitations." rate ".$rate." dons ".$donations." ave ".$average." last ".$lastAmount;
		//}
		$dunns=mysql_result($result,$i,"dunns");
		$dunnsPart = '<dunns/>';
		if (strlen($dunns) > 0)
			$dunnsPart = '<dunns>'.$dunns.'</dunns>';
		$url=mysql_result($result,$i,"url");
		$urlPart = '<url/>';
		if (strlen($url) > 0)
			$urlPart = '<url>'.$url.'</url>';
		$description=mysql_result($result,$i,"description");
		$descriptionPart = '<description/>';
		if (strlen($description) > 0)
			$descriptionPart = '<description>'.$description.'</description>';
		$numStars=mysql_result($result,$i,"numStars");
		$numStarsPart = '<stars>'.$numStars.'</stars>';
		
		//tjs110308
		$baseId=mysql_result($result,$i,"baseId");
		$baseIdPart = '<baseId/>';
		if (strlen($baseId) > 0)
			$baseIdPart = '<baseId>'.$baseId.'</baseId>';
		$isInactive=mysql_result($result,$i,"isInactive");
		$isInactivePart = '<isInactive/>';
		if (strlen($isInactive) > 0)
			$isInactivePart = '<isInactive>'.$isInactive.'</isInactive>';
		$isForProfit=mysql_result($result,$i,"isForProfit");
		$isForProfitPart = '<isForProfit/>';
		if (strlen($isForProfit) > 0)
			$isForProfitPart = '<isForProfit>'.$isForProfit.'</isForProfit>';
		
			// tjs 111027
		//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		
		$i++;
	}
}

mysql_close();
echo "</charities>";

?> 



