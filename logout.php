<?php
/***************************************
$Revision:: 149                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-01 11:02:26#$: Date of last commit
***************************************/
//require_once( "../common.inc.php" );
require_once( "common.inc.php" );
session_start();
$_SESSION["member"] = "";
displayPageHeader( "Logged out", true );
?>
    <p>Thank you, you are now logged out. <a href="login.php">Login again</a>.</p>
    <br/>
<?php
  displayPageFooter();
?>
