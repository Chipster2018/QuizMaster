<?php
/************************/
/* This file shows all the quizzes the current user
   can edit and displays them in a nice way 
   */
   
// first, boot
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}

global $db;

// Next, ensure that the user can edit quizzes
ensureCurrentUserCan("SeeStatistics","statistics-show-table");

$params;
// All right, now, lets see if they can edit all quizzes
// or just their own
if(currentUserHasPrivilege("SeeAllStatistics")) {
	$params["quizWhere"] = "";
	$params["quizParams"] = array();
	// show the creator
	$params["tableColNames"] = array("QuizName","QuizCreatorName","QuizCreationDate");

	// create a link to the statistics for this quiz
	$params["tableColValues"]["QuizName"] = "<a href=\"index.php?action=statistics-show-quiz-%QuizID%\">%QuizName%</a>";
} else { // they can only edit their own

	// get all the user's quizzes
	$params["quizWhere"] = "CreatorID=:quizID"; 
	$params["quizParams"] = array("quizID"=>getCurrentUserID());

	// they are the creator, 
	// don't show the creator, as they are implied
	$params["tableColNames"] = array("QuizName","QuizCreationDate");
	// create a link to the statistics for this quiz
	$params["tableColValues"]["QuizName"] = "<a href=\"index.php?action=statistics-show-quiz-%QuizID%\">%QuizName%</a>";
}
	$params["tableTag"] = "class=\"statistics-list\"";

// DEBUG: show the quizzes
//var_dump($quizzes);

// begin page:

addQMContent("<h2>Statistics</h2>\n");
addQMContent("<p>Please select a quiz from the list below to see its statistics.</p>\n");


// now, create the table
// and add it to the content
addQMContent(getQuizzesTable($params));


?>