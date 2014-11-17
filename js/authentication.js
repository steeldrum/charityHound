
/*
 * (function (jQuery, Firebase, Path) {
    "use strict";

 */

var rootRef = null;
var authenticatedAccount = null;

function User(email, password) {
	this.email = email;
	this.password = password;
}

// tjs 141112
function UserProfile(domainName, domainId, handle, name) {
	this.domainName = domainName;
	this.DomainID = domainId;
	this.handle = handle;
	this.name = name;
}

// e,g, Object {email: "tandmsoucy@verizon.net", password: "tom123"} authentication.html:218

    // Handle Email/Password login
    // returns a promise
//tjs 14 1114 add , id, domain, name, handle, email, password, pageName, menuChoice
//function authWithPassword(userObj) {
    function authWithPassword(userObj, id, domain, name, handle, email, password, pageName, menuChoice) {
        //console.log("authentication authWithPassword userObj " + userObj);
        console.log("authentication authWithPassword user email " + userObj.email);
        var deferred = $.Deferred();
        //console.log("authentication authWithPassword userObj " + userObj);
        console.log("authentication authWithPassword rootRef " + rootRef);
        rootRef.authWithPassword(userObj, function onAuth(err, user) {
            if (err) {
                console.log("authentication authWithPassword reject err " + err);
                deferred.reject(err);
            }

            if (user) {
                // Load user info
                //userRef = rootRef.child('users').child(user.uid);
            	var userRef = rootRef.child('users').child(user.uid);
                //console.log("authentication authWithPassword user email " + user.email);
                console.log("authentication authWithPassword userRef " + userRef);
                console.log("authentication authWithPassword uid " + user.uid);
                userRef.once('value', function (snap) {
                    var authUser = snap.val();
                    if (!authUser) {
                        console.log("authentication authWithPassword NO user info!");
                        // tjs 141115
                        var userProfile = new UserProfile(domain, id, handle, name);
                        userRef.set(userProfile, function onComplete() {
                            console.log("authentication authWithPassword registration succeeded and profile updated!");                    
                            // route
                            //routeTo(route);
                            newLocation(pageName, menuChoice);
                       });
                        // tjs 141114
                       //return;
                        //var rejectErr = "NO user!";
                        //deferred.reject(rejectErr);
                        //var registerPromise = createUserAndLogin(userObj);                        
                        //var registerPromise = createUserAndLogin(userObj, id, domain, name, handle, email, password, pageName, menuChoice);                        
                        //handleRegisterResponse(registerPromise, id, domain, name, handle, email, password, pageName, menuChoice);
                        return;
                    }
                    // the fields
                    console.log("authentication authWithPassword name " + authUser.name);                    
                    console.log("authentication authWithPassword handle " + authUser.handle);
                    console.log("authentication authWithPassword domainID " + authUser.DomainID);
                    deferred.resolve(user);
               });
               // deferred.resolve(user);
            }

        });
        console.log("authentication authWithPassword promise...");

        return deferred.promise();
    }

    // create a user but not login
    // returns a promsie
    function createUser(userObj) {
        var deferred = $.Deferred();
        rootRef.createUser(userObj, function (err) {

            if (!err) {
                console.log("authentication createUser...");
                deferred.resolve();
            } else {
                console.log("authentication createUser err " + err);
               deferred.reject(err);
            }

        });

        return deferred.promise();
    }

    // Create a user and then login in
    // returns a promise
    //function createUserAndLogin(userObj) {
    function createUserAndLogin(userObj, id, domain, name, handle, email, password, pageName, menuChoice) {
        return createUser(userObj)
            .then(function () {
                console.log("createUserAndLogin created User...");
                //return authWithPassword(userObj);
                //return authWithPassword(userObj, id, domain, name, handle, email, password, pageName, menuChoice);
               var registerPromise = authWithPassword(userObj, id, domain, name, handle, email, password, pageName, menuChoice);
               handleRegisterAuthResponse(registerPromise, id, domain, name, handle, email, password, pageName, menuChoice);
            return;
        });
    }

    // route to the specified route if sucessful
    // if there is an error, show the alert
    // pageName, menuChoice);
    
    //newLocation(pageName, menuChoice);
    // tjs 141112
    //       handleAuthResponse(loginPromise, id, domain, name, handle, email, password, pageName, menuChoice);
    //function handleAuthResponse(promise, id, pageName, menuChoice) {
    function handleAuthResponse(promise, id, domain, name, handle, email, password, pageName, menuChoice) {
    	console.log("authentication handleAuthResponse pageName " + pageName + " id " + id);
        $.when(promise)
            .then(function (authData) {
            	
            	authenticatedAccount = id;
            	console.log("authentication authenticatedAccount " + authenticatedAccount);

           // route
            //routeTo(route);
            newLocation(pageName, menuChoice);

        }, function (err) {
            console.log("handleAuthResponse err " + err);
            // tjs 141115
            var userAndPass = new User(email, password.trim());
            createUserAndLogin(userAndPass, id, domain, name, handle, email, password, pageName, menuChoice);

            /*
            // tjs 141112 - must first register as a new user, then login...
            var userAndPass = new User(email, password.trim());
            //var registerPromise = createUserAndLogin(userAndPass);
            var registerPromise = createUserAndLogin(userAndPass, id, domain, name, handle, email, password, pageName, menuChoice);
 
            handleRegisterResponse(registerPromise, id, domain, name, handle, email, password, pageName, menuChoice);
*/
            // pop up error
            /*showAlert({
                title: err.code,
                detail: err.message,
                className: 'alert-danger'
            });*/

        });
    }
