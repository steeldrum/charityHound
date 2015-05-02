/*
 * spa.model.js
 * Model module
*/

/*jslint         browser : true, continue : true,
  devel  : true, indent  : 2,    maxerr   : 50,
  newcap : true, nomen   : true, plusplus : true,
  regexp : true, sloppy  : true, vars     : false,
  white  : true
*/
/*global TAFFY, $, spa */

spa.model = (function () {
  'use strict';
  var
    configMap = { anon_id : 'a0' },
    stateMap  = {
      anon_user      : null,
      cid_serial     : 0,
      is_connected   : false,
      people_cid_map : {},
      people_db      : TAFFY(),
      user           : null
    },

    isFakeData = false,

    personProto, makeCid, clearPeopleDb, completeLogin,
    // tjs 131018
    completeProfile,
    // tjs 131022
    loginProfiledUser,
    makePerson, removePerson, people, chat, initModule;

  // The people object API
  // ---------------------
  // The people object is available at spa.model.people.
  // The people object provides methods and events to manage
  // a collection of person objects. Its public methods include:
  //   * get_user() - return the current user person object.
  //     If the current user is not signed-in, an anonymous person
  //     object is returned.
  //   * get_db() - return the TaffyDB database of all the person
  //     objects - including the current user - presorted.
  //   * get_by_cid( <client_id> ) - return a person object with
  //     provided unique id.
  //   * login( <user_name> ) - login as the user with the provided
  //     user name. The current user object is changed to reflect
  //     the new identity. Successful completion of login
  //     publishes a 'spa-login' global custom event.
  //   * logout()- revert the current user object to anonymous.
  //     This method publishes a 'spa-logout' global custom event.
  //
  // jQuery global custom events published by the object include:
  //   * spa-login - This is published when a user login process
  //     completes. The updated user object is provided as data.
  //   * spa-logout - This is published when a logout completes.
  //     The former user object is provided as data.
  //
  // Each person is represented by a person object.
  // Person objects provide the following methods:
  //   * get_is_user() - return true if object is the current user
  //   * get_is_anon() - return true if object is anonymous
  //
  // The attributes for a person object include:
  //   * cid - string client id. This is always defined, and
  //     is only different from the id attribute
  //     if the client data is not synced with the backend.
  //   * id - the unique id. This may be undefined if the
  //     object is not synced with the backend.
  //   * name - the string name of the user.
  //   * css_map - a map of attributes used for avatar
  //     presentation.
  //
  personProto = {
    get_is_user : function () {
      return this.cid === stateMap.user.cid;
    },
    get_is_anon : function () {
      return this.cid === stateMap.anon_user.cid;
    }
  };

  makeCid = function () {
    return 'c' + String( stateMap.cid_serial++ );
  };

  clearPeopleDb = function () {
    var user = stateMap.user;
    stateMap.people_db      = TAFFY();
    stateMap.people_cid_map = {};
    if ( user ) {
      stateMap.people_db.insert( user );
      stateMap.people_cid_map[ user.cid ] = user;
    }
  };

  completeLogin = function ( user_list ) {
    var user_map = user_list[ 0 ];
    delete stateMap.people_cid_map[ user_map.cid ];
    stateMap.user.cid     = user_map._id;
    stateMap.user.id      = user_map._id;
    stateMap.user.css_map = user_map.css_map;
    stateMap.people_cid_map[ user_map._id ] = stateMap.user;
    // tjs 131017 - maybe later
    chat.join();
    $.gevent.publish( 'spa-login', [ stateMap.user ] );
  };

  // tjs 131018
  completeProfile = function ( user_map ) {
	    $.gevent.publish( 'spa-profileMissing', [ user_map ] );
	  };

	  // tjs 131022
	  //loginProfiledUser = function ( user_map ) {
	  loginProfiledUser = function ( map_list ) {
		  var user_map, user_email, user_password, user_name, user_handle, user_strict_logger, isStrictLogger;
		  user_map = map_list[0];
		  user_email = user_map._id;
		  user_password = user_map.password;
		  user_name = user_map.name;
		  user_handle = user_map.handle;
		  isStrictLogger = user_map.isStrictLogger;
		  user_strict_logger = isStrictLogger? "true" : "false";
		  // tjs 131022
		  //alert("model loginProfiledUser login name " + user_name + " email " + user_email + " password " + user_password + " handle " + user_handle + " isStrictLogger? " + isStrictLogger);
	      spa.model.people.login( user_name, user_email, user_password, user_handle, user_strict_logger );
		  };

  makePerson = function ( person_map ) {
    var person,
      cid     = person_map.cid,
      // tjs 131017 e.g. email
      css_map = person_map.css_map,
      id      = person_map.id,
      // tjs 131017
      //email      = person_map.email,
      password      = person_map.password,
      handle      = person_map.handle,
      isStrictLogger      = person_map.isStrictLogger,
      name    = person_map.name;

    if ( cid === undefined || ! name ) {
      throw 'client id and name required';
    }

// tjs 131009
//alert("model makePerson name " + name);
// e.g. model makePerson name anonymous

    person         = Object.create( personProto );
    // tjs 131017 e.g. email
    person.cid     = cid;
    // tjs 131017
    //person.email    = email;
    person.password    = password;
    person.handle    = handle;
    person.isStrictLogger    = isStrictLogger;
    person.name    = name;
    person.css_map = css_map;

    if ( id ) { person.id = id; }

    stateMap.people_cid_map[ cid ] = person;

    stateMap.people_db.insert( person );
    return person;
  };

  removePerson = function ( person ) {
    if ( ! person ) { return false; }
    // cannot remove anonymous person
    if ( person.id === configMap.anon_id ) {
      return false;
    }

    stateMap.people_db({ cid : person.cid }).remove();
    if ( person.cid ) {
      delete stateMap.people_cid_map[ person.cid ];
    }
    return true;
  };

  people = (function () {
	  // tjs 131021
    var get_by_cid, get_db, get_user, get_user_profile, login, logout;

    get_by_cid = function ( cid ) {
      return stateMap.people_cid_map[ cid ];
    };

    get_db = function () { return stateMap.people_db; };

// tjs 131009
    //get_user = function () { return stateMap.user; };
    get_user = function () {
    //alert("model people get_user..." );
     return stateMap.user;
      };

      // tjs 131017
     // login = function ( name ) {
    login = function ( name, email, password, handle, isStrictLogger ) {
    // tjs 131009
    //alert("model people login isFakeData " + isFakeData);
    // e.g. model people login isFakeData false
    	// tjs 131021
    	//alert("model people login name " + name + " email " + email + " password " + password + " handle " + handle + " isStrictLogger? " + isStrictLogger);
    	 // e.g. model people login name Test1 email test1@test.com password pass1 handle Handle1 isStrictLogger? true
      var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
      var loggerType = isStrictLogger == "true"? true : false;

      stateMap.user = makePerson({
    	  // tjs 131017
          //cid     : makeCid(),
        cid     : email,
        css_map : {top : 25, left : 25, 'background-color':'#8f8'},
        // tjs 131017
        //email	: email,
        password	: password,
        handle	: handle,
        isStrictLogger	: loggerType,
        name    : name
      });

      sio.on( 'userupdate', completeLogin );

  	//alert("model people stateMap login name " + stateMap.user.name + " cid(email) " + stateMap.user.cid + " password " + stateMap.user.password + " handle " + stateMap.user.handle + " isStrictLogger? " + stateMap.user.isStrictLogger);
 // e.g. model people stateMap login name Test1 cid(email) test1@test.com password pass1 handle Handle1 isStrictLogger? true

      sio.emit( 'adduser', {
        cid     : stateMap.user.cid,
        css_map : stateMap.user.css_map,
        // tjs 131017
        //email	: stateMap.user.email,
        password	: stateMap.user.password,
        handle	: stateMap.user.handle,
        isStrictLogger	: stateMap.user.isStrictLogger,
        name    : stateMap.user.name
      });
    };

    logout = function () {
      var user = stateMap.user;

      chat._leave();
      stateMap.user = stateMap.anon_user;
      clearPeopleDb();

      $.gevent.publish( 'spa-logout', [ user ] );
    };

    // tjs 131018
    get_user_profile = function (email, password) {
    	// tjs 131021
        var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
   	//alert("model people get_user_profile for " + email);
    	// e.g. model people get_user_profile for test1@test.com
        sio.on( 'usernotprofiled', completeProfile );
        sio.on( 'userprofiled', loginProfiledUser );

        sio.emit( 'profileuser', {
           // cid     : stateMap.user.cid,
         cid     : email,
          //css_map : stateMap.user.css_map,
          // tjs 131017
          //email	: stateMap.user.email,
         //password	: stateMap.user.password,
          password	: password
          //handle	: stateMap.user.handle,
          //isStrictLogger	: stateMap.user.isStrictLogger,
          //name    : stateMap.user.name
        });

      }; 

      return {
          get_by_cid : get_by_cid,
          get_db     : get_db,
          get_user   : get_user,
          // tjs 1310129
          get_user_profile   : get_user_profile,
          login      : login,
          logout     : logout
        };
      }());

  // The chat object API
  // -------------------
  // The chat object is available at spa.model.chat.
  // The chat object provides methods and events to manage
  // chat messaging. Its public methods include:
  //  * join() - joins the chat room. This routine sets up
  //    the chat protocol with the backend including publishers
  //    for 'spa-listchange' and 'spa-updatechat' global
  //    custom events. If the current user is anonymous,
  //    join() aborts and returns false.
  //  * get_chatee() - return the person object with whom the user
  //    is chatting with. If there is no chatee, null is returned.
  //  * set_chatee( <person_id> ) - set the chatee to the person
  //    identified by person_id. If the person_id does not exist
  //    in the people list, the chatee is set to null. If the
  //    person requested is already the chatee, it returns false.
  //    It publishes a 'spa-setchatee' global custom event.
  //  * send_msg( <msg_text> ) - send a message to the chatee.
  //    It publishes a 'spa-updatechat' global custom event.
  //    If the user is anonymous or the chatee is null, it
  //    aborts and returns false.
  //  * update_avatar( <update_avtr_map> ) - send the
  //    update_avtr_map to the backend. This results in an
  //    'spa-listchange' event which publishes the updated
  //    people list and avatar information (the css_map in the
  //    person objects). The update_avtr_map must have the form
  //    { person_id : person_id, css_map : css_map }.
  //
  // jQuery global custom events published by the object include:
  //  * spa-setchatee - This is published when a new chatee is
  //    set. A map of the form:
  //      { old_chatee : <old_chatee_person_object>,
  //        new_chatee : <new_chatee_person_object>
  //      }
  //    is provided as data.
  //  * spa-listchange - This is published when the list of
  //    online people changes in length (i.e. when a person
  //    joins or leaves a chat) or when their contents change
  //    (i.e. when a person's avatar details change).
  //    A subscriber to this event should get the people_db
  //    from the people model for the updated data.
  //  * spa-updatechat - This is published when a new message
  //    is received or sent. A map of the form:
  //      { dest_id   : <chatee_id>,
  //        dest_name : <chatee_name>,
  //        sender_id : <sender_id>,
  //        msg_text  : <message_content>
  //      }
  //    is provided as data.
  //
  chat = (function () {
    var
      _publish_listchange, _publish_updatechat,
      _update_list, _leave_chat,

      get_chatee, join_chat, send_msg,
      set_chatee, update_avatar,

      chatee = null;

    // Begin internal methods
    _update_list = function( arg_list ) {
      var i, person_map, make_person_map, person,
        people_list      = arg_list[ 0 ],
        is_chatee_online = false;

      clearPeopleDb();

      PERSON:
      for ( i = 0; i < people_list.length; i++ ) {
        person_map = people_list[ i ];

        if ( ! person_map.name ) { continue PERSON; }

        // if user defined, update css_map and skip remainder
        if ( stateMap.user && stateMap.user.id === person_map._id ) {
          stateMap.user.css_map = person_map.css_map;
          continue PERSON;
        }

        make_person_map = {
          cid     : person_map._id,
          css_map : person_map.css_map,
          // tjs 131017 should be same as client...
          id      : person_map._id,
          // tjs 131017
          //email      : person_map.email,
          password      : person_map.password,
          handle      : person_map.handle,
          isStrictLogger      : person_map.isStrictLogger,

          name    : person_map.name
        };
        person = makePerson( make_person_map );

        if ( chatee && chatee.id === make_person_map.id ) {
          is_chatee_online = true;
          chatee = person;
        }
      }

      stateMap.people_db.sort( 'name' );

      // If chatee is no longer online, we unset the chatee
      // which triggers the 'spa-setchatee' global event
      if ( chatee && ! is_chatee_online ) { set_chatee(''); }
    };

    _publish_listchange = function ( arg_list ) {
      _update_list( arg_list );
      $.gevent.publish( 'spa-listchange', [ arg_list ] );
    };

    _publish_updatechat = function ( arg_list ) {
      var msg_map = arg_list[ 0 ];

      if ( ! chatee ) { set_chatee( msg_map.sender_id ); }
      else if ( msg_map.sender_id !== stateMap.user.id
        && msg_map.sender_id !== chatee.id
      ) { set_chatee( msg_map.sender_id ); }

      $.gevent.publish( 'spa-updatechat', [ msg_map ] );
    };
    // End internal methods

    _leave_chat = function () {
      var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
      chatee  = null;
      stateMap.is_connected = false;
      if ( sio ) { sio.emit( 'leavechat' ); }
    };

    get_chatee = function () { return chatee; };

    join_chat  = function () {
      var sio;

      if ( stateMap.is_connected ) { return false; }

      if ( stateMap.user.get_is_anon() ) {
        console.warn( 'User must be defined before joining chat');
        return false;
      }

      sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
      sio.on( 'listchange', _publish_listchange );
      sio.on( 'updatechat', _publish_updatechat );
      stateMap.is_connected = true;
      return true;
    };

    send_msg = function ( msg_text ) {
      var msg_map,
        sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();

      if ( ! sio ) { return false; }
      if ( ! ( stateMap.user && chatee ) ) { return false; }

      msg_map = {
        dest_id   : chatee.id,
        dest_name : chatee.name,
        sender_id : stateMap.user.id,
        msg_text  : msg_text
      };

      // we published updatechat so we can show our outgoing messages
      _publish_updatechat( [ msg_map ] );
      sio.emit( 'updatechat', msg_map );
      return true;
    };

    set_chatee = function ( person_id ) {
      var new_chatee;
      new_chatee  = stateMap.people_cid_map[ person_id ];
      if ( new_chatee ) {
        if ( chatee && chatee.id === new_chatee.id ) {
          return false;
        }
      }
      else {
        new_chatee = null;
      }

      $.gevent.publish( 'spa-setchatee',
        { old_chatee : chatee, new_chatee : new_chatee }
      );
      chatee = new_chatee;
      return true;
    };

    // avatar_update_map should have the form:
    // { person_id : <string>, css_map : {
    //   top : <int>, left : <int>,
    //   'background-color' : <string>
    // }};
    //
    update_avatar = function ( avatar_update_map ) {
      var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
      if ( sio ) {
        sio.emit( 'updateavatar', avatar_update_map );
      }
    };

    return {
      _leave        : _leave_chat,
      get_chatee    : get_chatee,
      join          : join_chat,
      send_msg      : send_msg,
      set_chatee    : set_chatee,
      update_avatar : update_avatar
    };
  }());

  log = (function () {
	    var
	      _publish_listchange, _publish_updatelog,
	      _update_list, _leave_log,

	      get_logee, open_log,
	      set_logee,

	      logee = null;

	    // Begin internal methods
	    _update_list = function( arg_list ) {
	      var i, charity_map, make_charity_map, charity,
	        charity_list      = arg_list[ 0 ];

	      clearCharityDb();

	      CHARITY:
	      for ( i = 0; i < charity_list.length; i++ ) {
	        charity_map = charity_list[ i ];

	        if ( ! charity_map.name ) { continue CHARITY; }

	        // if user defined, update css_map and skip remainder
	        /*
	        if ( stateMap.user && stateMap.user.id === person_map._id ) {
	          stateMap.user.css_map = person_map.css_map;
	          continue CHARITY;
	        } */

	        make_charity_map = {
	          cid     : charity_map._id,
	          //css_map : person_map.css_map,
	          id      : charity_map._id,

	          url      : charity_map.url,
	          isNonProfit      : charity_map.isNonProfit,

	          name    : charity_map.name
	        };
	        charity = makeCharity( make_charity_map );
/*
	        if ( chatee && chatee.id === make_person_map.id ) {
	          is_chatee_online = true;
	          chatee = person;
	        }*/
	      }

	      //stateMap.people_db.sort( 'name' );

	      // If chatee is no longer online, we unset the chatee
	      // which triggers the 'spa-setchatee' global event
	      //if ( chatee && ! is_chatee_online ) { set_chatee(''); }
	    };

	    _publish_listchange = function ( arg_list ) {
	      _update_list( arg_list );
	      $.gevent.publish( 'spa-listchange', [ arg_list ] );
	    };

	    _publish_updatelog = function ( arg_list ) {
	      var msg_map = arg_list[ 0 ];
/*
	      if ( ! chatee ) { set_chatee( msg_map.sender_id ); }
	      else if ( msg_map.sender_id !== stateMap.user.id
	        && msg_map.sender_id !== chatee.id
	      ) { set_chatee( msg_map.sender_id ); }*/

	      $.gevent.publish( 'spa-updatelog', [ msg_map ] );
	    };
	    // End internal methods

	    _leave_log = function () {
	      var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
	      logee  = null;
	      //stateMap.is_connected = false;
	      if ( sio ) { sio.emit( 'leavelog' ); }
	    };

	    get_logee = function () { return logee; };

	    open_log  = function () {
	      var sio;

	      if ( stateMap.is_connected ) { return false; }

	      if ( stateMap.user.get_is_anon() ) {
	        console.warn( 'User must be defined before opening log');
	        return false;
	      }

	      sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
	      sio.on( 'listchange', _publish_listchange );
	      sio.on( 'updatelog', _publish_updatelog );
	      stateMap.is_connected = true;
	      return true;
	    };
/*
	    send_msg = function ( msg_text ) {
	      var msg_map,
	        sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();

	      if ( ! sio ) { return false; }
	      if ( ! ( stateMap.user && chatee ) ) { return false; }

	      msg_map = {
	        dest_id   : chatee.id,
	        dest_name : chatee.name,
	        sender_id : stateMap.user.id,
	        msg_text  : msg_text
	      };

	      // we published updatechat so we can show our outgoing messages
	      _publish_updatechat( [ msg_map ] );
	      sio.emit( 'updatechat', msg_map );
	      return true;
	    }; */

	    set_logee = function ( person_id ) {
	      var new_logee;
	      new_logee  = stateMap.people_cid_map[ person_id ];
	      if ( new_logee ) {
	        if ( logee && logee.id === new_logee.id ) {
	          return false;
	        }
	      }
	      else {
	        new_logee = null;
	      }

	      $.gevent.publish( 'spa-setlogee',
	        { old_logee : logee, new_logee : new_logee }
	      );
	      logee = new_logee;
	      return true;
	    };

	    // avatar_update_map should have the form:
	    // { person_id : <string>, css_map : {
	    //   top : <int>, left : <int>,
	    //   'background-color' : <string>
	    // }};
	    //
	    /*
	    update_avatar = function ( avatar_update_map ) {
	      var sio = isFakeData ? spa.fake.mockSio : spa.data.getSio();
	      if ( sio ) {
	        sio.emit( 'updateavatar', avatar_update_map );
	      }
	    };*/

	    return {
	      _leave        : _leave_log,
	      get_logee    : get_logee,
	      open          : open_log,
	      //send_msg      : send_msg,
	      set_logee    : set_logee
	      //update_avatar : update_avatar
	    };
	  }());
  
  initModule = function () {
    // initialize anonymous person
    stateMap.anon_user = makePerson({
      cid   : configMap.anon_id,
      id    : configMap.anon_id,
      name  : 'anonymous'
    });
    stateMap.user = stateMap.anon_user;
  };

  return {
    initModule : initModule,
    chat       : chat,
    log       : log,
    people     : people
  };
}());
