<?php

foreach ($events as $event) 
{

	//Set when the event will need to be announced.
	$announce_time = strtotime($event['data']['datetime'])-$message_before;

	if(time() > $announce_time) 
	{	
		//Make sure that our upcoming event hasn't been acknowledged yet.
		if (!in_array($event['id'], $acked)) 
		{		
			//Create a friendly date/time string
			$friendly_time = date("m-d-Y g:i a", strtotime($event['data']['datetime']));
			//Create channel message.
			$channel_message = "Monitours the following event needs to be claimed/handled (Event ID: ".$event['id']."). \n Time: ".$friendly_time." \n Description: ".$event['data']['description']."\n Ticket: ".$event['data']['ticket']." \n Please claim this event via PM.";
			//Message Channel
			$conn->message($channel."@conference.".$domain."", $channel_message, "groupchat");
			//Create PM Message.
			$pm_message = "Hello. The following event needs to be handled. \n Time: ".$friendly_time."\n Description: ".$event['data']['description']."\n Ticket: ".$event['data']['ticket']."\n Claim this event by replying with: claim ".$event['id'];
			//Message Members
			foreach ($members as $member) 
			{
				$conn->message($member."@".$domain, $pm_message);
			}

			//Add this to our ack'ed array, to make sure we don't announce it again here.
			$acked[] = $event['id'];

			$event_id = $event['id'];
			$next_reminder = $announce_time+$update_next;
			$unclaimed_events["$event_id"] = array('next_reminder' => $next_reminder, 'event' => $event); 
		}
	}
}
