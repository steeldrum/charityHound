/***************************************
$Revision:: 144                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-10-27 10:08:07#$: Date of last commit
***************************************/
/* charity.js

tjs 101118

file version 1.00 

release version 1.00

*/
/*
api references:
http://www.datatables.net/api

*/

//function downloadPDF(jobId) {
function downloadPDF(loginId, jobId) {
	//alert("ccJobCost - downloadPDF loginId " + loginId + " jobId " + jobId);
	var refreshCacheRequest = getXMLHTTPRequest();
	var url = 'ccRefreshCache.php?account=' + loginId + '&jobId=' + jobId;
	requestXMLData(refreshCacheRequest, url, function() {
	//alert("ccJobCost - downloadPDF refreshCacheRequest.readyState " + refreshCacheRequest.readyState);
	   if(refreshCacheRequest.readyState == 4) {
	//alert("ccJobCost - downloadPDF refreshCacheRequest.status " + refreshCacheRequest.status);
		if(refreshCacheRequest.status == 200) {
	//alert("ccJobCost - downloadPDF loginId " + loginId + " jobId " + jobId);
	var url = 'http://localhost/ccJobCost2FPDF.php?account=' + loginId + '&jobId=' + jobId;
	window.location.href = url;
		}
	   }
	});
	//alert("ccJobCost - downloadPDF jobId " + jobId);
	//alert("ccJobCost - downloadPDF loginId " + loginId + " jobId " + jobId);
	//http://localhost/ccJobCost2FPDF.php?account=0&jobId=1
	//var url = 'http://localhost/ccJobCost2FPDF.php?account=' + loginId + '&jobId=' + jobId;
	//window.location.href = url;
}

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

//tjs101123
function postRequestXMLData(myRequest, url, responseHandler) {
	var myRandom=parseInt(Math.random()*99999999);
	//myRequest.open("GET", url, true);
	myRequest.open("POST", url + "&rand=" + myRandom, true);
	myRequest.onreadystatechange = responseHandler;
	// send the request
	myRequest.send(null);
}

//101213
function removeSpaces(string) {
 return string.split(' ').join('');
}

//globals for state control of charities table and donations table
var lastDonationCharityId = 0;

