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
mission.php 

tjs 110511

file version 1.00

release version 1.00
test checkin

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

  </head>

  <body>

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Our Mission</h1>

<p>
This site, hosted by Collogistics, adheres to their philosophy that a group of <span class="akey">collaborators
can manage formidable logistics</span>.  Specifically for this site our members are challenged with
the management of a marketing "blizzard" of donor solicitations.  Our members are
bewildered when trying to make contributions and they sincerely <span class="akey">wish to donate to
worthy charities without feeling that the hard-earned funds are NOT wasted</span>.
</p>

<p>
In order to fulfill this mission our collaborators have agreed to, in a way, <span class="akey">conduct a real
time research project</span>.  The collaborators receive donor solicitations and use this
application to track them.  Other collaborators do the same.  Later on data is aggregated
by Charity Hound into meaningful summary reports that, in effect, convey to the readers
<span class="akey">charities that appear to be less marketing oriented</span> (and therefore are likely to be
more efficient in their spending - hopfully plowing contributions into their cause).
</p>

<p>
Mother Teresa was once asked by an interviewer: "<span class="akey">What's the biggest problem in the world today?</span>"
Without hesitating she replied, "The biggest problem in the world today is that <span class="akey">we draw
the circle of our family too small. We need to draw it larger every day.</span>" 
</p>

<p>
Our mission, therefore, is to enable our members to draw larger circles that encompass
thoughtful and considerable giving with the knowledge that <span class="akey">what is given is mostly used
for the intended cause</span> (and not just used for further fundraisers)!
</p>

<br/>

<div class="iconControls">

<a href="javascript:newLocation('index', 'collogistics')"><img src="images/home.gif"></a>&nbsp;&nbsp;<button id="key"><img src="images/key.gif" /></button>

</div>

			</div>

			<div id="clear"></div>

		</div>

		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>

   <script type="text/javascript" src="js/ui/ui.core.js"></script>

    <script type="text/javascript" src="js/ui/ui.accordion.js"></script>

    <script type="text/javascript" src="js/effects.core.js"></script>

    <script type="text/javascript" src="js/effects.highlight.js"></script>

    <script type="text/javascript">

	  //function to execute when doc ready

	  $(function() {

	  	    

			//turn specified element into an accordion

	    $("#navAccordion").accordion({

				header: ".heading",

				//event: "mouseover",

				autoHeight: false,

				alwaysOpen: false,

				//tjs 110511
				//active: 1,

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

