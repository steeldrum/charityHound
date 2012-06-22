<?php
/***************************************
$Revision:: 152                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-15 09:35:07#$: Date of last commit
***************************************/
/*
charityhound/
admin.php
tjs 110104

file version 1.00 

release version 1.00
*/

//require_once( "common.inc.php" );
require_once( "custom.inc.php" );
// tjs 120621
//require_once "DataObject.class.php";
require_once "Charity.class.php";

//tjs 110511 above ensures that config.php has been loaded as well
//$username=DB_USERNAME;
//$password=DB_PASSWORD;
//$database=DB_NAME;

session_start();

//NB we are ensured that the user is logged in and has a session...
$account = 0;
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	$account = $member->getValue( "id" );
} 

if ( isset( $_POST["action"] ) and $_POST["action"] == "process" ) {
  processForm();
  //processForm($account);
} else {
  //displayForm( array(), array(), new Ad( array() ) );
  //displayForm( array(), array(), new AggregateReport( array() ) );
  displayForm( array(), array(), null );
}

function displayForm( $errorMessages, $missingFields, $aggregateReport ) {
  displayPageHeader( "Charity Hound Site Admin Functions" );

  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
      echo $errorMessage;
    }
  } else {
?>
    <p>Obtain Aggregate Report</p>
    <!-- p>To view Aggregate Report, please fill in your details below and click Get Aggregate Report.</p -->
    <!-- p>Fields marked with an asterisk (*) are required.</p -->
<?php } ?>

<table><tbody>
<tr><td>Start year:</td> <td><input id="start" type="number" /></td></tr>
<tr><td>End year:</td> <td><input id="end" type="number" /></td></tr>
<tr><td>Hide Donations Detail:</td> <td><input id="hideDonations" type="checkbox" checked="checked" /></td></tr>
</tbody>
</table>
    <p>
<button id="donations">View Aggregate Donations</button>
</p>

    <p/>
    <p>Distribute Charity To Member or Members</p>
    <form action="admin.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="process" />
		<table><tbody>
        <tr><td>Charity ID to be distributed (possibly a member's charity):</td>
        <td><input type="text" name="charityId" /></td></tr>
        <tr><td>Member ID to be distributed to ('0' means all, same as contributer means just propagate to base):</td>
        <td><input type="text" name="memberId" /></td></tr>
		</tbody></table>
        <div style="clear: both;">
          <input type="submit" name="submitButton" id="submitButton" value="Distribute Specified Charity" />
          <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
        </div>

      </div>
    </form>
    
    <p/>
    <p>Member Information</p>
    <br/>
	<a href="register.php">Register New Charity Hound Collaborator</a>
    <br/>
	<a href="memberManager.php">Manage New Charity Hound Collaborator</a>
    <br/>
	<a href="view_tokens.php">View List of Charity Hound Member Invitations</a>
    <br/>
	<a href="view_members.php">View List of Charity Hound Collaborators</a>
    <br/>
    <a href="index.php">Home</a>
<?php
  displayPageFooter();
}

function processForm() {
	//function processForm($account) {
	//the id to be distributed or propagated back to the base
	$id = $_POST["charityId"];
	//assume that the id represents a base row and therefore will also be the baseId
	// tjs 120622
	//$baseId = $id;
	$memberId = $_POST["memberId"];
	
	Charity::propagateCharity( $id, $memberId );

  displayThanks();
}

function displayThanks() {
  //displayPageHeader( "The Aggregate Report is completed!" );
  displayPageHeader( "The Charity Distribution is completed!" );
?>
    <p>Thank you, your charity is now ready for activation by the specified members.</p>
    <br/>
    <a href="index.php">Home</a>
<?php
  displayPageFooter();
}
?>
