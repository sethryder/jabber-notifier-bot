<?php

//Check to see if the user is an admin.
if(in_array($username, $admins)) 
{

	//List all our current users.
	if($cmd[0] == 'listusers') 
	{
		unset($current_members);
		foreach($members as $member) 
		{
			$current_members = $current_members.$member." :: ";
	 	}

		$conn->message($pl['from'], $body="$current_members", $type=$pl['type']);					  
	}

	//Add a user.
	if($cmd[0] == 'adduser') 
	{
	//Lets make sure the user does not exist already.
		if(in_array($cmd[1], $members)) 
		{
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" already exists!", $type=$pl['type']);
		}
		else
		{						
			$members[] = $cmd[1];
			members_write("members.txt", $members);
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" has been added.", $type=$pl['type']);

			log_event($cmd[0], $username, $cmd[1]);
		}
	}

	//Remove a user.
	if($cmd[0] == 'removeuser') 
	{
	//Make sure the user exists.
		if(in_array($cmd[1], $members)) 
		{
		$member_key = array_search($cmd[1], $members);
		unset($members[$member_key]);
		$members = array_values($members);
		members_write("members.txt", $members);
		$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" has been removed.", $type=$pl['type']);														
		}
		else
		{
		$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" does not exist.", $type=$pl['type']);
		}
	}

	//Add a user as an admin.
	if($cmd[0] == 'addadmin') 
	{
	//Lets make sure the user does not exist already.
		if(in_array($cmd[1], $admins)) 
		{
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" is already an admin!", $type=$pl['type']);
		}
		else
		{
			$admins[] = $cmd[1];
			members_write("admins.txt", $members);
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" is now an admin.", $type=$pl['type']);
			log_event($cmd[0], $username, $cmd[1]);
		}
	}

	//Remove an admin.
	if($cmd[0] == 'removeadmin') 
	{
	//Make sure the user exists.
		if(in_array($cmd[1], $admins)) 
		{
			$member_key = array_search($cmd[1], $admins);
			unset($admins[$member_key]);
			members_write("members.txt", $members);
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" is no longer an admin.", $type=$pl['type']);

			log_event($cmd[0], $username, $members);														
		}
		else
		{
			$conn->message($pl['from'], $body="The user \"{$cmd[1]}\" is not an admin!.", $type=$pl['type']);
		}
	}

	//Broadcast a message to our channel.
	if($cmd[0] == 'bc') 
	{
		$message = preg_replace('/bc/', '', $pl['body'], 1);
 		$conn->message($channel."@conference.".$domain."", $message, "groupchat");

		log_event($cmd[0], $username, $message);
	}

	//Broadcast a message to a specific user.
	if($cmd[0] == 'bu') 
	{
		$to_user = $cmd[1];
		//$message = preg_replace('/broadcast_channel\ '.$cmd[1]'/', '', $pl['body'], 1);
 		$message = str_replace(array("bu", $cmd[1]." "), '', $pl['body']);
		$conn->message($to_user."@".$domain, $message, $type=$pl['type']);

		log_event($cmd[0], $username, $message);
	}

	//Broadcast a message to all our members.
	if($cmd[0] == 'bus') 
	{
		$message = str_replace(array("bus", $cmd[0]), '', $pl['body']);
		foreach ($members as $member) 
		{
			$conn->message($member."@".$domain, $message, $type=$pl['type']);
		}
	
		log_event($cmd[0], $username, $message);
	}

	//Outputs the bots current memory usage.
	if($cmd[0] == 'memory') 
	{
		$memory_usage = convert(memory_get_usage());
		$conn->message($pl['from'], $body="$memory_usage", $type=$pl['type']);
	}

	//Outputs the bots current uptime.
	if($cmd[0] == 'uptime') 
	{
	$uptime = time()-$start_time;
    $pretty_time = Sec2Time($uptime);
	$message = "Uptime: ".$pretty_time['days']." Days ".$pretty_time['hours']." Hours ".$pretty_time['minutes']." Minutes ".$pretty_time['seconds']." Seconds";
	$conn->message($pl['from'], $body="$message", $type=$pl['type']);
    }
	
	//Show the help.
	if($cmd[0] == 'help') 
	{
		$message = "\n Available Admin Commands: 
		\n help -- show commands (you are running this command) 
		\n adduser <name> -- add the user to the notification list. 
		\n removeuser <name> -- remove user from the notification list. 
		\n listusers -- list users on the notification list. 
		\n bc -- send a message to the channel. 
		\n bu <username> <message> -- send a message to a user (example: bu sryder Hello there!) 
		\n bus -- send a message to all users on the notification list. 
		\n addadmin <name> -- give a user admin rights to be able to run these commands. 
		\n removeadmin <name> -- remove a users admin rights. 
		\n memory -- show the bots current memory usage.";
		$to_user = $cmd[1];
		$conn->message($pl['from'], $body="$message", $type=$pl['type']);
	}		
}
