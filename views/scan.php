<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
  <title><?php echo $page_title; ?></title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" type="text/css" href="public/css/styles.css">
  <script src="public/java/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="public/java/channel_scan.js"></script>
</head>

<body>
  <div id="main">
    <div id="fixed_header">
      <img src='public/images/tv_set.png' alt='tv_img' class='header_left'>Channel Scan
      <a class='header_right3' href='index.php?favorites=true'><img src='public/images/fav_head.png' alt='favorites' title='favorites'/></a>
      <a class='header_right2' href='./'><img src='public/images/home.png' alt='home' title='home'/></a>
      <a class='header_right1' href='settings.php'><img src='public/images/settings.png' alt='settings' title='settings'/></a>
    </div>

  <?php if(isset($message)) {;?>
    <strong><?php echo $message;?></strong><br/>
    <br/>
    <br/>
  <?php } else {;?>
    <div id="fixed_header_2">
      <button id="scan_button" type="button" onclick="initScan()">Start New Scan</button>
      <div id="scan_img"><img src="public/images/loading.gif" /></div>
    </div>

    <div id="header_spacer_2">
    </div>
  
    <div class="channel_info">
      <div id="scanState"><br/></div>
      <br/>
      <strong>Channels:</strong>
      <br/>
      <div id="scanChannels"></div>
      <br/>
    </div> <!-- channel_info -->
  <?php };?>

  </div> <!-- main -->
</body>

</html>
