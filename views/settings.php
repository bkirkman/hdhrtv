<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
  <title><?php echo $page_title; ?></title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" type="text/css" href="public/css/styles.css">
</head>

<body>
  <div id="main">
    <div id="fixed_header">
      <img src='public/images/tv_set.png' alt='tv_img' class='header_left'>Settings
      <a class='header_right2' href='index.php?favorites=true'><img src='public/images/fav_head.png' alt='favorites' title='favorites'/></a>
      <a class='header_right1' href='./'><img src='public/images/home.png' alt='home' title='home'/></a>
    </div>

    <div id="header_spacer">
    </div>

    <div class="dark">
    <form action="settings.php" method="post">
    <?php if($user_tuner == true) {;?>
      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['tuner'];?>
        </div> <!-- right -->
        HDHR Tuner:<br/>
        <span class="small">Select tuner to use for streaming. Tuner setting 'auto' available for DLNA devices only. Non-DLNA devices will error if 'auto' is selected.</span>
      </div> <!-- setting_row -->
    <?php };?>


    <?php if($enable_mythtv == true) {;?>
      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['listings'];?>
        </div> <!-- right -->
        Listing Source:<br/>
        <span class="small">Choose between detailed MythtTV listings or generic HDHomeRun channel numbers. MythTV listings will take longer to load.</span>
      </div> <!-- setting_row -->
    <?php };?>


    <?php if($hdhr_type == 'dlna') {;?>
      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['play_link'];?>
        </div> <!-- right -->
        Play Link:<br/>
        <span class="small">Set the link for the play button shown on each individual line in the channel list.</span>
      </div> <!-- setting_row -->
    <?php };?>


      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['vid_size'];?>
        </div>
        Video Size:<br/>
        <span class="small">Resolution of streaming video.</span>
      </div>

      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['vid_br'];?>
        </div>
        Video Bitrate:<br/>
        <span class="small">Video bitrate of streaming video.</span>
      </div>

      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['aud_br'];?>
        </div>
        Audio Bitrate:<br/>
        <span class="small">Audio bitrate of streaming video.</span>
      </div>

      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['aud_samp'];?>
        </div>
        Audio Sample Rate:<br/>
        <span class="small">Audio sample rate of streaming video.</span>
      </div>

      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['deint'];?>
        </div>
        Deinterlace:<br/>
        <span class="small">Choose whether or not to deinterlace source video.</span>
      </div>

      <div class="setting_row">
        <div class="right">
          <?php echo $dropdown['transcode'];?>
        </div>
        Transcoding:<br/>
        <span class="small">Select transcoding profile. The individual profiles are ffmpeg commands defined in the configuration file.</span>
      </div>

      <input type="hidden" name="hidden_settings_post" id="hidden_settings_post" value="hidden_settings_post">

      <input class="button" type="submit" value="Save"> 
    </form>

    <br/>

      <form action="settings.php" method="post">
        <input type="hidden" name="restore_defaults" id="restore_defaults" value="true">
        <input class="button" type="submit" value="Restore Defaults"> 
      </form>

      <br/>
      <hr/>

    <?php if($hdhr_type == 'legacy') {;?>
      <div class="setting_row">
        <a href="scan.php"><img src="public/images/go-next.png"></a>
        Channel Scan:<br/>
        <span class="small">Run a channel scan for non-DLNA devices and save to web server. This only needs to be run once and upon any kind of frequency table or channel mapping changes in the broadcast signal.</span>
      </div>
      <hr/>
    <?php };?>

    <?php if($reboot == true) {;?>
      <div class="setting_row">
        <a href="settings.php?action=reboot_tuner"><img src="public/images/go-next.png"></a>
        Reboot Tuner:<br/>
        <span class="small">At times the HDHomeRun tuner can hang. This will reboot the HDHomeRun if properly set up in the config file.</span>
      </div>
    <?php };?>

    </div> <!-- class="dark" -->
  </div> <!-- main -->
</body>

</html>
