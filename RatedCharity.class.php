<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
charityhound/
RatedCharity.class.php
tjs 120319

file version 1.00 

release version 1.00
*/

require_once "DataObject.class.php";
require_once "Charity.class.php";
//require_once( "Donation.class.php" );
require_once( "Rating.class.php" );

date_default_timezone_set ( "America/New_York" );

class RatedCharity extends Charity {
/*
    `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `memberId` smallint(5) unsigned NOT NULL,
  `charityName` varchar(64) NOT NULL,
  `shortName` varchar(15) DEFAULT NULL,
  `dunns` varchar(15) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `numStars` mediumint(9) NOT NULL,
  `createdDate` date NOT NULL,
  `isInactive` tinyint(4) DEFAULT NULL,
  `baseId` smallint(5) unsigned DEFAULT NULL,
  `isForProfit` smallint(5) unsigned DEFAULT NULL,
*/
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

  protected $data = array(
    "solicitations" => "",
    "blankEnvelopeAppeals" => "",
    "currencyBatedAppeals" => "",
    "appealReminderSchedules" => "",
    "appealPrivacyPledges" => ""
  );
/*
  public static function getCharities( $startRow, $numRows, $order ) {
$year = 2011;
  list( $charities, $totalRows ) = parent::getCharities( $startRow, $numRows, $order );
  $rowCount = 0;
      $ratedCharities = array();

foreach ( $charities as $charity ) {
  $rowCount++;
  $id = $charity->getValueEncoded( "id" );
  $rating = Rating::getCharityRatingForYear($memberId, $charityId, $year);
  $ratedCharity = new RatedCharity();
  $ratedCharity->data['id'] = $id;
  $ratedCharity->data['memberId'] = $ratedCharity->data['memberId'];
  $ratedCharity->data['charityName'] = $ratedCharity->data['charityName'];
  $ratedCharity->data['shortName'] = $ratedCharity->data['shortName'];
  //$ratedCahrity->data['dunns'] = ;
  //$ratedCahrity->data['url'] = ;
  //$ratedCahrity->data['description'] = ;
  $ratedCharity->data['numStars'] = $ratedCharity->data['numStars'];
 // $ratedCahrity->data['createdDate'] = ;
  //$ratedCahrity->data['isInactive'] = ;
  //$ratedCahrity->data['baseId'] = ;
  //$ratedCahrity->data['isForProfit'] = ;
  $ratedCharity->data['solicitations'] = $rating->getSolicitations();
  $ratedCharity->data['blankEnvelopeAppeals'] = $rating->getBlankEnvelopeAppeals();
  $ratedCharity->data['currencyBatedAppeals'] = $rating->getCurrencyBatedAppeals();
  $ratedCharity->data['appealReminderSchedules'] = $rating->getAppealReminderSchedules();
  $ratedCharity->data['appealPrivacyPledges'] = $rating->getAppealPrivacyPledges();
  $ratedCharities[] = $ratedCharity;
}
      return array( $ratedCharities, $rowCount );
  }
*/  
    
