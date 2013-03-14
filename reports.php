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
// tjs 130307
$aggregateProvider = AGGREGATE_DSN;
$aggregateDatabase = AGGREGATE_DB_NAME;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--------------------------------------
$Revision:: 168                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-12-21 11:41:14#$: Date of last commit
--------------------------------------->
<!--
charityhound/
reports.php 

tjs 110511

file version 1.00

release version 1.00

-->
<html lang="en">

  <head>

  <link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
  <!-- link rel="stylesheet" type="text/css" href="navAccordionTheme.css" -->
  <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
   <script type="text/javascript" src="js/argumenturl.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>

     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Collogistics</title>
		<script type="text/javascript">

//globals used for account management
	var loginAccountNumber = 0;
	var lastName = "collaborator";
	var firstName = "demo";

//tjs 110511
	//function processArgs() {
function processArgs(account) {
	
	var authenticated = account > 0? true : false;

	var elm;
	var dt = new Date();
	var my_year=dt.getFullYear();
	var start = $('#start').get(0);
	start.value = my_year;
	var end = $('#end').get(0);
	end.value = my_year;
	// tjs111024
	var current = $('#current').get(0);
	current.value = my_year;
	var prior = $('#prior').get(0);
	prior.value = my_year - 1;
	// tjs111028
	var from = $('#from').get(0);
	from.value = my_year;
	var to = $('#to').get(0);
	to.value = my_year;
	// tjs111115
	var fromlog = $('#fromlog').get(0);
	fromlog.value = my_year;
	var tolog = $('#tolog').get(0);
	tolog.value = my_year;
	// tjs120319
	var fromtag = $('#fromtag').get(0);
	fromtag.value = my_year;
	var totag = $('#totag').get(0);
	totag.value = my_year;
	
	//setAuthenticated();
	//alert("index.html processArgs authenticated " + authenticated);
	var elm = $('#donations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
			start.disabled="disabled";
			end.disabled="disabled";
				elm.disabled="disabled";
		}
	}
	// tjs 111024
	elm = $('#lapsedDonations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
			prior.disabled="disabled";
			current.disabled="disabled";
				elm.disabled="disabled";
		}
	}

	// tjs 111028
	elm = $('#remittedDonations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
			from.disabled="disabled";
			to.disabled="disabled";
				elm.disabled="disabled";
		}
	}

	// tjs 111221
	elm = $('#omittedDonations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
				elm.disabled="disabled";
		}
	}

	// tjs 111221
	elm = $('#solicitorDonations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
			fromlog.disabled="disabled";
			tolog.disabled="disabled";
				elm.disabled="disabled";
		}
	}

	// tjs 120319
	elm = $('#designatedDonations').get(0);
	if (elm) {
		if (authenticated == true) {
			//alert("index.html processArgs disable login, enable logout");
				elm.disabled="";	
		} else {
			//alert("index.html processArgs disable logout, enable login");
			fromtag.disabled="disabled";
			totag.disabled="disabled";
				elm.disabled="disabled";
		}
	}
	
}

		</script>

  </head>

  <body>

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Reports</h1>

<p> The report of donations lists charities in alphabetic order.  For each charity listed
you can see detail that includes the <span class="akey">amount contributed and the date of the contribution</span>.
The details are summarized showing the total count of solicitations and the total
amount of your contributions.  The report is most valuable provided that you <span class="akey">always
log each and every mailed solicitation</span> (often this means you choose to donate $0.00).
This logging effort helps identify non-profits that inundate you with solicitations all
year long!  At the <span class="akey">very end of the report you will observe a grand total</span> of your contributions.
</p>
<p>Before viewing the report decide on a range of years that you want to observe.
The default is the current year.  To select all possible years use a start year
such as '2000' and some future end year such as '2100'.  If preparing taxes you
would probably be interested in whatever the prior year was.  In that case simply
change the default END year to that value.  (By so doing the default START will
automatically be the same value as the end - so no need to change it unless you want to or 
it was already an earlier date).</p>
<p>
Start year: <input id="start" type="number" />&nbsp;&nbsp;End year: <input id="end" type="number" />&nbsp;&nbsp;Hide Solicitations Detail: <input id="hideSolicitations" type="checkbox" checked="checked" />
</p>
<p>
<button id="donations">View Donations</button>
</p>

<br/>

