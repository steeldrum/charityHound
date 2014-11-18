//globals used for account management
var loginAccountNumber = 0;
var lastName = "collaborator";
var firstName = "demo";
// tjs 130307
var aggregateProvider;
var aggregateDatabase;
// tjs 131031
var authenticationProvider;
// tjs 141102
var authRef;

// tjs 130329
var OPENINGS_SIZE = 10;
var numberOfRows = 0;
var tableHeaderRendered = false;

// Create our Firebase reference
// tjs 130401 - use signup database for this...
var signupRootRef = new Firebase('https://signup.firebaseio.com');
var signupSiteRef = signupRootRef.child('collogisticsSite');
var signupOpeningsRef = signupSiteRef.child('Openings');

// Keep a mapping of firebase locations to HTML elements, so we can move /
// remove elements as necessary.
// var htmlForPath = {};

function handleOpeningAdded(dataSnapshot, openingDescription, numberOfSignees) {
	var openingName = dataSnapshot.name();
	// alert("handleOpeningAdded openingSnapshot.val().name() " +
	// openingSnapshot.val().name());
	if (!tableHeaderRendered) {
		tableHeaderRendered = true;
		var newHeaderRow = "<tr><th>PositionName</th><th>Description</th>";
		if (isAdminUser) {
			newHeaderRow += "<th>Signees</th>";
		} else {
			newHeaderRow += "<th>Details</th>";
		}
		newHeaderRow += "</tr>";
		$("#openingsTable").append(newHeaderRow);
	}
	var newOpeningRow = $("<tr/>");
	newOpeningRow.append($("<td/>").append($("<em/>").text(openingName)));
	newOpeningRow.append($("<td/>").text(openingDescription));
	if (isAdminUser) {
		newOpeningRow.append($("<td/>").text(numberOfSignees));
	} else {
		var td = '<td><button onclick="viewOpeningDetails(' + "'"
				+ dataSnapshot.ref().toString() + "'"
				+ ');">Details</button></td>';
		// alert("handleOpeningAdded td " + td);
		newOpeningRow.append($(td));
	}
	$("#openingsTable").append(newOpeningRow);
}

// Helper function to handle a score object being removed; just removes the
// corresponding table row.
function handleOpeningRemoved(openingSnapshot) {
	var removedOpeningRow = htmlForPath[openingSnapshot.name()];
	removedOpeningRow.remove();
	// delete htmlForPath[openingSnapshot.name()];
}

// Add a callback to handle when a score changes or moves positions.
var changedCallback = function(openingSnapshot, prevOpeningName) {
	handleOpeningRemoved(openingSnapshot);
	handleOpeningAdded(openingSnapshot, prevOpeningName);
};

// tjs 130403
var isAdminUser = false;
var currentOpeningRef = null;
// function viewOpeningDetails(openingName) {
function viewOpeningDetails(openingRef) {
	// alert("viewOpeningDetails openingRef " + openingRef);
	currentOpeningRef = openingRef;
	$("#signupDialog").dialog("open");
}
function refreshSignee(signeesRef, id, name, phone, email) {
	// alert("refreshSignee signeesRef " + signeesRef + " id " + id + " name " +
	// name + " phone " + phone + " email " + email);
	// e.g. refreshSignee signeesRef
	// https://collogistics.firebaseio.com/collogisticsSite/collaboratorManagement/collaboratorsMaintenance/signees
	// id -Iqqg6XrbzGBZ8kcXva6 name Thomas J. Soucy phone 781 599-8014 email
	// tsoucy@me.com
	signeesRef.child(id).child('id').set(id);
	signeesRef.child(id).child('name').set(name);
	signeesRef.child(id).child('phone').set(phone);
	signeesRef.child(id).child('email').set(email);
	$("#signupDialog").dialog("close");
}