    public static function getDesignatedCharities( $memberId, $fromYear, $toYear, $startRow, $numRows, $order ) {

    $donations = Donation::getDonationsForYears( $memberId, $fromYear, $toYear);

    $charities = array();
    $designatedCharities = array();
    $allDesignatedCharities = array();
    $lastCharityId = 0;
    $amount = 0;
    $lastAmount = 0;
    //$count = 0;
    //$lastCount = 0;
    $charityId = 0;
	foreach ( $donations as $donation ) {
		$charityId = $donation->getCharityId();
		$lastAmount = $donation->getAmount();
		//echo "prior charityId ".$charityId." lastAmount ".$lastAmount;
		if ($lastCharityId == 0) {
			$lastCharityId = $charityId;
		}
		if ($charityId != $lastCharityId) {
			//if ($amount > 0) {
			if ($amount == 0) {
				//echo "prior lastCharityId ".$lastCharityId." amount ".$amount;
				$charities[] = Charity::getCharity( $lastCharityId );;
				//echo "size of priorYearCharities ".sizeof($priorYearCharities);
			}
				$amount = $lastAmount;
			$lastCharityId = $charityId;
		} else {
			//echo "charityId ".$charityId." amount ".$donation->getAmount();
			$amount += $lastAmount;
		}
	}
	if ($charityId != $lastCharityId) {
			//echo "prior final lastCharityId ".$lastCharityId." amount ".$amount;			
		//if ($amount > 0) {
		if ($amount == 0) {
			$charities[] = Charity::getCharity( $lastCharityId );
		}
	}
    
  for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i)
{
    $charity = $charities[$i];
    $charityId = $charity->data["id"];
  $ratings = Rating::getCharityRatingForYears($memberId, $charityId, $fromYear, $toYear);
  $blankCount = 0;
$currencyCount = 0;
$reminderCount = 0;
$confidentialCount = 0;
$solicitations = 0;
$cumRating = 0;  
    for($j = 0; $j < sizeof($ratings); $j++)
{
	$rating = $ratings[$j];
	//$solicitations = $rating->getSolicitations();
		    $blankCount += $rating->getBlankEnvelopeAppeals();
	    $currencyCount += $rating->getCurrencyBatedAppeals();
	    $reminderCount += $rating->getAppealReminderSchedules();
	    $confidentialCount += $rating->getAppealPrivacyPledges();
}
	    $cumRating = $blankCount + $currencyCount + $reminderCount + $confidentialCount; 
 if ($cumRating > 0) {
  $ratedCharity = new RatedCharity();
  $ratedCharity->data['id'] = $id;
  $ratedCharity->data['memberId'] = $charity->data['memberId'];
  $ratedCharity->data['charityName'] = $charity->data['charityName'];
  $ratedCharity->data['shortName'] = $charity->data['shortName'];
  //$ratedCahrity->data['dunns'] = ;
  //$ratedCahrity->data['url'] = ;
  //$ratedCahrity->data['description'] = ;
  //$ratedCharity->data['numStars'] = $charity->data['numStars'];
 // $ratedCahrity->data['createdDate'] = ;
  //$ratedCahrity->data['isInactive'] = ;
  //$ratedCahrity->data['baseId'] = ;
  //$ratedCahrity->data['isForProfit'] = ;
  //$ratedCharity->data['solicitations'] = $solicitations;
  $ratedCharity->data['blankEnvelopeAppeals'] = $blankCount;
  $ratedCharity->data['currencyBatedAppeals'] = $currencyCount;
  $ratedCharity->data['appealReminderSchedules'] = $reminderCount;
  $ratedCharity->data['appealPrivacyPledges'] = $confidentialCount;
 // $ratedCharities[] = $ratedCharity;
    //tjs 111115
    //list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
    list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
	//echo "charityId ".$charityId." rate ".$rate;
    $ratedCharity->data["baseId"] = $rate;
    //$charity->data["numStars"] = $donations*$average;
    $ratedCharity->data["numStars"] = $solicitations;
    $allDesignatedCharities[] = $ratedCharity;
 }
}

// tjs 111026 sort based on order
if ($order == "charityName") {
	usort($allDesignatedCharities, array("Charity", "cmp_charityName"));
} else if ($order == "numStars") {
	//usort($allRemittedCharities, array("Charity", "cmp_shortName"));
	usort($allDesignatedCharities, array("Charity", "cmp_numStars"));
} else if ($order == "baseid") {
	usort($allDesignatedCharities, array("Charity", "cmp_baseId"));
} else if ($order == "schedule") {
	usort($allDesignatedCharities, array("RatedCharity", "cmp_schedule"));
}
 else if ($order == "confidential") {
	usort($allDesignatedCharities, array("RatedCharity", "cmp_confidential"));
}
 else if ($order == "blank") {
	usort($allDesignatedCharities, array("RatedCharity", "cmp_blank"));
}
 else if ($order == "currency") {
	usort($allDesignatedCharities, array("RatedCharity", "cmp_currency"));
}


$sizeOfCharities = sizeof($allDesignatedCharities);
//echo "sizeOfAllLapsedCharities ".$sizeOfAllLapsedCharities;
$countCharities = 0;
if ($startRow < $sizeOfCharities) {
for ($k = $startRow; $k < $sizeOfCharities; ++$k) {
	if ($countCharities++ < $numRows) {
		$designatedCharities[] = $allDesignatedCharities[$k];
	}
}
}

return array( $designatedCharities, $sizeOfCharities );
  }
  
    static function cmp_blank($a, $b)
    {
        $al = $a->data["blankEnvelopeAppeals"];
        $bl = $b->data["blankEnvelopeAppeals"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }    
    static function cmp_currency($a, $b)
    {
        $al = $a->data["currencyBatedAppeals"];
        $bl = $b->data["currencyBatedAppeals"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    } 
    static function cmp_confidential($a, $b)
    {
        $al = $a->data["appealPrivacyPledges"];
        $bl = $b->data["appealPrivacyPledges"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }    
        static function cmp_schedule($a, $b)
    {
        $al = $a->data["appealReminderSchedules"];
        $bl = $b->data["appealReminderSchedules"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }    
    
     // public function getYear() {
    //return $this->data["year"];
 // }

    public function getSolicitations() {
    return $this->data["solicitations"];
  }
    public function getBlankEnvelopeAppeals() {
    return $this->data["blankEnvelopeAppeals"];
  }
    public function getCurrencyBatedAppeals() {
    return $this->data["currencyBatedAppeals"];
  }
    public function getAppealReminderSchedules() {
    return $this->data["appealReminderSchedules"];
  }
    public function getAppealPrivacyPledges() {
    return $this->data["appealPrivacyPledges"];
  }
  
  public function setSolicitations($solicitations) {
    $this->data["solicitations"] = $solicitations;
  }
  public function setBlankEnvelopeAppeals($blankEnvelopeAppeals) {
    $this->data["blankEnvelopeAppeals"] = $blankEnvelopeAppeals;
  }
    public function setCurrencyBatedAppeals($currencyBatedAppeals) {
    $this->data["currencyBatedAppeals"] = $currencyBatedAppeals;
  }
    public function setAppealReminderSchedules($appealReminderSchedules) {
    $this->data["appealReminderSchedules"] = $appealReminderSchedules;
  }
    public function setAppealPrivacyPledges($appealPrivacyPledges) {
    $this->data["appealPrivacyPledges"] = $appealPrivacyPledges;
  }

}

?>
