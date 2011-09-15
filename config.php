<?php

#Jabber Connection Infromation
$server = '';
$port = '';
$username = '';
$password = '';
$resource = '';
$domain = '';
$channel = '';
$nick = '';

//How soon before an event should it message.
$message_before = '300';
//How is the threshold for the update time for nagios servers before they upload.
$nagios_threshold = '180';
//Set how often monbot should spam about a nagios server havving issues.
$nagios_delay = '300';
//How to how until we spam again without an event being claimed.
$update_next = '300';
//This is the locatin of the event file it pulls.
$location = 'http://monocal.int.liquidweb.com/json.php';
//This is the command that is ran to get the nagios locations.
$nagios_check = 'bash /Users/seth/dev/monbot-repo/nagios.sh';

$channel_string = $channel."@conference.".$domain."/".$nick;
