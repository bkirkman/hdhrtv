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

class Channels_model {

	private $list_type;
	private $hdhr_type;
	private $programs;
	private $sql_con;
	private $is_con;
	private $con_err;

	function __construct($config, $user)
	{
		$this->list_type = $user['listings'];
		$this->hdhr_type = $config['hdhr_type'];
		
        	if ($user['listings'] == 'mythtv' && $config['enable_mythtv'] == true)
        	{
                	//create MythTV database MySQL connection
                	$this->sql_con = @mysqli_connect($config['mythtv_server'], $config['mythtv_user'],
					$config['mythtv_pass'], $config['mythtv_db']);

			$this->is_con = true;

                	if (mysqli_connect_errno()) {
                        	$this->is_con = false;
                                $this->con_err = 'Error connecting to MySQL database for MythTV listings. ' .
					'Check connection and configuration settings. Error Message: ' . mysqli_connect_error();
                	}

			// We also need to load the listings.xml model
			// if this is a legacy tuner because we have
			// to get the associated channel frequency
			// and program. Kind of hackish.
			if ($this->hdhr_type == 'legacy')
			{
				$this->_constructLegacy();
			}
        	}	
        	elseif ($config['hdhr_type'] == 'dlna')
        	{
			//Get XML from HDHR
			$hdhr_url = "http://{$config['hdhr_ip']}/lineup.xml";

			if($this->_remoteFileExists($hdhr_url))
			{
				$xml = simplexml_load_file($hdhr_url);
				$this->programs = $xml->Program;
				$this->is_con = true;
			}
			else
			{
                        	$this->is_con = false;
                                $this->con_err = 'Could not open lineup.xml file from HDHR device.';
			}
		}
		elseif ($config['hdhr_type'] == 'legacy')
		{
			$this->_constructLegacy();
		}
		else
		{
                        	$this->is_con = false;
                                $this->con_err = 'Error connecting to channel lineup. ' .
					'Check configuration settings for errors.';
		}
	}


	function _constructLegacy()
	{
                //Get XML from HDHR
                $hdhr_url = "scan/lineup.xml";

                if(file_exists($hdhr_url))
                {
                	$xml = simplexml_load_file($hdhr_url);
			$this->programs = $xml->Program;
                        $this->is_con = true;
		}
		else
		{
                       	$this->is_con = false;
                	$this->con_err = 'Could not open lineup.xml file. ' .
                               		'Run channel scan from settings page.';
		}
	}


        function is_connected()
        {
        	return $this->is_con;
        }


	function get_error()
	{
		return $this->con_err;
	}


	function get_all()
	{
		if ($this->list_type == 'mythtv')
		{
			return $this->_get_all_mythtv();
		}		
		elseif ($this->hdhr_type == 'dlna' || $this->hdhr_type == 'legacy')
		{
			return $this->_get_all_hdhr();
		}
	}


	function get_channel($channum)
	{
		if ($this->list_type == 'mythtv')
		{
			return $this->_get_channel_mythtv($channum);
		}		
		elseif ($this->hdhr_type == 'dlna' || $this->hdhr_type == 'legacy')
		{
			return $this->_get_channel_hdhr($channum);
		}
	}


	function get_channels(Array $channums)
	{
		if ($this->list_type == 'mythtv')
		{
			return $this->_get_channels_mythtv($channums);
		}		
		elseif ($this->hdhr_type == 'dlna' || $this->hdhr_type == 'legacy')
		{
			return $this->_get_channels_hdhr($channums);
		}
	}


