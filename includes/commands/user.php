<?php
//List all the upcoming events.
if($cmd[0] == 'upcoming') {

	$upcoming_events = $events;
	sort($upcoming_events);
	foreach ($upcoming_events as $event) {


		$friendly_time = date("m-d-Y g:i a", strtotime($event['data']['datetime']));
		$pm_message = $friendly_time.' - '.$event['data']['description'].' - '.$event['data']['ticket'];
	    $conn->message($pl['from'], $body="$pm_message", $type=$pl['type']);


	}
                
}
//Show available commands to all users.
if($cmd[0] == 'help') {

	$message = "\n Available User Commands: \n upcoming -- list all upcoming events. \n claim <id> -- allows you to claim an event that is in the queue.";
	$to_user = $cmd[1];
	$conn->message($pl['from'], $body="$message", $type=$pl['type']);
	
}

//Used for users claming events.
if($cmd[0] == 'claim') {

	$claim_id = $cmd[1];
	if(in_array($username, $members)) {

		if(in_array($claim_id, $unclaimed_events["$claim_id"]['event'])) {

			
			$friendly_time = date("m-d-Y g:i a", strtotime($unclaimed_events["$claim_id"]['event']['data']['datetime']));

			$pm_message = "You have claimed the following event! Thank you! \n Time: ".$friendly_time."\n Description: ".$unclaimed_events["$claim_id"]['event']['data']['description']."\n Ticket: ".$unclaimed_events["$claim_id"]['event']['data']['ticket'];
			$conn->message($pl['from'], $body="$pm_message", $type=$pl['type']);

			$channel_message = $username." has claimed Event ID: $claim_id";			
			$conn->message($channel."@conference.".$domain."", $channel_message, "groupchat");

			unset($unclaimed_events["$claim_id"]);
		
			
		} else {

			$pm_message = "An event with the id of $claim_id does not exist!"; 
			$conn->message($pl['from'], $body="$pm_message", $type=$pl['type']);
	
		}

	}


}