//columns to show
//charityName shortName averageDonation numberZeroAmounts numberNonZeroAmounts lastAmount ACTIONS
//function refreshCharities(torf) {
function refreshCharities(torfLoggedIn, torfDetail) {
	//alert("charity refreshCharities torfLoggedIn " + torfLoggedIn);
	var loggedIn = false;
	var detail = false;
	//alert("charity refreshCharities torfLoggedIn " + torfLoggedIn + " torfDetail " + torfDetail);
	if (torfLoggedIn != null && torfLoggedIn == 'true') {
		loggedIn = true;
	}
	if (torfDetail != null && torfDetail == 'true') {
		detail = true;
		$('#charityDetailList').empty();
	} else {
		$('#charityList').empty();
	}
	//alert("charity refreshCharities torfLoggedIn " + torfLoggedIn + " loggedIn " + loggedIn + " torfDetail " + torfDetail + " detail " + detail);
	var charityRequest = getXMLHTTPRequest();
	//tjs110308
	//var url = 'getCharitiesXML.php?account=' + loginAccountNumber;
	var url = 'getCharitiesXML.php?account=' + loginAccountNumber + '&detail=' + detail;
	//alert("charity refreshCharities url " + url);
	//alert("charity refreshCharities loggedIn " + loggedIn + " detail " + detail + " url " + url);
	//tjs101123
	//requestXMLData(charityRequest, url, function() {
	postRequestXMLData(charityRequest, url, function() {
	   if(charityRequest.readyState == 4) {
		// if server HTTP response is "OK"
		//alert("charity refreshCharities readyState 4 charityRequest.status " + charityRequest.status);
		if(charityRequest.status == 200) {
			//if (detail) {
			if (detail == true) {
				$('#charityDetailList').empty();
			} else {
				$('#charityList').empty();
			}
		    var data = charityRequest.responseXML;
			//var html = '<table id="charityTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Solicitations</th><th>Donations</th><th>Average</th><th>Amount</th><th>Action(<input type="image" onclick="addCharity()" src="images/icon_plus.gif"';
			var html;
			if (detail == true) {
			//tjs110308
				//html = '<table id="charityDetailTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Dunns</th><th>URL</th><th>Description</th><th>Rating</th><th>Action(<input type="image" onclick="addCharityDetail()" src="images/icon_plus.gif"';
				html = '<table id="charityDetailTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Duns</th><th>ForProfit</th><th>InActive</th><th>URL</th><th>Description</th><th>Rating</th><th>Action(<input type="image" onclick="addCharityDetail()" src="images/icon_plus.gif"';
			} else {
			//tjs110225
				//html = '<table id="charityTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Solicitations</th><th>Donations</th><th>Average</th><th>Amount</th><th>Action(<input type="image" onclick="addCharity()" src="images/icon_plus.gif"';
				html = '<table id="charityTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Solicitations</th><th>Rate</th><th>Donations</th><th>Average</th><th>Amount</th><th>Action(<input type="image" onclick="addCharity()" src="images/icon_plus.gif"';
			}
			if (loggedIn == false) {
				html += ' disabled="disabled" ';
			}
			html += '/>)</th></tr></thead><tbody>';
			$(data).find('charity').each(function() {
				var $charity = $(this);
				//html += '<tr>';
				html += '<tr class="gradeA">';
				var charityId = $charity.attr('id');
				var solicitations = $charity.attr('solicitations');
				var rate = $charity.attr('rate');
				var donations = $charity.attr('donations');
				var average = $charity.attr('average');
				// tjs 111027
				var lastAmount = $charity.attr('lastAmount');
				var donationMadeOnLastSolicitation = lastAmount > 0;
				html += '<td>' + charityId + '</td>';
				var children = $charity.children();
				var name = children[0].firstChild.nodeValue;
				var shortName = ' ';
				if (children[1].firstChild) {
					shortName = children[1].firstChild.nodeValue;
				}
				var url = '';
				html += '<td';
				if (donationMadeOnLastSolicitation) {
					html += ' class="highlightName"';
				}
				if (children[3].firstChild) {
					url = children[3].firstChild.nodeValue;
					url = removeSpaces(url);
					//html += '<td><a href="http://' + url + '" >' + name + '</a></td>';
					html += '><a href="http://' + url + '" >' + name + '</a></td>';
				} else {
					//html += '<td>' + name+ '</td>';
					html += '>' + name+ '</td>';
				}
				html += '<td>' + shortName + '</td>';
				if (detail) {
					var dunns = ' ';
					if (children[2].firstChild) {
						dunns = children[2].firstChild.nodeValue;
					}
					//tjs110317
					/*
					var url = '';
					if (children[3].firstChild) {
						url = children[3].firstChild.nodeValue;
						url = removeSpaces(url);
						html += '<td><a href="http://' + url + '" >' + name + '</a></td>';
					} else {
						html += '<td>' + name+ '</td>';
					}
					html += '<td>' + shortName + '</td>';
					*/
					var description = ' ';
					if (children[4].firstChild) {
						description = children[4].firstChild.nodeValue;
					}
					var numStars = ' ';
					if (children[5].firstChild) {
						numStars = children[5].firstChild.nodeValue;
					}
					var rating = Number(numStars);
					if (rating == 0 && solicitations > 0 && donations > 0) {
						var ratio = donations/solicitations;
						//alert("charity refreshCharities numStars " + numStars + " rating " + rating + " solicitations " + solicitations + " donations " + donations);
						if (ratio > .9) {
							rating = -5;
						} else if (ratio > .8) {
							rating = -4;
						} else if (ratio > .6) {
							rating = -3;
						} else if (ratio > .4) {
							rating = -2;
						} else {
							rating = -1;
						}
					}
					//tjs110308
					var isInactive = '0';
					if (children[7].firstChild) {
						isInactive = children[7].firstChild.nodeValue;
					}
					var isForProfit = '0';
					if (children[8].firstChild) {
						isForProfit = children[8].firstChild.nodeValue;
					}
					
					html += '<td>' + dunns + '</td>';
					if (isForProfit == '0') {
						html += '<td><input type="checkbox" disabled="disabled" /></td>';
					} else {
						html += '<td><input type="checkbox" checked="checked" disabled="disabled" /></td>';
					}
					if (isInactive == '0') {
						html += '<td><input type="checkbox" disabled="disabled" /></td>';
					} else {
						html += '<td><input type="checkbox" checked="checked" disabled="disabled" /></td>';
					}
					html += '<td>' + url + '</td><td>' + description + '</td><td>' + numStars + '</td>';
					//html += '<td><input type="image" onclick="modifyCharityDetail(' + charityId + ',\'' + name + '\',\'' + shortName + '\',\'' + dunns+ '\',\'' + url+ '\',\'' + description + '\','  + rating + ')" src="images/edit.gif"';
					html += '<td><input type="image" onclick="modifyCharityDetail(' + charityId + ',\'' + name + '\',\'' + shortName + '\',\'' + dunns + '\',\'' + isForProfit + '\',\'' + isInactive + '\',\''+ url+ '\',\'' + description + '\','  + rating + ')" src="images/edit.gif"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/>&nbsp;<input type="image" onclick="removeCharityDetail(' + charityId + ',\'' + name + '\',\'' + shortName + '\',\'' + dunns + '\',\'' + isForProfit + '\',\'' + isInactive + '\',\'' + url + '\',\'' + description + '\','  + rating + ')" src="images/delete.gif"';
					//html += '/>&nbsp;<input type="image" onclick="removeCharityDetail(' + charityId + ',\'' + name + '\',\'' + shortName + '\',\'' + dunns+ '\',\'' + url+ '\',\'' + description + '\','  + rating + ')" src="images/delete.gif"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/></td>';
				} else {				
					//html += '<td>' + name+ '</td>';
					//html += '<td>' + shortName + '</td>';
					//html += '<td>' + solicitations + '</td><td>' + donations + '</td><td>' + average + '</td><td><input id="' + charityId + '" type="number" value = "0" /></td>';
					html += '<td>' + solicitations + '</td><td>' + rate + '</td><td>' + donations + '</td><td>' + average + '</td><td><input id="' + charityId + '" type="number" value = "0" /></td>';
					//html += '<td><input type="image" onclick="doAmountTransaction(0,' + charityId + ',' + loginAccountNumber + ', 0, 0)" src="images/icon_plus.gif"/> <input type="image" onclick="modifyCharity(' + charityId + ',\'' + name + '\',\'' + shortName + '\')" src="images/edit.gif"/>&nbsp;<input type="image" onclick="removeCharity(' + charityId + ')" src="images/delete.gif"/>&nbsp;<input type="image" onclick="populateDonations(' + loginAccountNumber + ',' + charityId + ')" src="images/magnifier.png"/></td>';
					html += '<td><input type="image" onclick="doAmountTransaction(0,' + charityId + ',' + loginAccountNumber + ', 0, 0)" src="images/icon_plus.gif"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/> <input type="image" onclick="modifyCharity(' + charityId + ',\'' + name + '\',\'' + shortName + '\')" src="images/edit.gif"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/>&nbsp;<input type="image" onclick="removeCharity(' + charityId + ')" src="images/delete.gif"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/>&nbsp;<input type="image" onclick="populateDonations(' + loginAccountNumber + ',' + charityId + ')" src="images/magnifier.png"';
					if (loggedIn == false) {
						html += ' disabled="disabled" ';
					}
					html += '/></td>';
				}
				html += '</tr>';
			});
			html += '</tbody></table>';
			//alert("charity refreshCharities html " + html);
			
			if (detail == true) {
				$('#charityDetailList').append($(html));
				jQuery('#charityDetailTable').dataTable({
					bLengthChange: false,
					//sPaginationType: "two_button",
					sPaginationType: "full_numbers",
	
					aoColumns: [{
						 //bVisible: false,
						 bVisible: true,
						bSearchable: false,
						 bSortable: false
					 }, //id
					{
						 bVisible: true,
						 bSearchable: true,
						 bSortable: true
					 }, //name
					 {
						bVisible: true,
						 bSearchable: true,
						bSortable: true
					}, //shortName
					{
						 bVisible: true,
						 bSearchable: false,
						bSortable: false
					 }, //duns
					{
						 bVisible: true,
						 bSearchable: false,
						bSortable: false
					 }, //isForProfit
					{
						 bVisible: true,
						 bSearchable: false,
						bSortable: false
					 }, //isInactive
					{
						bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //url
					{
						 bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //description
					 {
						 bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //numStars
					{
						bVisible: true,
						bSearchable: false,
						bSortable: false
					 } //action
					 ]
	    	 	});
			} else {
				$('#charityList').append($(html));
				jQuery('#charityTable').dataTable({
					bLengthChange: false,
					//sPaginationType: "two_button",
					sPaginationType: "full_numbers",
					/*fnInfoCallback: function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
						//return iStart +" to "+ iEnd;
						if (iMax != iTotal) {
							if (lastDonationCharityId != 0) {
								populateDonations(0,0);
							}
						}
					},*/
					fnDrawCallback: function() {
							 //alert( 'DataTables has redrawn the table' );
							if (lastDonationCharityId != 0) {
								populateDonations(0,0);
							}
					},
	
					aoColumns: [{
						 //bVisible: false,
						 bVisible: true,
						bSearchable: false,
						 bSortable: false
					 }, //id
					{
						 bVisible: true,
						 bSearchable: true,
						 bSortable: true
					 }, //name
					 {
						bVisible: true,
						 bSearchable: true,
						bSortable: true
					}, //shortName
					{
						 bVisible: true,
						 bSearchable: false,
						bSortable: false
					 }, //solicitations
					{
						 bVisible: true,
						 bSearchable: false,
						bSortable: false
					 }, //rate
					{
						bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //donations
					{
						 bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //average
					 {
						 bVisible: true,
						bSearchable: false,
						bSortable: false
					}, //amount
					{
						bVisible: true,
						bSearchable: false,
						bSortable: false
					 } //action
					 ]
	    	 	});
	    	 }
	     //isDone = true;
		// i.e. means no response status 200
		} else {
		    // issue an error message for any other HTTP response
		    alert("An error has occurred: " + charityRequest.statusText);
		}
		// i.e. no ready state so waiting...
	    } else { // else waiting...
	    	var html = '<p>Waiting...</p>';
			if (detail == true) {
				$('#charityDetailList').append($(html));
			} else {
				$('#charityList').append($(html));
			}
	    }
	});
	if (detail == false) {
		populateDonations(0,0);
	}
}

//add
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=5&charityId=5&remove=false&id=0
//alter
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=15&charityId=5&remove=false&id=1
//delete
//http://localhost/~thomassoucy/philanthropy/donations.php?account=1&amount=5&charityId=5&remove=true&id=1

function doAmountTransaction(trxType, charityId, memberId, id, newAmount, newDate) {
//alert ("charity doAmountTransaction charity id " + charityId + " member id " + memberId);
//alert ("charity doAmountTransaction charity id " + charityId + " member id " + memberId + " id " + id + " new amount " + newAmount + " new date " + newDate);
var queryStr;
var amount = 0;
var remove = 'false';
if (trxType == 2)
	remove = 'true';
if (id == 0) {
	queryStr = "input#" + charityId;
    amount = jQuery(queryStr).get(0).value;
    } else {
    amount = newAmount;
    }
//alert ("charity amount " + amount);

	var donationsRequest = getXMLHTTPRequest();
	var url = 'donations.php?account=' + memberId + '&amount=' + amount + '&charityId=' + charityId + '&remove=' + remove + '&id=' + id;
	if (newDate != null) {
		url += '&date=' + newDate;
	}
	//alert("charity doAmountTransaction url " + url);
	requestXMLData(donationsRequest, url, function() {
	   if(donationsRequest.readyState == 4) {
		// if server HTTP response is "OK"
	//alert("ccJobCost refreshCustomers readyState 4 customerRequest.status " + customerRequest.status);
		if(donationsRequest.status == 200) {
		    //var data = donationsRequest.responseXML;
//tjs 101126
//TODO BUG!!!
//jQuery(queryStr).get(0).value = 0;
if (id == 0)
	jQuery(queryStr).get(0).value = 0;
populateDonations(memberId, charityId);
	
		} else {
		    // issue an error message for any other HTTP response
		    alert("An error has occurred: " + donationsRequest.statusText);
		}
	    }
	});
}

/*
<donations>
<donation id="10">
<memberId>0</memberId>
<charityId>4</charityId>
<amount>444</amount>
<date>2010-11-19 16:06:45</date>
</donation>
</donations>
*/
function populateDonations(memberId, charityId) {
//alert ("charity populateDonations charity id " + charityId + " member id " + memberId);
var html;
	$('#donationList').empty();
	if (charityId == 0) {
		lastDonationCharityId = 0;
		//html = '<table id="donationTable"><thead><tr><th>ID</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody /></table>';
		html = '<table id="donationTable"><thead><tr><th>CharityID</th><th>ID</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody /></table>';
			$('#donationList').append($(html));
		jQuery('#donationTable').dataTable({
		bLengthChange: false,
		//sPaginationType: "two_button",
		sPaginationType: "full_numbers",
	     aoColumns: [{
	     //bVisible: false,
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     }, //charityId
	     {
	     //bVisible: false,
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     }, //id
	     {
	     bVisible: true,
	     bSearchable: true,
	     bSortable: true
	     }, //amount
	     {
	     bVisible: true,
	     bSearchable: true,
	     bSortable: true
	     }, //date
	     {
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     } //action
	     ]
	     });
	
	} else {
		lastDonationCharityId = charityId;
	var donationRequest = getXMLHTTPRequest();
	//url = 'ccGetDonationsXML.php?account=' + loginAccountNumber;
	var url = 'getDonationsXML.php?account=' + memberId + '&charityId=' + charityId;
	//alert("charity refreshCharities url " + url);
	requestXMLData(donationRequest, url, function() {
	   if(donationRequest.readyState == 4) {
		// if server HTTP response is "OK"
	//alert("charity refreshCharities readyState 4 charityRequest.status " + charityRequest.status);
		if(donationRequest.status == 200) {
		    var data = donationRequest.responseXML;
		//var html = '<table id="charityTable"><thead><tr><th>ID</th><th>Charity Name</th><th>Short Name</th><th>Action(<input type="image" onclick="add(4,' + loginAccountNumber + ')" src="images/icon_plus.gif"/>)</th></tr></thead><tbody>';
		//var html = '<table id="donationTable"><thead><tr><th>ID</th><th>Amount</th><th>Date</th><th>Action(<input type="image" onclick="add(4,' + loginAccountNumber + ')" src="images/icon_plus.gif"/>)</th></tr></thead><tbody>';
		//html = '<table id="donationTable"><thead><tr><th>ID</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody>';
		html = '<table id="donationTable"><thead><tr><th>CharityID</th><th>ID</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead><tbody>';
		$(data).find('donation').each(function() {
		//$(customerData).find('customer').each(function() {
			var $donation = $(this);
			//html += '<tr>';
			html += '<tr class="gradeA">';
			html += '<td>' + charityId + '</td>';
			var id = $donation.attr('id');
			//html += '<td>' + $customer.attr('id')+ '</td>';
			html += '<td>' + id + '</td>';
			var children = $donation.children();
			//html += '<td><input id="' + id + '" type="text" value="' + children[2].firstChild.nodeValue + '" /></td>';
			var amount = children[2].firstChild.nodeValue;
			//todo input type number?
			html += '<td>' + amount + '</td>';
			//html += '<td>' + children[1].firstChild.nodeValue + '</td>';
			//todo date control
			var date = '';
			if (children[3].firstChild) {
				date = children[3].firstChild.nodeValue;
			}
			html += '<td>' + date + '</td>';
			//$dateId = charity_id + "_" + id;
			//html += '<td><input id="' + dateId + '" value="' + date + '" disabled="disabled" />&nbsp;&nbsp;</td>';
			html += '<td><input type="image" onclick="modifyDonation(' + id + ',' + memberId + ',' + charityId + ', ' + amount + ',\'' + date + '\')" src="images/edit.gif"/>&nbsp;<input type="image" onclick="removeDonation(' + id + ',' + memberId + ',' + charityId + ',' + amount + ',\'' + date + '\')" src="images/delete.gif"/></td>';
			html += '</tr>';
		});
		html += '</tbody></table>';
	//alert("charity refreshCharities html " + html);
	
		$('#donationList').append($(html));
		jQuery('#donationTable').dataTable({
		bLengthChange: false,
		//sPaginationType: "two_button",
		sPaginationType: "full_numbers",
	     aoColumns: [{
	     //bVisible: false,
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     }, //charityId
	     {
	     //bVisible: false,
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     }, //id
	     {
	     bVisible: true,
	     bSearchable: true,
	     bSortable: true
	     }, //amount
	     {
	     bVisible: true,
	     bSearchable: true,
	     bSortable: true
	     }, //date
	     {
	     bVisible: true,
	     bSearchable: false,
	     bSortable: false
	     } //action
	     ]
	     });
	     //isDone = true;
	
		} else {
		    // issue an error message for any other HTTP response
		    alert("An error has occurred: " + donationRequest.statusText);
		}
	    }
	});
	
	}

}

function addCharityDetail() {

			document.charityDetailForm.id.value = 0;
			document.charityDetailForm.remove.value = false;
			document.charityDetailForm.name.value = '';
			document.charityDetailForm.shortname.value = '';
			document.charityDetailForm.name.disabled = '';
			document.charityDetailForm.shortname.disabled = '';
			document.charityDetailForm.dunns.value = '';
			document.charityDetailForm.dunns.disabled = '';
			document.charityDetailForm.isForProfit.checked = '';
			document.charityDetailForm.isForProfit.disabled = '';
			document.charityDetailForm.isInactive.checked = '';
			document.charityDetailForm.isInactive.disabled = '';
			document.charityDetailForm.url.value = '';
			document.charityDetailForm.url.disabled = '';
			document.charityDetailForm.description.value = '';
			document.charityDetailForm.description.disabled = '';
			document.charityDetailForm.numStars.value = '';
			document.charityDetailForm.numStars.disabled = '';

			$('#charityDetailDialogMsg').hide();

			$("#charityDetailDialog").dialog("open");

}

function addCharity() {

			document.charityForm.id.value = 0;
			document.charityForm.remove.value = false;
			document.charityForm.name.value = '';
			document.charityForm.shortname.value = '';
			document.charityForm.name.disabled = '';
			document.charityForm.shortname.disabled = '';

			//tjs 101213
			$('#charityDialogMsg').hide();

			$("#charityDialog").dialog("open");

}

//function modifyCharityDetail(id, name, shortName, dunns, url, description, numStars) {
function modifyCharityDetail(id, name, shortName, dunns, isForProfit, isInactive, url, description, numStars) {
			document.charityDetailForm.id.value = id;
			document.charityDetailForm.remove.value = false;
			document.charityDetailForm.name.value = name;
			document.charityDetailForm.shortname.value = shortName;
			document.charityDetailForm.dunns.value = dunns;
			if (isForProfit == '0') {
				document.charityDetailForm.isForProfit.checked = '';
			} else {
				document.charityDetailForm.isForProfit.checked = 'checked';
			}
			if (isInactive == '0') {
				document.charityDetailForm.isInactive.checked = '';
			} else {
				document.charityDetailForm.isInactive.checked = 'checked';
			}
			document.charityDetailForm.url.value = url;
			document.charityDetailForm.description.value = description;
			document.charityDetailForm.numStars.value = numStars;
			//alert("charity modifyCharity name " + name);
			
			$('#charityDetailDialogMsg').hide();
			document.charityDetailForm.name.disabled = '';
			document.charityDetailForm.shortname.disabled = '';
			document.charityDetailForm.dunns.disabled = '';
			document.charityDetailForm.isForProfit.disabled = '';
			document.charityDetailForm.isInactive.disabled = '';
			document.charityDetailForm.url.disabled = '';
			document.charityDetailForm.description.disabled = '';
			document.charityDetailForm.numStars.disabled = '';

			$("#charityDetailDialog").dialog("open");


}

//function removeCharityDetail(id, name, shortName, dunns, url, description, numStars) {
function removeCharityDetail(id, name, shortName, dunns, isForProfit, isInactive, url, description, numStars) {
			document.charityDetailForm.id.value = id;
			document.charityDetailForm.remove.value = true;
			document.charityDetailForm.name.value = name;
			document.charityDetailForm.shortname.value = shortName;
			document.charityDetailForm.dunns.value = dunns;
			if (isForProfit == '0') {
				document.charityDetailForm.isForProfit.checked = '';
			} else {
				document.charityDetailForm.isForProfit.checked = 'checked';
			}
			if (isInactive == '0') {
				document.charityDetailForm.isInactive.checked = '';
			} else {
				document.charityDetailForm.isInactive.checked = 'checked';
			}
			document.charityDetailForm.url.value = url;
			document.charityDetailForm.description.value = description;
			document.charityDetailForm.numStars.value = numStars;
			//alert("charity removeCharity shortname " + shortname);
			document.charityDetailForm.name.disabled = 'disabled';
			document.charityDetailForm.shortname.disabled = 'disabled';
			document.charityDetailForm.dunns.disabled = 'disabled';
			document.charityDetailForm.isForProfit.disabled = 'disabled';
			document.charityDetailForm.isInactive.disabled = 'disabled';
			document.charityDetailForm.url.disabled = 'disabled';
			document.charityDetailForm.description.disabled = 'disabled';
			document.charityDetailForm.numStars.disabled = 'disabled';
			//document.charityForm.description.disabled = 'disabled';
					    //enableOrDisableJobSubmit();
			$('#charityDetailDialogMsg').show();

			$("#charityDetailDialog").dialog("open");


}

//function modifyCharity(id) {
function modifyCharity(id, name, shortName) {
			document.charityForm.id.value = id;
			document.charityForm.remove.value = false;
			document.charityForm.name.value = name;
			document.charityForm.shortname.value = shortName;
			//alert("charity modifyCharity name " + name);
			
			//tjs 101213
			$('#charityDialogMsg').hide();
			document.charityForm.name.disabled = '';
			document.charityForm.shortname.disabled = '';

			$("#charityDialog").dialog("open");


}

function removeCharity(id) {
			document.charityForm.id.value = id;
			document.charityForm.remove.value = true;
			queryStr = "input#" + id;
			var name = jQuery(queryStr).parent().parent().children()[1].firstChild.nodeValue;
			document.charityForm.name.value = name;
			//alert("charity removeCharity name " + name);
			var shortname = jQuery(queryStr).parent().parent().children()[2].firstChild.nodeValue;
			document.charityForm.shortname.value = shortname;
			//alert("charity removeCharity shortname " + shortname);
			document.charityForm.name.disabled = 'disabled';
			document.charityForm.shortname.disabled = 'disabled';
			//document.charityForm.description.disabled = 'disabled';
					    //enableOrDisableJobSubmit();
			$('#charityDialogMsg').show();

			$("#charityDialog").dialog("open");


}

function modifyDonation(id, memberId, charityId, amount, date) {
			document.donationForm.id.value = id;
			document.donationForm.remove.value = false;
			document.donationForm.charityId.value = charityId;
			document.donationForm.memberId.value = memberId;
			document.donationForm.amount.value = amount;
			document.donationForm.amount.disabled = '';
			//$('#datepicker').innerHTML= date;
			//document.donationForm.date.value = date;
			//$("#datepicker").datepicker({dateFormat: 'yy-mm-dd', minDate: '-120M', maxDate: 1, defaultDate: date });
			$("#datepicker").datepicker({dateFormat: 'yy-mm-dd', minDate: '-120M', maxDate: 1 });
   			var date1 = date.substring(0, date.indexOf(' '));
			//alert("charity modifyDonation amount " + amount + " date " + date + " date1 " + date1);
    		//charity modifyDonation amount 0 date 2010-11-10 00:00:00 date1 2010-11-10
   
   			var date0 = new Date(date1);
			//alert("charity modifyDonation amount " + amount + " date " + date + " date0 " + date0);
			var time = date0.getTime();
			var d = new Date()
			var offsetMinutes = d.getTimezoneOffset();
			var offsetMilliSeconds = offsetMinutes*60*1000;
			var offsetTime = time + offsetMilliSeconds;
			var date4 = new Date(offsetTime);
    		$('#datepicker').datepicker("setDate", date4);
    		$("#datepicker").datepicker("enable");
			//document.donationForm.date.disabled = 'disabled';
			//alert("charity modifyDonation amount " + amount);
			    //var date2 = $( "#datepicker" ).datepicker( "getDate" );
			   // var madeOn = $.datepicker.formatDate( "ISO_8601", date );
			   //'yy-mm-dd'
			    //var madeOn = $.datepicker.formatDate( 'yy-mm-dd', date2 );

			//alert("charity modifyDonation amount " + amount + " date2 " + date2 + " madeOn " + madeOn);
			//charity modifyDonation amount 0 date 2010-11-10 00:00:00 madeOn 2010-12-04

			$("#donationDialog").dialog("open");


}

function removeDonation(id, memberId, charityId, amount, date) {
			document.donationForm.id.value = id;
			document.donationForm.remove.value = true;
			document.donationForm.charityId.value = charityId;
			document.donationForm.memberId.value = memberId;
			
			//queryStr = "input#" + id;
			//var name = jQuery(queryStr).parent().parent().children()[1].firstChild.nodeValue;
			document.donationForm.amount.value = amount;
			//alert("charity removeCharity name " + name);
			//var shortname = jQuery(queryStr).parent().parent().children()[2].firstChild.nodeValue;
			//document.donationForm.date.value = date;
			//alert("charity removeCharity shortname " + shortname);
			//$("#datepicker").datepicker({dateFormat: 'yy-mm-dd', minDate: '-120M', maxDate: 1, defaultDate: date });
			$("#datepicker").datepicker({dateFormat: 'yy-mm-dd', minDate: '-120M', maxDate: 1 });
   			var date1 = date.substring(0, date.indexOf(' '));
			//alert("charity modifyDonation amount " + amount + " date " + date + " date1 " + date1);
    		//charity modifyDonation amount 0 date 2010-11-10 00:00:00 date1 2010-11-10
   
   			var date0 = new Date(date1);
			//alert("charity modifyDonation amount " + amount + " date " + date + " date0 " + date0);
			var time = date0.getTime();
			var d = new Date()
			var offsetMinutes = d.getTimezoneOffset();
			var offsetMilliSeconds = offsetMinutes*60*1000;
			var offsetTime = time + offsetMilliSeconds;
			var date4 = new Date(offsetTime);
    		$('#datepicker').datepicker("setDate", date4);
    
    		$("#datepicker").datepicker("disable");
			document.donationForm.amount.disabled = 'disabled';
			//document.donationForm.date.disabled = 'disabled';
			//document.charityForm.description.disabled = 'disabled';
					    //enableOrDisableJobSubmit();
			$('#donationDialogMsg').show();

			$("#donationDialog").dialog("open");


}

//tjs 101213
function processCharityDetailForm() {
			//var refresher = refreshCustomers;
			var name = document.charityDetailForm.name.value;
			var shortname = document.charityDetailForm.shortname.value;
			var dunns = document.charityDetailForm.dunns.value;
			var checked = document.charityDetailForm.isForProfit.checked;
			//alert("ccJobCost processForm checked " + checked);
			var isForProfit = '0';
			if (checked == true) {
				var isForProfit = '1';
			}
			checked = document.charityDetailForm.isInactive.checked;
			var isInactive = '0';
			if (checked == true) {
				var isInactive = '1';
			}
			var url = document.charityDetailForm.url.value;
			var description = document.charityDetailForm.description.value;
			var numStars = document.charityDetailForm.numStars.value;
			if (name == null || name.length == 0) {
				alert("ERROR: The name cannot be blank!");
				return;
			}
			var id = document.charityDetailForm.id.value;
			var remove = document.charityDetailForm.remove.value;
			var charityRequest = getXMLHTTPRequest();
			//url = 'charitiesDetail.php?charityName=' + name + '&shortName=' + shortname + '&dunns=' + dunns + '&url=' + url + '&description=' + description + '&numStars=' + numStars + '&id=' + id + '&remove=' + remove;
			url = 'charitiesDetail.php?charityName=' + name + '&shortName=' + shortname + '&dunns=' + dunns+ '&isForProfit=' + isForProfit+ '&isInactive=' + isInactive + '&url=' + url + '&description=' + description + '&numStars=' + numStars + '&id=' + id + '&remove=' + remove;
			//alert("ccJobCost processForm url " + url);
			requestXMLData(charityRequest, url, function() {
			//requestXMLData(customerRequest, url, function(refresher) {
			   if(charityRequest.readyState == 4) {
	//alert("ccJobCost processForm readyState 4 status " + customerRequest.status);
				// if server HTTP response is "OK"
				//if(customerRequest.status == 200) {
				if(charityRequest.status == 200 || charityRequest.status == 0) {
				    $("#charityDetailDialog").dialog("close");
					//refreshCharities();			
					refreshCharities('true', 'true');			
				} else {
				    // issue an error message for any other HTTP response
				    alert("An error has occurred: " + charityRequest.statusText);
				}
			    }
			});

}

//tjs 101126
function processCharityForm() {
			//var refresher = refreshCustomers;
			var name = document.charityForm.name.value;
			var shortname = document.charityForm.shortname.value;
			if (name == null || name.length == 0) {
				alert("ERROR: The name cannot be blank!");
				return;
			}
			var id = document.charityForm.id.value;
			var remove = document.charityForm.remove.value;
			var charityRequest = getXMLHTTPRequest();
			//url = 'ccCustomers.php?account=' + account + '&last=' + last + '&first=' + first;
			url = 'charities.php?charityName=' + name + '&shortName=' + shortname + '&id=' + id + '&remove=' + remove;
			//url = 'http://localhost/ccCustomers.php?account=' + account + '&last=' + last + '&first=' + first;
	//alert("ccJobCost processForm url " + url);
			requestXMLData(charityRequest, url, function() {
			//requestXMLData(customerRequest, url, function(refresher) {
			   if(charityRequest.readyState == 4) {
	//alert("ccJobCost processForm readyState 4 status " + customerRequest.status);
				// if server HTTP response is "OK"
				//if(customerRequest.status == 200) {
				if(charityRequest.status == 200 || charityRequest.status == 0) {
				    $("#charityDialog").dialog("close");
					//refreshCharities();			
					refreshCharities('true');			
				} else {
				    // issue an error message for any other HTTP response
				    alert("An error has occurred: " + charityRequest.statusText);
				}
			    }
			});

}

function processDonationForm() {
		//alert("charity processDonationForm...");
		var id = document.donationForm.id.value;
		var charityId = document.donationForm.charityId.value;
		var memberId = document.donationForm.memberId.value;
		var amount = document.donationForm.amount.value;
		var remove = document.donationForm.remove.value;
		//var madeOn = document.donationForm.date.value;
		    //var madeOn = $( "#datepicker" ).datepicker( "option", "defaultDate" );
		var date = $( "#datepicker" ).datepicker( "getDate" );
		   // var madeOn = $.datepicker.formatDate( "ISO_8601", date );
		   //'yy-mm-dd'
		// tjs 130314 will need year value to bump score...
		var madeOn = $.datepicker.formatDate( 'yy-mm-dd', date );
		//var yearMadeOn = $.datepicker.formatDate( 'yyyy-mm-dd', date );
		//var year = yearMadeOn.substr(0, 4);

		//alert("charity processDonationForm amount " + amount + " madeOn " + madeOn);
		//if (remove == true) {
		if (remove == 'true') {
			//doAmountTransaction(2, charityId, memberId, id, amount);
			doAmountTransaction(2, charityId, memberId, id, amount, null);
		} else {
			//doAmountTransaction(1, 0, 0, id, amount);
			doAmountTransaction(1, charityId, memberId, id, amount, madeOn);
			//todo release datepicker ??
			//alert ("charity processDonationForm blank " + document.donationForm.blank.checked + " currency " + document.donationForm.currency.checked);
		
			// tjs 120316
			var blank = document.donationForm.blank.checked;
			var currency = document.donationForm.currency.checked;
			var reminder = document.donationForm.reminder.checked;
			var confidential = document.donationForm.confidential.checked;
			//postRatingsUpdate(charityId, memberId, id, madeOn, blank, currency, reminder, confidential);
			// tjs 130314
			postRatingsUpdate(id, charityId, memberId, madeOn, blank, currency, reminder, confidential);
			//postRatingsUpdate(id, charityId, memberId, year, blank, currency, reminder, confidential);
			// tjs 120321
			document.donationForm.blank.checked = "";
			document.donationForm.currency.checked = "";
			document.donationForm.reminder.checked = "";
			document.donationForm.confidential.checked = "";
		}
		
		$("#donationDialog").dialog("close");
}

// tjs 130314
function postRatingsUpdate(donationId, charityId, memberId, date, blank, currency, reminder, confidential) {
//function postRatingsUpdate(donationId, charityId, memberId, year, blank, currency, reminder, confidential) {

	if (!blank && !currency && !reminder && !confidential)
		return;
	
	var ratingsRequest = getXMLHTTPRequest();
	var url = 'ratings.php?account=' + memberId + '&charityId=' + charityId + '&date=' + date + '&blank=' + blank + '&currency=' + currency + '&reminder=' + reminder + '&confidential=' + confidential;
	//alert ("charity postRatingsUpdate url " + url);
	// NB account = 0 means it derives using the session on server
// e.g. charity postRatingsUpdate url ratings.php?account=0&charityId=545&date=2012-03-16&blank=true&currency=false&reminder=false&confidential=false
	requestXMLData(ratingsRequest, url, function() {
		   if(ratingsRequest.readyState == 4) {
				if(ratingsRequest.status == 200) {
				    //var data = ratingsRequest;
					//alert ("charity postRatingsUpdate ratings posted!");
					postAggregateRatingsUpdate(donationId, charityId, memberId, date, blank, currency, reminder, confidential);
				} else {
				    // issue an error message for any other HTTP response
				    alert("postRatingsUpdate An error has occurred: " + ratingsRequest.statusText);
				}
		   }
	});
}

// tjs 130226
function postAggregateRatingsUpdate(donationId, charityId, memberId, date, blank, currency, reminder, confidential) {

	if (!blank && !currency && !reminder && !confidential)
		return;

	// tjs 130326
	var memberCharityId = charityId;
	// tjs 130314
	var year = date.substr(0, 4);
	//alert("postAggregateRatingsUpdate year " + year);
	// e.g. postAggregateRatingsUpdate year 2013
	var charityRequest = getXMLHTTPRequest();
	//tjs110308
	//var url = 'getCharitiesXML.php?account=' + loginAccountNumber;
	var url = 'getBaseCharityXML.php?account=' + loginAccountNumber + '&memberCharityId=' + charityId;
	//alert("charity postAggregateRatingsUpdate url " + url);
	//alert("charity postAggregateRatingsUpdate loggedIn " + loggedIn + " detail " + detail + " url " + url);
	postRequestXMLData(charityRequest, url, function() {
	   if(charityRequest.readyState == 4) {
		// if server HTTP response is "OK"
		//alert("charity postAggregateRatingsUpdate readyState 4 charityRequest.status " + charityRequest.status);
		if(charityRequest.status == 200) {
		    var data = charityRequest.responseXML;
		    var provider = $(data).find('charities').attr('provider');
		    var database = $(data).find('charities').attr('database');
			$(data).find('charity').each(function() {
				var $charity = $(this);
				var charityId = $charity.attr('id');
				// tjs 130326
				if (charityId != memberCharityId) {
					var children = $charity.children();
					var name = children[0].firstChild.nodeValue;
					var shortName = ' ';
					if (children[1].firstChild) {
						shortName = children[1].firstChild.nodeValue;
					}
					var aggregateList;
					if (blank) {
						aggregateList = 'blankScoreList';
						// tjs 130314
						syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, year, aggregateList);
						//syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, date, aggregateList);
					}
					if (currency) {
						aggregateList = 'currencyScoreList';
						syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, year, aggregateList);
					}
					if (reminder) {
						aggregateList = 'reminderScoreList';
						syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, year, aggregateList);
					}
					if (confidential) {
						aggregateList = 'confidentialScoreList';
						syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, year, aggregateList);
					}
				}
			});
		} else {
				    // issue an error message for any other HTTP response
				    alert("postAggregateRatingsUpdate An error has occurred: " + ratingsRequest.statusText);
				}
		   }
	});
}

// tjs 130314 - remove support for donation lists, add support to bump year's score by one
function syncAggregateRatingsUpdate(provider, database, donationId, charityId, name, year, aggregateList) {
	//alert("syncAggregateRatingsUpdate provider " + provider + " database " + database + " charityId " + charityId + " year " + year + " list " + aggregateList);
	// e.g. syncAggregateRatingsUpdate provider firebaseIO.com database collogistics charityId 9 year 2013 list blankScoreList
	var url = 'https://' + database + '.' + provider;
	var databaseRootRef = new Firebase(url);
	// tjs 130325
	//var blankScoreListRef = databaseRootRef.child(aggregateList);
	var aggregateListRef = databaseRootRef.child(aggregateList);
	//alert("syncAggregateRatingsUpdate url " + url);
	// e.g. syncAggregateRatingsUpdate url https://collogistics.firebaseIO.com/blankScoreList
	//var aggregateScoreListRef = new Firebase(url);
				//'https://' + database + '.' + provider + '//' + aggregateList);
			//'https://' + database + '.' + provider + '/' + aggregateList);
	//url);
	
	var currentScore = 0;
	//var userScoreRef = blankScoreListRef.child(charityId);
	var userScoreRef = aggregateListRef.child(charityId);
	//alert("userScoreRef...");
	var newYearString = String(year);
	var yearScoreRef = userScoreRef.child(newYearString);
	// tjs 130321
	var nameRef = userScoreRef.child('name');
	//yearScoreRef.on('value', function(dataSnapshot) {
	yearScoreRef.once('value', function(dataSnapshot) {
		  var childData = dataSnapshot.val();
		  //alert("bumpScore childData " + childData);
		  if (childData != null) {
			  //currentScore = childData;
			  currentScore = Number(childData);
		  }
		  currentScore++;
		  if (currentScore == 1) {
			  nameRef.set(name, function(error) {
				  yearScoreRef.set(currentScore, function(error) {
					  if (error == null) {						  
						//userScoreRef.on('value', function(dataSnapshot) { 
							userScoreRef.once('value', function(dataSnapshot) { 
								// Given a DataSnapshot containing a child 'fred' and a child 'wilma':
								var cumYearScores = 0;
								dataSnapshot.forEach(function(childSnapshot) {
								  // This code will be called twice.
								  var name = childSnapshot.name();
								  //alert("bumpScore name " + name);
								  if (name != 'name') {
									  var year = Number(name);
									  //alert("bumpScore year " + year);						
									  var childData = childSnapshot.val();
									  //alert("bumpScore for cum childData " + childData);						
									  cumYearScores += Number(childData);
								  }
								  // name will be 'fred' the first time and 'wilma' the second time.
								  // childData will be the actual contents of the child.
								});
								userScoreRef
								.setPriority(
										cumYearScores,
										function(
												error) {
											//alert("bumpScore cumYearScores " + cumYearScores);
										});
							});
						} else {
							  alert("bumpScore error " + error);						
						}
				  });				  
			  });
		  } else {
			  yearScoreRef.set(currentScore, function(error) {
				  if (error == null) {						  
					//userScoreRef.on('value', function(dataSnapshot) { 
						userScoreRef.once('value', function(dataSnapshot) { 
							// Given a DataSnapshot containing a child 'fred' and a child 'wilma':
							var cumYearScores = 0;
							dataSnapshot.forEach(function(childSnapshot) {
							  // This code will be called twice.
							  var name = childSnapshot.name();
							  //alert("bumpScore name " + name);
							  if (name != 'name') {
								  var year = Number(name);
								  //alert("bumpScore year " + year);						
								  var childData = childSnapshot.val();
								  //alert("bumpScore for cum childData " + childData);						
								  cumYearScores += Number(childData);
							  }
							  // name will be 'fred' the first time and 'wilma' the second time.
							  // childData will be the actual contents of the child.
							});
							userScoreRef
							.setPriority(
									cumYearScores,
									function(
											error) {
										//alert("bumpScore cumYearScores " + cumYearScores);
									});
						});
					} else {
						  alert("bumpScore error " + error);						
					}
			  });
		  }
	});
}
	

