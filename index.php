<?php
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
include_once("boot.php");


$action = getQMAction("browse-quizzes");

// if our action pertains to manage:
if( beginsWith("manager",$action)) {
	// Defer to the manager application
	require($QM_root . "/manager/index.php");
	//exit();
} else if(beginsWith("user",$action)) {
	// Defer to the login application
	require($QM_root . "/login/index.php");
	//exit();
} else if(beginsWith("statistics",$action)) {
	// Defer to the statistics application
	require($QM_root . "/statistics/index.php");

	//exit();
} else if(beginsWith("display",$action)) {
	// Defer to the statistics application
	require($QM_root . "/display/index.php");
}else if($action == "browse-quizzes") {
	$userData = getCurrentUserData();
	// Next, ensure that the user can edit quizzes
	ensureCurrentUserCan("PlayQuiz","browse-quizzes");

	$params;
	// All right, now, lets see if they can edit all quizzes
	// or just their own
	$params["quizWhere"] = "";
	$params["quizParams"] = array();
	// show the creator
	$params["tableColNames"] = array("QuizName","QuizCreatorName","QuizCreationDate","QuizPopularity","Play");
	// the play column
	$params["tableColValues"]["Play"] = "<a class=\"link-button\" href=\"index.php?action=display-quiz-%QuizID%\">Play</a>";
	$params["tableColHead"]["Play"] = "Play";
	// create a link to the statistics for this quiz
	$params["tableColValues"]["QuizName"] = "<a href=\"index.php?action=display-quiz-%QuizID%\">%QuizName%</a>";
	$params["tableTag"] = "class=\"play-list\"";

	// begin main page
	addQMContent("<h2>Welcome to Quizmaster!</h2>\n");
	addQMContent("<p>Please select a quiz to play from the list below.</p>\n");
	
	// now, create the table
	// and add it to the content
	addQMContent(getQuizzesTable($params));

} else {
	addQMContent( $action);	
}

getQMTemplate();
?>