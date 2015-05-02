/*
 * spa.data.js
 * Data module
*/

/*jslint         browser : true, continue : true,
  devel  : true, indent  : 2,    maxerr   : 50,
  newcap : true, nomen   : true, plusplus : true,
  regexp : true, sloppy  : true, vars     : false,
  white  : true
*/
/*global $, io, spa */

spa.data = (function () {
  'use strict';
  var
    stateMap = { sio : null },
    makeSio, getSio, initModule;

  makeSio = function (){
  // tjs 131009
  //alert("data makeSio io connecting...");
  // e.g. data makeSio io connecting...
  
    var socket = io.connect( '/chat' );
  // tjs 131009
  //alert("data makeSio socket " + socket);

    return {
      emit : function ( event_name, data ) {
        socket.emit( event_name, data );
      },
      on   : function ( event_name, callback ) {
        socket.on( event_name, function (){
          callback( arguments );
        });
      }
    };
  };

  getSio = function (){
  // tjs 131009
  //alert("data getSio stateMap.sio " + stateMap.sio);
  // e.g. data getSio stateMap.sio null
  
    if ( ! stateMap.sio ) { stateMap.sio = makeSio(); }
  // tjs 131009
  //alert("data getSio returned stateMap.sio " + stateMap.sio);
    return stateMap.sio;
  };

  initModule = function (){};

  return {
    getSio     : getSio,
    initModule : initModule
  };
}());
