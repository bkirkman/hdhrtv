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

class Channels_mythtv_model {

	private $conf;
	private $sql_con;


	function __construct($conf)
	{
		// load config settings file
		$this->conf = $conf;

		//create MythTV database MySQL connection
		$this->sql_con = mysqli_connect($this->conf['mythtv_server'], $this->conf['mythtv_user'], $this->conf['mythtv_pass'], $this->conf['mythtv_db']);
		if (!$this->sql_con) {
			die('Could not connect to MythTV database. Check connection or settings: ' . mysqli_error($con));
		}
	}


	function get_all()
	{
		//query database for channel info
		$sql = "select channum, name, title, subtitle from program " .
			"inner join channel on program.chanid = channel.chanid where starttime <= utc_timestamp() and endtime > utc_timestamp() order by channum + 0";
		$result = mysqli_query($this->sql_con, $sql);

		// loop through channels and create array
		while($row = mysqli_fetch_assoc($result))
		{
			foreach ($row as $key => $val)
			{
				$channel[$key] = $val;
			}
			$channels[] = $channel;
		}
		mysqli_free_result($result);
		return $channels;
	}


	function get_channel($channum)
	{
		$sql = "select channum, name, title, subtitle, description, category, starttime, endtime, subtitle from program" .
			" inner join channel on program.chanid = channel.chanid where channum = " . $channum .
			" and starttime <= utc_timestamp() and endtime > utc_timestamp()";
		$result = mysqli_query($this->sql_con, $sql);

		// get channel info as single row array
		$row = mysqli_fetch_assoc($result);

		return $row;
	}
}
