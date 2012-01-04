<?php
/***************************************
$Revision:: 155                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-15 18:11:07#$: Date of last commit
***************************************/
/*
charityhound/
Charity.class.php
tjs 111020

file version 1.00 

release version 1.00
*/

require_once "DataObject.class.php";
require_once( "Donation.class.php" );

date_default_timezone_set ( "America/New_York" );

class Charity extends DataObject {
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
  protected $data = array(
    "id" => "",
    "memberId" => "",
    "charityName" => "",
    "shortName" => "",
    "dunns" => "",
    "url" => "",
    "description" => "",
    "numStars" => "",
    "createdDate" => "",
    "isInactive" => "",
    "baseId" => "",
    "isForProfit" => "",
  );

  public static function getCharities( $startRow, $numRows, $order ) {
    $conn = parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_CHARITIES . " ORDER BY $order LIMIT :startRow, :numRows";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
      $charities = array();
      foreach ( $st->fetchAll() as $row ) {
        $charities[] = new Charity( $row );
      }
      $st = $conn->query( "SELECT found_rows() as totalRows" );
      $row = $st->fetch();
      parent::disconnect( $conn );
      return array( $members, $row["totalRows"] );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getCharity( $id ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_CHARITIES . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Charity( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
public static function getLapsedCharities( $memberId, $priorYear, $lapsedYear, $startRow, $numRows, $order ) {
    $lapsedCharities = array();
    $allLapsedCharities = array();
    $priorYearDonations = Donation::getDonationsForYear( $memberId, $priorYear);
    $lapsedYearDonations = Donation::getDonationsForYear( $memberId, $lapsedYear);
//echo "size of prior year donations ".sizeof($priorYearDonations)." size of lapsed year donations ".sizeof($lapsedYearDonations);
    $priorYearCharities = array();
    $lastCharityId = 0;
    $amount = 0;
    $lastAmount = 0;
    $charityId = 0;
	foreach ( $priorYearDonations as $donation ) {
		$charityId = $donation->getCharityId();
		$lastAmount = $donation->getAmount();
		//echo "prior charityId ".$charityId." lastAmount ".$lastAmount;
		if ($lastCharityId == 0) {
			$lastCharityId = $charityId;
		}
		if ($charityId != $lastCharityId) {
			if ($amount > 0) {
				//echo "prior lastCharityId ".$lastCharityId." amount ".$amount;
				$priorYearCharities[] = Charity::getCharity( $lastCharityId );;
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
		if ($amount > 0) {
			$priorYearCharities[] = Charity::getCharity( $lastCharityId );
		}
	}
	
    $lapsedYearCharities = array();
    $lastCharityId = 0;
    $amount = 0;
    $lastAmount = 0;
    foreach ( $lapsedYearDonations as $donation ) {
		$charityId = $donation->getCharityId();
		$lastAmount = $donation->getAmount();
		//echo "lapsed charityId ".$charityId." lastAmount ".$lastAmount;
		if ($lastCharityId == 0) {
			$lastCharityId = $charityId;
		}
		if ($charityId != $lastCharityId) {
			//echo "lapsed lastCharityId ".$lastCharityId." amount ".$amount;
			if ($amount == 0) {
				$lapsedYearCharities[] = Charity::getCharity( $lastCharityId );
			} 
				$amount = $lastAmount;
			$lastCharityId = $charityId;
		} else {
			$amount += $lastAmount;
		}
	}
	if ($charityId != $lastCharityId) {
		if ($amount == 0) { 
			$lapsedYearCharities[] = Charity::getCharity( $lastCharityId );
		} 
	}

$j = 0;
$sizeOfPriorYearCharities = sizeof($priorYearCharities);
//echo "size of priorYearCharities ".$sizeOfPriorYearCharities." size of lapsedYearCharities ".sizeof($lapsedYearCharities);

for($i = 0, $sizeOfLapsedYearCharities = sizeof($lapsedYearCharities); $i < $sizeOfLapsedYearCharities; ++$i)
{
    $lapsedYearCharity = $lapsedYearCharities[$i];
    $lapsedYearCharityId = $lapsedYearCharity->data["id"];
    $priorYearCharity = $priorYearCharities[$j];
    $priorYearCharityId = $priorYearCharity->data["id"];
    //echo "lapsedYearCharityId ".$lapsedYearCharityId." priorYearCharityId ".$priorYearCharityId;
    //if ($i ==0) {
    	//echo "lapsedYearCharityId ".$lapsedYearCharityId." priorYearCharityId ".$priorYearCharityId;
    //}
    while ($priorYearCharityId < $lapsedYearCharityId && $j < $sizeOfPriorYearCharities) {
    	$priorYearCharity = $priorYearCharities[++$j];
    	$priorYearCharityId = $priorYearCharity->data["id"];
    	//echo " i ".$i." j ".$j." lapsedYearCharityId ".$lapsedYearCharityId." priorYearCharityId ".$priorYearCharityId;
    }
    if ($lapsedYearCharityId == $priorYearCharityId) {
    	$laspsedYearCharityId = $lapsedYearCharity->data["id"];
    	// tjs 111027
    	//list( $solicitations, $rate, $donations, $average ) = Donation::deriveDonationInfo4Charity( $memberId, $laspsedYearCharityId );
    	//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $laspsedYearCharityId );
// tjs 111115
    	list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $laspsedYearCharityId, $lapsedYear, $lapsedYear );
    	$lapsedYearCharity->data["numStars"] = $rate;
    	$allLapsedCharities[] = $lapsedYearCharity;
    }
}

// tjs 111026 sort based on order
if ($order == "charityName") {
	usort($allLapsedCharities, array("Charity", "cmp_charityName"));
} else if ($order == "shortName") {
	usort($allLapsedCharities, array("Charity", "cmp_shortName"));
} else if ($order == "numStars") {
	usort($allLapsedCharities, array("Charity", "cmp_numStars"));
}

$sizeOfAllLapsedCharities = sizeof($allLapsedCharities);
//echo "sizeOfAllLapsedCharities ".$sizeOfAllLapsedCharities;
$countCharities = 0;
if ($startRow < $sizeOfAllLapsedCharities) {
for ($k = $startRow; $k < $sizeOfAllLapsedCharities; ++$k) {
	if ($countCharities++ < $numRows) {
		$lapsedCharities[] = $allLapsedCharities[$k];
	}
}
}

return array( $lapsedCharities, $sizeOfAllLapsedCharities );
  }

  public static function getRemittedCharities( $memberId, $fromYear, $toYear, $startRow, $numRows, $order ) {

    $donations = Donation::getDonationsForYears( $memberId, $fromYear, $toYear);

    $charities = array();
    $remittedCharities = array();
    $allRemittedCharities = array();
    $lastCharityId = 0;
    $amount = 0;
    $lastAmount = 0;
    $charityId = 0;
	foreach ( $donations as $donation ) {
		$charityId = $donation->getCharityId();
		$lastAmount = $donation->getAmount();
		//echo "prior charityId ".$charityId." lastAmount ".$lastAmount;
		if ($lastCharityId == 0) {
			$lastCharityId = $charityId;
		}
		if ($charityId != $lastCharityId) {
			if ($amount > 0) {
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
		if ($amount > 0) {
			$charities[] = Charity::getCharity( $lastCharityId );
		}
	}
    
  for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i)
{
    $charity = $charities[$i];
    $charityId = $charity->data["id"];
    //tjs 111115
    //list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
    list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
	//echo "charityId ".$charityId." rate ".$rate;
    $charity->data["baseId"] = $rate;
    $charity->data["numStars"] = $donations*$average;
    $allRemittedCharities[] = $charity;
}

// tjs 111026 sort based on order
if ($order == "charityName") {
	usort($allRemittedCharities, array("Charity", "cmp_charityName"));
} else if ($order == "shortName") {
	//usort($allRemittedCharities, array("Charity", "cmp_shortName"));
	usort($allRemittedCharities, array("Charity", "cmp_numStars"));
} else if ($order == "numStars") {
	usort($allRemittedCharities, array("Charity", "cmp_baseId"));
}

$sizeOfCharities = sizeof($allRemittedCharities);
//echo "sizeOfAllLapsedCharities ".$sizeOfAllLapsedCharities;
$countCharities = 0;
if ($startRow < $sizeOfCharities) {
for ($k = $startRow; $k < $sizeOfCharities; ++$k) {
	if ($countCharities++ < $numRows) {
		$remittedCharities[] = $allRemittedCharities[$k];
	}
}
}

return array( $remittedCharities, $sizeOfCharities );
  }

    public static function getOmittedCharities( $memberId, $fromYear, $toYear, $startRow, $numRows, $order ) {

    $donations = Donation::getDonationsForYears( $memberId, $fromYear, $toYear);

    $charities = array();
    $omittedCharities = array();
    $allOmittedCharities = array();
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
    //tjs 111115
    //list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
    list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
	//echo "charityId ".$charityId." rate ".$rate;
    $charity->data["baseId"] = $rate;
    //$charity->data["numStars"] = $donations*$average;
    $charity->data["numStars"] = $solicitations;
    $allOmittedCharities[] = $charity;
}

// tjs 111026 sort based on order
if ($order == "charityName") {
	usort($allOmittedCharities, array("Charity", "cmp_charityName"));
} else if ($order == "shortName") {
	//usort($allRemittedCharities, array("Charity", "cmp_shortName"));
	usort($allOmittedCharities, array("Charity", "cmp_numStars"));
} else if ($order == "numStars") {
	usort($allOmittedCharities, array("Charity", "cmp_baseId"));
}

$sizeOfCharities = sizeof($allOmittedCharities);
//echo "sizeOfAllLapsedCharities ".$sizeOfAllLapsedCharities;
$countCharities = 0;
if ($startRow < $sizeOfCharities) {
for ($k = $startRow; $k < $sizeOfCharities; ++$k) {
	if ($countCharities++ < $numRows) {
		$omittedCharities[] = $allOmittedCharities[$k];
	}
}
}

return array( $omittedCharities, $sizeOfCharities );
  }

  // tjs 111115
    public static function getSolicitationCountByCharities( $memberId, $fromYear, $toYear, $startRow, $numRows, $order ) {

    $donations = Donation::getDonationsForYears( $memberId, $fromYear, $toYear);

    $charities = array();
    //$counts = array();
    //$omittedCharities = array();
    $solicitorCharities = array();
    //$allOmittedCharities = array();
    $allCharities = array();
    $lastCharityId = 0;
    //$amount = 0;
    //$count = 0;
    //$total = 0;
    //$lastAmount = 0;
    //$count = 0;
    //$lastCount = 0;
    $charityId = 0;
	foreach ( $donations as $donation ) {
		$charityId = $donation->getCharityId();
		//$lastAmount = $donation->getAmount();
		//echo "prior charityId ".$charityId." lastAmount ".$lastAmount;
		if ($lastCharityId == 0) {
			$lastCharityId = $charityId;
		}
		if ($charityId != $lastCharityId) {
			//if ($amount > 0) {
			//if ($amount == 0) {
				//echo "prior lastCharityId ".$lastCharityId." amount ".$amount;
				$charities[] = Charity::getCharity( $lastCharityId );;
				//echo "size of priorYearCharities ".sizeof($priorYearCharities);
			//}
			//$counts[] = $count;
			//$total += $count;
				//$amount = $lastAmount;
			$lastCharityId = $charityId;
			//$count = 0;
		} //else {
			//echo "charityId ".$charityId." amount ".$donation->getAmount();
			//$amount += $lastAmount;
			//$count++;
		//}
	}
	if ($charityId != $lastCharityId) {
			//echo "prior final lastCharityId ".$lastCharityId." amount ".$amount;			
		//if ($amount > 0) {
		//if ($amount == 0) {
			$charities[] = Charity::getCharity( $lastCharityId );
			//$counts[] = $count;
			//$total += $count;
			//}
	}
    
	//$sizeOfCharities = sizeof($charities);
	$totalSolicitationsCount = 0;
	$totalDonationsCount = 0;
  for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i)
{
    $charity = $charities[$i];
    $charityId = $charity->data["id"];
    //tjs111115
    //list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
    list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
	//echo "charityId ".$charityId." rate ".$rate;
    //$charity->data["baseId"] = $rate;
    $charity->data["baseId"] = $donations;
    $totalDonationsCount += $donations;
    //$charity->data["numStars"] = $donations*$average;
    $charity->data["numStars"] = $solicitations;
    $totalSolicitationsCount += $solicitations;
    //$allOmittedCharities[] = $charity;
    $allCharities[] = $charity;
}

// sort based on order
if ($order == "charityName") {
	usort($allCharities, array("Charity", "cmp_charityName"));
} else if ($order == "shortName") {
	//usort($allRemittedCharities, array("Charity", "cmp_shortName"));
	usort($allCharities, array("Charity", "cmp_numStars"));
} else if ($order == "numStars") {
	usort($allCharities, array("Charity", "cmp_baseId"));
}

$sizeOfCharities = sizeof($allCharities);
//echo "sizeOfAllLapsedCharities ".$sizeOfAllLapsedCharities;
$countCharities = 0;
if ($startRow < $sizeOfCharities) {
for ($k = $startRow; $k < $sizeOfCharities; ++$k) {
	if ($countCharities++ < $numRows) {
		$solicitorCharities[] = $allCharities[$k];
	}
}
}
//echo "totalSolicitationsCount ".$totalSolicitationsCount." totalDonationsCount ".$totalDonationsCount;
return array( $solicitorCharities, $sizeOfCharities, $totalSolicitationsCount, $totalDonationsCount );
  }
  
  public function insert() {
    $conn = parent::connect();
    $sql = "INSERT INTO " . TBL_CHARITIES . " (
              memberId,
              charityName,
              shortName,
              dunns,
              url,
              description,
              numStars,
              createdDate,
              isInactive,
			baseId,
			isForProfit
            ) VALUES (
              :memberId,
              :charityName,
              :shortName,
              :dunns,
              :url,
              :description,
              :numStars,
              :createdDate,
              :isInactive,
			:baseId,
			:isForProfit
             )";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $this->data["memberId"], PDO::PARAM_STR );
      $st->bindValue( ":charityName", $this->data["charityName"], PDO::PARAM_STR );
      $st->bindValue( ":shortName", $this->data["shortName"], PDO::PARAM_STR );
      $st->bindValue( ":dunns", $this->data["dunns"], PDO::PARAM_STR );
      $st->bindValue( ":url", $this->data["url"], PDO::PARAM_STR );
      $st->bindValue( ":description", $this->data["description"], PDO::PARAM_STR );
      $st->bindValue( ":numStars", $this->data["numStars"], PDO::PARAM_STR );
      $st->bindValue( ":createdDate", $this->data["createdDate"], PDO::PARAM_STR );
      $st->bindValue( ":isInactive", $this->data["isInactive"], PDO::PARAM_STR );
      $st->bindValue( ":baseId", $this->data["baseId"], PDO::PARAM_STR );
      $st->bindValue( ":isForProfit", $this->data["isForProfit"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function update() {
    $conn = parent::connect();
    $sql = "UPDATE " . TBL_CHARITIES . " SET
              memberId = :memberId,
              charityName = :charityName,
              shortName = :shortName,
              dunns = :dunns,
              url = :url,
              description = :description,
              numStars = :numStars,
              createdDate = :createdDate,
              isInactive = :isInactive,
              baseId = :baseId,
              isForProfit = :isForProfit
            WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->bindValue( ":memberId", $this->data["memberId"], PDO::PARAM_STR );
      $st->bindValue( ":charityName", $this->data["charityName"], PDO::PARAM_STR );
      $st->bindValue( ":shortName", $this->data["shortName"], PDO::PARAM_STR );
      $st->bindValue( ":dunns", $this->data["dunns"], PDO::PARAM_STR );
      $st->bindValue( ":url", $this->data["url"], PDO::PARAM_STR );
      $st->bindValue( ":description", $this->data["description"], PDO::PARAM_STR );
      $st->bindValue( ":numStars", $this->data["numStars"], PDO::PARAM_STR );
      $st->bindValue( ":createdDate", $this->data["createdDate"], PDO::PARAM_STR );
      $st->bindValue( ":isInactive", $this->data["isInactive"], PDO::PARAM_STR );
      $st->bindValue( ":baseId", $this->data["baseId"], PDO::PARAM_STR );
      $st->bindValue( ":isForProfit", $this->data["isForProfit"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  public function delete() {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_CHARITIES . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  static function cmp_charityName($a, $b)
    {
        $al = strtolower($a->data["charityName"]);
        $bl = strtolower($b->data["charityName"]);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
    static function cmp_shortName($a, $b)
    {
        $al = strtolower($a->data["shortName"]);
        $bl = strtolower($b->data["shortName"]);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
    static function cmp_numStars($a, $b)
    {
        $al = $a->data["numStars"];
        $bl = $b->data["numStars"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }    
    static function cmp_baseId($a, $b)
    {
        $al = $a->data["baseId"];
        $bl = $b->data["baseId"];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }    
}

?>