function processArgs(account) {
	// setAuthenticated();
	var authenticated = account > 0 ? true : false;
	// alert("index.html processArgs authenticated " + authenticated);
	// alert("index.php processArgs authenticated " + authenticated + " account
	// " + account);
	// console.log("index.html processArgs authenticated " + authenticated + "
	// account " + account);
	// e.g. true 1
	var elm;
	if (authenticated == true) {
		// alert("index.php processArgs disable login, enable logout");
		elm = $('#login').get(0);
		if (elm) {
			elm.disabled = "disabled";
		}
		elm = $('#logout').get(0);
		if (elm) {
			elm.disabled = "";
		}
		// tjs 130412
		elm = $('#dogparkchat').get(0);
		if (elm) {
			elm.disabled = "";
		}
	} else {
		// alert("index.html processArgs disable logout, enable login");
		elm = $('#logout').get(0);
		if (elm) {
			elm.disabled = "disabled";
		}
		elm = $('#login').get(0);
		if (elm) {
			elm.disabled = "";
		}
		// tjs 130412
		elm = $('#dogparkchat').get(0);
		if (elm) {
			elm.disabled = "disabled";
		}
	}
	// alert("index.php admin?");
	// tjs 140206
	// enableOrDisableScheduledDisplayAds();
	enableOrDisableSiteAdmin();
	// alert("index.php admin or not established")
}

function doLogin() {
	// tjs 131031
	// TODO security leak re account showing...
	// tjs 131101
	// alert("index doLogin authenticationProvider " + authenticationProvider);
	// e.g. index doLogin authenticationProvider http://localhost:3000
	// getLogin(authenticationProvider);
	window.location.href = "login.php";
}

function doLogout() {
	// tjs 141102
	authRef.unauth();
	window.location.href = "logout.php";
}

// tjs 130412
function doChat() {
	window.location.href = "dogparkchat.php";
}

function doSiteAdmin() {
	window.location.href = "admin.php";
}

function doScheduleDisplayAd() {
	window.location.href = "adManager.php";
}

// tjs 111027
function doRegister() {
	// alert("index doRegister");
	$("#charityHoundRegisterDialog").dialog("open");
}

function processRegisterForm(token, username, password1, password2,
		emailAddress, firstName, lastName, gender, passwordMnemonicQuestion,
		passwordMnemonicAnswer) {
	// alert("charityhound processRegisterForm token " + token + " username " +
	// username + " emailAddress " + emailAddress + " firstName " + firstName +
	// " lastName " + lastName + " gender " + gender + " password1 " +
	// password1);
	$.ajax({
		type : "POST",
		url : "charityhoundRegister.php",
		data : {
			"token" : token,
			"username" : username,
			"password1" : password1,
			"password2" : password2,
			"emailAddress" : emailAddress,
			"firstName" : firstName,
			"lastName" : lastName,
			"gender" : gender,
			"passwordMnemonicQuestion" : passwordMnemonicQuestion,
			"passwordMnemonicAnswer" : passwordMnemonicAnswer
		},
		success : function(msg) {
			// alert("charityhound processRegisterForm success msg " + msg + "
			// len " + msg.length);
			var tempMsg = msg;
			var success = false;
			var duplicateUserNameError = false;
			var duplicateEMailError = false;
			var tokenMisMatchError = false;

			// e.g. $registerInfo = '["registerInfo",
			// {"success":"'.$success.'","missingFieldsError":"'.$missingFieldsError.'","passwordError":"'.$passwordError.'","duplicateUserNameError":"'.$duplicateUserNameError.'","duplicateEMailError":"'.$duplicateEMailError.'","$registrationTokenMisMatchError":"'.$registrationTokenMisMatchError.'"}]';
			JSON.parse(tempMsg, function(key, value) {
				// alert("charityhound processRegisterForm key " + key + " value
				// " + value);
				if (key == 'success') {
					success = ('ok' == value);
				} else if (key == 'duplicateUserNameError') {
					duplicateUserNameError = ('nok' == value);
				} else if (key == 'duplicateEMailError') {
					duplicateEMailError = ('nok' == value);
				} else if (key == 'registrationTokenMisMatchError') {
					tokenMisMatchError = ('nok' == value);
				}
			});
			// alert("charityhound processRegisterForm loginInfo.id " +
			// loginInfo.id + " loginInfo.userName " + loginInfo.userName + "
			// loginInfo.firstName " + loginInfo.firstName + "
			// loginInfo.lastName " + loginInfo.lastName);
			if (success) {
				// alert("charityhound processRegisterForm success closing
				// dialog...");
				$("#charityHoundRegisterDialog").dialog("close");
			} else {
				// alert("charityhound processRegisterForm
				// duplicateUserNameError " + duplicateUserNameError + "
				// duplicateEMailError " + duplicateEMailError + "
				// tokenMisMatchError " + tokenMisMatchError);
				if (duplicateUserNameError) {
					$("label#submit_error")
							.text('The username already exists!').show();
					$("input#username").focus();
				} else if (duplicateEMailError) {
					$("label#submit_error").text(
							'The email address already exists!').show();
					$("input#emailAddress").focus();
				} else if (tokenMisMatchError) {
					$("label#submit_error").text(
							'The invitation token is incorrect!').show();
					$("input#token").focus();
				}
			}
		}
	});
	// alert("charityhound processRegisterForm called ajax...");
	return false;
} // end processRegisterForm

