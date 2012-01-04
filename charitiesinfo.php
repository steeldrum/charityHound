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
charitiesinfo.php 

tjs 110511

file version 1.00

release version 1.00

-->
<html lang="en">

  <head>

  <link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
  <!-- link rel="stylesheet" type="text/css" href="navAccordionTheme.css" -->
  <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
  <link rel="stylesheet" type="text/css" href="css/demo_table.css">
   <script type="text/javascript" src="js/argumenturl.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>

     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Collogistics</title>
    		<script type="text/javascript">
//var myRequest;
//var url;

//var qsParm = new Array();
	//var loginAccountNumber = -1;
	var loginAccountNumber = 0;
</script>
  </head>

  <body class="example_alt_pagination">

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Chariites</h1>

<div id="charityDetailList">
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
<div id="charityDetailDialog">
	<div id="charityDetailDialogMsg" style="display: none;">
	Really Delete? <br/>
	</div>
	<form name="charityDetailForm">
		<input type="hidden" name="id" />
		<input type="hidden" name="remove" />
		<table><tbody>
		<tr>
		<td>name: <input type="text" name="name" /></td>
		</tr>
		<tr>
		<td>Shortname: <input type="text" name="shortname" /></td>
		</tr>
		<tr>
		<td>Duns: <input type="text" name="dunns" /></td>
		</tr>
		<tr>
		<td>For Profit?: <input type="checkbox" name="isForProfit" checked="" /></td>
		</tr>
		<tr>
		<td>Inactive?: <input type="checkbox" name="isInactive" checked="" /></td>
		</tr>
		<tr>
		<td>URL: <input type="text" name="url" /></td>
		</tr>
		<tr>
		<td>Description: <input type="text" name="description" /></td>
		</tr>
		<tr>
		<td>Number of Stars: <input type="text" name="numStars" /></td>
		</tr>
		</tbody></table>
		<button type="button" onclick="processCharityDetailForm()">Submit</button>
	</form>
</div>

		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>

   <script type="text/javascript" src="js/ui/ui.core.js"></script>

    <script type="text/javascript" src="js/ui/ui.accordion.js"></script>

    <script type="text/javascript" src="js/effects.core.js"></script>

    <script type="text/javascript" src="js/effects.highlight.js"></script>
     <script type="text/javascript" src="js/jquery.dataTables.js"></script>
   <script type="text/javascript" src="js/charity.js"></script>
	<script type="text/javascript" src="ui.resizable.js"></script>
	<script type="text/javascript" src="ui.dialog.js"></script>

    <script type="text/javascript">

	  //function to execute when doc ready

	  $(function() {

	  	  		   var charityDetailDialogOpts = {
			title: "Charities",
			width: "600px",
			autoOpen: false
			}

	//create the charity dialog
		$("#charityDetailDialog").dialog(charityDetailDialogOpts);
  

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
			//refreshCharitiesDetail(authenticated);
			refreshCharities(authenticated, 'true');

	  });

	</script>

  </body>

</html>

