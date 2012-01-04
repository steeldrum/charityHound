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
//tjs 110511 above ensures that config.php has been loaded as well
$username=DB_USERNAME;
$password=DB_PASSWORD;
$database=DB_NAME;

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

    <!-- form action="admin.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="process" />

        <input type="text" name="startDate" id="startDate" value="2000-01-01" />
        <input type="text" name="endDate" id="endDate" value="2100-12-31" />

        <div style="clear: both;">
          <input type="submit" name="submitButton" id="submitButton" value="Get Aggregate Report" />
          <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
        </div>

      </div>
    </form -->
<p>
Start year: <input id="start" type="number" />
</p>
<p>
End year: <input id="end" type="number" />
</p>
<p>
Hide Donations Detail: <input id="hideDonations" type="checkbox" checked="checked" />
</p>
    <p>
<button id="donations">View Aggregate Donations</button>
</p>

    <p/>
    <p>Distribute Charity To Member or Members</p>
    <form action="admin.php" method="post" style="margin-bottom: 50px;">
      <div style="width: 30em;">
        <input type="hidden" name="action" value="process" />
		<p>
        Charity ID to be distributed (possibly a member's charity):</p>
        <p><input type="text" name="charityId" /></p>
        <p>Member ID to be distributed to ('0' means all, same as contributer means just propagate to base):</p>
        <p><input type="text" name="memberId" /></p>

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
	$baseId = $id;
	$member = $_POST["memberId"];
	
	date_default_timezone_set ( "America/New_York" );
	//$date = time();
    $today = date("Y-m-d");
	
	//$username="root";
	//$password="root";
	//$database="COLLORG";
	
	$con = mysql_connect('localhost',$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");
	
	//find list of active members to distribute the newly added charity to
	$activeMembers = array();
	if ($member == 0) {
		$query="SELECT distinct memberId FROM charities";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while ($i < $num) {
			$memberId=mysql_result($result,$i,"memberId");
			if ($memberId <> 0) {
				$activeMembers[] = $memberId;
			}
			$i++;
		}
	} else {
		$activeMembers[] = $member;
	}
	
	//locate the newly added charity which is to be distributed (or propagated back to the base)
	//tjs110310
	//$query="SELECT * FROM charities where memberId = 0 and id =".$id;
	$query="SELECT * FROM charities where id =".$id;
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	if ($num == 1) {
		$memberId=mysql_result($result,$i,"memberId");	
		$charityName=mysql_result($result,$i,"charityName");
		$shortName=mysql_result($result,$i,"shortName");
		if (strlen($shortName) == 0)
			$shortName='';
		$dunns=mysql_result($result,$i,"dunns");
		if (strlen($dunns) == 0)
			$dunns='';
		$url=mysql_result($result,$i,"url");
		if (strlen($url) == 0)
			$url='';
		$numStars=mysql_result($result,$i,"numStars");
		if (strlen($numStars) == 0)
			$numStars='0';
		$isInactive=mysql_result($result,$i,"isInactive");
		if (strlen($isInactive) == 0)
			$isInactive='0';
		$isForProfit=mysql_result($result,$i,"isForProfit");
		if (strlen($isForProfit) == 0)
			$isForProfit='0';
		//$baseId=mysql_result($result,$i,"baseId");
		//echo "charityName ".$charityName." id ".$id."\n";
		//e.g. charityName Collogistics id 1999
		//echo "charityName ".$charityName." id ".$id." numStars ".$numStars."\n";
		//e.g. charityName Collogistics id 1999 numStars 0 
		//echo "memberId ".$memberId." charityName ".$charityName." id ".$id." numStars ".$numStars."\n";
		//e.g. memberId 1 charityName ALS Association id 1001 numStars 0 
	}
	
	//case where the id specified is a row that a member had created
	//this case means the member's row will be propagated back into the memberId = 0 (base) row
	//rows that are propagated back are then automatically picked up by future members
	//but if the associated member is zero then they are distributed now
	if ($memberId > 0) {
		$query="SELECT id FROM charities where memberId = 0 and charityName like '%".$charityName."%'";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		//echo "num ".$num."\n";
		//e.g. activeMember 1 num 0 
		//the base doesn't have a charity with a name like the new one that the member wants to distribute
		if ($num == 0) {
			//inserts a new charity into the base (i.e. propagates the member's cahrity to the base)
			//$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit) VALUES (0,"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.','.$today.',1,'.$isForProfit.')';
			//$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit) VALUES (0,"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.',"'.$today.'",1,'.$isForProfit.')';
			$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit) VALUES (0,"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.',"'.$today.'",0,'.$isForProfit.')';
			//echo "query: ".$query;
			$result=mysql_query($query);
			//now update the member's row to ensure it has the baseId value
			$query="SELECT id FROM charities where memberId = 0 and charityName = '".$charityName."'";
			$result=mysql_query($query);
			$num=mysql_numrows($result);
			$i=0;
			if ($num == 1) {
				$baseId=mysql_result($result,$i,"id");
				$query='UPDATE charities SET baseId ='.$baseId.' WHERE id = '.$id;
				$result=mysql_query($query);
			}	
		}
	} 
	//this is where the distribution of the charity from the base to one (or all) members occurs.
	foreach($activeMembers as $activeMember) {
		//echo "activeMember ".$activeMember."\n";
		$query="SELECT id FROM charities where memberId =".$activeMember." and charityName like '%".$charityName."%'";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		//echo "num ".$num."\n";
		//e.g. activeMember 1 num 0 
		//this member doesn't have a charity with a name like the new one so distribute it to that member
		if ($num == 0) {
			//$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit, baseId) VALUES ('.$activeMember.',"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.','.$date.',1,'.$isForProfit.','.$id.')';
			//$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit, baseId) VALUES ('.$activeMember.',"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.','.$today.',1,'.$isForProfit.','.$baseId.')';
			$query='INSERT INTO charities (memberId, charityName, shortName, dunns, url, numStars, createdDate, isInactive, isForProfit, baseId) VALUES ('.$activeMember.',"'.$charityName.'","'.$shortName.'","'.$dunns.'","'.$url.'",'.$numStars.',"'.$today.'",1,'.$isForProfit.','.$baseId.')';
			$result=mysql_query($query);
		}
	}
	
	
	mysql_close();

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
