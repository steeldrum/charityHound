<?php
/***************************************
 $Revision::                            $: Revision of last commit
 $LastChangedBy::                       $: Author of last commit
 $LastChangedDate::                     $: Date of last commit
 ***************************************/
/*
 charityhound/
 ratingsManager.php
 tjs 130301

 file version 1.00

 release version 1.00
 */
/*
 FYI
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `charityId` smallint(5) unsigned NOT NULL,
 `memberId` smallint(5) unsigned NOT NULL,
 `year` smallint(5) unsigned NOT NULL,
 `solicitations` smallint(5) unsigned NOT NULL,
 `blankEnvelopeAppeals` smallint(5) unsigned NOT NULL,
 `currencyBatedAppeals` smallint(5) unsigned NOT NULL,
 `appealReminderSchedules` smallint(5) unsigned NOT NULL,
 `appealPrivacyPledges` smallint(5) unsigned NOT NULL,
 `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `ratingForMemberYear` (`charityId`,`memberId`,`year`)
 */

date_default_timezone_set ( "America/New_York" );

require_once( "common.inc.php" );
require_once( "Rating.class.php" );
require_once 'firebaseLib.php';

//echo "memberManager starting...";

if ( isset( $_POST["action"] ) and $_POST["action"] == "manage" ) {
	processForm();
} else {
	displayForm( array(), array(), new Rating( array() ) );
}

function displayForm( $errorMessages, $missingFields, $rating ) {
	//displayPageHeader( "Sign up for the book club!" );
	displayPageHeader( "Manage Ratings!" );

	if ( $errorMessages ) {
		foreach ( $errorMessages as $errorMessage ) {
			echo $errorMessage;
		}
	} else {
		?>
<p>To manage ratings, please fill in your details below and click Post
	Update.</p>
<p>Fields marked with an asterisk (*) are required.</p>
		<?php } ?>

<form action="ratingsManager.php" method="post"
	style="margin-bottom: 50px;">
	<div style="width: 30em;">
		<input type="hidden" name="action" value="manage" /> <label for="year"
		<?php validateField( "year", $missingFields ) ?>>Year *</label> <input
			type="text" name="year" id="year"
			value="<?php echo $rating->getValueEncoded( "year" ) ?>" />
		<p>Enter a '1' to trigger post of year for the list(s):</p>
		<label for="blankEnvelopeAppeals"
		<?php validateField( "blankEnvelopeAppeals", $missingFields ) ?>>Blanks</label>
		<input type="text" name="blankEnvelopeAppeals"
			id="blankEnvelopeAppeals"
			value="<?php echo $rating->getValueEncoded( "blankEnvelopeAppeals" ) ?>" />
		<label for="currencyBatedAppeals"
		<?php validateField( "currencyBatedAppeals", $missingFields ) ?>>Currencies</label>
		<input type="text" name="currencyBatedAppeals"
			id="currencyBatedAppeals"
			value="<?php echo $rating->getValueEncoded( "currencyBatedAppeals" ) ?>" />
		<label for="appealReminderSchedules"
		<?php validateField( "appealReminderSchedules", $missingFields ) ?>>Reminders</label>
		<input type="text" name="appealReminderSchedules"
			id="appealReminderSchedules"
			value="<?php echo $rating->getValueEncoded( "appealReminderSchedules" ) ?>" />
		<label for="appealPrivacyPledges"
		<?php validateField( "appealPrivacyPledges", $missingFields ) ?>>Confidentials</label>
		<input type="text" name="appealPrivacyPledges"
			id="appealPrivacyPledges"
			value="<?php echo $rating->getValueEncoded( "appealPrivacyPledges" ) ?>" />

		<div style="clear: both;">
			<input type="submit" name="submitButton" id="submitButton"
				value="Post Year" /> <input type="reset" name="resetButton"
				id="resetButton" value="Reset Form" style="margin-right: 20px;" />
		</div>

	</div>
</form>
<br />
<a href="admin.php">Site Admin</a>
		<?php
		displayPageFooter();
}

