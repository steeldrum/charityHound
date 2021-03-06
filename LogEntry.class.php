<?php
/***************************************
$Revision:: 53                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-03-01 15:28:41#$: Date of last commit
***************************************/
/*
Collaborators/
LogEntry.class.php
tjs 101012

file version 1.00 

release version 1.06
*/

require_once "DataObject.class.php";

class LogEntry extends DataObject {

  protected $data = array(
  // tjs 130725
    "memberid" => "",
    "pageurl" => "",
    "numvisits" => "",
    "lastaccess" => ""
  );

  public static function getLogEntries( $memberId ) {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_ACCESS_LOG . " WHERE memberid = :memberId ORDER BY lastaccess DESC";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      $st->execute();
      $logEntries = array();
      foreach ( $st->fetchAll() as $row ) {
        $logEntries[] = new LogEntry( $row );
      }
      parent::disconnect( $conn );
      return $logEntries;
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public function record() {
    $conn = parent::connect();
    $sql = "SELECT * FROM " . TBL_ACCESS_LOG . " WHERE memberid = :memberId AND pageurl = :pageUrl";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_INT );
      $st->bindValue( ":pageUrl", $this->data["pageurl"], PDO::PARAM_STR );
      $st->execute();

      if ( $st->fetch() ) {
        $sql = "UPDATE " . TBL_ACCESS_LOG . " SET numvisits = numvisits + 1 WHERE memberid = :memberId AND pageurl = :pageUrl";
        $st = $conn->prepare( $sql );
        $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_INT );
        $st->bindValue( ":pageUrl", $this->data["pageurl"], PDO::PARAM_STR );
        $st->execute();
      } else {
        $sql = "INSERT INTO " . TBL_ACCESS_LOG . " ( memberid, pageurl, numvisits ) VALUES ( :memberId, :pageUrl, 1 )";
        $st = $conn->prepare( $sql );
        $st->bindValue( ":memberId", $this->data["memberid"], PDO::PARAM_INT );
        $st->bindValue( ":pageUrl", $this->data["pageurl"], PDO::PARAM_STR );
        $st->execute();
      }

      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

  public static function deleteAllForMember( $memberId ) {
    $conn = parent::connect();
    $sql = "DELETE FROM " . TBL_ACCESS_LOG . " WHERE memberid = :memberId";

    try {
      $st = $conn->prepare( $sql );
      $st->bindValue( ":memberId", $memberId, PDO::PARAM_INT );
      $st->execute();
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }

}

?>
