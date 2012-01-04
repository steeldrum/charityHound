<?php
/***************************************
$Revision:: 152                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-15 09:35:07#$: Date of last commit
***************************************/
/*
Collaborators/
config.php
tjs 101012

file version 1.00 

release version 1.06
*/

/* tjs 110512 moved sql to new database CHARITYHOUND
//tjs 101012
//define( "DB_DSN", "mysql:dbname=mydatabase" );
define( "DB_DSN", "mysql:dbname=COLLORG" );
//tjs 110511
define( "DB_NAME", "COLLORG" );
*/
define( "DB_DSN", "mysql:dbname=CHARITYHOUND" );
define( "DB_NAME", "CHARITYHOUND" );
define( "DB_USERNAME", "root" );
//define( "DB_PASSWORD", "mypass" );
define( "DB_PASSWORD", "root" );
define( "PAGE_SIZE", 5 );
define( "TBL_MEMBERS", "members" );
// tjs 111111
define( "TBL_TOKENS", "tokens" );
// tjs 111020
define( "TBL_CHARITIES", "charities" );
define( "TBL_DONATIONS", "donations" );
define( "TBL_ACCESS_LOG", "accessLog" );
define( "TBL_ADS", "ads" );
?>
