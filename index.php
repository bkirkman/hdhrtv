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

	// Call this to load config settings and classes
	include('./includes/main.php');

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

	//get channels
	$channels = $model->get_all();

	// edit channel data for use in view 
	foreach ($channels as $key => $val)
	{
		// append subtitle to title if it exists
		if ($channels[$key]['subtitle'] != "" )
		{
			$channels[$key]['title'] .= ' - ' . $channels[$key]['subtitle'];
		}

		// if title is blank, place a <br> tag in the title value so that there is a blank
		// line in channel listing view so that the channels don't squash each other 
		if ($channels[$key]['title'] == "" )
		{
			$channels[$key]['title'] = '<br>';
		}

		// add link to channel details
		$channels[$key]['link_details'] = "channel.php?id={$channels[$key]['channum']}";

		// add link to channel play
		if ($user_settings['play_link'] == "direct")
		{
			$channels[$key]['link_play'] = 
			"{$config['hdhr_protocol']}://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{$channels[$key]['channum']}";
		}
		else
		{
			$channels[$key]['link_play'] = "flashplayer.php?channel={$channels[$key]['channum']}";
		}


        	if (array_key_exists($channels[$key]['channum'], $icon)) {
			$channels[$key]['icon'] = "public/images/channel_icons/{$icon[$channels[$key]['channum']]}";
		}
		else
		{
			$channels[$key]['icon'] = '';
		}
	}

	// Set channel data to display in view
	$data['channels'] = $channels;

	// Set page title
	$data['page_title'] = $config['page_title'];

	// Load and display view
	$view = new View();
	echo $view->display('views/listings', $data);
