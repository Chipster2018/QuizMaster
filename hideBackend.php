<?php 

function hideIfMain($filename) {
	if ( basename($filename) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
	include("errors\\404.php");
	exit();
}
	
}


?>