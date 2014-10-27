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
function newLocation(pageName, menuChoice) {
	//var newLocation = pageName + ".html?authenticated=" + getAuthenticated() + "#" + menuChoice;
	var newLocation = pageName + ".php?#" + menuChoice;
	//.html#collogistics
	//return newLocation;
			//alert("newLocation newLocation " + newLocation);
	console.log("menu newLocation newLocation " + newLocation);
	window.location.href = newLocation;
}
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