// tjs 141031
function authenticateUserForDomain(provider, database, domain, name, handle,
		email, password, id, pageName, menuChoice) {
	console.log("menu authenticateUserForDomain provider " + provider
			+ " database " + database + " domain " + domain + " name " + name
			+ " handle " + handle + " email " + email + " password " + password
			+ " id " + id);

	var aggregateDB = "collogistics";
	// var aggregateDB = "docs-sandbox";
	// var signupReferenceURL = "https://" + aggregateDB + "." + aggregateDSN +
	// "/web/uauth";
	var authenticationReferenceURL = "https://" + database + "." + provider;
	console.log("menu authenticateUserForDomain url "
			+ authenticationReferenceURL);
	// temp disable

	var authRef = new Firebase(authenticationReferenceURL);
	console.log("authRef " + authRef);
	rootRef = authRef;

	// var user = new User(email, password);
	// var userAndPass = new User(email, password);
	var userAndPass = new User(email, password.trim());
	// var loginPromise = authWithPassword(userAndPass);
	var loginPromise = authWithPassword(userAndPass, id, domain, name, handle,
			email, password, pageName, menuChoice);

	// tjs 141112
	// handleAuthResponse(loginPromise, id, pageName, menuChoice);
	handleAuthResponse(loginPromise, id, domain, name, handle, email, password,
			pageName, menuChoice);

	// temp enable
	// newLocation(pageName, menuChoice);
}

// tjs 141118
function refreshLoginAccountNumber() {
	var account = 0;
	aggregateProvider = "firebaseIO.com";
	aggregateDatabase = 'collogistics';
	var authenticationReferenceURL = "https://" + aggregateDatabase + "."
			+ aggregateProvider;
	console.log("globals refreshLoginAccountNumber url "
			+ authenticationReferenceURL);
	// var authRef = new Firebase(
	authRef = new Firebase(authenticationReferenceURL);
	console.log("globals refreshLoginAccountNumber authRef " + authRef);
	// rootRef = authRef;

	var user = authRef.getAuth();

	// If current user...
	if (user) {
		var userRef = authRef.child('users').child(user.uid);
		// console.log("authentication authWithPassword user email " +
		// user.email);
		console.log("globals refreshLoginAccountNumber userRef " + userRef);
		console.log("globals refreshLoginAccountNumber uid " + user.uid);
		userRef
				.once(
						'value',
						function(snap) {
							var authUser = snap.val();
							if (!authUser) {
								console
										.log("globals refreshLoginAccountNumber NO user!");
							} else {
								// the fields
								console
										.log("globals refreshLoginAccountNumber name "
												+ authUser.name);
								console
										.log("globals refreshLoginAccountNumber handle "
												+ authUser.handle);
								console
										.log("globals refreshLoginAccountNumber domainID "
												+ authUser.DomainID);
								account = authUser.DomainID;
							}
							console
									.log("globals refreshLoginAccountNumber account "
											+ account);
							loginAccountNumber = account;
						});
	} else {
		console.log("globals refreshLoginAccountNumber no user account "
				+ account);
		// processArgs(account);
	}
	console.log("globals refreshLoginAccountNumber loginAccountNumber "
			+ loginAccountNumber);
}

