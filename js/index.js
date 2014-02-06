/***************************************
$Revision:: 53                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-03-01 15:28:41#$: Date of last commit
***************************************/
/* index.js

tjs 100928

file version 1.00 

release version 1.00

*/

function getXMLHTTPRequest() 
{
var req = false;
try 
  {
   req = new XMLHttpRequest(); /* e.g. Firefox */
  } 
catch(err1) 
  {
  try 
    {
     req = new ActiveXObject("Msxml2.XMLHTTP");  /* some versions IE */
    } 
  catch(err2) 
    {
    try 
      {
       req = new ActiveXObject("Microsoft.XMLHTTP");  /* some versions IE */
      } 
      catch(err3) 
        {
         req = false;
        } 
    } 
  }
return req;
}

function requestXMLData(url, responseHandler) {
	myRequest.open("GET", url, true);
	myRequest.onreadystatechange = responseHandler;
	// send the request
	myRequest.send(null);
}

function requestXMLData(myRequest, url, responseHandler) {
	var myRandom=parseInt(Math.random()*99999999);
	//myRequest.open("GET", url, true);
	myRequest.open("GET", url + "&rand=" + myRandom, true);
	myRequest.onreadystatechange = responseHandler;
	// send the request
	myRequest.send(null);
}

function login(loginAccountNumber, lastName, firstName) {
	var loginRequest = getXMLHTTPRequest();
	var url = 'login.php?account=' + loginAccountNumber + '&first=' + firstName + '&last=' + lastName;
	//alert("login url " + url);
	requestXMLData(loginRequest, url, function() {
	   if(loginRequest.readyState == 4) {
		if(loginRequest.status == 200) {
			//noop
		}
	   }
	});
}

// tjs 131031
//function getLogin() {
function getLogin(url) {
	var loginRequest = getXMLHTTPRequest();
	//var url = 'getLoginXML.php?account=0';
	// tjs 131101
	//alert("index getLogin url " + url);
	// e.g. index getLogin url http://localhost:3000
	requestXMLData(loginRequest, url, function() {
	   if(loginRequest.readyState == 4) {
		   alert("index getLogin loginRequest.readyState " + loginRequest.readyState);
		if(loginRequest.status == 200) {
		   alert("index getLogin loginRequest.status " + loginRequest.status);
		    var data = loginRequest.responseXML;
			   alert("index getLogin data " + data);
		    // tjs 131031
		    //$(data).find('account').each(function() {
			//var $account = $(this);
			//var accountId = $account.attr('id');
		   //alert("getLogin accountId " + accountId);
			//var children = $account.children();
			//var last = children[0].firstChild.nodeValue;
			//var first = children[1].firstChild.nodeValue;
			//alert("getLogin account " + accountId + " last " + last + " first " + first);
			//loginAccountNumber = accountId;
			loginAccountNumber = data;
			//lastName = last;
			//firstName = first;
			//var elm = document.getElementById('logout');						
			//if (loginAccountNumber != 0) {
			//	elm.disabled="";
			//} else {
			//	elm.disabled="disabled";
			//}
		//}
		    //});
			var processAccountUrl = "login.php?account=" + loginAccountNumber;
			window.location.href = processAccountUrl;
		}
	   }
	});
}

function logout() {
	var loginRequest = getXMLHTTPRequest();
	var url = 'logout.php?account=0';
	//alert("login url " + url);
	requestXMLData(loginRequest, url, function() {
	   if(loginRequest.readyState == 4) {
		if(loginRequest.status == 200) {
			//noop
		}
	   }
	});
}

function enableOrDisableScheduledDisplayAds() {
//test http://localhost/~thomassoucy/philanthropy/getCustomerInfoXML.php?account=1
	var customerRequest = getXMLHTTPRequest();
	var url = 'getCustomerInfoXML.php?account=0';
	//alert("index enableOrDisableScheduledDisplayAds url " + url);
	requestXMLData(customerRequest, url, function() {
	   if(customerRequest.readyState == 4) {
		   //alert("enableOrDisableScheduledDisplayAds loginRequest.readyState " + loginRequest.readyState);
		if(customerRequest.status == 200) {
		   //alert("index enableOrDisableScheduledDisplayAds loginRequest.status " + loginRequest.status);
			var elm = document.getElementById('scheduleDisplayAd');						
			elm.disabled="disabled";
		    var data = customerRequest.responseXML;
		    $(data).find('account').each(function() {
				//alert("index enableOrDisableScheduledDisplayAds found customer account...");
				var $account = $(this);
				elm.disabled="";
		    });
		}
	   }
	});
}

function enableOrDisableSiteAdmin() {
	//alert("index enableOrDisableSiteAdmin...");
	var adminRequest = getXMLHTTPRequest();
	var url = 'getAdminInfoXML.php?account=0';
	//console.log("index enableOrDisableSiteAdmin url " + url);
	//alert("index enableOrDisableSiteAdmin url " + url);
	requestXMLData(adminRequest, url, function() {
		   if(adminRequest.readyState == 4) {
			   //alert("enableOrDisableScheduledDisplayAds loginRequest.readyState " + loginRequest.readyState);
			if(adminRequest.status == 200) {
			   //alert("index enableOrDisableScheduledDisplayAds loginRequest.status " + loginRequest.status);
				var elm = document.getElementById('admin');						
				elm.disabled="disabled";
			    var data = adminRequest.responseXML;
			    //$(data).find('account').each(function() {
			    $(data).find('administrator').each(function() {
					//console.log("index enableOrDisableSiteAdmin found admin account...");
					//alert("index enableOrDisableSiteAdmin found admin account...");
					//var $account = $(this);
					var $administrator = $(this);
					//var children = $account.children();
					var children = $administrator.children();
					var account = children[0].firstChild.nodeValue;
					console.log("index enableOrDisableSiteAdmin account " + account);
					if (account != 0) {
						elm.disabled="";
					}
			    });
			}
		   }
		});
	/* tjs 140206
//test http://localhost/~thomassoucy/philanthropy/getCustomerInfoXML.php?account=1
	var customerRequest = getXMLHTTPRequest();
	var url = 'getCustomerInfoXML.php?account=0';
	//alert("index enableOrDisableScheduledDisplayAds url " + url);
	requestXMLData(customerRequest, url, function() {
	   if(customerRequest.readyState == 4) {
		   //alert("enableOrDisableScheduledDisplayAds loginRequest.readyState " + loginRequest.readyState);
		if(customerRequest.status == 200) {
		   //alert("index enableOrDisableScheduledDisplayAds loginRequest.status " + loginRequest.status);
			var elm = document.getElementById('admin');						
			elm.disabled="disabled";
		    var data = customerRequest.responseXML;
		    //$(data).find('account').each(function() {
		    $(data).find('customer').each(function() {
				//alert("index enableOrDisableScheduledDisplayAds found customer account...");
				//var $account = $(this);
				var $customer = $(this);
				//var children = $account.children();
				var children = $customer.children();
				var account = children[0].firstChild.nodeValue;
				//alert("index enableOrDisableSiteAdmin account " + account);
				if (account == 1) {
					elm.disabled="";
				}
		    });
		}
	   }
	});
	*/
}

