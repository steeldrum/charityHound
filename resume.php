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
resume.php 

tjs 110511

file version 1.00

release version 1.00

-->
<html lang="en">

  <head>

  <link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
  <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
  <link rel="stylesheet" type="text/css" href="css/index.css">
   <script type="text/javascript" src="js/argumenturl.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>

     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Collogistics</title>
  </head>

  <body>

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Knowledge Base</h1>

<p>
Charity Hound collaborators who are registered to use this <span class="akey">Charitable Contribution
Application</span> will find it quite convenient for <span class="akey">keeping track of contributions</span> and
yearly summary <span class="akey">reports geared for tax preparations</span>.
</p>
<p>
Like many applications there exists certain common sense rules that, if followed,
should lead towards a satisfying experience and, equally important, complete and accurate
results.
</p>
<p>
When using the application for the first time, if one browses to the Solicitations and
Donations page one will see that a <span class="akey">list of for-profits or non-profits already exists!</span>  This list
is simply a step to save the typical user from typing in names such as "American Red Cross".
It is critically important to <span class="akey">note that any charity so-listed is NOT in any way
"condoned" or "certified" by Charity Hound as to being a "worthy" recipient</span> of your
contributions!  Also note that whether a charity is a 'for' or 'non' profit could change at any time and
accuracy regarding that classification should depend on Internet browsing to the respective charity's site.
</p>
<p>
Some new users may find this list of about 500 organizations too much. The same users
will probably NEVER contribute to many in the list.  For those users we suggest a
<span class="akey">"one time" cleanup of the list</span>.  By using the menu choice that supports browsing
through the entire list of organizations the cleanup consists of merely making
certain selected ones "inactive".  Once inactive the name should never appear in the
Solicitations and/or Donations listing again (unless re-activated).  We suggest
doing this one-shot cleanup before recording Solicitations and/or Donations.
</p>
<p>
Having cleaned up the database the user is expected to use one (and only one)
page on a regular basis.  That page is called <span class="akey">"Solicitations Donations"</span>.  Basically
its purpose is to aid the user to keep track of all solicitations (mailed requests
for contributions).  As a side benefit the user is able to <span class="akey">track donations in
response to the solicitations</span>.
</p>
<p>
Because this page is used so often it has an embedded and <span class="akey">optimized "search engine"</span> geared
to quickly locate any charity by name.  For example simply typing "red" would immediately
show the "American Red Cross" organization (as well as any others with the sequence "red").  Once the
charity is found (via this optimized search) the user simply uses the '+' (meaning
'add') as an <span class="akey">Action</span>.  That records the event:  "I was mailed a donor request".
Optionally (before using the '+') an amount could be entered.  If so then the
recorded event is: "I was mailed a donor request and I contributed this specified amount".
</p>
<p>
To summarize the <span class="akey">typical user workflow steps</span>:
<ul>
<li>Use Browse Charities page to <span class="akey">cleanup list by inactivating some</span>. A "one shot" task. Also use Internet to verify if organization is a 'for' or 'non' profit.</li>
<li>Receive a <span class="akey">mailed donor solicitation enevlope</span>. </li>
<li>Use the Solicitations Donations page and <span class="akey">record solicitation</span>.  On occasion specify contributed amount. </li>
<li>In a cardboard box <span class="akey">replace any former envelope<span> (from a charity) with the latest one. </li>
<li>Every so often (perhaps once or twice a year) identify worthy charities that you had tracked with a zero amount and <span class="akey">make contributions</span> (updating your tracking). </li>
<li>As an aid for the prior step, use the Browse Charities to access web-pages, etc.  Also make sure you <span class="akey">review Charity Hound Aggregate Reports</span>.</li>
<li>When it comes to tax preparation time, <span class="akey">use the Contributions Report</span> page to summarize a year's contributions.</li>
<li>Charity Hound (not you) will periodically aggregate data from every registered user and promulgate results.  Over time <span class="akey">it should become clear which charities are more disciplined with regarding the "blizzard" of wasteful donor solicitations!</span> </li>
</ul>
</p>
<p>
Details about the Solicitations/Donations page:<br/><br/>
This page contains two "lists".  The top list, Charities, lists all active charities.  Some helpful information, such as the number of "solicitations" you have already logged appear.  A "Rate" column tries to derive how many per twelve months.  It should take several months of logging before the rate becomes a reliable metric.  The most common operation is to use the '+' control.  It does two things: (1) it logs your donation amount (typically zero).  This formally logs the solicitation.  If you fill in the amount then not only is the solicitation logged but your donation is also logged.  (2) By using the '+' as a side effect the lower panel ("Donations") gets refreshed.  This shows a detailed list of your solicitations that you had logged in the past.  Entires with non-zero amounts are actual donations.
</p>
<p>
There is a possibility that you are undecided about giving anything.  Perhaps you plan to research the cause further.  Indeed this is quite typical.  In such cases log the solicitation with a zero amount anyway.  Later on the same charity can be found (search in the Charities panel).  Then use the magnifying glass (icon) to immediately display the lower list of "Donations".  Perhaps the most recent donation shows a "zero" amount.  But you now plan to write a check.  In the lower list simply click the pencil icon (edits that list item).  A form is displayed.  Change the amount from zero to whatever you plan to donate.  Submit the form and you are done!
</p>
<p>
Every logged solicitation can be edited.  The most typical need is when (and if) you decide to contribute a few days after logging the solicitation.  To help identify charity organizations that are willing to contain fund-raising costs the same form has a few check boxes.  When appropriate check any boxes that apply.  This helps our reporting system sort through and identify cost conscious charities.
</p>
<br/>

<div class="iconControls">

<a href="javascript:newLocation('index', 'collogistics')"><img src="images/home.gif"></a>&nbsp;&nbsp;<button id="key"><img src="images/key.gif" /></button>

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
	<script type="text/javascript" src="js/ui/ui.resizable.js"></script>
	<script type="text/javascript" src="js/ui/ui.dialog.js"></script>

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
		//tjs 110511
		//setAuthenticated();
		var account = <?php echo $account; ?>;
		var authenticated = account > 0? 'true' : 'false';

	  });

	</script>

  </body>

</html>

