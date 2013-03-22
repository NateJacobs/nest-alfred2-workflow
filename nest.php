<?php

/**
 * Name: 		Alfred Nest API Control
 * Description:	An Alfred 2 worflow to view current information and contol your Nest Learning Thermostat
 *				Thanks to David Ferguson for his Workflow class - http://dferg.us/workflows-class/
 *				Also thanks to Guillaume Boudreau for his Nest API class - https://github.com/gboudreau/nest-api
 * Author:		Nate Jacobs (https://github.com/NateJacobs)
 * Revised:		03/22/2013
 * Version:		1.0
 */

// Get the required files
require_once('workflows.php');
require_once('nest.class.php');

$w = new Workflows();
$nest = new Nest();

// Get the system TimeZone
$tz = exec( 'systemsetup -gettimezone | cut -d " " -f 3 ' );
date_default_timezone_set( $tz );

// Get Nest username and password.
define( 'USERNAME', $w->get( 'username', 'settings.plist' ) );
define( 'PASSWORD', base64_decode( $w->get( 'password', 'settings.plist' ) ) );

// Get the device information:
$info = $nest->getDeviceInfo();

// Get current temperature from device
$current_temp = round( $info->current_state->temperature*9/5+32, 1, PHP_ROUND_HALF_UP );

// Determine settings
$ac = ( $info->current_state->ac ? 'On' : 'Off' );
$heat = ( $info->current_state->heat ? 'On' : 'Off' );
$fan = ( $info->current_state->fan ? 'On' : 'Off' );
$leaf = ( $info->current_state->leaf ? 'Yes' : 'No' );

// Get the results ready to push to Alfred
$w->result( 
	'nesttemp', 
	'na', 
	'Room Temperature: '.$current_temp.'°', 
	'Humidity: '.$info->current_state->humidity.'%. AC: '.$ac.'. Heat: '.$heat.'. Fan: '.$fan.'. Leaf: '.$leaf, 
	'icon.png' 
);

echo $w->toxml();