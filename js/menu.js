/***************************************
$Revision:: 92                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 11:49:13#$: Date of last commit
***************************************/
/* menu.js

tjs 101130

file version 1.00 

release version 1.00

*/

//global
/*
 * tjs 110511
	var authenticated = false;
	function getAuthenticated() {
	    if (authenticated) {
			//alert("getAuthenticated authenticated " + authenticated);
	    	return authenticated;
	    } 
			//alert("getAuthenticated authenticated false");
	    return false;
	}
*/
/*
function authenticateUserForDomain(provider, database, domain, name, handle, email, password, id, pageName, menuChoice) {
	console.log("menu authenticateUserForDomain provider " + provider + " database " + database + " domain " + domain + " name " + name + " handle " + handle + " email " + email + " password " + password + " id " + id);

	var aggregateDB = "collogistics";
	//var aggregateDB = "docs-sandbox";
	//var signupReferenceURL = "https://" + aggregateDB + "." + aggregateDSN + "/web/uauth";
	var authenticationReferenceURL = "https://" + database + "." + provider;
	console.log("menu authenticateUserForDomain url " + authenticationReferenceURL);
	// temp disable
	
    var authRef = new Firebase(
    		authenticationReferenceURL);
    console.log("authRef " + authRef);
    rootRef = authRef;
    var user = new User(email, password);
    var loginPromise = authWithPassword(userAndPass);
 
    handleAuthResponse(loginPromise, id, pageName, menuChoice);
    
	// temp enable
    //newLocation(pageName, menuChoice);
	}*/
function loginAuthenticateUserForDomain(provider, database, domain, name, handle, email, password, id, pageName, menuChoice) {
	console.log("menu authenticateUserForDomain provider " + provider + " database " + database + " domain " + domain + " name " + name + " handle " + handle + " email " + email + " password " + password + " id " + id);
	authenticateUserForDomain(provider, database, domain, name, handle, email, password, id, pageName, menuChoice);
}
function newLocation(pageName, menuChoice) {
	//var newLocation = pageName + ".html?authenticated=" + getAuthenticated() + "#" + menuChoice;
	var newLocation = pageName + ".php?#" + menuChoice;
	//.html#collogistics
	//return newLocation;
			//alert("newLocation newLocation " + newLocation);
	console.log("menu newLocation newLocation " + newLocation);
	window.location.href = newLocation;
	// TODO concept after site login do checkin to collogistics for more reliable session management.

	/*
	if (pageName == "index") {
		console.log("menu newLocation reloading " + newLocation);
		window.location.reload(true);
	} */
	// location.assign, replace, reload
	
}
/*function newLocation(pageName, menuChoice) {
	//var newLocation = pageName + ".html?authenticated=" + getAuthenticated() + "#" + menuChoice;
	var newLocation = pageName + ".php?#" + menuChoice;
	//.html#collogistics
	//return newLocation;
			//alert("newLocation newLocation " + newLocation);
	window.location.href = newLocation;
}*/
/*
 * tjs 110511
 */
/*
function setAuthenticated() {
	var args = new ArgumentURL();
	//var authenticated = false;
	authenticated = false;
	try {
		authenticated = args.getArgument('authenticated');
	} 
	catch(err) {
	//ignored
	}
	//alert("setAuthenticated authenticated " + authenticated);
}
*/