<?php
// e.g. for development test: http://new-host-7.home:8081/../SandBox/charityhound/dogparkchat.php?webSite=collogisticsSite
// e.g. for formal test: dogparkchat.php
// e.g. for production: dogparkchat.php?database=signup

require_once( "common.inc.php" );
session_start();
$account = 0;
$userFirstName = "guest";
if ($account == 0 && isset($_SESSION['member'])) {
	$member = $_SESSION['member'];
	if ($member != null) {
		$account = $member->getValue( "id" );
		$userFirstName = $member->getValue( "firstName" );
	}
}
$aggregateProvider = AGGREGATE_DSN;
$aggregateDatabase = AGGREGATE_DB_NAME;

$provider = isset( $_GET["provider"] ) ? preg_replace( "/[^ a-zA-Z.]/", "", $_GET["provider"] ) : $aggregateProvider;
$database = isset( $_GET["database"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["database"] ) : $aggregateDatabase;
$username = isset( $_GET["username"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["username"] ) : "guest";
//echo "dogparkchat provider $provider database $database userId $userId userFirstName $userFirstName";
if ($username === 'guest') {
	$username = $userFirstName;
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <!-- Include Firebase -->
  <script src="https://cdn.firebase.com/v0/firebase.js"></script>
  <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
   <script type="text/javascript" src="js/menu.js"></script>

  <!-- Include CodeMirror -->
  <!-- script src="codemirror/lib/codemirror.js"></script -->
  <!-- link rel="stylesheet" href="codemirror/lib/codemirror.css" / -->
  <!-- script src="js/codemirror.js"></script>
  <link rel="stylesheet" href="css/codemirror.css" / -->

  <!-- Include Firepad -->
  <!-- script src="firepad.js"></script -->
  <!-- link rel="stylesheet" href="firepad.css" / -->
  <!-- script src="js/firepad.js"></script>
  <link rel="stylesheet" href="css/firepad.css" / -->
   <link rel="stylesheet" type="text/css" href="css/navAccordionTheme.css">
  <link rel="stylesheet" href="css/dogparkchat.css" />
	
  <!-- Include example userlist. -->
  <!-- script src="firepad-userlist.js"></script -->
  <!-- link rel="stylesheet" href="firepad-userlist.css" / -->
  <script src="js/firepad-userlist.js"></script>
  <link rel="stylesheet" href="css/firepad-userlist.css" />

  <!-- Helper for generating URLs / Firebase references for example purposes. -->
  <!-- script src="helpers.js"></script -->
  <script src="js/helpers.js"></script>

  <style>
    html { height: 100%; }
    body { margin: 0; height: 100%; }
    /* Height / width / positioning can be customized for your use case.
       For demo purposes, we make the user list 175px and firepad fill the rest of the page. */
    .firepad-userlist {
      position: absolute; left: 0; top: 0; bottom: 0; height: auto;
      width: 175px;
    }
    /*
    .firepad {
      position: absolute; left: 175px; top: 0; bottom: 0; right: 0; height: auto;
    } */
    .dogparkgate {
      position: absolute; left: 185px; top: 0; bottom: 100px; right: 0; height: 100px;
    }
    .dogparkmessage {
      position: absolute; left: 185px; top: 100px; bottom: 175px; right: auto; height: 75px;
    }
    .dogparkchat {
      position: absolute; left: 185px; top: 180px; bottom: 0; right: 0; height: auto;
    }
  </style>
</head>

<body>
  <div id="userlist"></div>
  <!-- div id="firepad"></div -->
  <div class="dogparkgate">
  <br/>
<div class="iconControls">

<a href="javascript:newLocation('index', 'logistics')"><img src="images/home.gif"></a>

</div>

  Welcome to the Charity Hound "Dog Park".  A place where registered users can exchange ideas and opinions about
  nonprofits!  Please keep all chat discussions <em>civil</em>!  Opinions and observations are yours alone.
  </div>
    <input type='text' id='messageInput' class="dogparkmessage" placeholder='Message'>
    <div id='messagesDiv'  class="dogparkchat"></div>

  <script>
	var provider = <?php echo json_encode($provider); ?>;
	var database = <?php echo json_encode($database); ?>;
	var userName = <?php echo json_encode($username); ?>;
	var url = 'https://' + database + "." + provider;

	//// Initialize Firebase.
    // var firepadRef = new Firebase('<YOUR FIREBASE URL>');
    //var firepadRef = getExampleRef();
	var collogisticsRootRef = new Firebase(url);
	//alert("dogparkchat collogisticsRootRef " + collogisticsRootRef + " userId " + userId);
	var dogParkRef = collogisticsRootRef.child('dogpark');
	var dogParkChatRef = dogParkRef.child('messages');
	
    //// Create CodeMirror (with lineWrapping on).
    //var codeMirror = CodeMirror(document.getElementById('firepad'), { lineWrapping: true });

    // Create a random ID to use as our user ID (we must give this to firepad and FirepadUserList).
    var userId = Math.floor(Math.random() * 9999999999).toString();
 	//alert("dogparkchat userId " + userId);

    //// Create Firepad (with rich text features and our desired userId).
    //var firepad = Firepad.fromCodeMirror(firepadRef, codeMirror,
    //var firepad = Firepad.fromCodeMirror(dogParkRef, codeMirror,
        //{ richTextToolbar: true, richTextShortcuts: true, userId: userId});

    //// Create FirepadUserList (with our desired userId).
    //var firepadUserList = FirepadUserList.fromDiv(firepadRef.child('users'),
    var firepadUserList = FirepadUserList.fromDiv(dogParkRef.child('users'),
            document.getElementById('userlist'), userId, userName);

        //alert("dogparkchat userId " + userId + " changing Guest to " + userName);
        // e.g. dogparkchat userId 7812290464 changing Guest to Tom
	var dogParkUserNameRef = dogParkRef.child('users').child(userId);
	//alert("dogparkchat dogParkUserNameRef " + dogParkUserNameRef + " userId " + userId);
	// e.g. dogparkchat dogParkUserNameRef https://collogistics.firebaseio.com/dogpark/users/3175810970 userId 3175810970

    $('#messageInput').keypress(function (e) {
        if (e.keyCode == 13) {
            var text = $('#messageInput').val();
          dogParkUserNameRef.once('value', function(dataSnapshot) { 
            var name = dataSnapshot.child('name').val();
            //alert("dogparkchat messageInput name " + name + " text " + text);
            
              dogParkChatRef.push({name: name, text: text});
              $('#messageInput').val('');
        	  });
        }
      });

    function printChatMessage(name, text) {
        $('<div/>').text(text).prepend($('<em/>').text(name+': ')).appendTo($('#messagesDiv'));
        $('#messagesDiv')[0].scrollTop = $('#messagesDiv')[0].scrollHeight;
      };
      dogParkChatRef.on('child_added', function(snapshot) {
        var message = snapshot.val();
        printChatMessage(message.name, message.text);
      });

  </script>
</body>
</html>
