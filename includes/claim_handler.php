<?php

foreach ($unclaimed_events as $unclaimed_event) 
{

	if(time() > $unclaimed_event['next_reminder']) 
	{
	
		//Create a friendly date/time string
		$friendly_time = date("m-d-Y g:i a", strtotime($unclaimed_event['event']['data']['datetime']));
		//Create channel message.
		$channel_message = "Monitours the following still has not been claimed (Event ID: ".$unclaimed_event['event']['id'].")! \n Time: ".$friendly_time." \n Description: ".$unclaimed_event['event']['data']['description']."\n Ticket: ".$unclaimed_event['event']['data']['ticket']."\n Please claim this via PM.";
		//Message Channel
		$conn->message($channel."@conference.".$domain."", $channel_message, "groupchat");
		//Create PM Message.
		$pm_message = "Hello. The following event still has not been claimed! \n Time: ".$friendly_time."\n Description: ".$unclaimed_event['event']['data']['description']."\n Ticket: ".$unclaimed_event['event']['data']['ticket'];
		//Message Members
		foreach ($members as $member) 
		{
			$conn->message($member."@".$domain, $pm_message);
		}
	
		$event_id = $unclaimed_event['event']['id'];
		$next_reminder = $unclaimed_event['next_reminder']+$update_next;
		$unclaimed_events["$event_id"]['next_reminder'] = $next_reminder;

	}
}
