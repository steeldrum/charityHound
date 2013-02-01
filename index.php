<?php //session_start();
//require_once( "Member.class.php" );
require_once( "common.inc.php" );
session_start();
$account = 0;
// tjs 130126
//echo "index inited account: ".$account;
if ($account == 0 && isset($_SESSION['member'])) {
// tjs 130126
//echo "index member in session...";
	$member = $_SESSION['member'];
// tjs 130126
//echo "index member id: ".$member->getValue( "id" );
	if ($member != null) {
// tjs 130126
//echo "index member id: ".$member->getValue( "id" );
		$account = $member->getValue( "id" );
		// tjs 130126
		//echo "index account: ".$account;
	}
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--------------------------------------
$Revision:: 158                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-12-13 16:01:56#$: Date of last commit
--------------------------------------->
<!--
charityhound main entry point
charityhound/
index.php 

tjs 110511

-->

<html lang="en">

  <head>
<link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	

   <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
  <link rel="stylesheet" type="text/css" href="css/index.css">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Collaborative rating of CHARITIES to help navigate through donation choices and act as a watch dog for inefficient ones." />

    <title>Charity Hound</title>
    <!-- tjs 130126 -->
		<!-- script type="text/javascript" src="js/jquery-1.3.2.js"></script -->
		<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
		<!-- script type="text/javascript" src="js/jquery.js"></script -->

    <script type="text/javascript" src="js/ui/ui.core.js"></script>

    <script type="text/javascript" src="js/ui/ui.accordion.js"></script>

    <script type="text/javascript" src="js/effects.core.js"></script>

    <script type="text/javascript" src="js/effects.highlight.js"></script>
	<script type="text/javascript" src="js/ui/ui.dialog.js"></script>
   <script type="text/javascript" src="js/index.js"></script>
   <script type="text/javascript" src="js/argumenturl.js"></script>
   <script type="text/javascript" src="js/menu.js"></script>
		<script type="text/javascript">

//globals used for account management
	var loginAccountNumber = 0;
	var lastName = "collaborator";
	var firstName = "demo";

function processArgs(account) {
	//setAuthenticated();
	var authenticated = account > 0? true : false;
	//alert("index.html processArgs authenticated " + authenticated);
	//alert("index.html processArgs authenticated " + authenticated + " account " + account);
	//e.g. true 1
	var elm;
	if (authenticated == true) {
		//alert("index.html processArgs disable login, enable logout");
		elm = $('#login').get(0);
		if (elm) {
			elm.disabled="disabled";
		}
		elm = $('#logout').get(0);
		if (elm) {
			elm.disabled="";
		}
	} else {
		//alert("index.html processArgs disable logout, enable login");
		elm = $('#logout').get(0);
		if (elm) {
			elm.disabled="disabled";
		}
		elm = $('#login').get(0);
		if (elm) {
			elm.disabled="";
		}
	}
	enableOrDisableScheduledDisplayAds();
	enableOrDisableSiteAdmin();
}

function doLogin() {
	window.location.href = "login.php";
}

function doLogout() {
	window.location.href = "logout.php";
}

function doSiteAdmin() {
	window.location.href = "admin.php";
}

function doScheduleDisplayAd() {
	window.location.href = "adManager.php";
}

// tjs 111027
    function doRegister() {
    	//alert("index doRegister");
    		$("#charityHoundRegisterDialog").dialog("open");
    	}

    function processRegisterForm(token, username, password1, password2, emailAddress, firstName, lastName, gender, passwordMnemonicQuestion, passwordMnemonicAnswer)
{
    	//alert("charityhound  processRegisterForm token " + token + " username " + username + " emailAddress " + emailAddress + " firstName " + firstName + " lastName " + lastName + " gender " + gender + " password1 " + password1);
    	$.ajax({  
    	        type: "POST",  
    	      url: "charityhoundRegister.php",  
    	      data: { "token": token,
    	    	  "username": username,
    	    	  "password1": password1,
    	    	  "password2": password2,
    	    	  "emailAddress": emailAddress,
    	    	  "firstName": firstName,
    	    	  "lastName": lastName,
    	    	  "gender": gender,
    	    	  "passwordMnemonicQuestion": passwordMnemonicQuestion,
    	    	  "passwordMnemonicAnswer": passwordMnemonicAnswer
    	    	  },  
    	      success: function(msg) {
    	          //alert("charityhound processRegisterForm success msg " + msg + " len " + msg.length);
    	          var tempMsg = msg;
    	          var success = false;
    	          var duplicateUserNameError = false;
    	          var duplicateEMailError = false;
    	          var tokenMisMatchError = false;

    	    	  // e.g.   $registerInfo = '["registerInfo", {"success":"'.$success.'","missingFieldsError":"'.$missingFieldsError.'","passwordError":"'.$passwordError.'","duplicateUserNameError":"'.$duplicateUserNameError.'","duplicateEMailError":"'.$duplicateEMailError.'","$registrationTokenMisMatchError":"'.$registrationTokenMisMatchError.'"}]';        
    	    		JSON.parse(tempMsg, function (key, value) {
    	    			//alert("charityhound processRegisterForm key " + key + " value " + value);
    	    			if (key =='success') {
    	    				success = ('ok' == value);
    	    			} else if (key =='duplicateUserNameError') {
    	    				duplicateUserNameError = ('nok' == value);
    	    			} else if (key =='duplicateEMailError') {
    	    				duplicateEMailError = ('nok' == value);
    	    			} else if (key =='registrationTokenMisMatchError') {
    	    				tokenMisMatchError = ('nok' == value);
    	    			}
    	    			});
    				//alert("charityhound processRegisterForm loginInfo.id " + loginInfo.id + " loginInfo.userName " + loginInfo.userName + " loginInfo.firstName " + loginInfo.firstName + " loginInfo.lastName " + loginInfo.lastName);
    	    	  if (success) {
     	    		  //alert("charityhound processRegisterForm success closing dialog...");
    	    		  $("#charityHoundRegisterDialog").dialog("close");
    	    	  } else {
    	          //alert("charityhound processRegisterForm duplicateUserNameError " + duplicateUserNameError + " duplicateEMailError " + duplicateEMailError + " tokenMisMatchError " + tokenMisMatchError);
       	    	  if (duplicateUserNameError) {
        	    		    $("label#submit_error").text('The username already exists!').show();
        	    		    $("input#username").focus();
        	    	  } else if (duplicateEMailError) {
      	    		  $("label#submit_error").text('The email address already exists!').show();
      	    		$("input#emailAddress").focus();
        	    	  } else if (tokenMisMatchError) {
      	    		  $("label#submit_error").text('The invitation token is incorrect!').show();
      	    		$("input#token").focus();
       	    	  	  }
    	    	  }
    	      }  
    	    });  
    		//alert("charityhound  processRegisterForm called ajax...");
    	    return false;  
    	} // end processRegisterForm

		</script>
  </head>

  <body>

  <!--[if !IE]> <!-->

  <object data="collogistics.svg" type="image/svg+xml"

        width="400" height="100">

    <embed src="collogistics.svg" type="image/svg+xml"

            width="400" height="100" />

</object>

<!--<![endif]-->

<h1>Charity Hound</h1>

<hr/>Where Collaborators Hound Charities Making Them More Efficient<hr/>

<div id="container">
<?php
 require 'charityhoundnavigator.php';
?>

	<div id="contentCol">

		<h1>Collaborative Management of Charitable Contributions</h1>

  <table>

  <tr>

  <td>
  <p>Hosted by <a href="http://www.collogistics.com">Collogistics</a>
  we help <span class="akey">registered collaborators manage their charitable contributions</span>.  Collaborators
  are asked to record all donor solicitations along with the amount that they donated.
  By so doing collaborators are able to produce a report after a calendar year end that <span class="akey">helps with
  tax computations</span> for those who deduct charitable contributions (typically on 1040 Schedule A).
  </p>
  <p>
  As an additional benefit, participating collaborators are periodically emailed
  a <span class="akey">summary profile report</span> (on an aggregate basis) of both contributors
  and gift recipients.  No individual data is ever divulged. This means all users'
  private donations (along with the recipients) remain private.  However these aggregate-based
  reports are valuable tools.  They aid with measuring how one's giving compares to the
  overall database of all givers.  More important a profile of recipients (for-profits or non-profits)
  emerges based upon aggregate users' experiences.
  </p>
  <p>For example <span class="akey">some charities' fundraisers are
  prone to repeatedly inundate donors with mailed solicitations</span>.  The impression that
  they leave is that monies donated are largely used simply to market more donations!  We feel
  strongly that these solicitors should be exposed for their callous efforts in
  badgering donors.  The aggregate <span class="akey">reports show the most egregious charity fundraisers</span>.  Over time
  our members would (we hope) typically avoid directing hard-earned funds towards these
  abusers.  One hope is that by this negative re-enforcement  the charities will
  eventually "get the message" and <span class="akey">tone down the frequency of donor solicitations</span>.
  </p>
<p>
Further rationale and operational details are documented in our Knowledge Base.  However
the day-to-day operations are three (observe menu panel choices):
<ul>
<li>Record every donor solicitation, while at the same time possibly record your contribution. </li>
<li>Whenever desired, obtain your summary report as a PDF file (useful for tax preparation).</li>
<li>Periodically browse through your database of for-profit or non-profit organizations and make adjustments. </li>
</ul>
</p>
</td><td>
<p>Use 'solicitations/donations' for the most common operation (first step).  The 'rate' field tries to
show the rate of solicitations per annum.  For example '12' means monthly.  The lower, the better (meaning
the money you donate isn't being wasted on reminders being sent).  Charity names in red mean you just gave
(and have received no reminders since then).</p>
<p>
Of the three day-to-day operations, by far, the most commonly used is the first one.  For some folks
hardly a day will pass without receiving an appeal from some organization.  Our hope is that
by enabling our fellow collaborators an <span class="akey">easy way to "log"</span> these obstrusive appeals, eventually
(via negative publicity regarding their marketing agressiveness) the <span class="akey">frequency of wasted
mailed reminders will drop drastically</span>.  For our collaborators' experience we provide a special
mobile (e.g. smart phone or tablet) "<span class="akey">web app</span>" that handles the most frequent tasks.
</p>
<p>
To properly install this mobile app:
<ul>
<li>On mobile device use a browser such as Safari and visit this same web page.</li>
<li>Then click here: <button id="iPhoneDonationLogApp">Download Mobile App</button>&nbsp;&nbsp;<a href="CharityHoundWebApp.pdf">how does the Web App Work?</a>
</li>
<li>Finally use the browser action control <img src="images/appleActionIcon23x15.png" /> to add the web app to the device's home screen. 
</li>
</ul>
</p>
<p>
Finally, we provide all collaborators an opportunity to help our cause!  Understand that
<span class="akey">maintaining our web site and storing collaborators' data is a significant expense</span>.  Two
ways that help defer our costs are offered.
<ul>
<li>Since collaborators use our software (and/or smart phone apps) as well as storage of their data 
it is requested that they <span class="akey">volunteer skills to mature this site (still in beta).<span></li>
<li>Contact us and <span class="akey">ask about becomming a "sponsor"</span>. Sponsors will benefit by means of increased visibility
via our web site (and/or smart phone "apps").</li>
</ul>
</p>
<p>
Sponsors are solicited for scheduling the periodic display of information
about themselves!  This enables collaborators who use the system an <span class="akey">opportunity to know
who you are and find out more about you</span>.  As a sponsor you will be provided guidelines
to explain how this can be done.  As explained in the guidelines the button that follows
is enabled only for sponsors.
</p>
<p>
<button id="scheduleDisplayAd" onclick="doScheduleDisplayAd();" disabled="disabled">Upload My Sponsor Information</button>

</p>

</td>

  </tr>

<tr><td></td><td></td></tr>

<tr><td><h1>Tired of Charities Hounding You For Donations?</h1></td><td><h1>Join Us.  We Sniff Out Ones That Care About What You Donate!</h1></td></tr>

<tr><td><img src="images/charityBagSmall.jpg" /></td><td><img src="images/ZevaBlueRocksSmall.jpg" /></td></tr>

  </table>
<button id="login" onclick="doLogin();">Login</button>&nbsp;&nbsp;<button id="logout" onclick="doLogout();">Logout</button>&nbsp;&nbsp;<button id="register" onclick="doRegister();">Register</button>&nbsp;&nbsp;<button id="admin" disabled="disabled" onclick="doSiteAdmin();">Site Admin</button>

  <br/>

  <div class="iconControls">

  <p>Hosted for incubation by: <a href="http://www.collogistics.com"><img src="images/favicon.ico"></a>&nbsp;&nbsp;Use <button id="key"><img src="images/key.gif" /></button> to highlight key "take-away" pointers.</p>

  </div>

  </div>
			<div id="clear"></div>

		</div>

   <div id="charityHoundRegisterDialog" title="Dialog Title">
			<hr/>	
			<div id="charityHoundRegisterContents">
			<p style="color: red;">To register you must be invited.  Contact us to acquire the Invitation Token.</p>
      <fieldset>
      
        <label for="token">Assigned Invitation Token *</label>
        <input type="text" placeholder="token" name="token" id="token" size="20" value="" class="text-input" required />
        <label class="error" for="token" id="token_error">This field is required. Request token by email.</label>  
		<br/>
        <label for="username">Choose a username *</label>
        <input type="text" placeholder="username" name="username" id="username" size="25" value="" class="text-input" required />
        <label class="error" for="username" id="username_error">This field is required.</label>  
		<br/>
        <label for="password1">Choose a password *</label>
        <input type="password" name="password1" id="password1" value="" required />
        <label class="error" for="password1" id="password1_error">This field is required. (Minimum length is 8 characters)</label>  
		<br/>
        <label for="password2">Retype password *</label>
        <input type="password" name="password2" id="password2" value="" required />
        <label class="error" for="password2" id="password2_error">This field is required. (Minimum length is 8 characters)</label>  
		<br/>
        <label for="passwordMnemonicQuestion">Specify account access security question:</label>
        <input type="text" name="passwordMnemonicQuestion" id="passwordMnemonicQuestion"  size="45" value="" list="popularQuestions" />
		<datalist id="popularQuestions">
			<option label="What is you mother's maiden name?" value="What is you mother's maiden name?" />
			<option label="What was your first grade school's name?" value="What was your first grade school's name?" />
			<option label="What was your first pet's name?" value="What was your first pet's name?" />
		</datalist>
		<br/>
        <label for="passwordMnemonicAnswer">Specify answer to account access question:</label>
        <input type="text" name="passwordMnemonicAnswer" id="passwordMnemonicAnswer"  size="45" value="" />
		<br/>
        <label for="emailAddress">Email address *</label>
        <input type="email" placeholder="person@server.com" name="emailAddress" id="emailAddress"  size="25" value="" required />
        <label class="error" for="emailAddress" id="emailAddress_error">This field is required. Use person@domain.com (.net, etc. form)</label>  
		<br/>
        <label for="firstName">First name *</label>
        <input type="text" placeholder="first name" name="firstName" id="firstName" value="" required />
        <label class="error" for="firstName" id="firstName_error">This field is required.</label>  
		<br/>
        <label for="lastName">Last name *</label>
        <input type="text" placeholder="last name" name="lastName" id="lastName" value="" required />
        <label class="error" for="lastName" id="lastName_error">This field is required.</label>  
		<br/>
        <label>Your gender: *</label>
        <label for="genderMale">Male</label>
        <input type="radio" name="gender" class="gender" id="genderMale" value="m" />
        <label for="genderFemale">Female</label>
        <input type="radio" name="gender" class="gender" id="genderFemale" value="f" />
        <label class="error" for="gender" id="gender_error">This field is required.</label>  
        <br />  
        <input type="button" name="submit" class="button" id="submit_btn" value="Submit" />  
        <label class="error" for="submit" id="submit_error">The username, email and/or password already exists!</label>  
      </fieldset>  
			</div>
		</div>
		<!-- #charityHoundRegisterDialog -->


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

					//$('.akey').effect("highlight", {}, 20000);
					$('.akey').effect("highlight", {}, 30000);

				});

		 
        $("#iPhoneDonationLogApp").click(function() {

		 
			//open page
		 // tjs 130126
            //window.location.href = "../donationlog/index.html";
            // tjs 130128;
            //window.location.href = "http://www.charityHound.org/app/index.html";
            // tjs 130130;
            //window.location.href = "http://openappmkt.com/app/510695fdeb377200020020b9/CharityHound";
            //window.location.href = "app/index.html";
            //window.location.assign("app/index.html");
            //window.location.assign("http://new-host.home:8081/app/index.html");
            //window.location.assign("http://www.charityhound.org/app/index.html");
            window.open("http://www.charityhound.org/app/index.html",'','',true);

		 
		});


        var account = <?php echo $account; ?>;

        //tjs 110511
        //alert("index account " + account);
        //e.g. account 1
        
		processArgs(account);
				//processArgs();

		// tjs 111027
		var charityHoundRegisterDialogOpts = {
				title: "CharityHound Registration",
				width: "550px",
				dialogClass: 'dial',
				modal: true,
				autoOpen: false
				}
		//alert("index init charityHoundRegisterDialogOpts inited...");

				$("#charityHoundRegisterDialog").dialog(charityHoundRegisterDialogOpts);

