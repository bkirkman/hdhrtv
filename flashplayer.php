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
	include('./includes/main.php');

	// Load view
	$view = new View();

	// Set page title
	$data['page_title'] = $config['page_title'];

        // Call this to load channel model
        include('./models/channels_model.php');
        $model = new Channels_model($config, $user_settings);
        if (!$model->is_connected())
        {
                $data['message'] = $model->get_error();

                // Display view
                echo $view->display('views/notice', $data);
                die();
        }

	//Get channel parameter
        if (isset($_GET['channel']) && $_GET['channel'] != '' && $_GET['channel'] != null)
	{
		$tune_channel = $_GET["channel"];
	}
	else
	{
                // Set message
                $data['message'] = 'No Channel Set';

                // Display view
                echo $view->display('views/notice', $data);
                die();
	}

        //get channel data
        if (!$channel = $model->get_channel($tune_channel))
        {
                // Set message
                $data['message'] = 'Invalid Channel. Channel ' . $tune_channel . ' does not exist in the lineup.';

                // Display view
                echo $view->display('views/notice', $data);
                die();
        }

	//Use the following hack to get the http basepath of this file location
	//$this_file = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$this_file = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$full_path = str_replace(basename($this_file), '', $this_file);

	// get protocol
	if ($_SERVER['SERVER_PORT'] == "443")
	{
		$protocol = "https://";
	}
	else
	{
		$protocol = "http://";
	}

	//Set the path to serve the streaming flash file
	$data['stream_url'] = $protocol . $full_path . "flashstream.php?channel={$tune_channel}";

	// Display view
	echo $view->display('views/flashplayer', $data);