<p> The report of donations (above) is formed as a PDF.  This makes it easy to print out
and reference (such as for accounting or tax purposes).  A suite of reports (below) appear
on your screen and are not intended to be saved, emailed, etc.  These reports simply
are useful for helping you decide what charities, if any, merit your consideration.
</p>
<p>One handy report depicts charities that you have let lapse in turns of giving.  (Sometimes
you intentionally fail to sponsor charities every single year due to financial constraints,
or due to their constantly "hounding" you for money.  The latter group leaves you with
the impression that all the monies that you have contributed are wasted on solicitations for more money!).</p>
<p>It is, however, possible that you may have let sponsorship of a particular charity lapse
simply because you forgot about them (or they are NICE and don't send frequent reminders).  The
"Lapsed Contributions Report" displays a list of charities that you had formerly contributed to
(in some designated previous year), but you have not, so far at least, given to in the current year.</p>
<p>
Prior year: <input id="prior" type="number" />&nbsp;&nbsp;Current year: <input id="current" type="number" readonly="readonly" />
</p>
<p>
<button id="lapsedDonations">View Lapsed Donations</button>
</p>

<br/>

<p>Another handy report depicts charities that you have donated to.  It is similar to the
above PDF report but it displays the results on the screen and provides controls
for sort order (such as sorting on the rate column).</p>
<p>
From year: <input id="from" type="number" />&nbsp;&nbsp;To year: <input id="to" type="number" />
</p>
<p>
<button id="remittedDonations">View Remitted Donations</button>
</p>

<br/>

<p>Another handy report depicts charities that have solicitated you for donations but you have
omitted giving for some reason or another (perhaps justifibly so).  It is similar to the
above report. It displays the results on the screen and provides controls
for sort order (such as sorting on the rate column or number of solicitations column).</p>
<p>
<button id="omittedDonations">View Omitted Donations</button>
</p>

<br/>

<p>Another handy report depicts summary data (including the grand total of donations made in response to
the grand total of solicitations that have been logged).  It is similar to the
above report. It displays the results on the screen and provides controls
for sort order (such as sorting on the donations column or number of solicitations column).</p>
<p>
From year: <input id="fromlog" type="number" />&nbsp;&nbsp;To year: <input id="tolog" type="number" />
</p>
<p>
<button id="solicitorDonations">View Donations and Solicitations Grand Totals (also by Charity)</button>
</p>

<br/>

<p>Another handy report lists certain user-designated charities.  In the process of logging
solicitations some forms are readily identified for designation tags such as:
"they pledge complete confidentiality of received donations". (A pledge like this
implies they will NOT simply sell your name to some other fund-raiser).  Another
favorable designation would be tagged to charities that provide an area on the donor form
for the donor to specify when it might be best to be reminded for any subsequent contributions
(e.g. monthly, quarterly, yearly, etc.).  Charities with favorable designations would appear
to have the edge over others.  Note: this report also lists charities that have been tagged
with unfavorable designations.</p>
<p>
From year: <input id="fromtag" type="number" />&nbsp;&nbsp;To year: <input id="totag" type="number" />
</p>
<p>
<button id="designatedDonations">View Designated Charities (tagged as favorable or otherwise)</button>
</p>
<p>
The following reports use aggregate data (based on all users' information, not just yours):
<button id="designatedBlanks">Charities Who Send "Blank" Solicitations (no indication of sender)</button>
<button id="designatedCurrencies" disabled="disabled">Charities Who Send "Currency Bated" Solicitations (money in envelope)</button>
<button id="designatedConfidentials" disabled="disabled">Charities Who Pledge Privacy (won't sell your identity to others)</button>
<button id="designatedReminders" disabled="disabled">Charities Who Provide A Schedule (won't hound you with repeated solicitations)</button>
</p>

<br/>

<br/>

<div class="iconControls">

<a href="javascript:newLocation('index', 'logistics')"><img src="images/home.gif"></a>&nbsp;&nbsp;<button id="key"><img src="images/key.gif" /></button>

</div>

			</div>

			<div id="clear"></div>

		</div>

<!-- tjs 130126 -->
		<!-- script type="text/javascript" src="js/jquery.js"></script -->
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>

   <script type="text/javascript" src="js/ui/ui.core.js"></script>

    <script type="text/javascript" src="js/ui/ui.accordion.js"></script>

    <script type="text/javascript" src="js/effects.core.js"></script>

    <script type="text/javascript" src="js/effects.highlight.js"></script>
    <script language="javascript" type="text/javascript" src="js/mailme.jquery.js"></script>

    <script type="text/javascript">

	  //function to execute when doc ready

	  $(function() {

	  	    

			//turn specified element into an accordion

	    $("#navAccordion").accordion({

				header: ".heading",

				//event: "mouseover",

				autoHeight: false,

				alwaysOpen: false,

				//active:false,

				navigation: true  

			});

			

			//for highlights of key take-away pointers

		$("#key").click(function() {

					//highlight specified element

					//$('.akey').effect("highlight");

					$('.akey').effect("highlight", {}, 20000);

				});

		$("#donations").click(function() {
			//viewProspects(loginAccountNumber);
				var elm = $('#start').get(0);
				var start = elm.value;
				elm = $('#end').get(0);
				var end = elm.value;
				//tjs110304
				elm = $('#hideSolicitations').get(0);
				var checked = elm.checked;
				//alert("reports view donations checked " + checked + " value " + elm.value);
				//var url = 'donationsReport2FPDF.php?account=0&start=' + start + '&end=' + end;
				var url = 'donationsReport2FPDF.php?account=0&start=' + start + '&end=' + end + '&hideSolicitations=' + checked;
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

		    //tjs 111028
		    $("#from").bind('change', function(event) {
		    	var from = $('#from').val();
				var to = $('#to').val();
		        if (to < from) {
		        	$('#to').val(from);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

		    $("#to").bind('change', function(event) {
		    	var from = $('#from').val();
				var to = $('#to').val();
		        if (to < from) {
		        	$('#from').val(to);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });
		    
		    //tjs 111115
		    $("#fromlog").bind('change', function(event) {
		    	var from = $('#fromlog').val();
				var to = $('#tolog').val();
		        if (to < from) {
		        	$('#tolog').val(from);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

		    $("#tolog").bind('change', function(event) {
		    	var from = $('#fromlog').val();
				var to = $('#tolog').val();
		        if (to < from) {
		        	$('#fromlog').val(to);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

		    //tjs 120319
		    $("#fromtag").bind('change', function(event) {
		    	var from = $('#fromtag').val();
				var to = $('#totag').val();
		        if (to < from) {
		        	$('#totag').val(from);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });

		    $("#totag").bind('change', function(event) {
		    	var from = $('#fromtag').val();
				var to = $('#totag').val();
		        if (to < from) {
		        	$('#fromtag').val(to);
		        	//start.value = end;
					//alert("reports start now " + start.value);
		        }
		    });
		    
				//tjs 110511
        var account = <?php echo $account; ?>;

		// tjs 111021
		$("#lapsedDonations").click(function() {
	var elm = $('#prior').get(0);
	var prior = elm.value;
	elm = $('#current').get(0);
	var current = elm.value;
	//alert("reports view donations checked " + checked + " value " + elm.value);
	var url = 'view_lapsed_charities.php?account=' + account + '&prior=' + prior + '&current=' + current;
	//alert("reports view lapsed donations url " + url);
	window.location.href = url;

});	

		// tjs 111028
		$("#remittedDonations").click(function() {
	var elm = $('#from').get(0);
	var from = elm.value;
	elm = $('#to').get(0);
	var to = elm.value;
	//alert("reports view donations checked " + checked + " value " + elm.value);
	var url = 'view_remitted_charities.php?account=' + account + '&from=' + from + '&to=' + to;
	//alert("reports view remitted donations url " + url);
	window.location.href = url;
		});
		
	// tjs 111102
	$("#omittedDonations").click(function() {
//alert("reports view donations checked " + checked + " value " + elm.value);
var url = 'view_omitted_charities.php?account=' + account + '&from=2000&to=2100';
//alert("reports view remitted donations url " + url);
window.location.href = url;

});	
		
	// tjs 111115
	$("#solicitorDonations").click(function() {
var elm = $('#fromlog').get(0);
var from = elm.value;
elm = $('#tolog').get(0);
var to = elm.value;
//alert("reports view donations checked " + checked + " value " + elm.value);
var url = 'view_solicitor_charities.php?account=' + account + '&from=' + from + '&to=' + to;
//alert("reports view solicitor donations url " + url);
window.location.href = url;
	});

	// tjs 120319
	$("#designatedDonations").click(function() {
var elm = $('#fromtag').get(0);
var from = elm.value;
elm = $('#totag').get(0);
var to = elm.value;
//alert("reports view donations checked " + checked + " value " + elm.value);
var url = 'view_designated_charities.php?account=' + account + '&from=' + from + '&to=' + to;
//alert("reports view solicitor donations url " + url);
window.location.href = url;
	});

	// tjs 130307
	var aggregateProvider = <?php echo json_encode($aggregateProvider); ?>;
	var	aggregateDatabase = <?php echo json_encode($aggregateDatabase); ?>;
		//alert("account " + account + " aggregateProvider done");		
		//alert("aggregateProvider " + aggregateProvider + " aggregateDatabase " + aggregateDatabase);
		// hack for test - comment out!
		//aggregateDatabase = 'collogistics';
	
	$("#designatedBlanks").click(function() {
		//alert("reports view designatedBlanks...");		
var url = 'view_designated_aggregates.php?provider=' + aggregateProvider + '&database=' + aggregateDatabase + '&aggregateList=blankScoreList';
//alert("reports view designatedBlanks url " + url);
window.location.href = url;
	});
	$("#designatedCurrencies").click(function() {
		var url = 'view_designated_aggregates.php?provider=' + aggregateProvider + '&database=' + aggregateDatabase + '&aggregateList=currencyBatedList';
		//alert("reports view solicitor donations url " + url);
		window.location.href = url;
			});
	$("#designatedConfidentials").click(function() {
		var url = 'view_designated_aggregates.php?provider=' + aggregateProvider + '&database=' + aggregateDatabase + '&aggregateList=confidentialScoreList';
		//alert("reports view solicitor donations url " + url);
		window.location.href = url;
			});
	$("#designatedReminders").click(function() {
		var url = 'view_designated_aggregates.php?provider=' + aggregateProvider + '&database=' + aggregateDatabase + '&aggregateList=scheduleScoreList';
		//alert("reports view solicitor donations url " + url);
		window.location.href = url;
			});
	
	//tjs 110511
        //alert("index account " + account);
        //e.g. account 1
        
		processArgs(account);
				//processArgs();


	  });

	</script>

  </body>

</html>

