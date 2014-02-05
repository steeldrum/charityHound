<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
charityhound/
Rating.class.php
tjs 120316

file version 1.00 

release version 1.00
*/

require_once "DataObject.class.php";

date_default_timezone_set ( "America/New_York" );

class Rating extends DataObject {

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
  /*
    "id" => "",
    "charityId" => "",
    "memberId" => "",
    "year" => "",
    "solicitations" => "",
    "blankEnvelopeAppeals" => "",
      "currencyBatedAppeals" => "",
      "appealReminderSchedules" => "",
      "appealPrivacyPledges" => "",
      "date" => ""
      */
      "id" => "",
    "charityid" => "",
    "memberid" => "",
    "year" => "",
    "solicitations" => "",
    "blankenvelopeappeals" => "",
      "currencybatedappeals" => "",
      "appealreminderschedules" => "",
      "appealprivacypledges" => "",
      "date" => ""
  
  );

  public static function getRatings( $startRow, $numRows, $order ) {
    $conn = parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_RATINGS . " ORDER BY $order LIMIT :startRow, :numRows";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
      $ratings = array();
      foreach ( $st->fetchAll() as $row ) {
        $ratings[] = new Rating( $row );
      }
      $st = $conn->query( "SELECT found_rows() as totalRows" );
      $row = $st->fetch();
      parent::disconnect( $conn );
      return array( $ratings, $row["totalRows"] );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getRating( $id ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_RATINGS . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Rating( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getCharityRatingForYear($memberId, $charityId, $year) {
    $conn = parent::connect();
    // tjs 140205
    //$sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberId = ".$memberId." and year = '".$year."' and charityId = ".$charityId;
    $sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberid = ".$memberId." and year = '".$year."' and charityid = ".$charityId;
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      /* $ratings = array();
      foreach ( $st->fetchAll() as $row ) {
        $ratings[] = new Rating( $row );
      } */
      //$row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) { return new Rating( $row ); }
      else { return null; }
           // return $ratings;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }
  
public static function getRatingsForYear($memberId, $year) {
  $yearStart = $year."-01-01 00:00:00";
  $yearEnd = $year."-12-31 11:59:00";
    $conn = parent::connect();
    //$sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberId = ".$memberId." and madeOn > '".$yearStart."' and madeOn < '".$yearEnd."' order by charityId";
    //$sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberId = ".$memberId." and year = '".$year."' order by charityId";
    $sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberid = ".$memberId." and year = '".$year."' order by charityid";
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $ratings = array();
      foreach ( $st->fetchAll() as $row ) {
        $ratings[] = new Rating( $row );
      }
      //$row = $st->fetch();
      parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      return $ratings;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }

  public static function getRatingsForYears($memberId, $fromYear, $toYear) {
  $yearStart = $fromYear."-01-01 00:00:00";
  $yearEnd = $toYear."-12-31 11:59:00";
    $conn = parent::connect();
    //$sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberId = ".$memberId." and year >= '".$fromYear."' and year <= '".$toYear."' order by charityId";
    $sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberid = ".$memberId." and year >= '".$fromYear."' and year <= '".$toYear."' order by charityid";
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $ratings = array();
      foreach ( $st->fetchAll() as $row ) {
        $ratings[] = new Rating( $row );
      }
      //$row = $st->fetch();
      parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      return $ratings;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }

  //getCharityRatingForYears($memberId, $charityId, $fromYear, $toYear)
    public static function getCharityRatingForYears($memberId, $charityId, $fromYear, $toYear) {
  $yearStart = $fromYear."-01-01 00:00:00";
  $yearEnd = $toYear."-12-31 11:59:00";
    $conn = parent::connect();
    //$sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberId = ".$memberId." and charityId = ".$charityId." and year >= '".$fromYear."' and year <= '".$toYear."'";
    $sql = "SELECT * FROM " . TBL_RATINGS . " WHERE memberid = ".$memberId." and charityid = ".$charityId." and year >= '".$fromYear."' and year <= '".$toYear."'";
    //echo "SQL ".$sql;
//echo "memberId ".$memberId." yearStart ".$yearStart." yearEnd ".$yearEnd;
    try {
      $st = $conn->prepare( $sql );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_STR );
      //$st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      //$st->bindValue( ":yearStart", $yearStart, PDO::PARAM_STR );
      //$st->bindValue( ":yearEnd", $yearEnd, PDO::PARAM_STR );
      $st->execute();
      $ratings = array();
      foreach ( $st->fetchAll() as $row ) {
        $ratings[] = new Rating( $row );
      }
      //$row = $st->fetch();
      parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      return $ratings;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }

  }
  
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
  
  public function insert() {
    $conn = parent::connect();
    $sql = "INSERT INTO " . TBL_RATINGS . " (
              charityid,
              memberid,
              year,
			solicitations,
			blankenvelopeappeals,
			currencybatedappeals,
			appealreminderschedules,
			appealprivacypledges
            ) VALUES (
              :charityId,
              :memberId,
              :year,
			:solicitations,
			:blankEnvelopeAppeals,
			:currencyBatedAppeals,
			:appealReminderSchedules,
			:appealPrivacyPledges
             )";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":charityId", $this->data["charityid"], PDO::PARAM_STR );
      $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_STR );
      $st->bindValue( ":year", $this->data["year"], PDO::PARAM_STR );
      $st->bindValue( ":solicitations", $this->data["solicitations"], PDO::PARAM_STR );
      $st->bindValue( ":blankEnvelopeAppeals", $this->data["blankenvelopeappeals"], PDO::PARAM_STR );
      $st->bindValue( ":currencyBatedAppeals", $this->data["currencybatedappeals"], PDO::PARAM_STR );
      $st->bindValue( ":appealReminderSchedules", $this->data["appealreminderschedules"], PDO::PARAM_STR );
      $st->bindValue( ":appealPrivacyPledges", $this->data["appealprivacypledges"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function update() {
    $conn = parent::connect();
    $sql = "UPDATE " . TBL_RATINGS . " SET
              charityid = :charityId,
              memberid = :memberId,
              year = :year,
              solicitations = :solicitations,
              blankenvelopeappeals = :blankEnvelopeAppeals,
              currencybatedappeals = :currencyBatedAppeals,
              appealreminderschedules = :appealReminderSchedules,
              appealprivacypledges = :appealPrivacyPledges
              WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->bindValue( ":charityId", $this->data["charityid"], PDO::PARAM_STR );
      $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_STR );
      $st->bindValue( ":year", $this->data["year"], PDO::PARAM_STR );
      $st->bindValue( ":solicitations", $this->data["solicitations"], PDO::PARAM_STR );
      $st->bindValue( ":blankEnvelopeAppeals", $this->data["blankenvelopeappeals"], PDO::PARAM_STR );
      $st->bindValue( ":currencyBatedAppeals", $this->data["currencybatedappeals"], PDO::PARAM_STR );
      $st->bindValue( ":appealReminderSchedules", $this->data["appealreminderschedules"], PDO::PARAM_STR );
      $st->bindValue( ":appealPrivacyPledges", $this->data["appealprivacypledges"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  public function delete() {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_RATINGS . " WHERE id = :id";

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
  public function getYear() {
    return $this->data["year"];
  }

    public function getSolicitations() {
    return $this->data["solicitations"];
  }
    public function getBlankEnvelopeAppeals() {
    return $this->data["blankenvelopeappeals"];
  }
    public function getCurrencyBatedAppeals() {
    return $this->data["currencybatedappeals"];
  }
    public function getAppealReminderSchedules() {
    return $this->data["appealreminderschedules"];
  }
    public function getAppealPrivacyPledges() {
    return $this->data["appealprivacypledges"];
  }
  
  public function setSolicitations($solicitations) {
    $this->data["solicitations"] = $solicitations;
  }
  public function setBlankEnvelopeAppeals($blankEnvelopeAppeals) {
    $this->data["blankenvelopeappeals"] = $blankEnvelopeAppeals;
  }
    public function setCurrencyBatedAppeals($currencyBatedAppeals) {
    $this->data["currencybatedappeals"] = $currencyBatedAppeals;
  }
    public function setAppealReminderSchedules($appealReminderSchedules) {
    $this->data["appealreminderschedules"] = $appealReminderSchedules;
  }
    public function setAppealPrivacyPledges($appealPrivacyPledges) {
    $this->data["appealprivacypledges"] = $appealPrivacyPledges;
  }
  
}

?>
