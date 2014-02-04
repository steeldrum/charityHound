<?php
/***************************************
$Revision:: 156                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-18 13:51:23#$: Date of last commit
***************************************/
/*
Collaborators/
Member.class.php
tjs 101012

file version 1.00 

release version 1.06
*/

require_once "DataObject.class.php";

date_default_timezone_set ( "America/New_York" );

class Member extends DataObject {

  protected $data = array(
    "id" => "",
    "username" => "",
    "password" => "",
    /* tjs 130722
     * "firstName" => "",
    "lastName" => "",
    "joinDate" => "",
    "gender" => "",
    "primarySkillArea" => "",
    "emailAddress" => "",
    "otherSkills" => "",
    "cumDonationsForSites" => "",
    "lastDonationMadeOn" => "",
    "lastDonationForSite" => "",
    "lastLoginDate" => "",
    "permissionForSite" => "",
    "isSelectableForSite" => "",
    "passwordMnemonicQuestion" => "",
    "passwordMnemonicAnswer" => "",
    "isInactive" => ""*/
    "firstname" => "",
    "lastname" => "",
    "joindate" => "",
    "gender" => "",
    "primaryskillarea" => "",
    "emailaddress" => "",
    "otherskills" => "",
    "cumdonationsforsites" => "",
    "lastdonationmadeon" => "",
    "lastdonationforsite" => "",
    "lastlogindate" => "",
    "permissionforsite" => "",
    "isselectableforsite" => "",
    "passwordmnemonicquestion" => "",
    "passwordmnemonicanswer" => "",
    "isinactive" => ""
    );

// tjs 101012
// "favoriteGenre" => "",

//	primarySkillArea	ENUM( 'accounting', 'administration', 'architecture', 'art',
// 'clergy', 'contracting', 'culinary', 'education', 'engineering', 'health',
// 'labor', 'legal', 'management', 'music', 'politics', 'professional', 'retailing',
// 'software', 'trades', 'other' ) NOT NULL,
	
  private $_skills = array(
    "accounting" => "Accounting",
    "administration" => "Administration",
    "architecture" => "Architecture",
    "art" => "Art",
    "clergy" => "Clergy",
    "contracting" => "Contracting",
    "culinary" => "Culinary",
    "education" => "Education",
    "engineering" => "Engineering",
    "health" => "Health",
    "labor" => "Labor",
    "legal" => "Legal",
    "management" => "Management",
    "music" => "Music",
    "politics" => "Politics",
    "professional" => "Professional",
    "retailing" => "Retailing",
    "software" => "Software",
    "trades" => "Trades",
    "other" => "Other"
  );

