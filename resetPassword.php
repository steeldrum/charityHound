<?php
/***************************************
$Revision:: 91                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 11:31:55#$: Date of last commit
***************************************/
/*
charityhound/
resetPassword.php
tjs 110317

file version 1.00 

release version see above
*/
//test
//http://localhost/~thomassoucy/charityhound/resetPassword.php

date_default_timezone_set ( "America/New_York" );

require_once( "common.inc.php" );

if ( isset( $_POST["action"] ) and $_POST["action"] == "reset" ) {
  processForm1();
} else {
	if ( isset( $_POST["action"] ) and $_POST["action"] == "authorize" ) {
  		processForm2();
	} else { 
	displayForm( array(), array(), new Member( array() ) );
  }
}

function displayForm( $errorMessages, $missingFields, $member ) {
  displayPageHeader( "Reset Collogistics collaborator password!" );

  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
      echo $errorMessage;
    }
  } else {
?>
    <p>Do want to change your password?</p>
    <p>To reset it, please fill in your details below and click Send Details.</p>
    <p>Fields marked with an asterisk (*) are required.</p>
    <p>Also either your username OR your email is required.</p>
<?php } ?>

    <form action="resetPassword.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="reset" />

        <label for="username"<?php validateField( "username", $missingFields ) ?>>Specify your username</label>
        <input type="text" name="username" id="username" value="<?php echo $member->getValueEncoded( "username" ) ?>" />

        <label for="password1"<?php if ( $missingFields ) echo ' class="error"' ?>>Choose a new password *</label>
        <input type="password" name="password1" id="password1" value="" />
        <label for="password2"<?php if ( $missingFields ) echo ' class="error"' ?>>Retype the new password *</label>
        <input type="password" name="password2" id="password2" value="" />

        <label for="emailAddress"<?php validateField( "emailAddress", $missingFields ) ?>>Email address</label>
        <input type="text" name="emailAddress" id="emailAddress" value="<?php echo $member->getValueEncoded( "emailAddress" ) ?>" />

        <div style="clear: both;">
          <input type="submit" name="submitButton" id="submitButton" value="Send Details" />
          <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
        </div>

      </div>
    </form>
    <br/>
<?php
  displayPageFooter();
}

function processForm1() {
  $requiredFields = array( "password" );
  $missingFields = array();
  $errorMessages = array();

//prototype member
  $member = new Member( array( 
    "username" => isset( $_POST["username"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["username"] ) : "",
    "password" => ( isset( $_POST["password1"] ) and isset( $_POST["password2"] ) and $_POST["password1"] == $_POST["password2"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["password1"] ) : "",
    "emailAddress" => isset( $_POST["emailAddress"] ) ? preg_replace( "/[^ \@\.\-\_a-zA-Z0-9]/", "", $_POST["emailAddress"] ) : ""

  ) );

  foreach ( $requiredFields as $requiredField ) {
    if ( !$member->getValue( $requiredField ) ) {
      $missingFields[] = $requiredField;
    }
  }

  if ( $missingFields ) {
    $errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
  }

  if ( !isset( $_POST["password1"] ) or !isset( $_POST["password2"] ) or !$_POST["password1"] or !$_POST["password2"] or ( $_POST["password1"] != $_POST["password2"] ) ) {
    $errorMessages[] = '<p class="error">Please make sure you enter your password correctly in both password fields.</p>';
  }
  if ( $errorMessages ) {
    displayForm( $errorMessages, $missingFields, $member );
  } else {
	  if ( Member::getByUsername( $member->getValue( "username" ) ) ) {
		displayAuthorizationForm( array(), $member, Member::getByUsername( $member->getValue( "username" ) ) );
	  } else {
		  if ( Member::getByEmailAddress( $member->getValue( "emailAddress" ) ) ) {
			displayAuthorizationForm( array(), $member, Member::getByEmailAddress( $member->getValue( "emailAddress" ) ) );
		  } else {
    		$errorMessages[] = '<p class="error">Either username or email must be in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
    		displayForm( $errorMessages, $missingFields, $member );
		  }
	  }
  }
}

function displayAuthorizationForm( $errorMessages, $memberPrototype, $memberStored ) {
  displayPageHeader( "Reset Collogistics collaborator authorization" );

  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
      echo $errorMessage;
    }
  } else {
?>
    <p>To change your password requires authorization</p>
    <p>To change it, please answer the password mnemonic question you had originally specified and click Send Details.</p>
    <p>Fields marked with an asterisk (*) are required.</p>
<?php } ?>

    <form action="resetPassword.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="authorize" />
        <input type="hidden" name="memberId" value="<?php echo $memberStored->getValue( "id" ) ?>" />
        <input type="hidden" name="originalPasswordMnemonicQuestion" value="<?php echo $memberStored->getValue( "passwordMnemonicQuestion" ) ?>" />
        <input type="hidden" name="originalPasswordMnemonicAnswer" value="<?php echo $memberStored->getValue( "passwordMnemonicAnswer" ) ?>" />
        <input type="hidden" name="newPassword" value="<?php echo $memberPrototype->getValue( "password" ) ?>" />

        <label for="prototypePasswordMnemonicAnswer" ><?php echo $memberStored->getValue( "passwordMnemonicQuestion" ) ?>&nbsp;*</label>
        <input type="text" name="prototypePasswordMnemonicAnswer" id="prototypePasswordMnemonicAnswer" />

        <div style="clear: both;">
          <input type="submit" name="submitButton" id="submitButton" value="Send Details" />
          <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
        </div>

      </div>
    </form>
    <br/>
<?php
  displayPageFooter();
}

function processForm2() {
  $errorMessages = array();
	$memberId = $_POST["memberId"];
	$originalPasswordMnemonicQuestion = $_POST["originalPasswordMnemonicQuestion"];
	$originalPasswordMnemonicAnswer = $_POST["originalPasswordMnemonicAnswer"];
	$newPassword = $_POST["newPassword"];

	$prototypePasswordMnemonicAnswer = $_POST["prototypePasswordMnemonicAnswer"];
	
	//echo 'member Id '.$memberId.' original question '.$originalPasswordMnemonicQuestion.' answer '.$originalPasswordMnemonicAnswer.' password '.$newPassword.' prototype answer '.$prototypePasswordMnemonicAnswer;
	
	if ($prototypePasswordMnemonicAnswer <> $originalPasswordMnemonicAnswer) {
    	$errorMessages[] = '<p class="error">The answer you gave does not match your original answer. Please complete the highlighted field below and click Send Details to resend the form.</p>';
		$storedMember = new Member( array(
			"id" => $memberId,
			"passwordMnemonicQuestion" => $originalPasswordMnemonicQuestion,
			"passwordMnemonicAnswer" => $originalPasswordMnemonicAnswer
			)
		);
		$prototypeMember = new Member( array(
			"password" => $newPassword
			)
		);
		displayAuthorizationForm( $errorMessages, $prototypeMember, $storedMember );
	} else {
		//user is authorized to change the password!
		$member = Member::getMember( $memberId );
		//$member.updatePassword($newPassword);
		$member->updatePassword($newPassword);
	}
	displayThanks();
}

function displayThanks() {
  displayPageHeader( "You password has been reset!" );
?>
    <p>Thank you, you may login using the new password as a registered member of Collogistics.</p>
    <br/>
    <a href="index.php">Home</a>
<?php
  displayPageFooter();
}
?>
