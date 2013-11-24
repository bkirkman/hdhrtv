<?php 
/*
 * HDHRTV is a streaming web application for the HDHomeRun cable tuner
 * Copyright (C) 2013 Brian Kirkman (kirkman [dot] brian [at] gmail [dot] org)
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

	// Get the ffmpeg command from the config file and
	// as selected by the user.
	$ffmpeg = $ffmpeg_cmd[$user_settings['transcode']];

	//  Replace the channel tag with the correct channel to tune
	$ffmpeg = str_replace('{channel}', $tune_channel, $ffmpeg);

	// Replace tags with user settings
	$ffmpeg = str_replace('{size}', $user_settings['vid_size'], $ffmpeg);
	$ffmpeg = str_replace('{vbr}', $user_settings['vid_br'], $ffmpeg);
	$ffmpeg = str_replace('{abr}', $user_settings['aud_br'], $ffmpeg);
	$ffmpeg = str_replace('{asamp}', $user_settings['aud_samp'], $ffmpeg);
	$ffmpeg = str_replace('{deint}', $user_settings['deint'], $ffmpeg);


	// Start the streaming flash file by outputting the file header
	// and then outputting the stream delivered by the stdout of ffmpeg
	header('Content-type: video/x-flv');
	passthru($ffmpeg);