  public static function getMembers( $startRow, $numRows, $order ) {
     //echo "connecting...";
  	$conn = parent::connect();
    // tjs 130719 for postgreSQL conversion
     //echo "connected...";
    //$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . TBL_MEMBERS . " ORDER BY $order LIMIT :startRow, :numRows";
    $sql = "SELECT * FROM " . TBL_MEMBERS . " ORDER BY $order OFFSET :startRow LIMIT :numRows";
    $rowCount = 0;
    
    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
      $members = array();
      foreach ( $st->fetchAll() as $row ) {
        $members[] = new Member( $row );
        // tjs 130719
        $rowCount++;
      }
      //echo "rowCount $rowCount";
      // tjs 130719
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

  public static function getMember( $id ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new Member( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

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
    $sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE emailaddress = :emailAddress";

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

  //public function getFavoriteGenreString() {
  public function getPrimarySkillAreaString() {
    return ( $this->_skills[$this->data["primaryskillarea"]] );
  }

  public function getSkills() {
    return $this->_skills;
  }

  // tjs 111811
  public function setIsSelectableForSite($torf) {
    $this->data["isselectableforsite"] = $torf;
  }
  
  public function insert() {
    $conn = parent::connect();
    $sql = "INSERT INTO " . TBL_MEMBERS . " (
              username,
              password,
              firstname,
              lastname,
              joindate,
              gender,
              primaryskillarea,
              emailaddress,
              otherskills,
			cumdonationsforsites,
			lastdonationmadeon,
			lastdonationforsite,
			lastlogindate,
			permissionforsite,
			isselectableforsite,
			passwordmnemonicquestion,
			passwordmnemonicanswer,
			isinactive
            ) VALUES (
              :username,
              password(:password),
              :firstName,
              :lastName,
              :joinDate,
              :gender,
              :primarySkillArea,
              :emailAddress,
              :otherSkills,
			:cumDonationsForSites,
			:lastDonationMadeOn,
			:lastDonationForSite,
			:lastLoginDate,
			:permissionForSite,
			:isSelectableForSite,
			:passwordMnemonicQuestion,
			:passwordMnemonicAnswer,
			:isInactive
             )";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
      $st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR );
      $st->bindValue( ":firstName", $this->data["firstname"], PDO::PARAM_STR );
      $st->bindValue( ":lastName", $this->data["lastname"], PDO::PARAM_STR );
      $st->bindValue( ":joinDate", $this->data["joindate"], PDO::PARAM_STR );
      $st->bindValue( ":gender", $this->data["gender"], PDO::PARAM_STR );
      $st->bindValue( ":primarySkillArea", $this->data["primaryskillarea"], PDO::PARAM_STR );
      $st->bindValue( ":emailAddress", $this->data["emailaddress"], PDO::PARAM_STR );
      $st->bindValue( ":otherSkills", $this->data["otherskills"], PDO::PARAM_STR );
      $st->bindValue( ":cumDonationsForSites", $this->data["cumdonationsforsites"], PDO::PARAM_STR );
      $st->bindValue( ":lastDonationMadeOn", $this->data["lastdonationmadeon"], PDO::PARAM_STR );
      $st->bindValue( ":lastDonationForSite", $this->data["lastdonationforsite"], PDO::PARAM_STR );
      $st->bindValue( ":lastLoginDate", $this->data["lastlogindate"], PDO::PARAM_STR );
      $st->bindValue( ":permissionForSite", $this->data["permissionforsite"], PDO::PARAM_STR );
      $st->bindValue( ":isSelectableForSite", $this->data["isselectableforsite"], PDO::PARAM_STR );
      $st->bindValue( ":passwordMnemonicQuestion", $this->data["passwordmnemonicquestion"], PDO::PARAM_STR );
      $st->bindValue( ":passwordMnemonicAnswer", $this->data["passwordmnemonicanswer"], PDO::PARAM_STR );
      $st->bindValue( ":isInactive", $this->data["isinactive"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function update() {
    $conn = parent::connect();
    $passwordSql = $this->data["password"] ? "password = password(:password)," : "";
    $sql = "UPDATE " . TBL_MEMBERS . " SET
              username = :username,
              $passwordSql
              firstname = :firstName,
              lastname = :lastName,
              joindate = :joinDate,
              gender = :gender,
              primaryskillarea = :primarySkillArea,
              emailaddress = :emailAddress,
              otherskills = :otherSkills,
              cumdonationsforsites = :cumDonationsForSites,
              lastdonationmadeon = :lastDonationMadeOn,
              lastdonationforsite = :lastDonationForSite,
              lastlogindate = :lastLoginDate,
              permissionforsite = :permissionForSite,
              isselectableforsite = :isSelectableForSite,
              passwordmnemonicquestion = :passwordMnemonicQuestion,
              passwordmnemonicanswer = :passwordMnemonicAnswer,
              isinactive = :isInactive
            WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      $st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
      if ( $this->data["password"] ) $st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR );
      $st->bindValue( ":firstName", $this->data["firstname"], PDO::PARAM_STR );
      $st->bindValue( ":lastName", $this->data["lastname"], PDO::PARAM_STR );
      $st->bindValue( ":joinDate", $this->data["joindate"], PDO::PARAM_STR );
      $st->bindValue( ":gender", $this->data["gender"], PDO::PARAM_STR );
      $st->bindValue( ":primarySkillArea", $this->data["primaryskillarea"], PDO::PARAM_STR );
      $st->bindValue( ":emailAddress", $this->data["emailaddress"], PDO::PARAM_STR );
      $st->bindValue( ":otherSkills", $this->data["otherskills"], PDO::PARAM_STR );
      $st->bindValue( ":cumDonationsForSites", $this->data["cumdonationsforsites"], PDO::PARAM_STR );
      $st->bindValue( ":lastDonationMadeOn", $this->data["lastdonationmadeon"], PDO::PARAM_STR );
      $st->bindValue( ":lastDonationForSite", $this->data["lastdonationforsite"], PDO::PARAM_STR );
      $st->bindValue( ":lastLoginDate", $this->data["lastLoginDate"], PDO::PARAM_STR );
      $st->bindValue( ":permissionForSite", $this->data["permissionforsite"], PDO::PARAM_STR );
      $st->bindValue( ":isSelectableForSite", $this->data["isselectableforsite"], PDO::PARAM_STR );
      $st->bindValue( ":passwordMnemonicQuestion", $this->data["passwordmnemonicquestion"], PDO::PARAM_STR );
      $st->bindValue( ":passwordMnemonicAnswer", $this->data["passwordmnemonicanswer"], PDO::PARAM_STR );
      $st->bindValue( ":isInactive", $this->data["isinactive"], PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

//tjs110318 
   public function updatePassword( $newPassword ) {
    $conn = parent::connect();
    //$passwordSql = $this->data["password"] ? "password = password(:password)," : "";
    $passwordSql = $newPassword ? "password = password(:password)" : "";
    $sql = "UPDATE " . TBL_MEMBERS . " SET
              $passwordSql
            WHERE id = :id";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":id", $this->data["id"], PDO::PARAM_INT );
      //if ( $this->data["password"] ) $st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR );
      if ( $newPassword ) $st->bindValue( ":password", $newPassword, PDO::PARAM_STR );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  
  public function delete() {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_MEMBERS . " WHERE id = :id";

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

  public function authenticate() {
    $conn = parent::connect();
    // tjs 130725 kludge pg lacka password function
    //$sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE username = :username AND password = password(:password)";
    $sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE username = :username";
    // tjs 131101
    $password = $this->data["password"];
    //echo "password $password";
    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
      //$st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      //tjs110307
      //parent::disconnect( $conn );
      //if ( $row ) return new Member( $row );
      $today = date("Y-m-d");
	  if ( $row ) {
	  	$member = new Member( $row );
	  	//echo "member password ".$member->data["password"];
	  	//echo "member password ".$member->data["password"]. " password ".$password;
	  	//$torf = $member->data["password"] == $password;
	  	$torf = trim($member->data["password"]) == trim($password);
	  	//$torf = strcmp($member->data["password"], $password);
	  	//echo "member password are equal? ".$torf;
	  	//$pass = implode(str_split($password));
	  	//$dbpass = implode(str_split($member->data["password"]));
	  	//$torf = strcmp($pass, $dbpass);
	  	//echo "member password are equal? ".$torf;
	  	
	  	//if ($member->data["password"] == $password) {
	  	if ($torf == 1) {
	  	//echo "member password are equal: ".$member->data["password"]. " password ".$password;
	  		$sql = "UPDATE " . TBL_MEMBERS . " SET
				  lastlogindate = :lastLoginDate
				WHERE id = :id";
		  $st = $conn->prepare( $sql );
      		//$st->bindValue( ":id", $member->id, PDO::PARAM_INT );
      		$st->bindValue( ":id", $member->data["id"], PDO::PARAM_INT );
		  $st->bindValue( ":lastLoginDate", $today, PDO::PARAM_STR );
		  $st->execute();
	  	parent::disconnect( $conn );
	  	//echo "member id ".$member->data["id"];
	  	return $member;
	  	}
	  }
	  parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
    return null;
  }

}

?>
