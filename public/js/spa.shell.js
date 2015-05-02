/*
 * spa.shell.js
 * Shell module for SPA
*/

/*jslint         browser : true, continue : true,
  devel  : true, indent  : 2,    maxerr   : 50,
  newcap : true, nomen   : true, plusplus : true,
  regexp : true, sloppy  : true, vars     : false,
  white  : true
*/
/*global $, spa */

spa.shell = (function () {
  'use strict';
  //---------------- BEGIN MODULE SCOPE VARIABLES --------------
  var
    configMap = {
      anchor_schema_map : {
        chat  : { opened : true, closed : true }
      },
      resize_interval : 200,
      main_html : String()
        + '<div class="spa-shell-head">'
          + '<div class="spa-shell-head-logo">'
            + '<h1>CharityHound</h1>'
            + '<p>Collaborative Management of Charitable Contributions</p>'
          + '</div>'
          + '<div class="spa-shell-head-acct"></div>'
        + '</div>'
        + '<div class="spa-shell-main">'
          + '<div class="spa-shell-main-nav"></div>'
          // tjs 131022
         // + '<div class="spa-shell-main-content"></div>'
          + '<div class="spa-shell-main-content"><div class="spa-shell-main-content-body"></div></div>'
        + '</div>'
        + '<div class="spa-shell-foot"></div>'
        + '<div class="spa-shell-modal"></div>'
    },
    stateMap = {
      $container  : undefined,
      anchor_map  : {},
      resize_idto : undefined
    },
    jqueryMap = {},

    copyAnchorMap,    setJqueryMap,   changeAnchorPart,
    onResize,         onHashchange,
    onTapAcct,        onLogin,        onLogout,
    // tjs 131018
    onProfileMissing,
    setChatAnchor,    initModule;
  //----------------- END MODULE SCOPE VARIABLES ---------------

  //------------------- BEGIN UTILITY METHODS ------------------
  // Returns copy of stored anchor map; minimizes overhead
  copyAnchorMap = function () {
    return $.extend( true, {}, stateMap.anchor_map );
  };
  //-------------------- END UTILITY METHODS -------------------

  //--------------------- BEGIN DOM METHODS --------------------
  // Begin DOM method /setJqueryMap/
  setJqueryMap = function () {
    var $container = stateMap.$container;

    jqueryMap = {
      $container : $container,
      $acct      : $container.find('.spa-shell-head-acct'),
      $nav       : $container.find('.spa-shell-main-nav'),
      $body	: $container.find('.spa-shell-main-content-body')
    };
  };
  // End DOM method /setJqueryMap/

  // Begin DOM method /changeAnchorPart/
  // Purpose    : Changes part of the URI anchor component
  // Arguments  :
  //   * arg_map - The map describing what part of the URI anchor
  //     we want changed.
  // Returns    :
  //   * true  - the Anchor portion of the URI was updated
  //   * false - the Anchor portion of the URI could not be updated
  // Actions    :
  //   The current anchor rep stored in stateMap.anchor_map.
  //   See uriAnchor for a discussion of encoding.
  //   This method
  //     * Creates a copy of this map using copyAnchorMap().
  //     * Modifies the key-values using arg_map.
  //     * Manages the distinction between independent
  //       and dependent values in the encoding.
  //     * Attempts to change the URI using uriAnchor.
  //     * Returns true on success, and false on failure.
  //
  changeAnchorPart = function ( arg_map ) {
    var
      anchor_map_revise = copyAnchorMap(),
      bool_return       = true,
      key_name, key_name_dep;

    // Begin merge changes into anchor map
    KEYVAL:
    for ( key_name in arg_map ) {
      if ( arg_map.hasOwnProperty( key_name ) ) {

        // skip dependent keys during iteration
        if ( key_name.indexOf( '_' ) === 0 ) { continue KEYVAL; }

        // update independent key value
        anchor_map_revise[key_name] = arg_map[key_name];

        // update matching dependent key
        key_name_dep = '_' + key_name;
        if ( arg_map[key_name_dep] ) {
          anchor_map_revise[key_name_dep] = arg_map[key_name_dep];
        }
        else {
          delete anchor_map_revise[key_name_dep];
          delete anchor_map_revise['_s' + key_name_dep];
        }
      }
    }
    // End merge changes into anchor map

    // Begin attempt to update URI; revert if not successful
    try {
      $.uriAnchor.setAnchor( anchor_map_revise );
    }
    catch ( error ) {
      // replace URI with existing state
      $.uriAnchor.setAnchor( stateMap.anchor_map,null,true );
      bool_return = false;
    }
    // End attempt to update URI...

    return bool_return;
  };
  // End DOM method /changeAnchorPart/
  //--------------------- END DOM METHODS ----------------------

  //------------------- BEGIN EVENT HANDLERS -------------------
  // Begin Event handler /onHashchange/
  // Purpose    : Handles the hashchange event
  // Arguments  :
  //   * event - jQuery event object.
  // Settings   : none
  // Returns    : false
  // Actions    :
  //   * Parses the URI anchor component
  //   * Compares proposed application state with current
  //   * Adjust the application only where proposed state
  //     differs from existing and is allowed by anchor schema
  //
  onHashchange = function ( event ) {
    var
      _s_chat_previous, _s_chat_proposed, s_chat_proposed,
      anchor_map_proposed,
      is_ok = true,
      anchor_map_previous = copyAnchorMap();

    // attempt to parse anchor
    try { anchor_map_proposed = $.uriAnchor.makeAnchorMap(); }
    catch ( error ) {
      $.uriAnchor.setAnchor( anchor_map_previous, null, true );
      return false;
    }
    stateMap.anchor_map = anchor_map_proposed;

    // convenience vars
    _s_chat_previous = anchor_map_previous._s_chat;
    _s_chat_proposed = anchor_map_proposed._s_chat;

    // Begin adjust chat component if changed
    if ( ! anchor_map_previous
     || _s_chat_previous !== _s_chat_proposed
    ) {
      s_chat_proposed = anchor_map_proposed.chat;
      switch ( s_chat_proposed ) {
        case 'opened' :
          is_ok = spa.chat.setSliderPosition( 'opened' );
        break;
        case 'closed' :
          is_ok = spa.chat.setSliderPosition( 'closed' );
        break;
        default :
          spa.chat.setSliderPosition( 'closed' );
          delete anchor_map_proposed.chat;
          $.uriAnchor.setAnchor( anchor_map_proposed, null, true );
      }
    }
    // End adjust chat component if changed

    // Begin revert anchor if slider change denied
    if ( ! is_ok ) {
      if ( anchor_map_previous ) {
        $.uriAnchor.setAnchor( anchor_map_previous, null, true );
        stateMap.anchor_map = anchor_map_previous;
      }
      else {
        delete anchor_map_proposed.chat;
        $.uriAnchor.setAnchor( anchor_map_proposed, null, true );
      }
    }
    // End revert anchor if slider change denied

    return false;
  };
  // End Event handler /onHashchange/

  // Begin Event handler /onResize/
  onResize = function () {
    if ( stateMap.resize_idto ) { return true; }

    spa.chat.handleResize();
    stateMap.resize_idto = setTimeout(
      function () { stateMap.resize_idto = undefined; },
      configMap.resize_interval
    );

    return true;
  };
  // End Event handler /onResize/

  onTapAcct = function ( event ) {
    var acct_text, user_email, user_password,
    user = spa.model.people.get_user();
    if ( user.get_is_anon() ) {
    	// 131018
        user_email = prompt( 'Email?' );
        user_password = prompt( 'Password?' );
        spa.model.people.get_user_profile(user_email, user_password);
     jqueryMap.$acct.text( '... processing ...' );
    }
    else {
     spa.model.people.logout();
    }
    return false;
  };

  onLogin = function ( event, login_user ) {
    jqueryMap.$acct.text( login_user.name );
    // tjs 131025 user has logged in navigate to charities selection
    // and solicitation logging page
    
  };

  onLogout = function ( event, logout_user ) {
    jqueryMap.$acct.text( 'Please sign-in' );
//    jqueryMap.$content.html( '<p>Hosted by <a href="http://www.collogistics.com">Collogistics</a>    		  we help <span class="akey">registered collaborators manage their charitable contributions</span>.  Collaborators    		  are asked to record all donor solicitations along with the amount that they donated.    		  By so doing collaborators are able to produce a report after a calendar year end that <span class="akey">helps with    		  tax computations</span> for those who deduct charitable contributions (typically on 1040 Schedule A).    		  </p>    		  <p>    		  As an additional benefit, participating collaborators are periodically emailed    		  a <span class="akey">summary profile report</span> (on an aggregate basis) of both contributors   		  and gift recipients.  No individual data is ever divulged. This means all users\'    		  private donations (along with the recipients) remain private.  However these aggregate-based    		  reports are valuable tools.  They aid with measuring how one\'s giving compares to the    		  overall database of all givers.  More important a profile of recipients (for-profits or non-profits)    		  emerges based upon aggregate users\' experiences.    		  </p>' );
  };
  
  // tjs 131018
  //onProfileMissing = function ( event, profile_user ) {
  onProfileMissing = function ( event, user_list ) {
	  // tjs 131021
	  var profile_user = user_list[ 0 ];
	    var user_email, user_password, user_name, user_handle, user_strict_logger;
	    //jqueryMap.$acct.text( login_user.name );
		  //user_email = profile_user.id;
	  user_email = profile_user.cid;
	  user_password = profile_user.password;
	  //alert("shell onProfileMissing user_email " + user_email + " user_password " + user_password);
	  // e.g. shell onProfileMissing user_email test1@test.com user_password pass1
      user_name = prompt( 'Name?' );
      user_handle = prompt( 'Handle?' );
      user_strict_logger = prompt( 'Strict Logger?' );
      // cf     $.gevent.publish( 'spa-login', [ stateMap.user ] );
      spa.model.people.login( user_name, user_email, user_password, user_handle, user_strict_logger );
	  };
  
  //-------------------- END EVENT HANDLERS --------------------

  //---------------------- BEGIN CALLBACKS ---------------------
  // Begin callback method /setChatAnchor/
  // Example  : setChatAnchor( 'closed' );
  // Purpose  : Change the chat component of the anchor
  // Arguments:
  //   * position_type - may be 'closed' or 'opened'
  // Action   :
  //   Changes the URI anchor parameter 'chat' to the requested
  //   value if possible.
  // Returns  :
  //   * true  - requested anchor part was updated
  //   * false - requested anchor part was not updated
  // Throws   : none
  //
  setChatAnchor = function ( position_type ) {
    return changeAnchorPart({ chat : position_type });
  };
  // End callback method /setChatAnchor/
  
  // tjs 131025
  setLogAnchor = function ( position_type ) {
	    return changeAnchorPart({ log : position_type });
  };

  //----------------------- END CALLBACKS ----------------------

  //------------------- BEGIN PUBLIC METHODS -------------------
  // Begin Public method /initModule/
  // Example   : spa.shell.initModule( $('#app_div_id') );
  // Purpose   :
  //   Directs the Shell to offer its capability to the user
  // Arguments :
  //   * $container (example: $('#app_div_id')).
  //     A jQuery collection that should represent 
  //     a single DOM container
  // Action    :
  //   Populates $container with the shell of the UI
  //   and then configures and initializes feature modules.
  //   The Shell is also responsible for browser-wide issues
  //   such as URI anchor and cookie management
  // Returns   : none 
  // Throws    : none
  //
  initModule = function ( $container ) {
    // load HTML and map jQuery collections
    stateMap.$container = $container;
    $container.html( configMap.main_html );
    setJqueryMap();

    // configure uriAnchor to use our schema
    $.uriAnchor.configModule({
      schema_map : configMap.anchor_schema_map
    });

    // configure and initialize feature modules
    spa.chat.configModule({
      set_chat_anchor : setChatAnchor,
      chat_model      : spa.model.chat,
      people_model    : spa.model.people
    });
    spa.chat.initModule( jqueryMap.$container );

    // tjs 131025
    spa.log.configModule({
        set_log_anchor : setLogAnchor,
        log_model      : spa.model.log,
        people_model    : spa.model.people
      });
      //spa.log.initModule( jqueryMap.$container );
      spa.log.initModule( jqueryMap.$body );

    spa.avtr.configModule({
      chat_model   : spa.model.chat,
      people_model : spa.model.people
    });
    spa.avtr.initModule( jqueryMap.$nav );

    // Handle URI anchor change events.
    // This is done /after/ all feature modules are configured
    // and initialized, otherwise they will not be ready to handle
    // the trigger event, which is used to ensure the anchor
    // is considered on-load
    //
    $(window)
      .bind( 'resize',     onResize )
      .bind( 'hashchange', onHashchange )
      .trigger( 'hashchange' );

    $.gevent.subscribe( $container, 'spa-login',  onLogin  );
    $.gevent.subscribe( $container, 'spa-logout', onLogout );
    // tjs 131018
    $.gevent.subscribe( $container, 'spa-profileMissing', onProfileMissing );

    jqueryMap.$acct
      .text( 'Please sign-in')
      .bind( 'utap', onTapAcct );
    jqueryMap.$body.html( '<p>Hosted by <a href="http://www.collogistics.com">Collogistics</a>    		  we help <span class="akey">registered collaborators manage their charitable contributions</span>.  Collaborators    		  are asked to record all donor solicitations along with the amount that they donated.    		  By so doing collaborators are able to produce a report after a calendar year end that <span class="akey">helps with    		  tax computations</span> for those who deduct charitable contributions (typically on 1040 Schedule A).    		  </p>    		  <p>    		  As an additional benefit, participating collaborators are periodically emailed    		  a <span class="akey">summary profile report</span> (on an aggregate basis) of both contributors   		  and gift recipients.  No individual data is ever divulged. This means all users\'    		  private donations (along with the recipients) remain private.  However these aggregate-based    		  reports are valuable tools.  They aid with measuring how one\'s giving compares to the    		  overall database of all givers.  More important a profile of recipients (for-profits or non-profits)    		  emerges based upon aggregate users\' experiences.    		  </p>' );

  };
  // End PUBLIC method /initModule/

  return { initModule : initModule };
  //------------------- END PUBLIC METHODS ---------------------
}());