        private function _get_all_mythtv()
        {
                $channels = false;

                //query database for channel info
                $sql = "select channum, name, title, subtitle from program " .
                        "inner join channel on program.chanid = channel.chanid where starttime <= utc_timestamp() " .
                        "and endtime > utc_timestamp() order by channum + 0";
                $result = mysqli_query($this->sql_con, $sql);

                // Return false if no results
                if (!$result)
                {
                        return false;
                }

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


	private function _get_all_hdhr()
	{
		$channels = false;

		// loop through channels and create array
		foreach ($this->programs as $program)
		{
			$channel['channum'] = (string) $program->GuideNumber;
			$channel['name'] = (string) $program->GuideName;
			$channel['title'] = "";
			$channel['subtitle'] = "";
			$channel['starttime'] = "";
			$channel['endtime'] = "";
			$channels[] = $channel;
		}
		return $channels;
	}


        private function _get_channel_mythtv($channum)
        {
                $row = false;

                $sql = "select channum, name, title, subtitle, description, category, starttime, endtime, subtitle from program" .
                        " inner join channel on program.chanid = channel.chanid where channum = " . $channum .
                        " and starttime <= utc_timestamp() and endtime > utc_timestamp()";
                $result = mysqli_query($this->sql_con, $sql);

                // Return false if no results
                if (!$result)
                {
                        return false;
                }

                // get channel info as single row array
                $row = mysqli_fetch_assoc($result);
                mysqli_free_result($result);

		// if using a legacy HDHR box along with MythTV
		// listings, we need to get the frequency and 
		// program associated with the channel in order
		// to tune it. I couldn't find the frequency and
		// program in the mythconverg database. If someone
		// else can, feel free to add the specific db columns
		// to this model instead parsing the lineup.xml file.
		// Kind of hackish.
		if ($this->hdhr_type == 'legacy')
		{
			foreach ($this->programs as $program)
			{
				if ($program->GuideNumber == $channum)
				{
					$row['tune_chan'] = (string) $program->TunerChannel;
					$row['tune_prog'] = (string) $program->TunerProgram;
				}
			}
		}

                return $row;
        }


	private function _get_channel_hdhr($channum)
	{
		$channel = false;		

		// loop through channels to get channel number and name
		foreach ($this->programs as $program)
		{
			if ($program->GuideNumber == $channum)
			{
				$channel['channum'] = (string) $program->GuideNumber;
				$channel['name'] = (string) $program->GuideName;

				if ($this->hdhr_type == 'legacy')
				{
                                	$channel['tune_chan'] = (string) $program->TunerChannel;
                                	$channel['tune_prog'] = (string) $program->TunerProgram;
				}

				$channel['title'] = "";
				$channel['subtitle'] = "";
				$channel['starttime'] = "";
				$channel['endtime'] = "";
				break;
			}
		}
		return $channel;
	}


        private function _get_channels_mythtv(Array $chan_nums)
        {
                $channums = join("','", $chan_nums);
                $channels = false;

                //query database for channel info
                $sql = "select channum, name, title, subtitle from program " .
                        "inner join channel on program.chanid = channel.chanid where channum in ('$channums')" .
                        " and starttime <= utc_timestamp() and endtime > utc_timestamp() order by channum + 0";

                $result = mysqli_query($this->sql_con, $sql);

                // Return false if no results
                if (!$result)
                {
                        return false;
                }

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


	private function _get_channels_hdhr(Array $channums)
	{
		$channels = false;		

		// loop through channels to get channel number and name
		foreach ($this->programs as $program)
		{
			if (array_search($program->GuideNumber, $channums) !== false)
			{
				$channel['channum'] = (string) $program->GuideNumber;
				$channel['name'] = (string) $program->GuideName;
				$channel['title'] = "";
				$channel['subtitle'] = "";
				$channel['starttime'] = "";
				$channel['endtime'] = "";
				$channels[] = $channel;
			}
			
		}
		return $channels;
	}

	private function _remoteFileExists($url) {
		$ret = true;
		if (false === @fopen($url, 'r')) {
    			$ret = false;
		}
		return $ret;
	}


	private function _remoteFileExists2($url) {
		$curl = curl_init($url);

		//don't fetch the actual page, you only want to check the connection is ok
		curl_setopt($curl, CURLOPT_NOBODY, true);

		//do request
		$result = curl_exec($curl);

		$ret = false;

		//if request did not fail
		if ($result !== false) {
			//if request was ok, check response code
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

			if ($statusCode == 200) {
				$ret = true;   
			}
		}

		curl_close($curl);

		return $ret;
	}
}
