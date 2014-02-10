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
//echo "start";
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php?account=0
//http://localhost/~thomassoucy/charityhound/getCharitiesXML.php?account=1
//http://new-host-8.home:8081/getCharitiesXML.php?account=1

date_default_timezone_set ( "America/New_York" );

// tjs 140209
//$account = 0;
$account = $_GET['account'];
//echo "account $account";

// tjs 140208
$isSelectableForSite = 0;
$isSessionAccount = false;
require_once( "Member.class.php" );
// tjs 111026
// tjs 130723
//require_once( "Donation.class.php" );
require_once( "Charity.class.php" );
session_start();
//echo "account $account";

if (isset($_SESSION['member'])) {
//if ($account > 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	//echo "member?";
	if ($member) {
		//$account = $member->getValue( "id" );
		$sessionAccount = $member->getValue( "id" );
		//echo "member account $sessionAccount";
		$isSelectableForSite = $member->getValue( "isselectableforsite" );
		//if ($sessionAccount == $account) {
		$account = $sessionAccount;
			$isSessionAccount = true;
		//}
	} else {
		$isSelectableForSite = $_GET['tracker'];
		$isSessionAccount = true;
	}
} else {
	$isSelectableForSite = $_GET['tracker'];
	$isSessionAccount = true;
}
// for debug:
//echo "account $account isSelectableForSite $isSelectableForSite";

//tjs110225
/*
 if ($account == 0) {
 $account = $_GET['account'];
 $isSelectableForSite = $_GET['tracker'];
 }
 */
//tjs110308

$detail = $_GET['detail'];

header('Content-Type: application/xml, charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
//echo "<charities>";

//echo '<charity id="1" memberId="0" solicitations="0" rate="0" donations="0" average="0" lastAmount="0"><charityName>A Wider Circle</charityName><shortName></shortName><dunns/><url/><description/><stars>0</stars><baseId/><isInactive/><isForProfit/></charity>';

echo '<charities account = "';
echo $account;
echo '" tracker = "';
echo $isSelectableForSite;
echo '">';
printf("\n");

if ($isSessionAccount) {
	// tjs 130723
	list( $charities, $totalRows ) = Charity::getCharitiesForMember( $account, $detail);
	//echo "totalRows $totalRows";
	// e.g. totalRows 5
	$i=0;
	// tjs 140210
	$conn = null;
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
		//$memberId=$charity->getValue("memberid");

		//$charityName=$charity.getValue("charityName");
		$charityName=$charity->getValue("charityName");
		//echo "charityName $charityName";

		$shortName=$charity->getValue("shortName");
		// tjs 120725 TODO fix kludge
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId, 2000, 2100 );
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, 2000, 2100 );
		// tjs 140208
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $isSelectableForSite, $charityId, 2000, 2100 );
		// tjs 140210 - dummy up for test by commenting out...
		//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $isSelectableForSite, $charityId, 2000, 2100 );
		// tjs 140210 preserve the RDBS connection...
		list( $solicitations, $rate, $donations, $average, $lastAmount, $conn ) = Donation::deriveDonationInfo4Charity( $account, $isSelectableForSite, $charityId, $conn, 2000, 2100 );
		/* test dummy data:
		$solicitations = 1;
		$rate = 2;
		$donations = 1;
		$average = 10;
		$lastAmount = 0;
		*/
		//echo "solicitations $solicitations rate $rate donations $donations average $average lastAmount $lastAmount";
		$dunns=$charity->getValue("dunns");
		$dunnsPart = '<dunns/>';
		if (strlen($dunns) > 0) {
		$dunnsPart = '<dunns>'.$dunns.'</dunns>';}
		$url=$charity->getValue("url");
		$urlPart = '<url/>';
		if (strlen($url) > 0) {
		$urlPart = '<url>'.$url.'</url>';}
		$description=$charity->getValue("description");
		$descriptionPart = '<description/>';
		if (strlen($description) > 0) {
		$descriptionPart = '<description>'.$description.'</description>';}
		$numStars=$charity->getValue("numStars");
		$numStarsPart = '<stars>'.$numStars.'</stars>';

		//tjs110308
		$baseId=$charity->getValue("baseId");
		$baseIdPart = '<baseId/>';
		if (strlen($baseId) > 0) {
		$baseIdPart = '<baseId>'.$baseId.'</baseId>';}
		$isInactive=$charity->getValue("isInactive");
		$isInactivePart = '<isInactive/>';
		if (strlen($isInactive) > 0) {
		$isInactivePart = '<isInactive>'.$isInactive.'</isInactive>';}
		$isForProfit=$charity->getValue("isForProfit");
		$isForProfitPart = '<isForProfit/>';
		if (strlen($isForProfit) > 0) {
		$isForProfitPart = '<isForProfit>'.$isForProfit.'</isForProfit>';}

		// tjs 111027
		//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		//printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		//printf("<charity id=\"%s\" memberId=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $memberId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		printf("<charity id=\"%s\" solicitations=\"%s\" rate=\"%s\" donations=\"%s\" average=\"%s\" lastAmount=\"%s\"><charityName>%s</charityName><shortName>%s</shortName>%s%s%s%s%s%s%s</charity>\n", $charityId, $solicitations, $rate, $donations, $average, $lastAmount, $charityName, $shortName, $dunnsPart, $urlPart, $descriptionPart, $numStarsPart, $baseIdPart, $isInactivePart, $isForProfitPart);
		$i++;
	}
	// tjs 140210
	Donation::dropconnect($conn);
}

echo "</charities>";

?>



