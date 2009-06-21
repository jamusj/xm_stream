<?php
	require("config.inc.php");


        $path=$_SERVER["PATH_INFO"];
        preg_match("/(\d+)/",$path,$channel);
        header('Content-Type: audio/x-mpegurl');
	echo($XMSTREAM_URL."?chan_play=".$channel[0]."&auth=".$XMSTREAM_KEY."\n");
?>
