<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
charityhound/
view_designated_aggregates.php
tjs 130307

file version 1.00 

release version 1.00
*/
require_once( "common.inc.php" );
require_once( "config.php" );

$provider = isset( $_GET["provider"] ) ? preg_replace( "/[^ a-zA-Z.]/", "", $_GET["provider"] ) : "firebaseIO.com";
$database = isset( $_GET["database"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["database"] ) : "collogistics-test";
$aggregateList = isset( $_GET["aggregateList"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["aggregateList"] ) : "blankScoreList";
$title = "Charities That Send Appeals In Envelopes With No Return Address (blank appeals)";
//if ($aggregateList == 'currencyBatedList') {
if ($aggregateList == 'currencyScoreList') {
	$title = "Charities That Send Appeals Containing Currency (expensive appeals)";	
} else if ($aggregateList == 'confidentialScoreList') {
	$title = "Charities That Formally Commit to Donar Privacy (they do NOT sell your identity information)";	
} else if ($aggregateList == 'reminderScoreList') {
	$title = "Charities That Provide Donors a Schedule For Subsequent Appeals (they do NOT hound you repeatedly!)";	
}
//echo "PHP provider $provider database $database list $aggregateList"; 

displayPageHeader( $title );
?>
<html>
<head>
	<script src="https://cdn.firebase.com/v0/firebase.js"></script>
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
</head>
<body>
 <p>During our site development phase, some registered collaborators have identified <span id="numberRows"></span> nonprofits
  in this group.</p>
  <div id="listTypeDescription"></div>
	<table id="leaderboardTable"></table>
	<div style="float:left;">
	<input type="button" id="more" value="More">
</div>

	<script>
	//alert("blankScoreListRef...");
		//var LEADERBOARD_SIZE = 5;
		var LEADERBOARD_SIZE = 10;
		// tjs 130326
		var numberOfRows = 0;
		var size = LEADERBOARD_SIZE;
		var increment = LEADERBOARD_SIZE;
		var maxSize = 100;
		
	var provider = <?php echo json_encode($provider); ?>;
	var database = <?php echo json_encode($database); ?>;
	//alert("database " + database);
	// tjs 130401 - comment out hack use oddbulb for formal tests...
// tjs 130325 hack for now...
//if (database == 'collogisticstest') {
//	database = 'collogistics-test';
	//alert("hacked database " + database);
//}
	var aggregateList = <?php echo json_encode($aggregateList); ?>;
	//var aggregateListType = "blankScoreList";
	//if ()
	var url = 'https://' + database + "." + provider + '/' + aggregateList;
	//alert("provider " + provider);
	//alert("url " + url);
		// Create our Firebase reference
	var scoreListRef = new Firebase(url);
	var tableHeaderRendered = false;

		//alert("blankScoreListRef " + blankScoreListRef);
		
		// Keep a mapping of firebase locations to HTML elements, so we can move / remove elements as necessary.
		var htmlForPath = {};

		// Helper function that takes a new score snapshot and adds an appropriate row to our leaderboard table.
		function handleScoreAdded(scoreSnapshot, prevScoreName) {
			if (!tableHeaderRendered) {
				tableHeaderRendered = true;
				//var newHeaderRow = "<tr><th>Name</th><th>Count</th></tr>";
				var newHeaderRow = "<tr><th>Name</th></tr>";
				$("#leaderboardTable").append(newHeaderRow);
			}
			var newScoreRow = $("<tr/>");
			newScoreRow.append($("<td/>").append(
					$("<em/>").text(scoreSnapshot.val().name)));
			//newScoreRow.append($("<td/>").text(scoreSnapshot.val().score));

			// Store a reference to the table row so we can get it again later.
			htmlForPath[scoreSnapshot.name()] = newScoreRow;

			// Insert the new score in the appropriate place in the table.
			if (prevScoreName === null) {
				$("#leaderboardTable").append(newScoreRow);
			} else {
				var lowerScoreRow = htmlForPath[prevScoreName];
				lowerScoreRow.before(newScoreRow);
			}
		}

		// Helper function to handle a score object being removed; just removes the corresponding table row.
		function handleScoreRemoved(scoreSnapshot) {
			var removedScoreRow = htmlForPath[scoreSnapshot.name()];
			removedScoreRow.remove();
			delete htmlForPath[scoreSnapshot.name()];
		}

		// Add a callback to handle when a score changes or moves positions.
		var changedCallback = function(scoreSnapshot, prevScoreName) {
			handleScoreRemoved(scoreSnapshot);
			handleScoreAdded(scoreSnapshot, prevScoreName);
		};

		function displayView() {
			//alert("displayView...");
			$('#listTypeDescription').empty();
			//alert("displayView scoreListRef " + scoreListRef);
			if (aggregateList == "blankScoreList") {
				//alert("displayView scoreListRef " + scoreListRef);
				$('#listTypeDescription').text('The group consists of charities who send out "blank-envelope" solicitations (i.e. with no clear indication who the sender is).  This list is sorted by those nonprofits who choose to annoy potential donors most frequently at the top.');
			} else if (aggregateList == "currencyScoreList") {
				$('#listTypeDescription').text('The group consists of charities who send out "currency-bated" solicitations (i.e. with actual coins, stamps, etc.).  This list is sorted by those nonprofits who choose to waste on fundraising this way most frequently at the top.');
			} else if (aggregateList == "confidentialScoreList") {
				$('#listTypeDescription').text('The group consists of charities who send out solicitations that provide donors the ability to ensure privacy (i.e. inhibit selling identity information to other nonprofits). This list is sorted by those favorable nonprofits who choose to do this most frequently at the top.');
			} else if (aggregateList == "reminderScoreList") {
				$('#listTypeDescription').text('The group consists of charities who send out solicitations that provide donors with control over future reminders (i.e. refer to text of iBook: Dead Giveaway - Sleuthing Around Nonprofits). This list is sorted by those favorable nonprofits who choose to do this most frequently at the top.');
			}
			$('#leaderboardTable').empty();
			var scoreListView = scoreListRef.limit(size);
			//alert("displayView startRow " + start + " endRow " + end);

			// Add a callback to handle when a new score is added.
			scoreListView.on('child_added', function(newScoreSnapshot,
					prevScoreName) {
				handleScoreAdded(newScoreSnapshot, prevScoreName);
			});
			// Add a callback to handle when a score is removed
			scoreListView.on('child_removed', function(oldScoreSnapshot) {
				handleScoreRemoved(oldScoreSnapshot);
			});

			scoreListView.on('child_moved', changedCallback);
			scoreListView.on('child_changed', changedCallback);			
		}

		scoreListRef.once('value', function(dataSnapshot) {
			numberOfRows = dataSnapshot.numChildren();
			$("#numberRows").append(numberOfRows);
			if (numberOfRows == 0) {
				$("#more").attr('disabled', 'disabled');
			}
			displayView();
		});
		$("#more").click(function() {
			if (size < maxSize - increment) {
				size = numberOfRows - size > increment? size + increment : numberOfRows;
			} else {
				size = numberOfRows < maxSize? numberOfRows : maxSize;
			}
			if (size >= numberOfRows) {
				$("#more").attr('disabled', 'disabled');
			}
			displayView();
		});
		</script>
    <br/>
    <p>
<a class="even" href="javascript:newLocation('reports', 'logistics')" title="Reports">Back to Contributions Report</a>
</p>
		<?php
displayPageFooter();
?>
		
</body>
</html>

