<?php

// Get configuration data
require ("../config.inc.php");

//Exit if auth key doesn't match
if ($_GET["auth"] != $XMSTREAM_KEY) {
	echo "refused";
	exit;
}

header('Content-Type: audio/mpeg');

// I addapted this from a script I found, but forgot to 
// document where I got this from at the time.  I think
// I got it from http://www.musicpd.org/forum/index.php?topic=1812.0;wap2

//First login
$post_data[user_id] = $XM_USERNAME;
$post_data[pword] = $XM_PASSWORD;

$opts[CURLOPT_POST] = TRUE;
$opts[CURLOPT_POSTFIELDS] = "user_id=" . $post_data[user_id] . "&pword=" . $post_data[pword];
$opts[CURLOPT_COOKIEFILE] = "/tmp/xmstream_cookie";
$opts[CURLOPT_COOKIEJAR] = "/tmp/xmstream_cookie";
$opts[CURLOPT_RETURNTRANSFER] = 1;
$ch = curl_init("http://xmro.xmradio.com/xstream/login_servlet.jsp");

curl_setopt_array($ch, $opts);

$result = curl_exec($ch);
curl_close($ch);

//Load player webpage
$ch = curl_init("http://player.xmradio.com/player/2ft/playMedia.jsp?ch=" . $_GET["chan_play"] . "&speed=high");
$opts[CURLOPT_POST] = FALSE;
unset ($opts[CURLOPT_POSTFIELDS]);

curl_setopt_array($ch, $opts);

$result = curl_exec($ch);
curl_close($ch);

//Get playlist file from webpage
$result = preg_match("/PARAM NAME=\"FileName\" VALUE=\"(.*)\"/", $result, $matches);

//Download playlist file
$ch = curl_init($matches[1]);

curl_setopt_array($ch, $opts);

$result = curl_exec($ch);
curl_close($ch);

//Find stream
$result = preg_match("/Ref href=\"(.*)\"/", $result, $matches);

//Change transport from mms to http
$url = preg_replace("/mms:/", "http:", $matches[1]);

//FIFO from mplayer to lame
$wav_file = "/tmp/xms_streaming" . posix_getpid() . ".wav";
posix_mkfifo($wav_file, 0666);

$descriptorspec = array (
	0 => array (
		"pipe",
		"r"
	), // stdin is a pipe that the child will read from
	1 => array (
		"pipe",
		"w"
	), // stdout is a pipe that the child will write to
	2 => array (
		"file",
		"/tmp/error-output.txt",
		"a"
	) // stderr is a file to write to
	
);

//Start mplayer
$handle = proc_open($XMSTREAM_MPLAYER." -quiet -really-quiet  -vc null -vo null -ao pcm:file=$wav_file $url ", $descriptorspec, $pipes);
//Start lame
$handle2 = proc_open($XMSTREAM_LAME." $wav_file - ", $descriptorspec, $pipes2);

$file2 = $pipes2[1];

while (1) {
	$data = fread($file2, 1024);
	if ($data === FALSE)
		exit ();
	echo $data;
}


?>
