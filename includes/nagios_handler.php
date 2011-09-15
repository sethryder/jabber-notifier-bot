<?php

if($next_check < time()) 
{
	$nagios_servers = pull_nagios($nagios_check);
	unset($nagios_servers[0]);

	foreach ($nagios_servers as $server) 
	{
		$s = explode(',', $server);

		$hostname = $s[0];
		$last_update = $s[1];

		if($last_update < time()-$nagios_threshold) 
		{		
			if ($nagios_alerts["$hostname"] < time()-$nagios_delay) 
			{
				$last_notice = time();

				$nagios_alerts["$hostname"] = $last_notice;

				//Create a friendly date/time string
				$friendly_time = date("m-d-Y g:i a", $last_update);
				//Create channel message.
				$channel_message = "Monitours! The following Nagios server has stopped updating. Please investigate! Server: $hostname. Last Update: $friendly_time.";
				//Message Channel
				$conn->message($channel."@conference.".$domain."", $channel_message, "groupchat");

			}

		}

	}
	$next_check = time()+30;
}


