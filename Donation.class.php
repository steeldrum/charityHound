<?php
/***************************************
$Revision:: 156                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-18 13:51:23#$: Date of last commit
***************************************/
/*
charityhound/
Donation.class.php
tjs 111020

file version 1.00 

release version 1.00
*/

require_once "DataObject.class.php";

date_default_timezone_set ( "America/New_York" );

class Donation extends DataObject {

/*
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `charityId` smallint(5) unsigned NOT NULL,
  `memberId` smallint(5) unsigned NOT NULL,
  `amount` float NOT NULL,
  `madeOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
*/
  protected $data = array(
    "id" => "",
    "charityid" => "",
    "memberid" => "",
    "amount" => "",
    "madeon" => "",
    /* tjs 130725
    "charityId" => "",
    "memberId" => "",
    "amount" => "",
    "madeOn" => "",*/
  );

  public static function getDonations( $startRow, $numRows, $order ) {
    $conn = parent::connect();
    //$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_DONATIONS . " ORDER BY $order LIMIT :startRow, :numRows";
    $sql = "SELECT * FROM " . TBL_DONATIONS . " ORDER BY $order OFFSET :startRow LIMIT :numRows";
    $rowCount = 0;
    
    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
      $donations = array();
      foreach ( $st->fetchAll() as $row ) {
        $donations[] = new Donation( $row );
        // tjs 130725
        $rowCount++;
      }
      //$st = $conn->query( "SELECT found_rows() as totalRows" );
      //$row = $st->fetch();
      parent::disconnect( $conn );
      // tjs 120316
      //return array( $members, $row["totalRows"] );
      return array( $donations, $rowCount );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getDonation( $id ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Donation( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  //tjs 130902
  public static function getDonationsByCharityId( $memberId, $charityId ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_DONATIONS . " where memberid = :memberId and charityid = :charityId order by madeon desc";
    $rowCount = 0;
    
    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      $st->bindValue( ":charityId", $charityId, PDO::PARAM_INT );
      $st->execute();
      $donations = array();
      foreach ( $st->fetchAll() as $row ) {
        $donations[] = new Donation( $row );
        $rowCount++;
      }
      parent::disconnect( $conn );
      return array( $donations, $rowCount );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  // tjs 111115
  //public static function deriveDonationInfo4Charity( $account, $charityId ) {
  public static function deriveDonationInfo4Charity( $account, $charityId, $fromYear, $toYear ) {
  $yearStart = $fromYear."-01-01 00:00:00";
  $yearEnd = $toYear."-12-31 11:59:00";
  	
  	//this number is TBD.  For the time being assume about one month's duration.
//After data becomes aggregated this amount could be fine-tuned and dynamically derived.
// tjs 120619
//$averageDaysDurationBetweenSolicitations = 30;
$averageDaysDurationBetweenSolicitations = AVERAGE_SOLICITATION_GAP;

//when users initially populate their database it is likely that they may log
//several prior solicitations all at once (i.e. the same date).  This could
//seriously skew the solicitation rate computation.  Therefore as the data is
//accessed from the log of solicitations (loop below) any initial logged
//data that occurs on the same day are tracked.  Then the duration days are
//surmised based upon $averageDaysDurationBetweenSolicitations for those initial
//logged solicitations.
$numberOfSameDayLoggedSolicitations = 0;
$solicitationsLoggedTheSameDay = true;
  	
// from donations capture # of Solicitations, # of Donations and Average Amount
	$start = time();
	$end = $start;

	$conn = parent::connect();
	// tjs 130725
	//$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberId = ".$account." and charityId = ".$charityId." and madeOn > '".$yearStart."' and madeOn < '".$yearEnd."' order by madeOn";
// tjs 140205
	//$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$account." and charityid = ".$charityId." and madeon > '".$yearStart."' and madeon < '".$yearEnd."' order by madeon";
	$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$account." and charityid = ".$charityId." and madeon > timestamp '".$yearStart."' and madeon < timestamp '".$yearEnd."' order by madeon";
    //echo "sql ".$sql;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":id", $id, PDO::PARAM_INT );
      /*$st->bindValue( ":memberId", $account, PDO::PARAM_INT );
      $st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      $st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->bindValue( ":charityId", $charityId, PDO::PARAM_INT );
      $st->execute();
      */
      //$st->execute(array($account, $yearStart, $yearEnd, $charityId ));
      $st->execute();
      $donations = array();
      $solicitationsCount = 0;
      foreach ( $st->fetchAll() as $row ) {
        $donations[] = new Donation( $row );
        $solicitationsCount++;
      }
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
	$j=0;
	$donationsCount = 0;
	$total = 0;
	// tjs 111027
	$lastAmount = 0;	
	while ($j < $solicitationsCount) {
		$amount=$donations[$j]->data["amount"];
		$date=$donations[$j]->data["madeOn"];
		$end = strtotime($date);
		if ($j == 0) {
			$start = $end;
		} else {
			$secondsDuration = $end - $start;
			$daysDuration = (int) ( $secondsDuration /60 / 60 / 24 );
			if ($daysDuration == 0 && $solicitationsLoggedTheSameDay == true) {
				$numberOfSameDayLoggedSolicitations++;
			} else {
				$solicitationsLoggedTheSameDay = false;
			}
		}

		//$donation= '<donation id="'.$id.'"><memberId>'.$account.'</memberId><charityId>'.$charityId.'</charityId><amount>'.$amount.'</amount><date>'.$date.'</date></donation>';
		//echo $donation;
		if ($amount > 0) {
			$donationsCount++;
			$total += $amount;
			$lastAmount = -1*$amount; // negative last amount means there were solicitations since last donation was made
			if ($j == $solicitationsCount - 1) {
				$lastAmount = -1*$lastAmount; // positive last amount means no following solicitations since last donation
			}
		}
		$j++;
	}
	$average = 0;
	if ($donationsCount > 0) {
		$average = round( $total/$donationsCount );
	}
	
	// tjs 120618
	if ($account > 0) {
	// tjs 111811
	$member = Member::getMember($account);
	$isSelectableForSite = $member->getValue( "isSelectableForSite" );
	if ($isSelectableForSite <> 0) {
		//tjs110225
		$secondsDuration = $end - $start;
		$daysDuration = (int) ( $secondsDuration /60 / 60 / 24 ) + $numberOfSameDayLoggedSolicitations*$averageDaysDurationBetweenSolicitations;
		
		//lots of solicitations logs in few days indicated backfill data from historical records
		//should never happen due to above corrections
		if ($solicitationsCount > $daysDuration) {
			$secondsDuration = time() - $start;
			$daysDuration = (int) ( $secondsDuration /60 / 60 / 24 );	
		}
	
	//handle case were data entered that is essencially historical data	
		//too small a sample
		$factor = 1;
		$factorMitigator = 1;
		//less than six months?
		if ($daysDuration  < 182 && $solicitationsCount > 0) {
			$factor = 2;
			$factorMitigator = 6 / $solicitationsCount;
			//less than five months?
			if ($daysDuration  < 151) {
				$factor = 2.4;
				$factorMitigator = 5 / $solicitationsCount;
				//less than four months?
				if ($daysDuration  < 120) {
					$factor = 3;
					$factorMitigator = 4 / $solicitationsCount;
					//less than three months?
					if ($daysDuration  < 90) {
						$factor = 4;
						$factorMitigator = 3 / $solicitationsCount;
						//less than two months?
						if ($daysDuration  < 60) {
							$factor = 6;
							$factorMitigator = 2 / $solicitationsCount;
							//less than a month?
							if ($daysDuration  < 30) {
								//possible initial data entry of historical data all packed into narrow
								//data range.  The nominal value expected would be about 1 solicitation
								//per month.  Hence nominal factor would be 12.
								// tjs 120619
								//$factor = 12;
								$factor = NOMINAL_RATE;
								if ($daysDuration < $factor) {
									$factor = $daysDuration;
								}
								$factorMitigator = 1 / $solicitationsCount;
							}
						}
					}
				}
			}
			//possible initial data entry of historical data all packed into narrow
			//data range.  The nominal value expected would be about 1 solicitation
			//per month.  Hence nominal factor would be 12 if one solicitation per month.
			
			if ($factorMitigator > 1) {
				$factorMitigator = 1;
			}
			if ($solicitationsCount == 1) {
				if ($daysDuration > $averageDaysDurationBetweenSolicitations) {
					$rate = round( ( $solicitationsCount/$daysDuration ) * 365 );
				} else {
					$rate = round( 365/$averageDaysDurationBetweenSolicitations );
				}
			} else {		
				$rate = round( $solicitationsCount*$factor*$factorMitigator );
			}
		} else {
			if ($daysDuration > 0) {
				$rate = round( ( $solicitationsCount/$daysDuration ) * 365 );
			} else if ($solicitationsCount == 0) {
				$rate = 0;
			} else {
				$rate = round( 365/$averageDaysDurationBetweenSolicitations );
			}
		}
	} else {
		$rate = 0;
	}
	} else {
		$rate = 0;
	}
	// tjs 111027
	//return array( $solicitationsCount, $rate, $donationsCount, $average );	
	return array( $solicitationsCount, $rate, $donationsCount, $average, $lastAmount );	
  }
  
public static function getDonationsForYear($memberId, $year) {
  $yearStart = $year."-01-01 00:00:00";
  $yearEnd = $year."-12-31 11:59:00";
    $conn = parent::connect();
 	// tjs 130725
     //$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberId = ".$memberId." and madeOn > '".$yearStart."' and madeOn < '".$yearEnd."' order by charityId";
// tjs 140205
    //$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$memberId." and madeon > '".$yearStart."' and madeon < '".$yearEnd."' order by charityid";
    $sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$memberId." and madeon > timestamp '".$yearStart."' and madeon < timestamp '".$yearEnd."' order by charityid";
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $donations = array();
      foreach ( $st->fetchAll() as $row ) {
        $donations[] = new Donation( $row );
      }
      //$row = $st->fetch();
      parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      return $donations;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }

  public static function getDonationsForYears($memberId, $fromYear, $toYear) {
  $yearStart = $fromYear."-01-01 00:00:00";
  $yearEnd = $toYear."-12-31 11:59:00";
    $conn = parent::connect();
    // tjs 130725
    //$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberId = ".$memberId." and madeOn > '".$yearStart."' and madeOn < '".$yearEnd."' order by charityId";
// tjs 140205
    //$sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$memberId." and madeon > '".$yearStart."' and madeon < '".$yearEnd."' order by charityid";
    $sql = "SELECT * FROM " . TBL_DONATIONS . " WHERE memberid = ".$memberId." and madeon > timestamp '".$yearStart."' and madeon < timestamp '".$yearEnd."' order by charityid";
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $donations = array();
      foreach ( $st->fetchAll() as $row ) {
        $donations[] = new Donation( $row );
      }
      //$row = $st->fetch();
      parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      return $donations;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }
  
  public function insert() {
  	//echo "Donation insert...";
    $conn = parent::connect();
    // tjs 130725 field names...
    $sql = "INSERT INTO " . TBL_DONATIONS . " (
              charityid,
              memberid,
              amount,
			madeon
            ) VALUES (
              :charityId,
              :memberId,
              :amount,
			:madeOn
             )";
    //echo "Donation insert sql $sql";
    
    //$charityId = $this->data["charityid"];
    //echo "Donation insert charityId $charityId";
    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":charityId", $this->data["charityid"], PDO::PARAM_STR );
      $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_STR );
      $st->bindValue( ":amount", $this->data["amount"], PDO::PARAM_STR );
      $st->bindValue( ":madeOn", $this->data["madeon"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function update() {
    $conn = parent::connect();
    $sql = "UPDATE " . TBL_DONATIONS . " SET
              charityid = :charityId,
              memberid = :memberId,
              amount = :amount,
              madeon = :madeOn
            WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->bindValue( ":charityId", $this->data["charityid"], PDO::PARAM_STR );
      $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_STR );
      $st->bindValue( ":amount", $this->data["amount"], PDO::PARAM_STR );
      $st->bindValue( ":madeOn", $this->data["madeon"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  public function delete() {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_DONATIONS . " WHERE id = :id";

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

  public function getCharityId() {
    return $this->data["charityid"];
  }
  public function getAmount() {
    return $this->data["amount"];
  }
  
}

?>
