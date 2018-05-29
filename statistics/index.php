<?php
// first, boot
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}

$action = getQMAction("statistics-show-table");

if($action == "statistics-show-table") {
	unsetQMAction();
	
	require($QM_root . "/statistics/statTable.php");
} else if (beginsWith("statistics-show-quiz",$action)) {
	// get the quizID
	$quizID = substr(strrchr($action,'-'),1);

	// unset the QM action
	unsetQMAction();
	
	// include the edit page
	require($QM_root . "/statistics/quizStatistics.php");
	
}


?>