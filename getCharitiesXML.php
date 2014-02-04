<?php
/***************************************
$Revision:: 155                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-15 18:11:07#$: Date of last commit
***************************************/
/*
getCharitiesXML.php
tjs 101118

file version 1.00 

release version 1.00
*/
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php?account=0
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php?account=1
//http://new-host-8.home:8081/getCharitiesXML.php?account=1

date_default_timezone_set ( "America/New_York" );

$account = 0;
require_once( "Member.class.php" );
// tjs 111026
// tjs 130723
//require_once( "Donation.class.php" );
require_once( "Charity.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
//$username=DB_USERNAME;
//$password=DB_PASSWORD;
//$database=DB_NAME;
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
//tjs110308
$detail = $_GET['detail'];

// tjs 130723
/*
//tjs101011
define("MYSQL_HOST", "localhost");

$con = mysql_connect("".MYSQL_HOST."",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$query="SELECT id FROM charities where memberId = ".$account;
//echo $query;
$result=mysql_query($query);
$num=mysql_numrows($result);
// e.g. 489
//echo $num;
if ($num < 1) {
	$query="SELECT * FROM charities where memberId = 0 and isInactive is null";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	//e.g. 486
	//echo $num;
	if ($num > 0) {
		$sql="INSERT INTO charities (memberId, charityName, shortName, baseId, isForProfit) VALUES ";
		$i=0;
		while ($i < $num) {
			$charityId=mysql_result($result,$i,"id");

			$charityName=mysql_result($result,$i,"charityName");
			$shortName=mysql_result($result,$i,"shortName");
			if ($shortName == null) {
				$shortName = ' ';
			}
			$isForProfit=mysql_result($result,$i,"isForProfit");
			if ($isForProfit == null) {
				$isForProfit = '0';
			} else {
				$isForProfit = '1';
			}
			$sql .= '('.$account.',"'.$charityName.'","'.$shortName.'",'.$charityId.','.$isForProfit.')';
//echo $sql;
			if ($i < $num - 1) {
				//$sql += "('$account','$charityName','$shortName'),";
				//$sql = $sql + ",";
				$sql .= ",";
			} else {
				//$sql = $sql + ";";
				$sql .= ";";
			}
			$i++;
		}
		//echo $sql;
		if (!mysql_query($sql,$con))
  		{
  			die('Error: ' . mysql_error());
  		}	
	}
}
*/

header('Content-Type: application/xml, charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "<charities>";
printf("\n");

// tjs 130723
list( $charities, $totalRows ) = Charity::getCharitiesForMember( $account, $detail);
	//echo "totalRows $totalRows";
	// e.g. totalRows 5
$i=0;
foreach ( $charities as $charity ) {
	//echo "index counter $i";
	// e.g. index counter 0
//while ($i < $totalRows) {
	//$charity = $charities[$i];
	//$charityId=$charity.getValue("id");
	$charityId=$charity->getValue("id");
	//echo "charityId $charityId";
	// e.g. charityId 6
	// tjs 130902
	$memberId=$charity->getValue("memberid");
	
	//$charityName=$charity.getValue("charityName");
	$charityName=$charity->getValue("charityName");
	//echo "charityName $charityName";
	
	$shortName=$charity->getValue("shortName");
	// tjs 120725 TODO fix kludge
	//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId, 2000, 2100 );
	list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, 2000, 2100 );
	//$solicitations = 0;
	//$rate = 0;
	//$donations = 0;
	//$average = 0;
	//$lastAmount = 0;
	//echo "solicitations $solicitations rate $rate donations $donations average $average lastAmount $lastAmount";
	$dunns=$charity->getValue("dunns");
	$dunnsPart = '<dunns/>';
	if (strlen($dunns) > 0)
		$dunnsPart = '<dunns>'.$dunns.'</dunns>';
	$url=$charity->getValue("url");
	$urlPart = '<url/>';
	if (strlen($url) > 0)
		$urlPart = '<url>'.$url.'</url>';
	$description=$charity->getValue("description");
	$descriptionPart = '<description/>';
	if (strlen($description) > 0)
		$descriptionPart = '<description>'.$description.'</description>';
	$numStars=$charity->getValue("numStars");
	$numStarsPart = '<stars>'.$numStars.'</stars>';
	
	//tjs110308
	$baseId=$charity->getValue("baseId");
	$baseIdPart = '<baseId/>';
	if (strlen($baseId) > 0)
		$baseIdPart = '<baseId>'.$baseId.'</baseId>';
	$isInactive=$charity->getValue("isInactive");
	$isInactivePart = '<isInactive/>';
	if (strlen($isInactive) > 0)
		$isInactivePart = '<isInactive>'.$isInactive.'</isInactive>';
	$isForProfit=$charity->getValue("isForProfit");
	$isForProfitPart = '<isForProfit/>';
	if (strlen($isForProfit) > 0)
		$isForProfitPart = '<isForProfit>'.$isForProfit.'</isForProfit>';
	
/*
$query="SELECT * FROM charities where memberId = ".$account;
if ($detail == 'false') {
	$query="SELECT * FROM charities where memberId = ".$account." and (isInactive is null or isInactive = 0)";
}
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
	list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId, 2000, 2100 );
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
*/
	
		// tjs 111027
	//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
	//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
	printf("<charity id=\"%s\" memberId=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $memberId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
	
	$i++;
}

// tjs 130723
//mysql_close();
echo "</charities>";

?> 



