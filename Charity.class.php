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
	/* tjs 130723
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
	 */
	);

	public static function getCharities( $startRow, $numRows, $order ) {
		$conn = parent::connect();
		// tjs 130725
		//$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_CHARITIES . " ORDER BY $order LIMIT :startRow, :numRows";
		$sql = "SELECT * FROM " . TBL_CHARITIES . " ORDER BY $order OFFSET :startRow LIMIT :numRows";
		$rowCount = 0;

		try {
			$st = $conn->prepare( $sql );
			$st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
			$st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
			$st->execute();
			$charities = array();
			foreach ( $st->fetchAll() as $row ) {
				$charities[] = new Charity( $row );
				// tjs 130725
				$rowCount++;
			}
			//$st = $conn->query( "SELECT found_rows() as totalRows" );
			//$row = $st->fetch();
			parent::disconnect( $conn );
			//return array( $members, $row["totalRows"] );
			return array( $members, $rowCount );
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

	public static function getCharitiesForMember( $memberId, $detail) {
		//$memberCharities = array();
		$charities = array();

		$conn = parent::connect();
		// "SELECT * FROM charities where memberId = ".$account;
		$sql = "SELECT * FROM " . TBL_CHARITIES . " WHERE memberid = :memberId";

		if ($detail == 'false') {
			//$query="SELECT * FROM charities where memberId = ".$account." and (isInactive is null or isInactive = 0)";
			//$sql += " and (isInactive is null or isInactive = 0)";
			$sql .= " and (isinactive is null or isinactive = 0)";
		}

		//echo "sql $sql";
		// e.g. SELECT * FROM charities WHERE memberid = :memberId and (isInactive is null or isInactive = 0)

		try {
			$st = $conn->prepare( $sql );
			$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
			$st->execute();
			//$charities = array();
			foreach ( $st->fetchAll() as $row ) {
				$charities[] = new Charity( $row );
			}
			//$st = $conn->query( "SELECT found_rows() as totalRows" );
			//$row = $st->fetch();
			parent::disconnect( $conn );
			//return array( $members, $row["totalRows"] );
		} catch ( PDOException $e ) {
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}

		$sizeOfCharities = sizeof($charities);
		//echo "sizeOfCharities $sizeOfCharities";
		// e.g. sizeOfCharities 0

		if ($sizeOfCharities == 0) {
			//echo "clone charities for new member $memberId";
			$conn = parent::connect();
			$account = 0;
			// "SELECT * FROM charities where memberId = ".$account;
			$sql = "SELECT * FROM " . TBL_CHARITIES . " WHERE memberid = :memberId and isInactive is null";

			try {
				$st = $conn->prepare( $sql );
				$st->bindValue( ":memberId", $account, PDO::PARAM_INT );
				$st->execute();
				//$charities = array();
				foreach ( $st->fetchAll() as $row ) {
					$charities[] = new Charity( $row );
				}
				//$st = $conn->query( "SELECT found_rows() as totalRows" );
				//$row = $st->fetch();
				//return array( $members, $row["totalRows"] );
				parent::disconnect( $conn );
				$sizeOfCharities = sizeof($charities);
				//echo "sizeOfCharities of account zero $sizeOfCharities";
				// e.g. sizeOfCharities of account zero 5
					
				if ($sizeOfCharities > 0) {
					//$charity->inserts($charities);
					//inserts($charities);
					//echo "call inserts...";
					//$this->inserts($charities);
					//Charity::inserts($charities);
					Charity::inserts($charities, $memberId);
					/* tjs 130724
					 foreach ( $charities as $charity ) {
					 //$charityName = $charity->getValueEncoded("charityName");
					 //echo "name $charityName";
					 $charity->setId('');
					 $charity->setMemberId($memberId);
					 $charity->insert();
					 }*/
					//$sql="INSERT INTO charities (memberId, charityName, shortName, baseId, isForProfit) VALUES ";
					/*
					$i=0;
					while ($i < $sizeOfCharities) {
					//echo "index i $i";
					// e.g. index i 0
					//$id = $charities[$i].getValue('id');
					$id = $charities[$i].getValueEncoded('id');
					echo "index i $i id $id";

					$charity = $charities[$i];
					$charityName = $charity.getValueEncoded("charityName");
					echo "name $charityName";
					$charity.setMemberId($memberId);
					$charity.insert();
					$i++;
					}*/
					//echo $sql;
					//if (!mysql_query($sql,$con))
					//{
					//	die('Error: ' . mysql_error());
					//}
				}
			} catch ( PDOException $e ) {
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}
			// reselect the newly inserted ones...
			$charities = array();
			$conn = parent::connect();
			// "SELECT * FROM charities where memberId = ".$account;
			$sql = "SELECT * FROM " . TBL_CHARITIES . " WHERE memberid = :memberId";

			if ($detail == 'false') {
				//$query="SELECT * FROM charities where memberId = ".$account." and (isInactive is null or isInactive = 0)";
				$sql .= " and (isinactive is null or isinactive = 0)";
			}

			try {
				$st = $conn->prepare( $sql );
				$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
				$st->execute();
				//$charities = array();
				foreach ( $st->fetchAll() as $row ) {
					$charities[] = new Charity( $row );
				}
				//$st = $conn->query( "SELECT found_rows() as totalRows" );
				//$row = $st->fetch();
				parent::disconnect( $conn );
				//return array( $members, $row["totalRows"] );
			} catch ( PDOException $e ) {
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}

			$sizeOfCharities = sizeof($charities);
		}
		//echo "sizeOfMemberCharities ".$sizeOfMemberCharities;

		return array( $charities, $sizeOfCharities );
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
		// tjs 121127
		//if ($charityId != $lastCharityId) {
		if ($amount == 0) {
			$lapsedYearCharities[] = Charity::getCharity( $lastCharityId );
		}
		//}

		$j = 0;
		$sizeOfPriorYearCharities = sizeof($priorYearCharities);
		//echo "size of priorYearCharities ".$sizeOfPriorYearCharities." size of lapsedYearCharities ".sizeof($lapsedYearCharities);

		for($i = 0, $sizeOfLapsedYearCharities = sizeof($lapsedYearCharities); $i < $sizeOfLapsedYearCharities; ++$i) {
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
// tjs 140205
				//$lapsedYearCharity->data["numStars"] = $rate;
				$lapsedYearCharity->data["numstars"] = $rate;
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
		// tjs 121127
		//if ($charityId != $lastCharityId) {
		//echo "prior final lastCharityId ".$lastCharityId." amount ".$amount;
		if ($amount > 0) {
			$charities[] = Charity::getCharity( $lastCharityId );
		}
		//}

		for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i) {
			$charity = $charities[$i];
			$charityId = $charity->data["id"];
			//tjs 111115
			//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
			list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
			//echo "charityId ".$charityId." rate ".$rate;
			// tjs 140205
			//$charity->data["baseId"] = $rate;
			//$charity->data["numStars"] = $donations*$average;
			$charity->data["baseid"] = $rate;
			$charity->data["numstars"] = $donations*$average;
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
		// tjs 130902
		//echo "getOmittedCharities memberId ".$memberId." fromYear ".$fromYear;
		// e.g. getOmittedCharities memberId 1 fromYear 2000

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
			// e.g. prior charityId 7 lastAmount 0prior charityId 7 lastAmount 20prior charityId 7 lastAmount 0

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
		// tjs 121127
		//if ($charityId != $lastCharityId) {
		//echo "prior final lastCharityId ".$lastCharityId." amount ".$amount;
		//if ($amount > 0) {
		if ($amount == 0) {
			$charities[] = Charity::getCharity( $lastCharityId );
		}
		//}

		for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i) {
			$charity = $charities[$i];
			$charityId = $charity->data["id"];
			//tjs 111115
			//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
			list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
			//echo "charityId ".$charityId." rate ".$rate;
			// tjs 130902
			//$charity->data["baseId"] = $rate;
			$charity->data["baseid"] = $rate;
			//$charity->data["numStars"] = $donations*$average;
			//$charity->data["numStars"] = $solicitations;
			$charity->data["numstars"] = $solicitations;
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
		for($i = 0, $sizeOfCharities = sizeof($charities); $i < $sizeOfCharities; ++$i) {
			$charity = $charities[$i];
			$charityId = $charity->data["id"];
			//tjs111115
			//list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId );
			list( $solicitations, $rate, $donations, $average, $lastAmount ) = Donation::deriveDonationInfo4Charity( $memberId, $charityId, $fromYear, $toYear );
			//echo "charityId ".$charityId." rate ".$rate;
			//$charity->data["baseId"] = $rate;
			// tjs 140205
			//$charity->data["baseId"] = $donations;
			$charity->data["baseid"] = $donations;
			$totalDonationsCount += $donations;
			//$charity->data["numStars"] = $donations*$average;
			$charity->data["numstars"] = $solicitations;
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

	// tjs 120621
	//public static function setMemberId( $memberId ) {
	public function setMemberId( $memberId ) {
		// tjs 130723
		//$this->data["memberId"] = $memberId;
		$this->data["memberid"] = $memberId;
	}

	//public static function setBaseId( $baseId ) {
	public function setBaseId( $baseId ) {
		// tjs 130723
		//$this->data["baseId"] = $baseId;
		$this->data["baseid"] = $baseId;
	}

	// tjs 120622
	//public static function setId( $id ) {
	public function setId( $id ) {
		$this->data["id"] = $id;
	}

		// tjs 140203
	public function setCharityName( $charityName ) {
		$this->data["charityname"] = $charityName;
	}
	public function setShortName( $shortName ) {
		$this->data["charityname"] = $shortName;
	}
	
	public static function propagateCharity( $id, $memberId ) {
		//$this->data["memberId"] = $memberId;
		//the id to be distributed or propagated back to the base
		//$id = $_POST["charityId"];
		//assume that the id represents a base row and therefore will also be the baseId
		$baseId = $id;
		//$member = $_POST["memberId"];

		//date_default_timezone_set ( "America/New_York" );
		//$today = date("Y-m-d");

		$conn = parent::connect();
		$charity = null;

		//find list of active members to distribute the newly added charity to
		$activeMembers = array();
		if ($memberId == 0) {
			$sql = "SELECT distinct memberId FROM " . TBL_CHARITIES;

			try {
				$st = $conn->prepare( $sql );
				//$st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
				//$st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
				$st->execute();
				//$charities = array();
				foreach ( $st->fetchAll() as $row ) {
					//$charities[] = new Charity( $row );
					$charity = new Charity( $row );
					$currentMemberId = $charity->getValue("memberId");
					if ($currentMemberId <> 0) {
						$activeMembers[] = $currentMemberId;
					}
				}
			} catch ( PDOException $e ) {
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}
		} else {
			$activeMembers[] = $memberId;
		}

		// for debug only e.g. active member 1 active member 6 active member 2 active member 10
		//foreach($activeMembers as $activeMember) {
		//	echo " active member ".$activeMember;
		//}

		//locate the newly added charity which is to be distributed (or propagated back to the base)
		$charity = null;
		$charityCount = 0;
		$fromMemberId = 0;
		$charityName="";
		$shortName="";
		$dunns="";
		$url="";
		$numStars="0";
		$isInactive="0";
		$isForProfit="0";

		$sql = "SELECT * FROM  " . TBL_CHARITIES . " where id = :id";
		try {
			$st = $conn->prepare( $sql );
			$st->bindValue( ":id", $id, PDO::PARAM_INT );
			$st->execute();
			//$charities = array();
			foreach ( $st->fetchAll() as $row ) {
				//$charities[] = new Charity( $row );
				$charity = new Charity( $row );
				$fromMemberId = $charity->getValue("memberId");
				$charityName=$charity->getValue("charityName");
				$shortName=$charity->getValue("shortName");
				if (strlen($shortName) == 0) {
					$shortName='';
				}
				$dunns=$charity->getValue("dunns");
				if (strlen($dunns) == 0) {
					$dunns='';
				}
				$url=$charity->getValue("url");
				if (strlen($url) == 0) {
					$url='';
				}
				$numStars=$charity->getValue("numStars");
				if (strlen($numStars) == 0) {
					$numStars='0';
				}
				$isInactive=$charity->getValue("isInactive");
				if (strlen($isInactive) == 0) {
					$isInactive='0';
				}
				$isForProfit=$charity->getValue("isForProfit");
				if (strlen($isForProfit) == 0) {
					$isForProfit='0';
				}
			}
		} catch ( PDOException $e ) {
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}

		// for debug only e.g. fromMemberId 1 charityName American Parkinson Disease Association shortName APDA url
		//echo "fromMemberId ".$fromMemberId." charityName ".$charityName." shortName ".$shortName." url ".$url;

		//case where the id specified is a row that a member had created
		//this case means the member's row will be propagated back into the memberId = 0 (base) row
		//rows that are propagated back are then automatically picked up by future members
		//but if the associated member is zero then they are distributed now
		if ($fromMemberId > 0) {
			//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName like '%:charityName%'";
			//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = ':charityName'";
			$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = :charityName";
			try {
				$st = $conn->prepare( $sql );
				$st->bindValue( ":charityName", $charityName, PDO::PARAM_STR );
				$st->execute();
				// e.g. fromMemberId 1 charityName American Parkinson Disease Association
				//echo "fromMemberId ".$fromMemberId." charityName ".$charityName;
				$charityCount = 0;
				foreach ( $st->fetchAll() as $row ) {
					$charityCount++;
					// e.g. found member 0 charityName American Parkinson Disease Association
					//echo "found member 0 charityName ".$charityName;
				}
				// e.g. charityCount 1
				//echo "charityCount ".$charityCount;
				if ($charityCount == 0) {
					// e.g. charityCount 0 charity member id 1
					//echo "charity member id ".$charity->getValue('memberId');
					//inserts a new charity into the base (i.e. propagates the 'from' member's charity to the base)
					//$charity->data["memberId"] = 0;
					$charity->setMemberId(0);
					// tjs 130405
					$charity->setBaseId(null);
					//echo "charity member id ".$charity->getValue('memberId');
					$charity->insert();
					// updates the from members charity that being propagated with the new base id
					// first get the baseId
					//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = ':charityName'";
					$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = :charityName";
					$st = $conn->prepare( $sql );
					$st->bindValue( ":charityName", $charityName, PDO::PARAM_STR );
					$st->execute();
					$charityCount = 0;
					$baseId = 0;
					foreach ( $st->fetchAll() as $row ) {
						$charityCount++;
						$charity = new Charity( $row );
						$baseId = $charity->getValue('id');
					}
					// update the baseId in the fromMember's charity
					if ($charityCount == 1) {
						$charity->setId($id);
						$charity->setMemberId($fromMemberId);
						$charity->setBaseId($baseId);
						$charity->update();
					}
				} else if ($charityCount == 1) {	// derive the base id
					//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = ':charityName'";
					$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = 0 and charityName = :charityName";
					$st = $conn->prepare( $sql );
					$st->bindValue( ":charityName", $charityName, PDO::PARAM_STR );
					$st->execute();
					foreach ( $st->fetchAll() as $row ) {
						$baseCharity = new Charity( $row );
						$baseId = $baseCharity->getValue('id');
					}
					// e.g. derived baseId 2502
					//echo "derived baseId ".$baseId;
				}
			} catch ( PDOException $e ) {
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}
		}
		//this is where the distribution of the charity from the base to one (or all) members occurs.
		foreach($activeMembers as $activeMember) {
			// e.g. baseId 0 active member 1 charity name American Parkinson Disease AssociationQuery
			//echo "baseId ".$baseId." active member ".$activeMember." charity name ".$charityName;

			//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = :activeMember and charityName like '%:charityName%'";
			//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = :activeMember and charityName = ':charityName'";
			$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = :activeMember and charityName = :charityName";
			try {
				$st = $conn->prepare( $sql );
				$st->bindValue( ":activeMember", $activeMember, PDO::PARAM_INT );
				$st->bindValue( ":charityName", $charityName, PDO::PARAM_STR );
				$st->execute();
				$charityCount = 0;
				foreach ( $st->fetchAll() as $row ) {
					$charityCount++;
				}
				if ($charityCount == 0) {
					//inserts a new charity (i.e. propagates the member's charity to another active)
					//$charity->data["memberId"] = 0;
					$charity->setMemberId($activeMember);
					$charity->setBaseId($baseId);
					$charity->insert();
				} else if ($charityCount == 1) {
					//$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = :activeMember and charityName = ':charityName'";
					$sql = "SELECT * FROM  " . TBL_CHARITIES . "  where memberId = :activeMember and charityName = :charityName";
					$st = $conn->prepare( $sql );
					$st->bindValue( ":activeMember", $activeMember, PDO::PARAM_INT );
					$st->bindValue( ":charityName", $charityName, PDO::PARAM_STR );
					$st->execute();
					foreach ( $st->fetchAll() as $row ) {
						$activeMemberCharity = new Charity( $row );
						$activeMemberCharityBaseId = $activeMemberCharity->getValue('baseId');
						// tjs 130405
						if ($activeMemberCharityBaseId  == null || $activeMemberCharityBaseId == 0 || $activeMemberCharityBaseId != $baseId) {
							$activeMemberCharity->setBaseId($baseId);
							$activeMemberCharity->update();
						}
					}
				}
			} catch ( PDOException $e ) {
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}
		}
	}

	public function insert() {
		$conn = parent::connect();
		/* tjs 130723
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
		 */
		$sql = "INSERT INTO " . TBL_CHARITIES . " (
              memberid,
              charityname,
              shortname,
              dunns,
              url,
              description,
              numstars,
              createddate,
              isinactive,
			baseid,
			isforprofit
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
			/* tjs 130723
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
			 */
			$st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_STR );
			$st->bindValue( ":charityName", $this->data["charityname"], PDO::PARAM_STR );
			$st->bindValue( ":shortName", $this->data["shortname"], PDO::PARAM_STR );
			$st->bindValue( ":dunns", $this->data["dunns"], PDO::PARAM_STR );
			$st->bindValue( ":url", $this->data["url"], PDO::PARAM_STR );
			$st->bindValue( ":description", $this->data["description"], PDO::PARAM_STR );
			$st->bindValue( ":numStars", $this->data["numstars"], PDO::PARAM_STR );
			$st->bindValue( ":createdDate", $this->data["createddate"], PDO::PARAM_STR );
			$st->bindValue( ":isInactive", $this->data["isinactive"], PDO::PARAM_STR );
			$st->bindValue( ":baseId", $this->data["baseid"], PDO::PARAM_STR );
			$st->bindValue( ":isForProfit", $this->data["isforprofit"], PDO::PARAM_STR );
			$st->execute();
			parent::disconnect( $conn );
		} catch ( PDOException $e ) {
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}

	// tjs 130724
	public static function inserts($charities, $memberId) {
		//echo "inserts...";
		$conn = parent::connect();
		$sql = "INSERT INTO " . TBL_CHARITIES . " (
              memberid,
              charityname,
              shortname,
              dunns,
              url,
              description,
              numstars,
              createddate,
              isinactive,
			baseid,
			isforprofit
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
			$sizeOfCharities = sizeof($charities);
			//echo "inserts sizeOfCharities $sizeOfCharities";
			foreach ( $charities as $charity ) {
				//$st = $conn->prepare( $sql );
				//$st->bindValue( ":memberId", $charity->data["memberid"], PDO::PARAM_STR );
				$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
				$st->bindValue( ":charityName", $charity->data["charityname"], PDO::PARAM_STR );
				$st->bindValue( ":shortName", $charity->data["shortname"], PDO::PARAM_STR );
				$st->bindValue( ":dunns", $charity->data["dunns"], PDO::PARAM_STR );
				$st->bindValue( ":url", $charity->data["url"], PDO::PARAM_STR );
				$st->bindValue( ":description", $charity->data["description"], PDO::PARAM_STR );
				$st->bindValue( ":numStars", $charity->data["numstars"], PDO::PARAM_STR );
				$st->bindValue( ":createdDate", $charity->data["createddate"], PDO::PARAM_STR );
				$st->bindValue( ":isInactive", $charity->data["isinactive"], PDO::PARAM_STR );
				$st->bindValue( ":baseId", $charity->data["baseid"], PDO::PARAM_STR );
				$st->bindValue( ":isForProfit", $charity->data["isforprofit"], PDO::PARAM_STR );
				$st->execute();
			}
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
		// tjs 140205
		//$al = strtolower($a->data["charityName"]);
		//$bl = strtolower($b->data["charityName"]);
		$al = strtolower($a->data["charityname"]);
		$bl = strtolower($b->data["charityname"]);
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	static function cmp_shortName($a, $b)
	{
		//$al = strtolower($a->data["shortName"]);
		//$bl = strtolower($b->data["shortName"]);
		$al = strtolower($a->data["shortname"]);
		$bl = strtolower($b->data["shortname"]);
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	static function cmp_numStars($a, $b)
	{
		//$al = $a->data["numStars"];
		//$bl = $b->data["numStars"];
		$al = $a->data["numstars"];
		$bl = $b->data["numstars"];
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	static function cmp_baseId($a, $b)
	{
		//$al = $a->data["baseId"];
		//$bl = $b->data["baseId"];
		$al = $a->data["baseid"];
		$bl = $b->data["baseid"];
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
}

?>
