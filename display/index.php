<?php
// Make sure we have have booted
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}
// check for the action
$action = getQMAction("display-quiz-1");

if(beginsWith("display-quiz",$action)) {
	$quizID = substr(strrchr($action,'-'),1);
	
	unsetQMAction();
	
	require("play.php");
} else if($action == "display-answer") {
	unsetQMAction();
	
	require("answer.php");	
}



?>