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
 
class Settings
{
	private $user_config;

	// The main user_config array from the config file
	// is passed to the constructor 
	function __construct($user_config)
	{
		$this->user_config = $user_config;
	}

        public function get_user_settings()
        {
                if (isset($_COOKIE[COOKIE_SETTINGS]))
                {
                        //if the user settings cookie is retrieved, unserialize the settings
                        $user_settings = unserialize($_COOKIE[COOKIE_SETTINGS]);
			
			//scrub user settings
			$user_settings = $this->_scrub_user_settings($user_settings);
                }
                else
                {
                        //if there is no user cookie, create default user settings and cookie, which returns default user settings array
                        $user_settings = $this->_create_default_cookie();
                }

                return $user_settings;
        }


	public function get_default_user_settings()
	{
		foreach($this->user_config as $key => $val)
		{
			$user_settings[$key] = $val['default'];
		}

		return $user_settings;
	}


        public function set_user_settings($user_settings)
        {
                $this->_create_cookie($user_settings);
        }


        public function restore_defaults()
        {
		$defaults = $this->get_default_user_settings();
                $this->_create_cookie($defaults);
        }

	public function create_settings_dropdown($setting)
	{
		// get user cookie settings to set the current values
		// for the dropdowns
		$user_settings = $this->get_user_settings();

		$dropdown = "<select name=\"{$setting}\" id=\"{$setting}\">\n";

		// loop through all of the user settings in the config file
		foreach ($this->user_config[$setting]['selections'] as $key => $val)
		{
			if ($key == $user_settings[$setting])
			{
				$dropdown .= "<option value=\"{$key}\" selected=\"selected\">{$val}</option>\n";
			}
			else
			{
				$dropdown .= "<option value=\"{$key}\">{$val}</option>\n";
			}
		}

		$dropdown .= "</select>\n";

		return $dropdown;
	}


        private function _create_default_cookie()
        {
		$user_settings = $this->get_default_user_settings();

                $this->_create_cookie($user_settings);

                return $user_settings;
        }


        private function _create_cookie($user_settings)
        {
                setcookie(COOKIE_SETTINGS, serialize($user_settings), time() + 60 * 60 * 24 * 365 * 2);
        }


	// This adds defaults to user settings if they don't exist and
	// deletes settings from user settings that are no longer part
	// of the settings defined in the config and app.
	private function _scrub_user_settings($user_settings)
	{
		$scrubbed = false;
		$default_settings = $this->get_default_user_settings();

		// Iterate through user settings. If the key
		// doesn't exist as a key in the default
		// user setting array, dump it.
		foreach($user_settings as $key => $val)
		{
			if(!array_key_exists($key, $default_settings))
			{
				$scrubbed = true;
				unset($user_settings[$key]);
			}
		}

		// Iterate through default settings. If the key
		// doesn't exist as a key in the user settings
		// array, add the setting and it's default to the
		// user settings array.
		foreach($default_settings as $key => $val)
		{
			if(!array_key_exists($key, $user_settings))
			{
				$scrubbed = true;
				$user_settings[$key] = $val;	
			}
		}

		// save user settings in case of any scrubbing
		if($scrubbed)
		{
			$this->_create_cookie($user_settings);
		}

		return $user_settings;
	}
}
