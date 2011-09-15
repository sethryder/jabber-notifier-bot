<?php

//This will read a file for username names and return them as an array.
function members_read($file) 
{
	$pf = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) or die('Unable to open file, are you sure that its there?');
	$members = explode(":", $pf[0]);
	
	return $members;
}

function vps_report($vps_location) 
{
	$pf = file($vps_location, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) or die('Unable to open file, are you sure that its there?');

	foreach ($pf as $vps_report) 
	{
		$fancy_vps = explode(',',$vps_report);
		$vps_hostname = $fancy_vps[0];
		$vps_report_type = $fancy_vps[1];
		$vps_reports["$vps_report_type"][] = $vps_hostname;	
	}
}

//This takes an array and writes it to the specified file.
function members_write($file, $members) 
{
	$fp = fopen($file, "w");	
	foreach ($members as $member) 
	{
		fwrite($fp, ":".$member);
	}
}

//This will explode the full username and just give us the base.
function get_username($long_name) 
{
      $explode_name = explode("@", $long_name);
      $short_name = $explode_name[0];

      return $short_name;
}

//This will pull the events and return them as an array.
function pull_events($location) 
{
	$json_events = file_get_contents($location);

	//This is super janky, for some reason the json that moncal is returning is not valid.
	//This just corrects this issue by making it valid with a couple str_replaces.
	$replace_1 = str_replace('}', '},', $json_events);
	$replace_2 = str_replace(',]', ']', $replace_1);

	//print_r($test_replace);
	$events = json_decode($replace_2, TRUE);

	return $events;
}

function pull_nagios($nagios_check) 
{
	$output = shell_exec($nagios_check);
	
	$servers = explode('|', $output);

	return $servers;
}

//This will make our events more usable.
function get_events($fresh_events) 
{
	$events = array();
	foreach ($fresh_events as $new_event) 
	{
		//Generate our unique event hash, based on the event time and id.
		$event_hash = strtotime($new_event['datetime'])+$new_event['id'];	
	
		//Lets re-write our array so its more friendly for us to use.
		$events[] = array("id" => $event_hash, "data" => array("datetime" => $new_event['datetime'], "description" => $new_event['description'], "ticket" => $new_event['ticket']));
	}

	return $events;
}

//Function to covert bytes into kb, mb, gb, etc.
function convert($size) 
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

//Write our pid to a file.
function write_pid() 
{
	//Get our running pid.
	$pid = getmypid();
	//Write the pid to our .pid file.
	shell_exec("echo $pid > /home/monbot/includes/monbot.pid");
}

//Convert seconds to a friendly time.
function Sec2Time($time)
{
  if(is_numeric($time))
{
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926)
	{
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400)
	{
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600)
	{
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60)
	{
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    return (array) $value;
  	}
	else
	{
    return (bool) FALSE;
  	}
}

/*Not yet implemented
//For pulling external quotes and jokes.
function external_quotes ($location) {

	$raw_quotes = file_get_contents($location);
	
	$quotes = explode("\n", $raw_quotes);

	return $quotes;
}
*/

function log_event($cmd, $user, $message) 
{
	$log_time = date("m.d.y H:i:s");

	$output = $log_time.": ".$cmd." - ".$user." - ".$message;

	shell_exec("echo $output >> /home/monbot/monbot.log");
}