// tjs 111027
			      $('.error').hide();
					
$(".button").click(function() {  
// validate and process form here  

$('.error').hide();  
  var token = $("input#token").val();  
    if (token == "") {  
  $("label#token_error").show();  
  $("input#token").focus();  
  return false;  
}  

    var username = $("input#username").val();  
    if (username == "") {  
  $("label#username_error").show();  
  $("input#username").focus();  
  return false;  
}  
    var password1 = $("input#password1").val();  
    if (password1 == "") {  
  $("label#password1_error").show();  
  $("input#password1").focus();  
  return false;  
}    
    if (password1.length < 8) {  
        $("label#password1_error").show();  
        $("input#password1").focus();  
        return false;  
      }    
    var password2 = $("input#password2").val();  
    if (password2 == "") {  
  $("label#password2_error").show();  
  $("input#password2").focus();  
  return false;  
}    
    if (password2.length < 8) {  
	          $("label#password2_error").show();  
	          $("input#password2").focus();  
	          return false;  
	        }    
    var passwordMnemonicQuestion = $("input#passwordMnemonicQuestion").val();  
    var passwordMnemonicAnswer = $("input#passwordMnemonicAnswer").val();  

    // tjs 110903
    var emailInfo = $("input#emailAddress");
    if (emailInfo.willValidate) {
    	if (!emailInfo.validity.valid) {
	          $("label#emailAddress_error").show();  
          $("input#emailAddress").focus();  
          return false;  
    	}
    }
    var emailAddress = $("input#emailAddress").val();  
    if (emailAddress == "") {  
  $("label#emailAddress_error").show();  
  $("input#emailAddress").focus();  
  return false;  
}    

    // tjs 110904
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            if(!emailReg.test(emailAddress)) {
                //$("#UserEmail").after('<span class="error">Enter a valid email address.</span>');
                //hasError = true;
  	          $("label#emailAddress_error").show();  
	          $("input#emailAddress").focus();  
	          return false;  
            }
     
    
    var firstName = $("input#firstName").val();  
    if (firstName == "") {  
  $("label#firstName_error").show();  
  $("input#firstName").focus();  
  return false;  
}    

    var lastName = $("input#lastName").val();  
    if (lastName == "") {  
  $("label#lastName_error").show();  
  $("input#lastName").focus();  
  return false;  
}   
    var gender = $("input.gender:checked").val();
    if (gender != "m" && gender != "f" ) {  
        $("label#gender_error").show();  
        $("input#gender").focus();  
        return false;  
      }   

    processRegisterForm(token, username, password1, password2, emailAddress, firstName, lastName, gender, passwordMnemonicQuestion, passwordMnemonicAnswer);
}); 

	  });

	</script>

  </body>

</html>
