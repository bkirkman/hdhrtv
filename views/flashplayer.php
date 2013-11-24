<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
  <title><?php echo $page_title; ?></title>
  <script src="public/flowplayer/flowplayer-3.2.12.min.js"></script>
</head>

<body>

<div id='flash_player' style='display:block;width:720px;height:396px'>
</div>
<script type="text/javascript">
    $f('flash_player', 'public/flowplayer/flowplayer-3.2.16.swf', {
         play:
                {               
                    opacity: 0.0,
                    label: null, // label text; by default there is no text
                    replayLabel: null, // label text at end of video clip
                },
                clip: {live: true,
                        autoPlay: true,
			onBegin: function () {
				this.setVolume(100); // set volume property
			}
                },
                canvas: {
                        backgroundGradient: 'none'
                },
                    playlist: [
                        // Then we have the video
                        {
                            url: "<?php echo $stream_url;?>",
                            autoPlay: true,
                            scaling: 'fit',
                            // Would be nice to auto-buffer, but we don't want to
                            // waste bandwidth and CPU on the remote machine.
                            autoBuffering: false
                            }
                        ],
                plugins: {
                        controls: {
                                tooltips: {
					buttons: true,
                                },
				tooltipColor: '#112233',
				tooltipTextColor: '#8899ff',
				scrubber: false
				}
                        }
    
        });
</script>

</body>

</html>
