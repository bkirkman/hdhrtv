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

	// Call this to load config settings and classes
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

        // Check if viewing favorites
	$favorites = false;
        if (isset($_GET["favorites"]))
        {
        	if ($_GET["favorites"] == 'true')
                {
			$favorites = true;
			
			// Set header title
			$data['header_title'] = 'Favorites';
			$data['listing_type'] = 'favorites';
                }
        }
	else
	{
		// Set header title
		$data['header_title'] = 'Current Listings';
		$data['listing_type'] = 'home';
	}

	//get channels
	if($favorites)
	{
		$favorites = new Favorites();
		$favorite_channels = $favorites->get_favorites();
		if(!$channels = $model->get_channels($favorite_channels))
		{
			$channels = array();
		}
	}
	else
	{
		if(!$channels = $model->get_all())
		{
			$channels = array();
		}
	}

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
		if ($user_settings['play_link'] == "direct" && $config['hdhr_type'] == "dlna")
		{
			$channels[$key]['link_play'] = 
			"http://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{$channels[$key]['channum']}";
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

	// Display view
	echo $view->display('views/listings', $data);
