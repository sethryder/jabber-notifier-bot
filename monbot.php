#!/usr/bin/php
<?php

//Activate full error reporting
//error_reporting(E_ALL & E_STRICT);

include 'config.php';
include 'includes/functions.php';
include 'includes/XMPPHP/XMPP.php';

//Write our pid.
write_pid();

//Create empty the arrays that will be used in the script.
$acked = array();
$events = array();
$sent_messages = array();
$unclaimed_events = array();
$nagios_alerts = array();

//When did we start up the script? We use this for uptime.
$start_time = time();

//Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
//If this doesn't work, are you running 64-bit PHP with < 5.2.6?
$conn = new XMPPHP_XMPP($server, $port, $username, $password, $resource, $domain, $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
$conn->autoSubscribe();

//Read our saved members and admins.
$members = members_read("members.txt");
$admins = members_read("admins.txt");

unset($members[0]);
$members = array_values($members);

try 
{
    $conn->connect();
    while(!$conn->isDisconnected()) 
	{
    	$payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));

		//Include our event handler, this handles broadcasting our events to the channel and members.
		include 'includes/event_handler.php';
		//Include our claim handler, this handles people claming events and re-announcing unclaimed events.
		include 'includes/claim_handler.php';
		//Include our nagios handler, this checks to see if nagios servers are still running properly.
		include 'includes/nagios_handler.php';

		//Pull our new events and rewrite them to a friendly format.
		$new_events = pull_events($location);
		$events = get_events($new_events);

		foreach($payloads as $event) 
		{		
    		$pl = $event[1];
			switch($event[0]) 
				{
				//Process when we get a private message.    			
				case 'message':
					//Get the real username without the extra stuff.
					$username = get_username($pl['from']);

					//Grab the command.
					$cmd = explode(' ', $pl['body']);

					//Include our user commands.
					include 'includes/commands/user.php';

					//Include our admin commands.
					include 'includes/commands/admin.php';    				
				break;

				//Process on a users presence change (such as going away, signing on, etc). 
    			case 'presence':
					//When a user changes presence, output it to the console.				
					//print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
    			break;
			
				//Process on connect.
				case 'session_start':
					print "Session Start\n";
					$conn->presence(NULL, "available", $channel_string);
					$conn->getRoster();
					$conn->presence($status="Monitoring!");
				break;
			}
	  	}
	}
}
catch(XMPPHP_Exception $e)
{
    die($e->getMessage());
}
