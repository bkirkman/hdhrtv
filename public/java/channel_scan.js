var channelLoop;
var scanLoop; 

$(document).ready(function() {
	if (getScanState() == true)
	{
		showAnimation();
		hideButton();
		$('#scanState').html('Channel scan already in progress...');
  		channelLoop = setInterval("refreshScreen()", 1000);
  		scanLoop = setInterval("scanCheck()", 3000);
	}
	else
	{
		hideAnimation();
		showButton();
		refreshScreen();
	}
});


function initScan()
{
	$('#scanChannels').empty();
	hideButton();
	showAnimation();

	if (getScanState() == false)
	{
  		startScan();
	}
	else
	{
		$('#scanState').html('Channel scan already in progress...');
	}
  
	channelLoop = setInterval("refreshScreen()", 1000);
  	scanLoop = setInterval("scanCheck()", 3000);
}


function scanCheck()
{
	if (getScanState() == false)
	{
		showButton();
		hideAnimation();
		$('#scanState').html('Channel scan complete.');

		clearInterval(channelLoop);	
		clearInterval(scanLoop);	
	}	
}



function startScan()
{
  	var post_url = "scan/functions.php";
	var post_data = "action=startscan";

	$.ajax({
		type: "POST",
		url: post_url,
		data: post_data,
		cache: false,
		success: function(data){
			$('#scanState').html(data);
		}
	});
}


function refreshScreen()
{
  	var post_url = "scan/functions.php";
	var post_data = "action=getchannels";

	$.ajax({
		type: "POST",
		url: post_url,
		data: post_data,
		cache: false,
		success: function(data){
			$('#scanChannels').html(data);
		}
	});
}


function getScanState()
{
  	var post_url = "scan/functions.php";
	var post_data = "action=getstate";

	$.ajax({
		type: "POST",
		url: post_url,
		data: post_data,
		cache: false,
		async: false,
		success: function(data){
			state = data;
		}
	});

	return state;
}


function showAnimation()
{
  	document.getElementById('scan_img').style.visibility='visible';
}


function hideAnimation()
{
  	document.getElementById('scan_img').style.visibility='hidden';
}


function showButton()
{
  	document.getElementById('scan_button').disabled = false;
  	document.getElementById('scan_button').firstChild.data = 'Start New Scan';
}


function hideButton()
{
  	document.getElementById('scan_button').firstChild.data = 'Scanning...';
  	document.getElementById('scan_button').disabled = true;
}
