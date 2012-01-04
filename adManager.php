<?php
/***************************************
$Revision:: 91                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 11:31:55#$: Date of last commit
***************************************/
/*
Collaborators/
adManager.php
tjs 110104

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );

session_start();

//NB we are ensured that the user is logged in and has a session...
$account = 0;
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	$account = $member->getValue( "id" );
} 

if ( isset( $_POST["action"] ) and $_POST["action"] == "process" ) {
  //processForm();
  processForm($account);
} else {
  displayForm( array(), array(), new Ad( array() ) );
}

function displayForm( $errorMessages, $missingFields, $ad ) {
  displayPageHeader( "Assign ad for Collogistics collaborator!" );

  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
      echo $errorMessage;
    }
  } else {
?>
    <p>Thanks for placing an ad with Collogistics.</p>
    <p>To schedule your ad, please fill in your details below and click Send Details.</p>
    <p>Fields marked with an asterisk (*) are required.</p>
<?php } ?>

    <form action="adManager.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="process" />
        <input type="hidden" name="width" value="320" />
        <input type="hidden" name="height" value="50" />
        <input type="hidden" name="numDisplayed" value="0" />
        <input type="hidden" name="displayedDate" value="2011-01-01" />
        <input type="hidden" name="circulationNumber" value="0" />
        <input type="hidden" name="description" value="display ad" />
        <input type="hidden" name="numOccurences" value="0" />
        <input type="hidden" name="perOccurence" value="0" />
        <input type="hidden" name="expirationDate" value="2111-01-01" />
        <input type="hidden" name="createdDate" value="2011-01-01" />
        <input type="hidden" name="circulationWeight" value="0" />

		<!-- fields start here -->
		<!-- fields to handle
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
    		circulationWeight -->

        <!-- label for="memberId"<?php validateField( "memberId", $missingFields ) ?>>Choose a member ID *</label>
        <input type="text" name="memberId" id="memberId" value="<?php echo $ad->getValueEncoded( "memberId" ) ?>" / -->

       <label for="adType">What's the ad type?</label>
        <select name="adType" id="adType" size="1">
        <?php foreach ( $ad->getAdTypes() as $value => $label ) { ?>
          <option value="<?php echo $value ?>"<?php setSelected( $ad, "adType", $value ) ?>><?php echo $label ?></option>
        <?php } ?>
        </select>

        <label for="adName"<?php validateField( "adName", $missingFields ) ?>>Choose an ad name *</label>
        <input type="text" name="adName" id="adName" value="<?php echo $ad->getValueEncoded( "adName" ) ?>" />

        <label for="tabLine"<?php validateField( "tabLine", $missingFields ) ?>>Choose a tab line *</label>
        <input type="text" name="tabLine" id="tabLine" value="<?php echo $ad->getValueEncoded( "tabLine" ) ?>" />

        <div style="clear: both;">
          <input type="submit" name="submitButton" id="submitButton" value="Send Details" />
          <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
        </div>

      </div>
    </form>
<?php
  displayPageFooter();
}

//function processForm() {
function processForm($account) {
  //$requiredFields = array( "memberId", "adName", "tabLine" );
  $requiredFields = array( "adName", "tabLine" );
  $missingFields = array();
  $errorMessages = array();
  $circulatedAdResults = Ad::getAds(0, 1, 'circulationNumber');
  $numRows = $circulatedAdResults[1];
	$circulatedAds = $circulatedAdResults[0];
	$nextAdForCirculation = $circulatedAds[0];
	$currentCirculationNumber = $nextAdForCirculation->getValue('circulationNumber');
	$currentCirculationNumber--;
	
  $ad = new Ad( array( 
  /*
     "joinDate" => date( "Y-m-d" )
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
    */
    //"memberId" => isset( $_POST["memberId"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["memberId"] ) : "",
    "memberId" => $account,
    "adType" => isset( $_POST["adType"] ) ? preg_replace( "/[^ \'\-a-zA-Z0-9]/", "", $_POST["adType"] ) : "",
    "adName" => isset( $_POST["adName"] ) ? preg_replace( "/[^ \'\-\.a-zA-Z0-9]/", "", $_POST["adName"] ) : "",
    "width" => isset( $_POST["width"] ) ? preg_replace( "/[^0-9]/", "", $_POST["width"] ) : "320",
    "height" => isset( $_POST["height"] ) ? preg_replace( "/[^0-9]/", "", $_POST["height"] ) : "50",
    //"tabLine" => isset( $_POST["tabLine"] ) ? preg_replace( "/[^ \'\-\.%a-zA-Z0-9]/", "", $_POST["tabLine"] ) : "",
    "tabLine" => isset( $_POST["tabLine"] ) ? preg_replace( "/[^ \'\-\.%$!a-zA-Z0-9]/", "", $_POST["tabLine"] ) : "",
    "displayedDate" => isset( $_POST["displayedDate"] ) ? preg_replace( "/[^\-0-9]/", "", $_POST["displayedDate"] ) : "",
    "circulationNumber" => $currentCirculationNumber,
	"description" => isset( $_POST["description"] ) ? preg_replace( "/[^ \'\-\.%a-zA-Z0-9]/", "", $_POST["description"] ) : "",
    "expirationDate" => isset( $_POST["expirationDate"] ) ? preg_replace( "/[^\-0-9]/", "", $_POST["expirationDate"] ) : "",
    "createdDate" => isset( $_POST["createdDate"] ) ? preg_replace( "/[^\-0-9]/", "", $_POST["createdDate"] ) : ""
  ) );

  foreach ( $requiredFields as $requiredField ) {
    if ( !$ad->getValue( $requiredField ) ) {
      $missingFields[] = $requiredField;
    }
  }

  if ( $missingFields ) {
    $errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
  }
/*
  if ( Member::getByUsername( $member->getValue( "username" ) ) ) {
    $errorMessages[] = '<p class="error">A member with that username already exists in the database. Please choose another username.</p>';
  }

  if ( Member::getByEmailAddress( $member->getValue( "emailAddress" ) ) ) {
    $errorMessages[] = '<p class="error">A member with that email address already exists in the database. Please choose another email address, or contact the webmaster to retrieve your password.</p>';
  }
*/
  if ( $errorMessages ) {
    displayForm( $errorMessages, $missingFields, $ad);
  } else {
    $ad->insert();
   // displayThanks();
   $name = $ad->getValue('adName');
    uploadFile($name);
  }
}

function uploadFile($name) {
  displayPageHeader( "Thanks for requesting the ad!  Next we need the ad's image file." );
  //todo validate the chosen name
?>
<form action="adManagerFile.php" method="post"
enctype="multipart/form-data">
<p>
Note: All display ads appear on mobile clients only!
<br />
Create display ads at 72dpi with a width of 320 pixels and height of 50 pixels.
<br />
(The total size MUST be under 100Kb.  Types supported include gif, png and jpeg.)
</p>
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>
<br/>
    <a href="index.php">Home</a>
<?php
  displayPageFooter();
}

function displayThanks() {
  displayPageHeader( "Thanks for adding the ad!" );
?>
    <p>Thank you, your ad is now scheduled to run via Collogistics browser client access.</p>
    <br/>
    <a href="index.php">Home</a>
<?php
  displayPageFooter();
}
?>
