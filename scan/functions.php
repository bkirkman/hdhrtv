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
        $state_file = 'scanning.pid';
	$channels_file = 'ajax_channels.txt';

	if(!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')))
	{
		echo('You do not have permission to access this page.');
	}


	// evaluate action and call required function
	if (isset($_POST["action"])) {
		switch($_POST["action"]) {
			case "startscan":
			start_scan($state_file, $config['php']);
			break;

			case "getchannels":
			get_scan_channels($channels_file);
			break;

			case "getstate":
			get_scan_state($state_file);
			break;
		}
	}



	function start_scan($state_file, $php_loc)
	{
		// Create scanning state file.
		// This state file will also be created
		// in the channel_scan function executed
		// below, but it's also created here to try to
		// eliminate a race case if multiple users try
		// to start a scan at the same time.
		touch ($state_file);

		// Start execution of channel_scan. Note that it's
		// forked to be run by PHP as a separate process
		// rather than directly by the web server.
		shell_exec("{$php_loc} -q channel_scan.php > /dev/null &");
		echo 'running channel scan...';
	}



	function get_scan_channels($channels_file)
	{
		if (file_exists($channels_file))
		{
			echo file_get_contents($channels_file);
		}
		else
		{
			echo 'No channels. Run channel scan.';
		}
	}


	function get_scan_state($state_file)
	{
		if (file_exists($state_file))
		{
			echo true;
		}
		else
		{
			echo false;
		}
	}
