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

	// Call this to load config settings and View class
	include('./includes/main.php');
 
	// get channel
	if (isset($_GET["id"]))
	{
		$channel_id = $_GET["id"];
	}
	else
	{
		echo "No channel set.";
		die();
	}

	//Use either MythTV database or HDHR data to create channels and info model
	if ($user_settings['listings'] == 'mythtv' && $config['enable_mythtv'] == true)
	{
		include('./models/channels_mythtv_model.php');
		$model = new Channels_mythtv_model($config);
	}
	else
	{
		include('./models/channels_hdhr_model.php');
		$model = new Channels_hdhr_model($config);
	}

	//get channel data
	$channel = $model->get_channel($channel_id);

	// append subtitle to title if it exists
	if ($channel['subtitle'] != "" )
	{
		$channel['title'] .= ' - ' . $channel['subtitle'];
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
	$data['link_direct'] = "{$config['hdhr_protocol']}://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{$channel['channum']}";
	$data['link_flash'] = "flashplayer.php?channel={$channel['channum']}";

	// Set page title
	$data['page_title'] = $config['page_title'];

	// Load and display view 
	$view = new View();
	echo $view->display('views/channel', $data);