function processForm() {
	//$requiredFields = array( "emailAddress", "token" );
	$requiredFields = array( "year");
	$missingFields = array();
	$errorMessages = array();


	$rating = new Rating( array(
    "year" => isset( $_POST["year"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["year"] ) : "",    
    "blankEnvelopeAppeals" => isset( $_POST["blankEnvelopeAppeals"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["blankEnvelopeAppeals"] ) : "",
      "currencyBatedAppeals" => isset( $_POST["currencyBatedAppeals"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["currencyBatedAppeals"] ) : "",
      "appealReminderSchedules" => isset( $_POST["appealReminderSchedules"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["appealReminderSchedules"] ) : "",
      "appealPrivacyPledges" => isset( $_POST["pappealPrivacyPledges"] ) ? preg_replace( "/[^ \-\_a-zA-Z0-9]/", "", $_POST["appealPrivacyPledges"] ) : ""      
      ) );

      //echo "memberManager processForm token created...";

      foreach ( $requiredFields as $requiredField ) {
      	if ( !$rating->getValue( $requiredField ) ) {
      		$missingFields[] = $requiredField;
      	}
      }

      if ( $missingFields ) {
      	$errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Post Year to resend the form.</p>';
      }

      if ( $errorMessages ) {
      	displayForm( $errorMessages, $missingFields, $token );
      } else {
      	//echo "memberManager processForm inserting token...";
      	//$token->insert();
      	$year = $rating->getValueEncoded( "year" );
      	//echo "year $year";
      	 
      	$blankEnvelopeAppeals = $rating->getValueEncoded( "blankEnvelopeAppeals" );
      	$currencyBatedAppeals = $rating->getValueEncoded( "currencyBatedAppeals" );
      	$appealReminderSchedules = $rating->getValueEncoded( "appealReminderSchedules" );
      	$appealPrivacyPledges = $rating->getValueEncoded( "appealPrivacyPledges" );
      	 
      	//echo "year $year blankEnvelopeAppeals $blankEnvelopeAppeals currencyBatedAppeals $currencyBatedAppeals appealReminderSchedules $appealReminderSchedules appealPrivacyPledges $appealPrivacyPledges";
      	$username=DB_USERNAME;
      	$password=DB_PASSWORD;
      	$database=DB_NAME;
      	// tjs 130226
      	$aggregateDSN=AGGREGATE_DSN;
      	$aggregateDatabase=AGGREGATE_DB_NAME;
      	session_start();
      	define("MYSQL_HOST", "localhost");

      	$con = mysql_connect("".MYSQL_HOST."",$username,$password);
      	//select r.blankEnvelopeAppeals, c.baseId from ratings r, charities c where r.year = 2013 and r.blankEnvelopeAppeals > 0 and r.charityId = c.id order by c.baseId;

      	// for test only:
      	if ($blankEnvelopeAppeals > 0) {
      		$aggregateList = 'blankScoreList';
      		$aggregateType = 'blankEnvelopeAppeals';
      		syncAggregatePrep($aggregateDSN, $database, $aggregateDatabase, $aggregateType, $aggregateList, $year);
      		/*
      		 $provider = $aggregateDSN;
      		 // hack for test - comment out!
      		 //$aggregateDatabase = 'collogistics';

      		 @mysql_select_db($database) or die( "Unable to select database");
      		 $query="SELECT r.blankEnvelopeAppeals s, c.baseId b, c.charityName n FROM ratings r, charities c where r.year = ".$year." and r.blankEnvelopeAppeals > 0 and r.charityId = c.id order by c.baseId";
      		 $result=mysql_query($query);
      		 $num=mysql_numrows($result);
      		 // e.g 488
      		 //echo "num for select ".$num;
      		 $i=0;
      		 while ($i < $num) {
      		 $baseId=mysql_result($result,$i,"b");
      		 $charityName=mysql_result($result,$i,"n");
      		 $score=mysql_result($result,$i,"s");
      		 // tjs 130321
      		 if ($baseId != null && $charityName != null && $score > 0) {
      		 //echo "baseId $baseId name $charityName score $score";
      		 //ensureNameBaseIdAndList($provider, $aggregateDatabase, $baseId, $charityName, $aggregateList);
      		 syncAggregateRatingsUpdate($provider, $aggregateDatabase, $baseId, $charityName, $year, $aggregateList, $score);
      		 }
      		 $i++;
      		 }*/
      	}

      	if ($currencyBatedAppeals > 0) {
      		$aggregateList = 'currencyScoreList';
      		$aggregateType = 'currencyBatedAppeals';
      		syncAggregatePrep($aggregateDSN, $database, $aggregateDatabase, $aggregateType, $aggregateList, $year);
      		/*
      		 //$provider = 'firebaseio.com';
      		 $provider = $aggregateDSN;
      		 // hack for test - comment out!
      		 //$aggregateDatabase = 'collogistics';

      		 @mysql_select_db($database) or die( "Unable to select database");
      		 $query="SELECT r.currencyBatedAppeals s, c.baseId b, c.charityName n FROM ratings r, charities c where r.year = ".$year." and r.currencyBatedAppeals > 0 and r.charityId = c.id order by c.baseId";
      		 $result=mysql_query($query);
      		 $num=mysql_numrows($result);
      		 // e.g 488
      		 //echo "num for select ".$num;
      		 $i=0;
      		 while ($i < $num) {
      		 $baseId=mysql_result($result,$i,"b");
      		 $charityName=mysql_result($result,$i,"n");
      		 $score=mysql_result($result,$i,"s");
      		 //echo "baseId $baseId name $charityName score $score";
      		 //ensureNameBaseIdAndList($provider, $aggregateDatabase, $baseId, $charityName, $aggregateList);
      		 // tjs 130321
      		 if ($baseId != null && $charityName != null && $score > 0) {
      		 syncAggregateRatingsUpdate($provider, $aggregateDatabase, $baseId, $charityName, $year, $aggregateList, $score);
      		 }
      		 $i++;
      		 }*/
      	}
      	if ($appealReminderSchedules > 0) {
      		$aggregateList = 'reminderScoreList';
      		$aggregateType = 'appealReminderSchedules';
      		syncAggregatePrep($aggregateDSN, $database, $aggregateDatabase, $aggregateType, $aggregateList, $year);
      	}
      	if ($appealPrivacyPledges > 0) {
      		$aggregateList = 'confidentialScoreList';
      		$aggregateType = 'appealPrivacyPledges';
      		syncAggregatePrep($aggregateDSN, $database, $aggregateDatabase, $aggregateType, $aggregateList, $year);
      	}
      	displayThanks();
      }
}

function syncAggregatePrep($provider, $database, $aggregateDatabase, $aggregateType, $aggregateList, $year) {
	@mysql_select_db($database) or die( "Unable to select database");
	//$query="SELECT r.blankEnvelopeAppeals s, c.baseId b, c.charityName n FROM ratings r, charities c where r.year = ".$year." and r.blankEnvelopeAppeals > 0 and r.charityId = c.id order by c.baseId";
	$query="SELECT r.".$aggregateType." s, c.baseId b, c.charityName n FROM ratings r, charities c where r.year = ".$year." and r.".$aggregateType." > 0 and r.charityId = c.id order by c.baseId";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	// e.g 488
	//echo "num for select ".$num;
	$i=0;
	while ($i < $num) {
		$baseId=mysql_result($result,$i,"b");
		$charityName=mysql_result($result,$i,"n");
		$score=mysql_result($result,$i,"s");
		// tjs 130321
		if ($baseId != null && $charityName != null && $score > 0) {
			//echo "baseId $baseId name $charityName score $score";
			//ensureNameBaseIdAndList($provider, $aggregateDatabase, $baseId, $charityName, $aggregateList);
			syncAggregateRatingsUpdate($provider, $aggregateDatabase, $baseId, $charityName, $year, $aggregateList, $score);
		}
		$i++;
	}
}

function syncAggregateRatingsUpdate($provider, $database, $baseId, $charityName, $year, $aggregateList, $score) {
	list( $referenceYear, $currentYearScore, $cumYearScores) = getYearListReferenceAndScore( $provider, $database, $aggregateList, $baseId, $year );
	//echo "syncAggregateRatingsUpdate referenceYear $referenceYear currentYearScore $currentYearScore cumYearScores $cumYearScores score $score";
	// e.g. syncAggregateRatingsUpdate referenceYear currentYearScore 0 cumYearScores 0

	if ($currentYearScore != $score) {
		//setNameYearPriorityScore( $provider, $database, $aggregateList, $baseId, $charityName, $year, $score, $cumYearScores );
		setNameYearPriorityScore( $provider, $database, $aggregateList, $baseId, $charityName, $year, $score );
	}
}

function getYearListReferenceAndScore($provider, $database, $listType, $baseId, $year) {
	$referenceYear = null;
	$score = 0;
	$yearScore = 0;
	$cumScores = 0;
	$url = "https://".$database.".".$provider."/";
	$fb = new fireBase($url);
	$baseIdPath = $aggregateList."/".$baseId;
	$result = $fb->get($baseIdPath);
	$resultArray = json_decode($result, true);
	$foundYear = false;
	$yearScore = 0;
	if ($resultArray != null) {
		foreach($resultArray as $key=>$value)
		{
			//echo "Key: ", $key, " is $value ";
			// e.g. Key: -IopwnuAmjn30Yj1qn0V is Array
			if ($key != 'name') {
				//echo "Key: ", $key2, " is $value2 ";
				// e.g. Key: score is 44 Key: year is 2013
				if ($key == $year) {
					$yearScore = $value;
					$referenceYear = $year;
					$foundYear = true;
				}
				$cumScores += $value;
			}
		}
	}
	if ($foundYear  && $score == 0) {
		$score = $yearScore;
	}
	return array( $referenceYear, $score, $cumScores );
}

function setNameYearPriorityScore( $provider, $database, $aggregateList, $baseId, $name, $year, $yearScore) {
	//echo "setNameYearPriorityScore name $name year $year yearScore $yearScore score $score";
	// setNameYearPriorityScore name Africare year 2011 yearScore 1 score 0
	//echo "setNameYearPriorityScore provider $provider database $database aggregateList $aggregateList baseId $baseId name $name year $year yearScore $yearScore score $score";

	$url = "https://".$database.".".$provider."/";
	$fb = new fireBase($url);
	$baseIdPath = $aggregateList."/".$baseId;

	$result = $fb->get($baseIdPath);
	$resultArray = json_decode($result, true);
	//echo "setNameYearPriorityScore resultArray $resultArray";
	$priority = 0;
	$data = array(
	//'name' => $name,
	'name' => $name
	//'.priority' => $score
	);
	$foundYear = false;
	if ($resultArray != null) {
		//echo "setNameYearPriorityScore resultArray $resultArray";
		foreach($resultArray as $key=>$value) {
			if ($key != "name") {
				if ($key == $year) {
					$data[$key] = $yearScore;
					$foundYear = true;
					$priority += $yearScore;
				} else {
					$data[$key] = $value;
					$priority += $value;
				}
			}
		}
	}
	if(!$foundYear) {
		$data[$year] = $yearScore;
		$priority += $yearScore;
	}
	$data['.priority'] = $priority;
	//echo "setNameYearPriorityScore baseIdPath $baseIdPath data $data";
	$result = $fb->set($baseIdPath, $data);
}

function displayThanks() {
	displayPageHeader( "Ratings management is completed!" );
	?>
<br />
<a href="admin.php">Site Admin</a>
<?php
  displayPageFooter();
}
?>
