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

	// Call this to load config settings
	include('./includes/main.php');

	// Get channel to tune
	if (isset($_GET["channel"]))
	{
		$tune_channel = $_GET["channel"];
	}
	else
	{
		echo "No channel set.";
        	die();
	}


	// Get tuner selection from config or from user setting if allowed.
	if ($config['user_tuner'] == true)
	{
		$tuner = $user_settings['tuner'];
	}
	else
	{
		$tuner = $config['hdhr_tuner'];
	}

	// Get the ffmpeg binary command from the config file.
	$ffmpeg = $config['ffmpeg_cmd'];

	// Append the input stream to the command.
	// If the tuner is a DLNA tuner, the input 
	// stream will be an HTTP stream. If the
	// tuner is non-DLNA, commands must be sent
	// to the tuner to tune to correct channel and
	// the input stream will be STDIN from the
	// hdhomerun_config STDOUT stream.
	if ($config['hdhr_type'] == 'dlna')
	{
		// define input stream
		$ffmpeg .= " -y -i http://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$tuner}/v{$tune_channel} ";
	}
	else
	{
		// load channel info model
		include('./models/channels_model.php');
		$model = new Channels_model($config, $user_settings);

		// append hdhomerun_config to beginning
		// and pipe output to ffmpeg
		$ffmpeg = $config['hdhr_utility'] . " {$config['hdhr_ip']} save /{$tuner} - 2>/dev/null | " . $ffmpeg;

		// define input stream as the stdout
		// from hdhomerun_config save
		$ffmpeg .= " -y -i - ";

		// Parse the channel lineup to get the
		// channel frequency and program

		$channel = $model->get_channel($tune_channel);
		$channel_freq = $channel['tune_chan'];
		$channel_prog = $channel['tune_prog'];

		// Set the tuning commands
		$tune_cmd1 = "{$config['hdhr_utility']} {$config['hdhr_ip']} set /{$tuner}/channel auto:{$channel_freq}";
		$tune_cmd2 = "{$config['hdhr_utility']} {$config['hdhr_ip']} set /{$tuner}/program {$channel_prog}";

		// Tune the tuner to the correct channel
		shell_exec($tune_cmd1);
		sleep(1);
		shell_exec($tune_cmd2);
		sleep(1);	
	}

	// Get the ffmpeg options from the config file and
	// as selected by the user and append to ffmpeg command.
	$ffmpeg .= $ffmpeg_opts[$user_settings['transcode']];

	// Replace tags with user settings
	$ffmpeg = str_replace('{size}', $user_settings['vid_size'], $ffmpeg);
	$ffmpeg = str_replace('{vbr}', $user_settings['vid_br'], $ffmpeg);
	$ffmpeg = str_replace('{abr}', $user_settings['aud_br'], $ffmpeg);
	$ffmpeg = str_replace('{asamp}', $user_settings['aud_samp'], $ffmpeg);
	$ffmpeg = str_replace('{deint}', $user_settings['deint'], $ffmpeg);

	// Append ssh to the beginning of command to offload transcoding
	// to remote server over ssh if needed.
	if($config['remote_ssh'] != '')
	{
		$ffmpeg = "ssh " . $config['remote_ssh'] . " '" . $ffmpeg . "'";
	}
	
	// Start the streaming flash file by outputting the file header
	// and then outputting the stream delivered by the stdout of ffmpeg
	header('Content-type: video/x-flv');

	if ($config['output_type'] != 'buffer')
	{
		// Add the output at the end
		$ffmpeg .= ' /dev/stdout 2>/dev/null';
		passthru($ffmpeg);
	}
	else
	{
		// Add the output at the end
		$ffmpeg .= ' pipe:1';

		//set constants
		define('P_STDIN', 0);
		define('P_STDOUT', 1);
		define('P_STDERR', 2);
		define('CHUNKSIZE', 500*1024); // number of bytes fread() reads from stdout of FFmpeg
		define('FFMPEG_PRIORITY', '15'); // man nice

		// Execute ffmpeg
		$descriptorspec = array(
			P_STDIN => array("pipe", "r"),  // stdin (we write the process reads)
			P_STDOUT => array("pipe", "w"),  // stdout (we read the process writes)
			P_STDERR => array("pipe", "w")   // stderr (we read the process writes)
		);

		$process = proc_open("nice -n ".FFMPEG_PRIORITY." ".$ffmpeg, $descriptorspec, $pipes);

		$stdout_size = 0;

		if (is_resource($process))
		{
			while(!feof($pipes[P_STDOUT]))
			{
				$chunk = fread($pipes[P_STDOUT], CHUNKSIZE);
				$stdout_size += strlen($chunk);

				if ($chunk !== false && !empty($chunk))
				{
					echo $chunk;

					// flush output
					if (ob_get_length())
					{
						@ob_flush();
						@flush();
						@ob_end_flush();
					}
					@ob_start();
				}

				if(connection_aborted())
				{
					break;
				}
			}

			fclose($pipes[P_STDOUT]);

			// not read anything from stdout indicates error
			if($stdout_size == 0)
			{ 
				$stderr = stream_get_contents($pipes[P_STDERR]);
			}

			fclose($pipes[P_STDERR]);

			// this should quit the encoding process
			fwrite($pipes[P_STDIN], "q\r\n");
			fclose($pipes[P_STDIN]);

			$return_value = proc_close($process);
		}
	}	
