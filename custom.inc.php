<?php
/***************************************
$Revision:: 61                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-03-04 15:18:58#$: Date of last commit
***************************************/
/*
Collaborators/
custom.inc.php
tjs 110303
*/

require_once( "config.php" );
require_once( "Member.class.php" );

function displayPageHeader( $pageTitle, $membersArea = false ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <!-- [if lt IE 9]>
  <script src="js/html5.js"></script>
  <![endif]-->
    <title><?php echo $pageTitle?></title>
    <link rel="stylesheet" type="text/css" href="<?php if ( $membersArea ) echo "../" ?>css/common.css" />
    <style type="text/css">
    #container { border:1px solid #85d791; width:1101px; margin: 20px; padding: 20px; }

      th { text-align: left; background-color: #bbb; }
      th, td { padding: 0.4em; }
      tr.alt td { background: #ddd; }
      .error { background: #d33; color: white; padding: 0.2em; }
    </style>

  </head>
  <body>
  <header>
<hgroup>
<h1>Collogistics</h1>
<hr/>  Collogistics - Collaborative Management of Logistics <hr/>
</hgroup>
</header>
<div id="container">

    <h1><?php echo $pageTitle?></h1>
<?php
}

function displayPageFooter() {
?>
</div>
<footer>
<h2>Internet Incubation of Innovative Ideas </h2>
</footer>
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
   <script type="text/javascript">

	  //function to execute when doc ready

	  $(function() {

	var dt = new Date();
	var my_year=dt.getFullYear();
	var start = $('#start').get(0);
	start.value = my_year;
	var end = $('#end').get(0);
	end.value = my_year;

		$("#donations").click(function() {
			//viewProspects(loginAccountNumber);
				var elm = $('#start').get(0);
				var start = elm.value;
				elm = $('#end').get(0);
				var end = elm.value;
				//tjs110304
				elm = $('#hideDonations').get(0);
				var checked = elm.checked;
				//alert("reports view donations checked " + checked + " value " + elm.value);

				//var url = 'aggregateDonationsReport2FPDF.php?account=0&start=' + start + '&end=' + end;
				var url = 'aggregateDonationsReport2FPDF.php?account=0&start=' + start + '&end=' + end + '&hideDonations=' + checked;
				//alert("reports view donations url " + url);
				window.location.href = url;
		    });	
		    
		    //tjs 101208
		    $("#start").bind('change', function(event) {
		    	var start = $('#start').val();
				var end = $('#end').val();
		        if (end < start) {
		        	$('#end').val(start);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

		    $("#end").bind('change', function(event) {
		    	var start = $('#start').val();
				var end = $('#end').val();
		        if (end < start) {
		        	$('#start').val(end);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

	  });

	</script>
  </body>
</html>
<?php
}

function validateField( $fieldName, $missingFields ) {
  if ( in_array( $fieldName, $missingFields ) ) {
    echo ' class="error"';
  }
}

function setChecked( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj->getValue( $fieldName ) == $fieldValue ) {
    echo ' checked="checked"';
  }
}

function setSelected( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj->getValue( $fieldName ) == $fieldValue ) {
    echo ' selected="selected"';
  }
}

function checkLogin() {
  session_start();
  if ( !$_SESSION["member"] or !$_SESSION["member"] = Member::getMember( $_SESSION["member"]->getValue( "id" ) ) ) {
    $_SESSION["member"] = "";
    header( "Location: login.php" );
    exit;
  } else {
    $logEntry = new LogEntry( array (
      "memberId" => $_SESSION["member"]->getValue( "id" ),
      "pageUrl" => basename( $_SERVER["PHP_SELF"] )
    ) );
    $logEntry->record();
  }
}


?>
