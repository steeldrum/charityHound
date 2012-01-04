/***************************************
$Revision:: 53                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-03-01 15:28:41#$: Date of last commit
***************************************/
/* argumenturl.js

tjs 101130

file version 1.00 

release version 1.00

(based on search http://webfx.eae.net/docs/argPassing/argsp2.html)

code idioms:

var object = new ArgumentURL();

This objects has three methods that is used for manipulating the arguments. They are

object.getArgument(argName) Returns the value as a string. If no argument exists with that name it returns null.

object.setArgument(argName, argValue) Sets the argument argName with the value argValue

object.removeArgument(argName) Removes the argument.

object.toString() or just object This will create a string that contains the end part of the url, starting with a "?". This one is useful for passing along arguments. The following loads the page foo.html with the arguments that you have set:

window.location = "foo.html" + object;
// the toString method is the standard method used
// when something is transformed into a string

*/
function ArgumentURL() {
	this.getArgument = _getArg;
	this.setArgument = _setArg;
	this.removeArgument = _removeArg;
	this.toString    = _toString;	//Allows the object to be printed
					//no need to write toString()
	this.arguments   = new Array();

	// Initiation
	var separator = "&";
	var equalsign = "=";
	
	var str = window.location.search.replace(/%20/g, " ");
	var index = str.indexOf("?");
	var sInfo;
	var infoArray = new Array();

	var tmp;
	
	if (index != -1) {
		sInfo = str.substring(index+1,str.length);
		infoArray = sInfo.split(separator);
	}

	for (var i=0; i<infoArray.length; i++) {
		tmp = infoArray[i].split(equalsign);
		if (tmp[0] != "") {
			var t = tmp[0];
			this.arguments[tmp[0]] = new Object();
			this.arguments[tmp[0]].value = tmp[1];
			this.arguments[tmp[0]].name = tmp[0];
		}
	}
	

	
	function _toString() {
		var s = "";
		var once = true;
		for (i in this.arguments) {
			if (once) {
				s += "?";
				once = false;
			}
			s += this.arguments[i].name;
			s += equalsign;
			s += this.arguments[i].value;
			s += separator;
		}
		return s.replace(/ /g, "%20");
	}
	
	function _getArg(name) {
		if (typeof(this.arguments[name].name) != "string")
			return null;
		else
			return this.arguments[name].value;
	}
	
	function _setArg(name,value) {
		this.arguments[name] = new Object()
		this.arguments[name].name = name;
		this.arguments[name].value = value;
	}
	
	function _removeArg(name) {
		this.arguments[name] = null;
	}
	
	return this;
}
