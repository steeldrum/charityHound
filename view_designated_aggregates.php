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

//require_once( "common.inc.php" );
//require_once( "config.php" );
//require_once( "Member.class.php" );
//require_once( "Charity.class.php" );
//require_once( "RatedCharity.class.php" );
/*
$account = isset( $_GET["account"] ) ? (int)$_GET["account"] : 0;
$from = isset( $_GET["from"] ) ? (int)$_GET["from"] : 2000;
$to = isset( $_GET["to"] ) ? (int)$_GET["to"] : 2100;
$start = isset( $_GET["start"] ) ? (int)$_GET["start"] : 0;
$order = isset( $_GET["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["order"] ) : "charityName";
//list( $charities, $totalRows, $totalSolicitations, $totalDonations ) = Charity::getSolicitationCountByCharities( $account, $from, $to, $start, PAGE_SIZE, $order );
list( $charities, $totalRows ) = RatedCharity::getDesignatedCharities( $account, $from, $to, $start, PAGE_SIZE, $order );
displayPageHeader( "View Designated Charities" );
*/
        // tjs 121127
//delta for lastAmount
//displayPageHeader( "View Designated Charities" );
displayPageHeader( $title );
?>
<html>
<head>
	<script src="https://cdn.firebase.com/v0/firebase.js"></script>
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
</head>
<body>
	<table id="leaderboardTable"></table>

	<script>
	//alert("blankScoreListRef...");
		//var LEADERBOARD_SIZE = 5;
		var LEADERBOARD_SIZE = 10;
	var provider = <?php echo json_encode($provider); ?>;
	var database = <?php echo json_encode($database); ?>;
	//alert("database " + database);
// tjs 130325 hack for now...
if (database == 'collogisticstest') {
	database = 'collogistics-test';
	//alert("hacked database " + database);
}
	var aggregateList = <?php echo json_encode($aggregateList); ?>;
	var url = 'https://' + database + "." + provider + '/' + aggregateList;
	//alert("provider " + provider);
	//alert("url " + url);
		// Create our Firebase reference
		var blankScoreListRef = new Firebase(
				//'https://collogistics.firebaseio.com//blankScoreList');
	url);
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

		// Create a view to only receive callbacks for the last LEADERBOARD_SIZE scores
		var scoreListView = blankScoreListRef.limit(LEADERBOARD_SIZE);

		// Add a callback to handle when a new score is added.
		scoreListView.on('child_added', function(newScoreSnapshot,
				prevScoreName) {
			handleScoreAdded(newScoreSnapshot, prevScoreName);
		});

		// Add a callback to handle when a score is removed
		scoreListView.on('child_removed', function(oldScoreSnapshot) {
			handleScoreRemoved(oldScoreSnapshot);
		});

		// Add a callback to handle when a score changes or moves positions.
		var changedCallback = function(scoreSnapshot, prevScoreName) {
			handleScoreRemoved(scoreSnapshot);
			handleScoreAdded(scoreSnapshot, prevScoreName);
		};
		scoreListView.on('child_moved', changedCallback);
		scoreListView.on('child_changed', changedCallback);
		</script>
    <br/>
<a class="even" href="javascript:newLocation('reports', 'logistics')" title="Reports">Back to Contributions Report</a>
		<?php
displayPageFooter();
?>
		
</body>
</html>

