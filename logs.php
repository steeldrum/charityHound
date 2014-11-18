<?php
require_once( "common.inc.php" );
session_start();
$account = 0;
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	if ($member != null) {
		$account = $member->getValue( "id" );
	}
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--------------------------------------
$Revision:: 98                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-18 10:36:33#$: Date of last commit
--------------------------------------->
<!--
charityhound/
logs.php 

tjs 110511

file version 1.00

release version 1.00

-->
<html lang="en">

  <head>
	<link type="text/css" href="themes/base/ui.all.css" rel="stylesheet" />
  <link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
<link rel="stylesheet" type="text/css" href="css/logs.css">
  <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
  <link rel="stylesheet" type="text/css" href="css/demo_table.css">
   <script type="text/javascript" src="js/argumenturl.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>
    <!-- tjs 130314 -->
	<script src="https://cdn.firebase.com/v0/firebase.js"></script>

     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Charity Hound</title>
    		<script type="text/javascript">
	var loginAccountNumber = 0;
</script>

  </head>

  <body class="example_alt_pagination">

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">

<!--
Filter selections:  this year, last year, start year to end year
?Order By selections: 

search on name or shortname lists results with count solicitations, count donations,
average donation, cumulative donation, default amount (the most recent amount added)

action column (+) adds a donation record (if amount <> 0) OR adds a solicitation
record otherwise (while tyhe (-) action simply adds a solicitation record)

eventually show date added (and allow changed value)
-->
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Charities</h1>

<div id="charityList">
</div>
<p/>
<br/>
<br/>

<h1>Donations</h1>

<div id="donationList">
</div>


<br/>

<br/>

<div class="iconControls">

<a href="javascript:newLocation('index', 'logistics')"><img src="images/home.gif"></a>

</div>

			</div>

			<div id="clear"></div>

		</div>

<!-- add/edit charity dialog -->
<div id="charityDialog">
	<div id="charityDialogMsg" style="display: none;">
	Really Delete? <br/>
	</div>
	<form name="charityForm">
		<input type="hidden" name="id" />
		<input type="hidden" name="remove" />
		<table><tbody>
		<tr>
		<td>name: <input type="text" name="name" /></td>
		</tr>
		<tr>
		<td>Shortname: <input type="text" name="shortname" /></td>
		</tr>
		</tbody></table>
		<button type="button" onclick="processCharityForm()">Submit</button>
	</form>
</div>

<!-- add/edit donation dialog -->
<div id="donationDialog">
	<div id="donationDialogMsg" style="display: none;">
	Really Delete? <br/>
	</div>
	<form name="donationForm">
		<input type="hidden" name="id" />
		<input type="hidden" name="remove" />
		<input type="hidden" name="charityId" />
		<input type="hidden" name="memberId" />
		<table><tbody>
		<tr>
		<td>Amount: <input type="number" name="amount" /></td>
		</tr>
		<tr>
		<td>Date: <div id="datepicker" /></td>
		</tr>
		<tr>
		<td>Blank Envelope Appeal: <input type="checkbox" name="blank" value="1" /></td>
		</tr>
		<tr>
		<td>Currency Bated Appeal: <input type="checkbox" name="currency" value="1" /></td>
		</tr>
		<tr>
		<td>Appeal Includes Reminder Schedule: <input type="checkbox" name="reminder" value="1" /></td>
		</tr>
		<tr>
		<td>Appeal Pledges Donor Privacy: <input type="checkbox" name="confidential" value="1" /></td>
		</tr>
		</tbody></table>
		<button type="button" onclick="processDonationForm()">Submit</button>
	</form>
</div>

<!-- tjs 130126 -->
		<!-- script type="text/javascript" src="js/jquery.js"></script -->

<!-- tjs 141118 -->
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
		<!-- script type="text/javascript" src="js/jquery-2.1.1.min.js"></script -->
<script src="https://cdn.firebase.com/js/client/1.1.3/firebase.js"></script>

   <script type="text/javascript" src="js/ui/ui.core.js"></script>

    <script type="text/javascript" src="js/ui/ui.accordion.js"></script>
	<script type="text/javascript" src="js/ui/ui.datepicker.js"></script>

    <script type="text/javascript" src="js/effects.core.js"></script>

    <script type="text/javascript" src="js/effects.highlight.js"></script>
     <script type="text/javascript" src="js/jquery.dataTables.js"></script>
<!-- tjs 141118 -->
   <script type="text/javascript" src="js/globals.js"></script>
   <script type="text/javascript" src="js/charity.js"></script>
	<script type="text/javascript" src="js/ui/ui.resizable.js"></script>
	<script type="text/javascript" src="js/ui/ui.dialog.js"></script>

    <script type="text/javascript">

	  //function to execute when doc ready

	  $(function() {

	  	   // qs();
		   var url;
		   
		   var charityDialogOpts = {
			title: "Charities",
			width: "600px",
			autoOpen: false
			}

	//create the charity dialog
		$("#charityDialog").dialog(charityDialogOpts);
	  	    
		   var donationDialogOpts = {
			title: "Donation",
			width: "600px",
			autoOpen: false
			}

	//create the donation dialog
		$("#donationDialog").dialog(donationDialogOpts);

			//turn specified element into an accordion

	    $("#navAccordion").accordion({

				header: ".heading",

				//event: "mouseover",

				autoHeight: false,

				alwaysOpen: false,

				//active:false,

				navigation: true  

			});

		//tjs 110511
		//setAuthenticated();
	var account = <?php echo $account; ?>;
	var authenticated = account > 0? 'true' : 'false';
	//tjs 120618 e.g. 0 if not logged in
	//alert("logs account " + account);
	// tjs 120618
			//refreshCharities(authenticated);
			// tjs 141118
			//refreshCharities(authenticated, 'false');
			refreshLoginAccountNumberAndCharities(authenticated, 'false');

	  });

	</script>

  </body>

</html>

