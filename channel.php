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

	// get channel
	if (isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] != null)
	{
		$channel_id = $_GET["id"];
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
	if (!$channel = $model->get_channel($channel_id))
	{
		// Set message
		$data['message'] = 'Invalid Channel. Channel ' . $channel_id . ' does not exist in the lineup.';

		// Display view 
		echo $view->display('views/notice', $data);
		die();
	}

	// append subtitle to title if it exists
	if ($channel['subtitle'] != "" )
	{
		$channel['title'] .= ' - ' . $channel['subtitle'];
	}

	// convert time strings
	if ($channel['starttime'] != "" )
	{
		$channel['starttime'] = date('h:i', strtotime($channel['starttime'] .' UTC'));
	}

	if ($channel['endtime'] != "" )
	{
		$channel['endtime'] = date('h:i', strtotime($channel['endtime'] .' UTC'));
	}

	//load channel data into data variables for view
	foreach ($channel as $key => $val)
	{
		$data[$key] = $val;
	}

	if (array_key_exists($data['channum'], $icon)) {
		$data['icon'] = "public/images/channel_icons/{$icon[$data['channum']]}";
	}
	else
	{
		$data['icon'] = '';
	}

	//set links for playing channel
	if ($config['hdhr_type'] == 'dlna')
	{
		$data['link_direct'] = "http://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{$channel['channum']}";
	}
	else
	{
		$data['link_direct'] = '';
	}

	$data['link_flash'] = "flashplayer.php?channel={$channel['channum']}";


	// Display view 
	echo $view->display('views/channel', $data);
