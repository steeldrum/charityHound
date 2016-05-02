<?php
/***************************************
$Revision:: 148                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-10-31 11:39:02#$: Date of last commit
***************************************/
/*
Collaborators/
common.inc.php
tjs 101012

file version 1.01 

release version 1.12
*/

require_once( "config.php" );
require_once( "Member.class.php" );
require_once( "Ad.class.php" );
require_once( "LogEntry.class.php" );

function displayPageHeader( $pageTitle, $membersArea = false ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <!-- [if lt IE 9]>
  <script src="js/html5.js"></script>
  <![endif]-->
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<!-- tjs 141031 compatibility plugin -->
		<!--  tjs 160430 for SSL -->
<script src="https://code.jquery.com/jquery-1.9.0.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script src="https://cdn.firebase.com/js/client/1.1.3/firebase.js"></script>
   <script type="text/javascript" src="js/authentication.js"></script>
   <script type="text/javascript" src="js/globals.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>
    <title><?php echo $pageTitle?></title>
    <link rel="stylesheet" type="text/css" href="<?php if ( $membersArea ) echo "../" ?>css/common.css" />
    <style type="text/css">
    #container { border:1px solid #85d791; width:1101px; margin: 20px; padding: 20px; }

      th { text-align: left; background-color: #bbb; }
      th, td { padding: 0.4em; }
      tr.alt td { background: #ddd; }
      .error { background: #d33; color: white; padding: 0.2em; }
    </style>
  </head>
  <body>
  <header>
<hgroup>
<h1>Charity Hound</h1>
<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>
</hgroup>
</header>
<div id="container">

    <h1><?php echo $pageTitle?></h1>
<?php
}

function displayPageFooter() {
?>
</div>
<footer>
<h2>Join Us.  We Sniff Out Ones That Care About What You Donate!</h2>
<br/>

<div class="iconControls">

<a href="javascript:newLocation('index', 'logistics')"><img src="images/home.gif"></a>

</div>

</footer>
  </body>
</html>
<?php
}

function validateField( $fieldName, $missingFields ) {
  if ( in_array( $fieldName, $missingFields ) ) {
    echo ' class="error"';
  }
}

function setChecked( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj->getValue( $fieldName ) == $fieldValue ) {
    echo ' checked="checked"';
  }
}

function setSelected( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj->getValue( $fieldName ) == $fieldValue ) {
    echo ' selected="selected"';
  }
}

function checkLogin() {
  session_start();
  if ( !$_SESSION["member"] or !$_SESSION["member"] = Member::getMember( $_SESSION["member"]->getValue( "id" ) ) ) {
    $_SESSION["member"] = "";
    header( "Location: login.php" );
    exit;
  } else {
    $logEntry = new LogEntry( array (
      "memberid" => $_SESSION["member"]->getValue( "id" ),
      "pageurl" => basename( $_SERVER["PHP_SELF"] )
    ) );
    $logEntry->record();
  }
}


?>
