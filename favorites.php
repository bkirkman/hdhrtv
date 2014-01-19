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

	// Load favorites class
	$favorites = new Favorites();

	// ajax function to add or delete favorites
	if(isset($_POST['channel']) && isset($_POST['action']))
	{

		if(!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')))
		{
			echo('You do not have permission to access this function.');
		}

		$channel = $_POST['channel'];
		$action = $_POST['action'];

		if ($action == 'add')
		{
			$channels = $favorites->add_favorite($channel);
		}
		elseif ($action = 'delete')
		{
			$channels = $favorites->delete_favorite($channel);
		}

		echo $channels;
		die();
	}

	// Get current favorite channels
	$favorite_channels = $favorites->get_favorites();
	
	// Get channel listings
	if(!$channels = $model->get_all())
	{
		$channels = array();
	}

	// Iterate through favorites and delete if not included in listing.
	// Create array of channels
	foreach ($channels as $channel)
	{
		$chan_array[] = $channel['channum'];
	}

	// Iterate through favorites. If the favorite
	// doesn't exist as a value in the chan_array,
	// dump it.
	foreach($favorite_channels as  $favorite_channel)
	{
                if(($key = array_search($favorite_channel, $chan_array)) === false)
		{
			$favorites->delete_favorite($favorite_channel);
		}
	}
	
	// Get current favorite channels
	$favorite_channels = $favorites->get_favorites();

	// edit channel data for use in view 
	foreach ($channels as $key => $val)
	{
		// Get channel icon
        	if (array_key_exists($channels[$key]['channum'], $icon)) {
			$channels[$key]['icon'] = "public/images/channel_icons/{$icon[$channels[$key]['channum']]}";
		}
		else
		{
			$channels[$key]['icon'] = '';
		}

		// Get channel favorite icon
                if(($fav_key = array_search($channels[$key]['channum'], $favorite_channels)) !== false)
		{
			$channels[$key]['fav_icon'] = "public/images/fav.png";
			$channels[$key]['fav_id'] = "fav_" . $channels[$key]['channum'];
		}
		else
		{
			$channels[$key]['fav_icon'] = "public/images/fav_no.png";
			$channels[$key]['fav_id'] = "favno_" . $channels[$key]['channum'];
		}
	}

	// Set channel data to display in view
	$data['channels'] = $channels;

	// Display view
	echo $view->display('views/favorites', $data);
