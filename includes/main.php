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

	//error_reporting(E_ALL);
	//ini_set('display_errors', 'on');
	define('CONFIG', 'my_config');
	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/channel_icon_map.php');
	include_once(dirname(__FILE__).'/../classes/view.php');
	include_once(dirname(__FILE__).'/../classes/settings.php');

	$settings = new Settings($user_config);
	$user_settings = $settings->get_user_settings();
