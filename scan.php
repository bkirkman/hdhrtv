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

	// check tuner type
	if ($config['hdhr_type'] != 'legacy')
	{
		// Set message
		$data['message'] = 'Channel scan only avaialable for non-DLNA tuners. Change configuration ' . 
					'settings on server to legacy non-DLNA tuner if required.';
	
		// Display view
		echo $view->display('views/notice', $data);
		die();
	}

	// Display view
	echo $view->display('views/scan', $data);