/*
    function handleRegisterResponse(promise, id, domain, name, handle, email, password, pageName, menuChoice) {
    	console.log("authentication handleRegisterResponse pageName " + pageName + " id " + id);
        $.when(promise)
            .then(function (authData) {
            	
            	authenticatedAccount = id;
            	console.log("authentication handleRegisterResponse authenticatedAccount " + authenticatedAccount);
                // Get the current newly registered user
                var user = rootRef.getAuth();
                // If no current user then error...
                if (!user) {
                    console.log("registration failed!");
                    return;
                }

                var userRef;
                // Load user info
                userRef = rootRef.child('users').child(user.uid);
                var userProfile = new UserProfile(domain, id, handle, name);
                userRef.set(userProfile, function onComplete() {
                    console.log("registration succeeded and profile updated!");                    
                    // route
                    //routeTo(route);
                    newLocation(pageName, menuChoice);
               });
           // route
            //routeTo(route);
            //newLocation(pageName, menuChoice);

        }, function (err) {
            console.log("authentication handleRegisterResponse err " + err);
            // pop up error

        });
    }*/

    function handleRegisterAuthResponse(promise, id, domain, name, handle, email, password, pageName, menuChoice) {
    	console.log("authentication handleRegisterAuthResponse pageName " + pageName + " id " + id);
        $.when(promise)
            .then(function (authData) {
            	
            	authenticatedAccount = id;
            	console.log("authentication handleRegisterAuthResponse authenticatedAccount " + authenticatedAccount);
                // Get the current newly registered user
                var user = rootRef.getAuth();
                // If no current user then error...
                if (!user) {
                    console.log("registration failed!");
                    return;
                }

                var userRef;
                // Load user info
                userRef = rootRef.child('users').child(user.uid);
                var userProfile = new UserProfile(domain, id, handle, name);
                userRef.set(userProfile, function onComplete() {
                    console.log("handleRegisterAuthResponse registration succeeded and profile updated!");                    
                    // route
                    //routeTo(route);
                    newLocation(pageName, menuChoice);
               });
           // route
            //routeTo(route);
            //newLocation(pageName, menuChoice);

        }, function (err) {
            console.log("authentication handleRegisterAuthResponse err " + err);
            // pop up error
            /*showAlert({
                title: err.code,
                detail: err.message,
                className: 'alert-danger'
            });*/

        });
    }
