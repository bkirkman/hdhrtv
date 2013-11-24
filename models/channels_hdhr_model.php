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

class Channels_hdhr_model {

	private $conf;

	function __construct($conf)
	{
		// load config settings file
		$this->conf = $conf;
	}


	function get_all()
	{
		//Get XML from HDHR
		$completeurl = "http://{$this->conf['hdhr_ip']}/lineup.xml";
		$xml = simplexml_load_file($completeurl);
		$programs = $xml->Program;

		// loop through channels and create array
		foreach ($programs as $program)
		{
			$channel['channum'] = (string) $program->GuideNumber;
			$channel['name'] = (string) $program->GuideName;
			$channel['title'] = "";
			$channel['subtitle'] = "";
			$channels[] = $channel;
		}
		return $channels;
	}


	function get_channel($channum)
	{
		//Get XML from HDHR
		$completeurl = "http://{$this->conf['hdhr_ip']}/lineup.xml";
		$xml = simplexml_load_file($completeurl);
		$programs = $xml->Program;

		// loop through channels to get channel number and name
		foreach ($programs as $program)
		{
			if ($program->GuideNumber == $channum)
			{
				$channel['channum'] = (string) $program->GuideNumber;
				$channel['name'] = (string) $program->GuideName;
				$channel['title'] = "";
				$channel['subtitle'] = "";
				break;
			}
		}
		return $channel;
	}
}
