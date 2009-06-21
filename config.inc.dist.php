<?php

/* Pulled from http://code.activestate.com/recipes/207176/ */

if( basename( __FILE__ ) == basename( $_SERVER['PHP_SELF'] ) )
{
  exit();
}

# username/password used to login to xmradio
$XM_USERNAME="";
$XM_PASSWORD="";

# unique key used to authenticate with xm_stream.php becasue xm_stream.php may have to reside in 
# a location that isn't authenticated via http auth
$XMSTREAM_KEY="";

# URL of xm_stream.php
$XMSTREAM_URL=""; 

# where mplayer/lame reside
$XMSTREAM_MPLAYER="/sw/bin/mplayer";
$XMSTREAM_LAME="/sw/bin/lame";

?>
