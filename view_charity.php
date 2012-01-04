<?php
/***************************************
$Revision:: 151                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-02 10:37:44#$: Date of last commit
***************************************/
/*
charityhound/
view_charity.php
tjs 111021

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );
require_once( "config.php" );
require_once( "Charity.class.php" );
require_once( "LogEntry.class.php" );

//echo "account ".$account." prior ".$prior." current ".$current." start ".$start." order ".$order." back ".$back;

$charityId = isset( $_REQUEST["charityId"] ) ? (int)$_REQUEST["charityId"] : 0;

if ( !$charity = Charity::getCharity( $charityId ) ) {
  displayPageHeader( "Error" );
  echo "<div>Charity not found.</div>";
  displayPageFooter();
  exit;
}

if ( isset( $_POST["action"] ) and $_POST["action"] == "Save Changes" ) {
  saveCharity();
} elseif ( isset( $_POST["action"] ) and $_POST["action"] == "Delete Charity" ) {
  deleteCharity();
} else {
  displayForm( array(), array(), $charity );
}

function displayForm( $errorMessages, $missingFields, $charity ) {
  //$logEntries = LogEntry::getLogEntries( $member->getValue( "id" ) );
  displayPageHeader( "View charity: " . $charity->getValueEncoded( "charityName" ));

  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
      echo $errorMessage;
    }
  }

  // tjs111024
$account = isset( $_GET["account"] ) ? (int)$_GET["account"] : 0;
$prior = isset( $_GET["prior"] ) ? (int)$_GET["prior"] : 2000;
$current = isset( $_GET["current"] ) ? (int)$_GET["current"] : 2100;
  
  $start = isset( $_REQUEST["start"] ) ? (int)$_REQUEST["start"] : 0;
  $order = isset( $_REQUEST["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_REQUEST["order"] ) : "charityName";
  // tjs 111102
$back = isset( $_GET["back"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["back"] ) : "lapsed";
  
?>
    <form action="view_charity.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="charityId" id="charityId" value="<?php echo $charity->getValueEncoded( "id" ) ?>" />
        <input type="hidden" name="start" id="start" value="<?php echo $start ?>" />
        <input type="hidden" name="order" id="order" value="<?php echo $order ?>" />

        <label for="charityName"<?php validateField( "charityName", $missingFields ) ?>>CharityName *</label>
        <input type="text" name="charityName" id="charityName" value="<?php echo $charity->getValueEncoded( "charityName" ) ?>" />
        <label for="shortName">ShortName</label>
        <input type="text" name="shortName" id="shortName" value="<?php echo $charity->getValueEncoded( "shortName" ) ?>" />
        <label for="dunns">Dunns</label>
        <input type="text" name="dunns" id="dunns" value="<?php echo $charity->getValueEncoded( "dunns" ) ?>" />
        <label for="url"<?php validateField( "url", $missingFields ) ?>>URL *</label>
        <input type="text" name="url" id="url" value="<?php echo $charity->getValueEncoded( "url" ) ?>" />
        <label for="description"<?php validateField( "description", $missingFields ) ?>>Description *</label>
        <input type="text" name="description" id="description" value="<?php echo $charity->getValueEncoded( "description" ) ?>" />
        <label for="createdDate"<?php validateField( "createdDate", $missingFields ) ?>>Created Date *</label>
        <input type="text" name="createdDate" id="createdDate" value="<?php echo $charity->getValueEncoded( "createdDate" ) ?>" />
        <label for="isForProfit"<?php validateField( "isForProfit", $missingFields ) ?>>Is For Profit *</label>
        <input type="text" name="isForProfit" id="isForProfit" value="<?php echo $charity->getValueEncoded( "isForProfit" ) ?>" />
       <div style="clear: both;">
          <input type="submit" name="action" id="saveButton" value="Save Changes" disabled="disabled" />
          <input type="submit" name="action" id="deleteButton" value="Delete Member"  disabled="disabled" style="margin-right: 20px;" />
        </div>
      </div>
    </form>

    <div style="width: 30em; margin-top: 20px; text-align: center;">
<?php
	if ($back == "lapsed") {
//echo " for lapsed: account ".$account." prior ".$prior." current ".$current." start ".$start." order ".$order." back ".$back;
		?>
      <a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo $start ?>&amp;order=<?php echo $order ?>">Back</a>
<?php
	} else if ($back == "remitted") {
?>
      <a href="view_remitted_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo $start ?>&amp;order=<?php echo $order ?>">Back</a>
<?php
	} else { // i.e. ($back == 'omitted') {
?>
      <a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo $start ?>&amp;order=<?php echo $order ?>">Back</a>
<?php
	}
?>

    </div>

<?php
  displayPageFooter();
}

function saveCharity() {
/*
  $requiredFields = array( "username", "emailAddress", "firstName", "lastName", "joinDate", "gender" );
  $missingFields = array();
  $errorMessages = array();

  $member = new Member( array(
    "id" => isset( $_POST["memberId"] ) ? (int) $_POST["memberId"] : "",
    "username" => isset( $_POST["username"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["username"] ) : "",
    "password" => isset( $_POST["password"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["password"] ) : "",
    "emailAddress" => isset( $_POST["emailAddress"] ) ? preg_replace( "/[^ \@\.\-\_a-zA-Z0-9]/", "", $_POST["emailAddress"] ) : "",
    "firstName" => isset( $_POST["firstName"] ) ? preg_replace( "/[^ \'\-a-zA-Z0-9]/", "", $_POST["firstName"] ) : "",
    "lastName" => isset( $_POST["lastName"] ) ? preg_replace( "/[^ \'\-a-zA-Z0-9]/", "", $_POST["lastName"] ) : "",
    "joinDate" => isset( $_POST["joinDate"] ) ? preg_replace( "/[^\-0-9]/", "", $_POST["joinDate"] ) : "",
    "gender" => isset( $_POST["gender"] ) ? preg_replace( "/[^mf]/", "", $_POST["gender"] ) : "",
    "primarySkillArea" => isset( $_POST["primarySkillArea"] ) ? preg_replace( "/[^a-zA-Z]/", "", $_POST["favoriteGenre"] ) : "",
    "otherSkills" => isset( $_POST["otherSkills"] ) ? preg_replace( "/[^ \'\,\.\-a-zA-Z0-9]/", "", $_POST["otherSkills"] ) : "",
        "cumDonationsForSites" => "0",
    "lastDonationMadeOn" => "",
    "lastDonationForSite" => "0",
    "lastLoginDate" => "",
    "permissionForSite" => "15",
    "isSelectableForSite" => "0",
    "passwordMnemonicQuestion" => isset( $_POST["passwordMnemonicQuestion"] ) ? preg_replace( "/[^a-zA-Z]/", "", $_POST["passwordMnemonicQuestion"] ) : "",
    "passwordMnemonicAnswer" => isset( $_POST["passwordMnemonicAnswer"] ) ? preg_replace( "/[^a-zA-Z]/", "", $_POST["passwordMnemonicAnswer"] ) : "",
    "isInactive" => ""

  ) );

  foreach ( $requiredFields as $requiredField ) {
    if ( !$member->getValue( $requiredField ) ) {
      $missingFields[] = $requiredField;
    }
  }

  if ( $missingFields ) {
    $errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Save Changes to resend the form.</p>';
  }

  if ( $existingMember = Member::getByUsername( $member->getValue( "username" ) ) and $existingMember->getValue( "id" ) != $member->getValue( "id" ) ) {
    $errorMessages[] = '<p class="error">A member with that username already exists in the database. Please choose another username.</p>';
  }

  if ( $existingMember = Member::getByEmailAddress( $member->getValue( "emailAddress" ) ) and $existingMember->getValue( "id" ) != $member->getValue( "id" ) ) {
    $errorMessages[] = '<p class="error">A member with that email address already exists in the database. Please choose another email address.</p>';
  }

  if ( $errorMessages ) {
    displayForm( $errorMessages, $missingFields, $member );
  } else {
    $member->update();
    displaySuccess();
  }
  */
}

function deleteCharity() {
/*
$member = new Member( array(
    "id" => isset( $_POST["memberId"] ) ? (int) $_POST["memberId"] : "",
  ) );
  LogEntry::deleteAllForMember( $member->getValue( "id" ) );
  $member->delete();
  displaySuccess();
  */
}

function displaySuccess() {
  $start = isset( $_REQUEST["start"] ) ? (int)$_REQUEST["start"] : 0;
  $order = isset( $_REQUEST["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_REQUEST["order"] ) : "username";
  displayPageHeader( "Changes saved" );
?>
    <p>Your changes have been saved. <a href="view_lapsed_charities.php?start=<?php echo $start ?>&amp;order=<?php echo $order ?>">Return to member list</a></p>
<?php
  displayPageFooter();
}

?>

