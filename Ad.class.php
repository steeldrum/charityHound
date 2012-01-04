<?php
/***************************************
$Revision:: 20                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-01-28 10:32:07#$: Date of last commit
***************************************/
/*
Collaborators/
Ad.class.php
tjs 110104

file version 1.00 

release version 1.00
*/

require_once "DataObject.class.php";

class Ad extends DataObject {

/*
INSERT INTO `ads` (`id`, `memberId`, `adType`, `adName`, `width`, `height`, `tabLine`, `numDisplayed`, `displayedDate`, `circulationNumber`, `description`, `numOccurences`, `perOccurence`, `expirationDate`, `createdDate`, `circulationWeight`, `isInactive`) VALUES
(1, 1, 'mobile', 'collogisticsAd.png', 320, 50, 'Consider Collogistics While Giving...', 0, '2010-10-12', 0, 'demo for collogistics site', 0, 0, '2020-01-01', '2010-10-12', 0, NULL),
(2, 1, 'mobile', 'QuillessAssociates.png', 320, 50, "Don't Go Clueless. Consider Quilless!", 0, '2010-10-12', 0, 'demo for collogistics site', 0, 0, '2020-01-01', '2010-10-12', 0, NULL);
*/
  protected $data = array(
    "id" => "",
    "memberId" => "",
    "adType" => "",
    "adName" => "",
    "width" => "",
    "height" => "",
    "tabLine" => "",
    "numDisplayed" => "",
    "displayedDate" => "",
    "circulationNumber" => "",
    "description" => "",
    "numOccurences" => "",
    "perOccurence" => "",
    "expirationDate" => "",
    "createdDate" => "",
    "circulationWeight" => "",
    "isInactive" => ""
  );

//'mobile','desktop','other'	
  private $_types = array(
    "mobile" => "Mobile",
    "desktop" => "Desktop",
    "other" => "Other"
  );

  public static function getAds( $startRow, $numRows, $order ) {
    $conn = parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_ADS . " ORDER BY $order LIMIT :startRow, :numRows";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
      $ads = array();
      foreach ( $st->fetchAll() as $row ) {
        $ads[] = new Ad( $row );
      }
      $st = $conn->query( "SELECT found_rows() as totalRows" );
      $row = $st->fetch();
      parent::disconnect( $conn );
      return array( $ads, $row["totalRows"] );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getAd( $id ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_ADS . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Ad( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
/*
  public static function getByUsername( $username ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE username = :username";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":username", $username, PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Member( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function getByEmailAddress( $emailAddress ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE emailAddress = :emailAddress";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":emailAddress", $emailAddress, PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Member( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function getGenderString() {
    return ( $this->data["gender"] == "f" ) ? "Female" : "Male";
  }
*/
  public function getAdTypeString() {
    return ( $this->_types[$this->data["adType"]] );
  }

  public function getAdTypes() {
    return $this->_types;
  }

  public function insert() {
    $conn = parent::connect();
    $sql = "INSERT INTO " . TBL_ADS . " (
    		memberId,
    		adType,
    		adName,
    		width,
    		height,
    		tabLine,
    		numDisplayed,
    		displayedDate,
    		circulationNumber,
    		description,
    		numOccurences,
    		perOccurence,
    		expirationDate,
    		createdDate,
    		circulationWeight
            ) VALUES (
              :memberId,
              :adType,
              :adName,
              :width,
              :height,
              :tabLine,
              :numDisplayed,
              :displayedDate,
              :circulationNumber,
              :description,
              :numOccurences,
              :perOccurence,
              :expirationDate,
              :createdDate,
              :circulationWeight
            )";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $this->data["memberId"], PDO::PARAM_STR );
      $st->bindValue( ":adType", $this->data["adType"], PDO::PARAM_STR );
      $st->bindValue( ":adName", $this->data["adName"], PDO::PARAM_STR );
      $st->bindValue( ":width", $this->data["width"], PDO::PARAM_STR );
      $st->bindValue( ":height", $this->data["height"], PDO::PARAM_STR );
      $st->bindValue( ":tabLine", $this->data["tabLine"], PDO::PARAM_STR );
      $st->bindValue( ":numDisplayed", $this->data["numDisplayed"], PDO::PARAM_STR );
      $st->bindValue( ":displayedDate", $this->data["displayedDate"], PDO::PARAM_STR );
      $st->bindValue( ":circulationNumber", $this->data["circulationNumber"], PDO::PARAM_STR );
      $st->bindValue( ":description", $this->data["description"], PDO::PARAM_STR );
      $st->bindValue( ":numOccurences", $this->data["numOccurences"], PDO::PARAM_STR );
      $st->bindValue( ":perOccurence", $this->data["perOccurence"], PDO::PARAM_STR );
      $st->bindValue( ":expirationDate", $this->data["expirationDate"], PDO::PARAM_STR );
      $st->bindValue( ":createdDate", $this->data["createdDate"], PDO::PARAM_STR );
      $st->bindValue( ":circulationWeight", $this->data["circulationWeight"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function update() {
    $conn = parent::connect();
    //$passwordSql = $this->data["password"] ? "password = password(:password)," : "";
    $sql = "UPDATE " . TBL_ADS . " SET
              memberId = :memberId,
              adType = :adType,
              adName = :adName,
              width = :width,
              height = :height,
              tabLine = :tabLine,
              numDisplayed = :numDisplayed,
              displayedDate = :displayedDate,
              circulationNumber = :circulationNumber,
              description = :description,
              numOccurences = :numOccurences,
              perOccurence = :perOccurence,
              expirationDate = :expirationDate,
              createdDate = :createdDate,
              circulationWeight = :circulationWeight
            WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->bindValue( ":memberId", $this->data["memberId"], PDO::PARAM_STR );
      $st->bindValue( ":adType", $this->data["adType"], PDO::PARAM_STR );
      $st->bindValue( ":adName", $this->data["adName"], PDO::PARAM_STR );
      $st->bindValue( ":width", $this->data["width"], PDO::PARAM_STR );
      $st->bindValue( ":height", $this->data["height"], PDO::PARAM_STR );
      $st->bindValue( ":tabLine", $this->data["tabLine"], PDO::PARAM_STR );
      $st->bindValue( ":numDisplayed", $this->data["numDisplayed"], PDO::PARAM_STR );
      $st->bindValue( ":displayedDate", $this->data["displayedDate"], PDO::PARAM_STR );
      $st->bindValue( ":circulationNumber", $this->data["circulationNumber"], PDO::PARAM_STR );
      $st->bindValue( ":description", $this->data["description"], PDO::PARAM_STR );
      $st->bindValue( ":numOccurences", $this->data["numOccurences"], PDO::PARAM_STR );
      $st->bindValue( ":perOccurence", $this->data["perOccurence"], PDO::PARAM_STR );
      $st->bindValue( ":expirationDate", $this->data["expirationDate"], PDO::PARAM_STR );
      $st->bindValue( ":createdDate", $this->data["createdDate"], PDO::PARAM_STR );
      $st->bindValue( ":circulationWeight", $this->data["circulationWeight"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  public function delete() {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_ADS . " WHERE id = :id";

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
}

?>
