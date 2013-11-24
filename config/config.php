<?php  if ( ! defined('CONFIG')) exit('No direct script access allowed');


#################################
#                               #
#        General Config         #
#                               #
#################################

// Web Page Title
$config['page_title'] = 'HDHR TV';

// HDHomeRun protocol, IP, and port settings
$config['hdhr_protocol'] = 'http';
$config['hdhr_ip'] = '192.168.1.10';
$config['hdhr_port'] = '5004';

// Set HDHR tuner
// This can be tuner0, tuner1, tuner2, or auto.
// Note that if MythTV is being used, MythTV
// won't lock the HDHR tuner, so conflicts could
// exist between MythTV and anything else, such as
// this streaming app, that tries to access the HDHR.
// If using MythTV, set this to tuner2 and hope that
// MythTV will only need to use tuner0 and tuner 1.
// If not using MythTV, set this to auto.
//
// $config['hdhr_tuner'] = [tuner0 | tuner1 | tuner2 | auto]
$config['hdhr_tuner'] = 'auto';

// Set the command to reboot the HDHomeRun.
// Leave blank to diable this feature.
$config['hdhr_reboot_cmd'] = "hdhomerun_config {$config['hdhr_ip']}  set /sys/restart self";

// Enable MythTV listings to be used.
// The user will be able to choose between
// MythTV listings or HDHR generic listings
// if this is enabled. If not, only the 
// HDHR generic listing will be used.
// $config['enable_mythtv'] = [true | false];
$config['enable_mythtv'] = false;

// MythtTV database settings if MythTV
// listings are to be used.
$config['mythtv_server'] = 'localhost';
$config['mythtv_db'] = 'mythconverg';
$config['mythtv_user'] = 'mythtv';
$config['mythtv_pass'] = 'mythtv';


#################################
#                               #
#        FFmpeg Config          #
#                               #
#################################

// The following are ffmpeg commands.
// The variable name is placed in the user setting array,
// named, and in turn selected by the user. 

// This is the original ffmpeg command used during development.
// This is similar to the transoding command used by MythWeb.
// This creates a standard flv stream using the sorenson codec.
// This cannot be multithreaded so it most likely won't work
// very well for HD video.
$ffmpeg_cmd['flv'] = "ffmpeg"
		." -y"
		." -i {$config['hdhr_protocol']}://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{channel}"
		." -threads 0"
		." -g 30"
		." -f flv"
		." -async 2"
		." -ac 2"
		." -ar {asamp}"
		." -b:a {abr}"
		." -vf {deint}"
		."scale=\"{size}\""
		." -b:v {vbr}"
		." /dev/stdout 2>/dev/null";

// This command transcodes to h264 video, mp3 audio,
// and places in an flv container. The libx264 library
// can be multithreaded which is better suited for HD video.
$ffmpeg_cmd['h264'] = "ffmpeg"
		." -y"
		." -i {$config['hdhr_protocol']}://{$config['hdhr_ip']}:{$config['hdhr_port']}/{$config['hdhr_tuner']}/v{channel}"
		." -threads 0"
		." -f flv"
		." -vf {deint}"
		."scale=\"{size}\""
		." -pix_fmt yuv420p"
		." -c:v libx264"
		." -preset:v ultrafast"
		." -b:v {vbr}"
		." -c:a libmp3lame"
		//." -async 2"
		." -ac 2"
		." -b:a {abr}"
		." -ar {asamp}"
		." -strict -2"
		." /dev/stdout 2>/dev/null";



#################################
#                               #
#     User Settings Config      #
#                               #
#################################

// Set the cookie name for storing user settings
define('COOKIE_NAME', 'hdhrtv_settings');

// The following are user settings
// and their respective defaults.
//
// The key => val pairs of the 'selections'
// array will populate the dropdowns in the
// settings page.

// Pull channel data from MythTV or HDHR.
$user_config['listings'] =
	array('selections' => 
		array('mythtv' => 'MythTV',
		      'hdhr' => 'HDHomeRun'),
		'default' => 'hdhr');
	
// Set the link for the play button shown
// on each individual line in the channel list.
$user_config['play_link'] =
	array('selections' => 
		array('flash' => 'Flash Player',
		      'direct' => 'Direct Link'),
		'default' => 'flash');

// Set the video size either as a defined size or
// scaled to the original source.
$user_config['vid_size'] =
	array('selections' => 
		array('640:480' => '640x480 (4:3)',
		      '480:368' => '480x360 (4:3)',
		      '320:240' => '320x240 (4:3)',
		      '1280:720' => '1280x720 (16:9)',
		      '960:544' => '960x540 (16:9)',
		      '640:368' => '640x360 (16:9)',
		      '320:176' => '320x180 (16:9)',
		      'iw/4*sar:ih/4' => '1/4 scale',
		      'iw/3*sar:ih/3' => '1/3 scale',
		      'iw/2*sar:ih/2' => '1/2 scale',
		      'iw*2/3*sar:ih*2/3' => '2/3 scale',
		      'iw*3/4*sar:ih*3/4' => '3/4 scale',
		      'iw*sar:ih' => 'full scale'),
		'default' => 'iw/2*sar:ih/2');

// Set the video bitrate used by ffmpeg
// to transocde.
$user_config['vid_br'] =
	array('selections' => 
		array('500k' => '500k',
		      '700k' => '700k',
		      '1000k' => '1000k',
		      '1500k' => '1500k',
		      '2000k' => '2000k',
		      '2500k' => '2500k',
		      '3000k' => '3000k',
		      '3500k' => '3500k',
		      '4000k' => '4000k',
		      '4500k' => '4500k',
		      '5000k'=> '5000k'),
		'default' => '1000k');

// Set the audio bitrate used by ffmpeg
// to transocde.
$user_config['aud_br'] =
	array('selections' => 
		array('32k' => '32k',
		      '64k' => '64k',
		      '96k' => '96k',
		      '128k' => '128k',
		      '192k' => '192k'),
		'default' => '32k');

// Set the audio sample rate used by ffmpeg
// to transocde.
$user_config['aud_samp'] =
	array('selections' => 
		array('11025' => '11025',
		      '22050' => '22050',
		      '44100' => '44100',
		      '48000' => '48000'),
		'default' => '11025');

// Enable or disable deinterlacing
$user_config['deint'] =
	array('selections' => 
		array('yadif,' => 'Yes',
		      '' => 'No'),
		'default' => 'yadif,');

// Set up the transcoding selection.
// This selects from the ffmpeg commands above.
$user_config['transcode'] =
	array('selections' => 
		array('h264' => 'h264/mp3/flv (multithread)',
		      'flv' => 'FLV (no multithread)'),
		'default' => 'h264');
