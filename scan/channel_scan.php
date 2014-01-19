<?php
/*
 * HDHRTV is a streaming web application for the HDHomeRun cable tuner
 * Copyright (C) 2013 Brian Kirkman (kirkman [dot] brian [at] gmail [dot] com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

        // Call this to load config settings and View class
        include('../includes/main.php');

	$channels = null;
	$channel = "";
	$program = "";
	$name = "";
	$channel_file = 'ajax_channels.txt';
	$xml_file = 'lineup.xml';
	$state_file = 'scanning.pid';


	// Create scanning state file
	touch ($state_file); 	

	// Overwirte ajax file and create the first line showing the start of the scan 
	file_put_contents ($channel_file, 'connecting to tuner...<br/>');

	// Get tuner selection from config or from user setting if allowed.
	if ($config['user_tuner'] == true)
	{
		$tuner = $user_settings['tuner'];
	}
	else
	{
		$tuner = $config['hdhr_tuner'];
	}

	// Set scan command
	$cmd = "{$config['hdhr_utility']} {$config['hdhr_ip']} scan /tuner0";

	// Show commands
	file_put_contents ($channel_file, $cmd . '<br/>', FILE_APPEND);

	// Set descriptor pipes array	
	$descriptorspec = array(
		0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
		2 => array("pipe", "w")    // stderr is a pipe that the child will write to
	);

	// Clear any data
	flush();

	// Set firstline to indicate writing of first channel line
	$firstline = true;

	// Start process
	$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
	if (is_resource($process)) {
		// Parse each line
		while ($line = fgets($pipes[1])) {

			// Get channel frequency
			if (preg_match("/SCANNING: [0-9]+ \(.*us-(bcast|irc):([0-9]+)/", $line, $match))
			{
				$channel = $match[2];
				$program = "";
				$number = "";
				$name = "";
			}

			// Get program (sub-channel), vchannel number, and channel name
			if (preg_match("/PROGRAM ([0-9]+): ([^\s]+) ([^\s]+)/", $line, $match))
			{
				$program = $match[1];
				$number = $match[2];
				$name = $match[3];
			}

			// Build channel array
			if($program != "" && $number != '0') {
				$channels[$number] = array(
							'name' => $name,
							'channel' => $channel,
							'program' => $program);


				if ($firstline)
				{
					$file_append = null;
				}
				else
				{
					$file_append = FILE_APPEND;
				
				}

				// Build channel list for ajax call to update browser while scanning 
				file_put_contents ($channel_file, $number . ' ' . $name . ' ' . $channel . ' ' . $program . '<br/>', $file_append);
				$firstline = false;
			}

			flush();
		}
	}

/**
////////////////
	sleep (5);
	$firstline = true;
	for ($i=1; $i<=20; $i++)
	{
		$channel = $i + 2;
		$program = $i + 14;
		$number = $i;
		$name = 'Channel_' . $i;

		sleep (1);	

		// Build channel array
		if($program != "" && $number != '0') {
			$channels[$number] = array(
						'name' => $name,
						'channel' => $channel,
						'program' => $program);

			if ($firstline)
			{
				$file_append = null;
			}
			else
			{
				$file_append = FILE_APPEND;
			
			}

			// Build channel list for ajax call to update browser while scanning 
			file_put_contents ($channel_file, $number . ' ' . $name . ' ' . $channel . ' ' . $program . '<br/>', $file_append);
			$firstline = false;
		}
	}
///////////////
**/

	// Build and save XML file of channel listings
	$xml = '';
	$xml .= '<?xml version="1.0" standalone="yes"?' . '>' .
		"\n<Lineup>\n";

	if (is_array($channels))
	{
		ksort($channels);

		foreach ($channels as $key => $val)
		{
			// Clean up special characters - specifically ampersands
			$chan_name = htmlspecialchars($val['name']);
			$xml .= "\t<Program>\n";
			$xml .= "\t\t<GuideNumber>{$key}</GuideNumber>\n";
			$xml .= "\t\t<GuideName>{$chan_name}</GuideName>\n";
			$xml .= "\t\t<TunerChannel>{$val['channel']}</TunerChannel>\n";
			$xml .= "\t\t<TunerProgram>{$val['program']}</TunerProgram>\n";
			$xml .= "\t</Program>\n";
		}
	}

	$xml .= "</Lineup>";

	// Create lineup file 
	file_put_contents ($xml_file, $xml);

	// Delete scanning state file
	unlink ($state_file); 	

	proc_close($process);
