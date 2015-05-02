/*
 * spa.log.js
 * Log feature module for SPA
 * tjs 131025
*/

/*jslint         browser : true, continue : true,
  devel  : true, indent  : 2,    maxerr   : 50,
  newcap : true, nomen   : true, plusplus : true,
  regexp : true, sloppy  : true, vars     : false,
  white  : true
*/

/*global $, spa */

spa.log = (function () {
  'use strict';
  //---------------- BEGIN MODULE SCOPE VARIABLES --------------
  var
    configMap = {
      main_html : String()
        + '<div class="spa-log">'
          + '<div class="spa-log-head">'
            + '<div class="spa-log-head-toggle">+</div>'
            + '<div class="spa-log-head-title">'
              + 'Log'
            + '</div>'
          + '</div>'
          + '<div class="spa-log-closer">x</div>'
          + '<div class="spa-log-sizer">'
            + '<div class="spa-log-list">'
              + '<div class="spa-log-list-box"></div>'
            + '</div>'
            + '<div class="spa-log-msg">'
              + '<div class="spa-log-msg-log"></div>'
              + '<div class="spa-log-msg-in">'
                + '<form class="spa-log-msg-form">'
                  + '<input type="text"/>'
                  + '<input type="submit" style="display:none"/>'
                  + '<div class="spa-log-msg-send">'
                    + 'send'
                  + '</div>'
                + '</form>'
              + '</div>'
            + '</div>'
          + '</div>'
        + '</div>',

      settable_map : {
        slider_open_time    : true,
        slider_close_time   : true,
        slider_opened_em    : true,
        slider_closed_em    : true,
        slider_opened_title : true,
        slider_closed_title : true,

        log_model      : true,
        people_model    : true,
        set_log_anchor : true
      },

      slider_open_time     : 250,
      slider_close_time    : 250,
      slider_opened_em     : 18,
      slider_closed_em     : 2,
      slider_opened_title  : 'Tap to close',
      slider_closed_title  : 'Tap to open',
      slider_opened_min_em : 10,
      window_height_min_em : 20,

      log_model      : null,
      people_model    : null,
      set_log_anchor : null
    },
    stateMap  = {
      $append_target   : null,
      position_type    : 'closed',
      px_per_em        : 0,
      slider_hidden_px : 0,
      slider_closed_px : 0,
      slider_opened_px : 0
    },
    jqueryMap = {},

    setJqueryMap,  setPxSizes,   scrollLog,
    writeLog,     writeAlert,   clearLog,
    setSliderPosition,
    onTapToggle,   onSubmitMsg,  onTapList,
    onSetlogee,   onUpdatelog, onListchange,
    onLogin,       onLogout,
    configModule,  initModule,
    removeSlider,  handleResize;
  //----------------- END MODULE SCOPE VARIABLES ---------------

  //------------------- BEGIN UTILITY METHODS ------------------
  //-------------------- END UTILITY METHODS -------------------

  //--------------------- BEGIN DOM METHODS --------------------
  // Begin DOM method /setJqueryMap/
  setJqueryMap = function () {
    var
      $append_target = stateMap.$append_target,
      $slider        = $append_target.find( '.spa-log' );

    jqueryMap = {
      $slider   : $slider,
      $head     : $slider.find( '.spa-log-head' ),
      $toggle   : $slider.find( '.spa-log-head-toggle' ),
      $title    : $slider.find( '.spa-log-head-title' ),
      $sizer    : $slider.find( '.spa-log-sizer' ),
      $list_box : $slider.find( '.spa-log-list-box' ),
      $msg_log  : $slider.find( '.spa-log-msg-log' ),
      $msg_in   : $slider.find( '.spa-log-msg-in' ),
      $input    : $slider.find( '.spa-log-msg-in input[type=text]'),
      $send     : $slider.find( '.spa-log-msg-send' ),
      $form     : $slider.find( '.spa-log-msg-form' ),
      $window   : $(window)
    };
  };
  // End DOM method /setJqueryMap/

  // Begin DOM method /setPxSizes/
  setPxSizes = function () {
    var px_per_em, window_height_em, opened_height_em;

    px_per_em = spa.util_b.getEmSize( jqueryMap.$slider.get(0) );
    window_height_em = Math.floor(
      ( jqueryMap.$window.height() / px_per_em ) + 0.5
    );

    opened_height_em
      = window_height_em > configMap.window_height_min_em
      ? configMap.slider_opened_em
      : configMap.slider_opened_min_em;

    stateMap.px_per_em        = px_per_em;
    stateMap.slider_closed_px = configMap.slider_closed_em * px_per_em;
    stateMap.slider_opened_px = opened_height_em * px_per_em;
    jqueryMap.$sizer.css({
      height : ( opened_height_em - 2 ) * px_per_em
    });
  };
  // End DOM method /setPxSizes/

  // Begin public method /setSliderPosition/
  // Example   : spa.log.setSliderPosition( 'closed' );
  // Purpose   : Move the log slider to the requested position
  // Arguments :
  //   * position_type - enum('closed', 'opened', or 'hidden')
  //   * callback - optional callback to be run end at the end
  //     of slider animation.  The callback receives a jQuery
  //     collection representing the slider div as its single
  //     argument
  // Action    :
  //   This method moves the slider into the requested position.
  //   If the requested position is the current position, it
  //   returns true without taking further action
  // Returns   :
  //   * true  - The requested position was achieved
  //   * false - The requested position was not achieved
  // Throws    : none
  //
  setSliderPosition = function ( position_type, callback ) {
    var
      height_px, animate_time, slider_title, toggle_text;

    // position type of 'opened' is not allowed for anon user;
    // therefore we simply return false; the shell will fix the
    // uri and try again.
    if ( position_type === 'opened'
      && configMap.people_model.get_user().get_is_anon()
    ){ return false; }

    // return true if slider already in requested position
    if ( stateMap.position_type === position_type ){
      if ( position_type === 'opened' ) {
        jqueryMap.$input.focus();
      }
      return true;
    }

    // prepare animate parameters
    switch ( position_type ){
      case 'opened' :
        height_px    = stateMap.slider_opened_px;
        animate_time = configMap.slider_open_time;
        slider_title = configMap.slider_opened_title;
        toggle_text  = '=';
        jqueryMap.$input.focus();
      break;

      case 'hidden' :
        height_px    = 0;
        animate_time = configMap.slider_open_time;
        slider_title = '';
        toggle_text  = '+';
      break;

      case 'closed' :
        height_px    = stateMap.slider_closed_px;
        animate_time = configMap.slider_close_time;
        slider_title = configMap.slider_closed_title;
        toggle_text  = '+';
      break;

      // bail for unknown position_type
      default : return false;
    }

    // animate slider position change
    stateMap.position_type = '';
    jqueryMap.$slider.animate(
      { height : height_px },
      animate_time,
      function () {
        jqueryMap.$toggle.prop( 'title', slider_title );
        jqueryMap.$toggle.text( toggle_text );
        stateMap.position_type = position_type;
        if ( callback ) { callback( jqueryMap.$slider ); }
      }
    );
    return true;
  };
  // End public DOM method /setSliderPosition/

  // Begin private DOM methods to manage log message
  scrollLog = function() {
    var $msg_log = jqueryMap.$msg_log;
    $msg_log.animate(
      { scrollTop : $msg_log.prop( 'scrollHeight' )
        - $msg_log.height()
      },
      150
    );
  };

  writeLog = function ( person_name, text, is_user ) {
    var msg_class = is_user
      ? 'spa-log-msg-log-me' : 'spa-log-msg-log-msg';

    jqueryMap.$msg_log.append(
      '<div class="' + msg_class + '">'
      + spa.util_b.encodeHtml(person_name) + ': '
      + spa.util_b.encodeHtml(text) + '</div>'
    );

    scrollLog();
  };

  writeAlert = function ( alert_text ) {
    jqueryMap.$msg_log.append(
      '<div class="spa-log-msg-log-alert">'
        + spa.util_b.encodeHtml(alert_text)
      + '</div>'
    );
    scrollLog();
  };

  clearLog = function () { jqueryMap.$msg_log.empty(); };
  // End private DOM methods to manage log message
  //---------------------- END DOM METHODS ---------------------

  //------------------- BEGIN EVENT HANDLERS -------------------
  onTapToggle = function ( event ) {
    var set_log_anchor = configMap.set_log_anchor;
    if ( stateMap.position_type === 'opened' ) {
      set_log_anchor( 'closed' );
    }
    else if ( stateMap.position_type === 'closed' ){
      set_log_anchor( 'opened' );
    }
    return false;
  };

  onSubmitMsg = function ( event ) {
    var msg_text = jqueryMap.$input.val();
    if ( msg_text.trim() === '' ) { return false; }
    configMap.log_model.send_msg( msg_text );
    jqueryMap.$input.focus();
    jqueryMap.$send.addClass( 'spa-x-select' );
    setTimeout(
      function () { jqueryMap.$send.removeClass( 'spa-x-select' ); },
      250
    );
    return false;
  };

  onTapList = function ( event ) {
    var $tapped  = $( event.elem_target ), logee_id;
    if ( ! $tapped.hasClass('spa-log-list-name') ) { return false; }

    logee_id = $tapped.attr( 'data-id' );
    if ( ! logee_id ) { return false; }

    configMap.log_model.set_logee( logee_id );
    return false;
  };

  onSetlogee = function ( event, arg_map ) {
    var
      new_logee = arg_map.new_logee,
      old_logee = arg_map.old_logee;

    jqueryMap.$input.focus();
    if ( ! new_logee ) {
      if ( old_logee ) {
        writeAlert( old_logee.name + ' has left the log' );
      }
      else {
        writeAlert( 'Your friend has left the log' );
      }
      jqueryMap.$title.text( 'Log' );
      return false;
    }

    jqueryMap.$list_box
      .find( '.spa-log-list-name' )
      .removeClass( 'spa-x-select' )
      .end()
      .find( '[data-id=' + arg_map.new_logee.id + ']' )
      .addClass( 'spa-x-select' );

    writeAlert( 'Now logting with ' + arg_map.new_logee.name );
    jqueryMap.$title.text( 'Log with ' + arg_map.new_logee.name );
    return true;
  };

  onListchange = function ( event ) {
    var
      list_html = String(),
      people_db = configMap.people_model.get_db(),
      logee    = configMap.log_model.get_logee();


    people_db().each( function ( person, idx ) {
      var select_class = '';

      if ( person.get_is_anon() || person.get_is_user()
      ) { return true;}

      if ( logee && logee.id === person.id ) {
        select_class=' spa-x-select';
      }
      list_html
        += '<div class="spa-log-list-name'
        +  select_class + '" data-id="' + person.id + '">'
        +  spa.util_b.encodeHtml( person.name ) + '</div>';
    });

    if ( ! list_html ) {
      list_html = String()
        + '<div class="spa-log-list-note">'
        + 'To log alone is the fate of all great souls...<br><br>'
        + 'No one is online'
        + '</div>';
      clearLog();
    }
    // jqueryMap.$list_box.html( list_html );
    jqueryMap.$list_box.html( list_html );
  };

  onUpdatelog = function ( event, msg_map ) {
    var
      is_user,
      sender_id = msg_map.sender_id,
      msg_text  = msg_map.msg_text,
      logee    = configMap.log_model.get_logee() || {},
      sender    = configMap.people_model.get_by_cid( sender_id );

    if ( ! sender ) {
      writeAlert( msg_text );
      return false;
    }

    is_user = sender.get_is_user();

    if ( ! ( is_user || sender_id === logee.id ) ) {
      configMap.log_model.set_logee( sender_id );
    }

    writeLog( sender.name, msg_text, is_user );

    if ( is_user ) {
      jqueryMap.$input.val( '' );
      jqueryMap.$input.focus();
    }
  };

  onLogin = function ( event, login_user ) {
    configMap.set_log_anchor( 'opened' );
  };

  onLogout = function ( event, logout_user ) {
    configMap.set_log_anchor( 'closed' );
    jqueryMap.$title.text( 'Log' );
    clearLog();
  };

  //-------------------- END EVENT HANDLERS --------------------

  //------------------- BEGIN PUBLIC METHODS -------------------
  // Begin public method /configModule/
  // Example   : spa.log.configModule({ slider_open_em : 18 });
  // Purpose   : Configure the module prior to initialization
  // Arguments :
  //   * set_log_anchor - a callback to modify the URI anchor to
  //     indicate opened or closed state. This callback must return
  //     false if the requested state cannot be met
  //   * log_model - the log model object provides methods
  //       to interact with our instant messaging
  //   * people_model - the people model object which provides
  //       methods to manage the list of people the model maintains
  //   * slider_* settings. All these are optional scalars.
  //       See mapConfig.settable_map for a full list
  //       Example: slider_open_em is the open height in em's
  // Action    :
  //   The internal configuration data structure (configMap) is
  //   updated with provided arguments. No other actions are taken.
  // Returns   : true
  // Throws    : JavaScript error object and stack trace on
  //             unacceptable or missing arguments
  //
  configModule = function ( input_map ) {
    spa.util.setConfigMap({
      input_map    : input_map,
      settable_map : configMap.settable_map,
      config_map   : configMap
    });
    return true;
  };
  // End public method /configModule/

  // Begin public method /initModule/
  // Example    : spa.log.initModule( $('#div_id') );
  // Purpose    :
  //   Directs Log to offer its capability to the user
  // Arguments  :
  //   * $append_target (example: $('#div_id')).
  //     A jQuery collection that should represent
  //     a single DOM container
  // Action     :
  //   Appends the log slider to the provided container and fills
  //   it with HTML content.  It then initializes elements,
  //   events, and handlers to provide the user with a log-room
  //   interface
  // Returns    : true on success, false on failure
  // Throws     : none
  //
  initModule = function ( $append_target ) {
    var $list_box;

    // load log slider html and jquery cache
    stateMap.$append_target = $append_target;
    $append_target.append( configMap.main_html );
    setJqueryMap();
    setPxSizes();

    // initialize log slider to default title and state
    jqueryMap.$toggle.prop( 'title', configMap.slider_closed_title );
    stateMap.position_type = 'closed';

    // Have $list_box subscribe to jQuery global events
    $list_box = jqueryMap.$list_box;
    $.gevent.subscribe( $list_box, 'spa-listchange', onListchange );
    $.gevent.subscribe( $list_box, 'spa-setlogee',  onSetlogee  );
    $.gevent.subscribe( $list_box, 'spa-updatelog', onUpdatelog );
    $.gevent.subscribe( $list_box, 'spa-login',      onLogin      );
    $.gevent.subscribe( $list_box, 'spa-logout',     onLogout     );

    // bind user input events
    jqueryMap.$head.bind(     'utap', onTapToggle );
    jqueryMap.$list_box.bind( 'utap', onTapList   );
    jqueryMap.$send.bind(     'utap', onSubmitMsg );
    jqueryMap.$form.bind(   'submit', onSubmitMsg );
  };
  // End public method /initModule/

  // Begin public method /removeSlider/
  // Purpose    :
  //   * Removes logSlider DOM element
  //   * Reverts to initial state
  //   * Removes pointers to callbacks and other data
  // Arguments  : none
  // Returns    : true
  // Throws     : none
  //
  removeSlider = function () {
    // unwind initialization and state
    // remove DOM container; this removes event bindings too
    if ( jqueryMap.$slider ) {
      jqueryMap.$slider.remove();
      jqueryMap = {};
    }
    stateMap.$append_target = null;
    stateMap.position_type  = 'closed';

    // unwind key configurations
    configMap.log_model      = null;
    configMap.people_model    = null;
    configMap.set_log_anchor = null;

    return true;
  };
  // End public method /removeSlider/

  // Begin public method /handleResize/
  // Purpose    :
  //   Given a window resize event, adjust the presentation
  //   provided by this module if needed
  // Actions    :
  //   If the window height or width falls below
  //   a given threshold, resize the log slider for the
  //   reduced window size.
  // Returns    : Boolean
  //   * false - resize not considered
  //   * true  - resize considered
  // Throws     : none
  //
  handleResize = function () {
    // don't do anything if we don't have a slider container
    if ( ! jqueryMap.$slider ) { return false; }

    setPxSizes();
    if ( stateMap.position_type === 'opened' ){
      jqueryMap.$slider.css({ height : stateMap.slider_opened_px });
    }
    return true;
  };
  // End public method /handleResize/

  // return public methods
  return {
    setSliderPosition : setSliderPosition,
    configModule      : configModule,
    initModule        : initModule,
    removeSlider      : removeSlider,
    handleResize      : handleResize
  };
  //------------------- END PUBLIC METHODS ---------------------
}());
