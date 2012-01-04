/* collogistics.js
tjs 100928
file version 1.00 
release version 1.00
*/

function showResults() {
	//alert("showResults");
	var allLikes = $('#likes div');
	var allDisLikes = $('#dislikes div');
	//var allLikes = $('#likes');
	//var allDisLikes = $('#dislikes');
	var likesLen = allLikes.length;
	var disLikesLen = allDisLikes.length;
	if (likesLen != disLikesLen) {
	alert("Be sure to leave four selections in each list!");
	return;
	}
		//alert("showResults likesLen " + likesLen);
	//var allLikesScores = $('#likes div[score]');
	//var allDisLikesScores = $('#dislikes div[score]');
	/*
	var allLikesScores = $('#likes div.attr(score)');
	var allDisLikesScores = $('#dislikes div.attr(score)');
	var allLikesScoresLen = allLikesScores.length;
	var allDisLikesScoresLen = allDisLikesScores.length;
	for (var i = 0; i < allLikesScoresLen; i++) {
		//alert("showResults likes " + i + " score " + allLikesScores[i]);
		alert("showResults likes " + i + " score " + allLikesScores.get(i));
	}*/
	/*
	for (var i = 0; i < likesLen; i++) {
		var like = allLikes[i];
		//NOK
		var score = like.score;
		alert("showResults likes " + i + " score " + score);
		//var score = like.attr('score');
		//alert("showResults likes " + i + " score " + score);
		//works
		//var item = like.innerHTML;
		//alert("showResults likes " + i + " item " + item);
	}*/
	//var allLikesScores = $('#likes div[score=1])');
	//var allDisLikesScores = $('#dislikes div[score=1]');
	//var allLikesScoresLen = allLikesScores.length;
	//var allDisLikesScoresLen = allDisLikesScores.length;
	//	alert("showResults allLikesScoresLen " + allLikesScoresLen);
	/*
	for (var i = 0; i < likesLen; i++) {
		//var like = allLikes[i];
		//var score = allLikes[i].get(0).score;
		var score = allLikes[i].score;
		alert("showResults likes " + i + " score " + score);
		//var score = like.attr('score');
		//alert("showResults likes " + i + " score " + score);
		//works
		//var item = like.innerHTML;
		//alert("showResults likes " + i + " item " + item);
	}*/
		//var allLikesScores = $('#likes div[score])');
		//var allLikesScores = $('#likes div[title])');
		//var allLikesScores = $('#likes div').attr('title');
	//var allDisLikesScores = $('#dislikes div[score=1]');
	//var allLikesScoresLen = allLikesScores.length;
	//var allDisLikesScoresLen = allDisLikesScores.length;
		//alert("showResults allLikesScoresLen " + allLikesScoresLen);
//var allLikesScores = $('#likes').find('div').attr('title','1');
/*
var allLikesScores = $('#likes').find('div').attr('title');
var allLikesScoresLen = allLikesScores.length;
alert("showResults allLikesScoresLen " + allLikesScoresLen);
var allDisLikesScores = $('#dislikes').find('div').attr('title');
var allDisLikesScoresLen = allDisLikesScores.length;
alert("showResults allDisLikesScoresLen " + allDisLikesScoresLen);
*/
var score = 0;
	for (var i = 0; i < likesLen; i++) {
		var like = allLikes[i];
		//var score = allLikes[i].get(0).score;
		//var score = allLikes[i].score;
		//alert("showResults likes " + i + " score " + score);
		//var score = like.attr('score');
		//alert("showResults likes " + i + " score " + score);
		//works
		var item = like.innerHTML;
		//alert("showResults likes " + i + " item " + item);
		if (item == 'Delivery that Works Reliably' ||
			item == 'Group Interactions' ||
		item == 'Customer Collaboration' ||
		item == 'Adaptibity to Change')
			++score;
	}
			//alert("showResults score " + score);
			var message = "Your list arrangement is indicative of traditional unified processes for development";
			if (score == 4)
			    message = "Your list arrangement is indicative of full usage of agile processes for development";
		    else if (score == 3)
			    message = "Your list arrangement is indicative of some usage of agile processes for development";
		    else if (score == 2)
			    message = "Your list arrangement is indicative of little usage of agile processes for development";
		    else if (score == 1)
			    message = "Your list arrangement is indicative of somewhat traditional unified processes for development";
		    displayResults(score, message);

		return;

}

function displayResults(score, message) {
	var result = "The test score is " + score + " out of 4. " + message;
	//alert("displayResults result " + result);
	//$('#testResults').innerHTML = result;
	var node = $('#testResults').get(0);
	node.innerHTML = result;
	return;
}

function IsThisBrowserIE6() {
    return ((window.XMLHttpRequest == undefined) && (ActiveXObject != undefined))
}

