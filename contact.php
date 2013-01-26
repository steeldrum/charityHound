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
contact.php 

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

    <title>Charity Hound</title>

  </head>

  <body>

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

<h1>Contact</h1>

				<br/>

<p>

Charity Hound depends on ernest Collaborators and uses <span class="akey">Software Services (via Collogistics) to manage the
Logistics associated with efficient giving</span>.  To reach us please mail your request
for some of our <span class="akey">free services</span>.  If you have used several free services and have interest
in filling out a <span class="akey">sponsership application</span>, kindly email a request and
we will respond!  

<p>

</p>

<br/>

<br/>

<br/>

Thank you,

<br/>

Thomas J. Soucy, Collaborator Coordinator

</p>

<br/>

<br/>

<table>

<tr><td>Main email: <span class="mailme" title="site solicited contact">collaborators at collogistics dot com</span>

</td></tr>

</table>

<br/>

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
				
							// Replaces all the matching elements with a <a href="mailto:..> tag.		

	$('span.mailme').mailme();

							//tjs 110511
				//setAuthenticated();
	var account = <?php echo $account; ?>;
	var authenticated = account > 0? 'true' : 'false';

	  });

	</script>

  </body>

</html>