function refreshLoginAccountNumberAndCharities(torfLoggedIn, torfDetail) {
	var loggedIn = false;
	if (torfLoggedIn != null && torfLoggedIn == 'true') {
		loggedIn = true;
	}
	if (loggedIn) {
		enforceOrForceLogin(torfLoggedIn, torfDetail);
/*		
		var account = 0;
		aggregateProvider = "firebaseIO.com";
		aggregateDatabase = 'collogistics';
		var authenticationReferenceURL = "https://" + aggregateDatabase + "."
				+ aggregateProvider;
		console.log("globals refreshLoginAccountNumberAndCharities url "
				+ authenticationReferenceURL);
		// var authRef = new Firebase(
		authRef = new Firebase(authenticationReferenceURL);
		console.log("globals refreshLoginAccountNumberAndCharities authRef " + authRef);
		// rootRef = authRef;
	
		var user = authRef.getAuth();
	
		// If current user...
		if (user) {
			var userRef = authRef.child('users').child(user.uid);
			// console.log("authentication authWithPassword user email " +
			// user.email);
			console.log("globals refreshLoginAccountNumberAndCharities userRef " + userRef);
			console.log("globals refreshLoginAccountNumberAndCharities uid " + user.uid);
			userRef
					.once(
							'value',
							function(snap) {
								var authUser = snap.val();
								if (!authUser) {
									console
											.log("globals refreshLoginAccountNumberAndCharities NO user!");
									refreshCharities(torfLoggedIn, torfDetail);
								} else {
									// the fields
									console
											.log("globals refreshLoginAccountNumberAndCharities name "
													+ authUser.name);
									console
											.log("globals refreshLoginAccountNumberAndCharities handle "
													+ authUser.handle);
									console
											.log("globals refreshLoginAccountNumberAndCharities domainID "
													+ authUser.DomainID);
									account = authUser.DomainID;
									console
									.log("globals refreshLoginAccountNumberAndCharities account "
											+ account);
									loginAccountNumber = account;
									refreshCharities(torfLoggedIn, torfDetail);
								}
							});
		} else {
			console.log("globals refreshLoginAccountNumberAndCharities no user account "
					+ account);
			refreshCharities(torfLoggedIn, torfDetail);
			// processArgs(account);
		}
		*/
	} else {
		// tjs 141118
		console.log("globals refreshLoginAccountNumberAndCharities not logged in!");
		//refreshCharities(torfLoggedIn, torfDetail);
		// though not logged in (perhaps due to race condition)...check if authorized to log in!
		
	}
	//console.log("globals refreshLoginAccountNumber loginAccountNumber "
	//		+ loginAccountNumber);
}

function enforceOrForceLogin(torfLoggedIn, torfDetail) {
	var account = 0;
	aggregateProvider = "firebaseIO.com";
	aggregateDatabase = 'collogistics';
	var authenticationReferenceURL = "https://" + aggregateDatabase + "."
			+ aggregateProvider;
	console.log("globals refreshLoginAccountNumberAndCharities url "
			+ authenticationReferenceURL);
	// var authRef = new Firebase(
	authRef = new Firebase(authenticationReferenceURL);
	console.log("globals refreshLoginAccountNumberAndCharities authRef " + authRef);
	// rootRef = authRef;

	var user = authRef.getAuth();

	// If current user...
	if (user) {
		var userRef = authRef.child('users').child(user.uid);
		// console.log("authentication authWithPassword user email " +
		// user.email);
		console.log("globals refreshLoginAccountNumberAndCharities userRef " + userRef);
		console.log("globals refreshLoginAccountNumberAndCharities uid " + user.uid);
		userRef
				.once(
						'value',
						function(snap) {
							var authUser = snap.val();
							if (!authUser) {
								console
										.log("globals refreshLoginAccountNumberAndCharities NO user!");
								refreshCharities(torfLoggedIn, torfDetail);
							} else {
								// the fields
								console
										.log("globals refreshLoginAccountNumberAndCharities name "
												+ authUser.name);
								console
										.log("globals refreshLoginAccountNumberAndCharities handle "
												+ authUser.handle);
								console
										.log("globals refreshLoginAccountNumberAndCharities domainID "
												+ authUser.DomainID);
								account = authUser.DomainID;
								console
								.log("globals refreshLoginAccountNumberAndCharities account "
										+ account);
								loginAccountNumber = account;
								refreshCharities(torfLoggedIn, torfDetail);
							}
						});
	} else {
		console.log("globals refreshLoginAccountNumberAndCharities no user account "
				+ account);
		refreshCharities(torfLoggedIn, torfDetail);
		// processArgs(account);
	}	
}