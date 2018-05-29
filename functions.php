<?php
// If we are the main, exit
hideIfMain(__FILE__);

// function to check if a given string ($haystack) starts
// with another string ($needle)
function beginsWith($needle,$haystack) {
	return substr($haystack,0,strlen($needle)) == $needle;
}
// function to get the current action
// returns $default id none
/*function getQMAction($default) {
	// if our action is set
	if(isset($_REQUEST["action"])) {
		return $_REQUEST["action"];
	} else {
		return $default;	
	}
}*/
// function to get the QM template
function getQMTemplate() {
	global $QM_root; 
	// for now, just include the template
	require_once($QM_root . "/template.php");
}
// function to add content to the page
function addQMContent($content) {
	global $QM_PAGE;
	// simple .= will do, although a "<BR>\n" could be added
//	echo $content;
	$QM_PAGE["content"] .= $content;	
}
