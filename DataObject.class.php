<?php
/***************************************
$Revision:: 53                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-03-01 15:28:41#$: Date of last commit
***************************************/
/*
Collaborators/
DataObject.class.php
tjs 101012

file version 1.00 

release version 1.06
*/

require_once "config.php";

abstract class DataObject {

  protected $data = array();

  public function __construct( $data ) {
    foreach ( $data as $key => $value ) {
      if ( array_key_exists( $key, $this->data ) ) $this->data[$key] = $value;
    }
  }

  public function getValue( $field ) {
  	// tjs 130722
    //if ( array_key_exists( $field, $this->data ) ) {
  	if ( array_key_exists( strtolower($field), $this->data ) ) {
      //return $this->data[$field];
      return $this->data[strtolower($field)];
  	} else {
      die( "Field not found" );
    }
  }

  public function getValueEncoded( $field ) {
    return htmlspecialchars( $this->getValue( $field ) );
  }

  protected function connect() {
    try {
    	// tjs 130719 - conversion to postgreSQL
      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    	//$conn = new PDO( DB_DSN, DB_HOST, DB_USERNAME, DB_PASSWORD );
      $conn->setAttribute( PDO::ATTR_PERSISTENT, true );
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      // tjs 140209
      $conn->setAttribute( PDO::ATTR_TIMEOUT, 30);
    } catch ( PDOException $e ) {
      die( "Connection failed: " . $e->getMessage() );
    }

    return $conn;
  }

  protected function disconnect( $conn ) {
    $conn = "";
  }
}

?>
