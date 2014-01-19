<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
  <title><?php echo $page_title; ?></title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" type="text/css" href="public/css/styles.css">
  <script src="public/java/jquery-1.10.2.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('.row_right').click(function(e){
        var this_id = $(this).attr('id');
        var split_id = this_id.split("_");
        var favorite = split_id[0];
        var channel = split_id[1];
	var target = $(this);

	if(favorite == 'fav')
	{
		var post_url = "favorites.php";
		var string = 'channel=' + channel + '&action=delete';	

		$.ajax({
			type: "POST",
			url: post_url,
			data: string,
			cache: false,
			success: function(data){
            			target.attr('src','public/images/fav_no.png');
            			target.attr('id','favno_' + channel);
			}
		});

		return false;
	}
	else if(favorite == 'favno')
	{
		var post_url = "favorites.php";
		var string = 'channel=' + channel + '&action=add';	

		$.ajax({
			type: "POST",
			url: post_url,
			data: string,
			cache: false,
			success: function(data){
            			target.attr('src','public/images/fav.png');
            			target.attr('id','fav_' + channel);
			}
		});

		return false;
	}
    })
}); 
</script>



</head>

<body>
  <div id='main'>
    <div id='fixed_header'>
      <img src='public/images/tv_set.png' alt='tv_img' class='header_left'/>Edit Favorites
      <a class='header_right3' href='index.php?favorites=true'><img src='public/images/fav_head.png' alt='favorites' title='favorites'/></a>
      <a class='header_right2' href='./'><img src='public/images/home.png' alt='home' title='home'/></a>
      <a class='header_right1' href='settings.php'><img src='public/images/settings.png' alt='settings' title='settings'/></a>
    </div>

    <div id='header_spacer'>
      <a id='page_top'> - </a>
    </div>

  <?php
    $dark = true;

    // loop through channels
    foreach ($channels as $row)
    {
        if ($dark) {
            $row_color  = "dark";
        } else {
            $row_color  = "";
        }
  ?>

    <div class='<?php echo $row_color;?>'>
      <div class='favorites_row'>
        <strong><?php echo $row['channum'];?> - <?php echo $row['name'];?></strong><br/>
	<br/>
      <?php if ($row['icon'] != '') {;?>
        <img class='row_icon' src='<?php echo $row['icon'];?>' alt='icon' title='icon'>
      <?php };?>
        <a href=#><img class='row_right' id='<?php echo $row['fav_id'];?>' src='<?php echo $row['fav_icon'];?>' alt='<?php echo $row['fav_id'];?>' title='<?php echo $row['fav_id'];?>'></a>
      </div> <!-- favorite_row -->
    </div> <!-- row_color -->

  <?php
    $dark = !$dark;
  };?>

    <div class='channel_row'>
      <a class='row_right' href='#page_top'><img src='public/images/go-top.png' alt='top' title='top'></a>
    </div>

  </div> <!-- main -->
</body>
</html>
