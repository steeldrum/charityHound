<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
ratings.php
tjs 120316

file version 1.00 

release version 1.00
*/
// memberId 1 is SteelDrum
//add
//http://localhost/~thomassoucy/charityhound/ratings.php?account=1&charityId=545&date=2012-03-16&blank=true&currency=false&reminder=false&confidential=false
//alter
//delete

require_once( "Member.class.php" );
require_once( "Rating.class.php" );
require_once( "Donation.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
//echo "ratings...";

session_start();
$account = 0;
if (isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	if ($member) {
		$account = $member->getValue( "id" );
	}
} 
if ($account == 0) {
	$account = $_GET['account'];
}
//echo "account ".$account;

$date = $_GET['date'];
//echo "date ".$date;
$year = substr($date, 0, 4);
$charityId = $_GET['charityId'];
$blank = $_GET['blank'];
$currency = $_GET['currency'];
$reminder = $_GET['reminder'];
$confidential = $_GET['confidential'];

$blankCount = 0;
$currencyCount = 0;
$reminderCount = 0;
$confidentialCount = 0;
if ($blank == 'true')
	$blankCount++;
if ($currency == 'true')
	$currencyCount++;
if ($reminder == 'true')
	$reminderCount++;
if ($confidential == 'true')
	$confidentialCount++;

//echo "blankCount ".$blankCount." currencyCount ".$currencyCount;
	
	$id = 0;

	// tjs 140212
	$conn = null;
	$isSelectableForSite = Member::getMember( $account  )->getValue( "isselectableforsite" );		
	//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $account, $charityId, $year, $year );
			list( $solicitations, $rate, $donations, $average, $lastAmount, $conn  ) = Donation::deriveDonationInfo4Charity( $account, $isSelectableForSite, $charityId, $conn, $year, $year );
		// tjs 140212
	Donation::dropconnect($conn);
			
$rating = Rating::getCharityRatingForYear( $account, $charityId, $year );
if ($rating == null) {
	/*
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `charityId` smallint(5) unsigned NOT NULL,
  `memberId` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `solicitations` smallint(5) unsigned NOT NULL,
  `blankEnvelopeAppeals` smallint(5) unsigned NOT NULL,
  `currencyBatedAppeals` smallint(5) unsigned NOT NULL,
  `appealReminderSchedules` smallint(5) unsigned NOT NULL,
  `appealPrivacyPledges` smallint(5) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ratingForMemberYear` (`charityId`,`memberId`,`year`)
*/
/*	
	  $rating = new Rating( array( 
    "charityId" => $charityId,
    "memberId" => $account,
    "year" => $year,
    "solicitations" => $solicitations,
    "blankEnvelopeAppeals" => $blankCount,
    "currencyBatedAppeals" => $currencyCount,
    "appealReminderSchedules" => $reminderCount,
    "appealPrivacyPledges" => $confidentialCount
  ) );
  */
		  $rating = new Rating( array( 
    "charityid" => $charityId,
    "memberid" => $account,
    "year" => $year,
    "solicitations" => $solicitations,
    "blankenvelopeappeals" => $blankCount,
    "currencybatedappeals" => $currencyCount,
    "appealreminderschedules" => $reminderCount,
    "appealprivacypledges" => $confidentialCount
  ) );
		$rating->insert();
} else {
	    $cumBlankEnvelopeAppeals = $blankCount + $rating->getBlankEnvelopeAppeals();
	    $cumCurrencyBatedAppeals = $currencyCount + $rating->getCurrencyBatedAppeals();
	    $cumAppealReminderSchedules = $reminderCount + $rating->getAppealReminderSchedules();
	    $cumAppealPrivacyPledges = $confidentialCount + $rating->getAppealPrivacyPledges();
  $rating->setSolicitations($solicitations);
  $rating->setBlankEnvelopeAppeals($cumBlankEnvelopeAppeals);
    $rating->setCurrencyBatedAppeals($cumCurrencyBatedAppeals);
    $rating->setAppealReminderSchedules($cumAppealReminderSchedules);
    $rating->setAppealPrivacyPledges($cumAppealPrivacyPledges);
	 $rating->update();   
}

?> 


