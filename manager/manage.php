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
ensureCurrentUserCan("EditQuiz","manager-manage");

$params;
// All right, now, lets see if they can edit all quizzes
// or just their own
if(currentUserHasPrivilege("EditAllQuizzes")) {
	$params["quizWhere"] = "";
	$params["quizParams"] = array();
	// show the creator
	$params["tableColNames"] = array("QuizName","QuizCreatorName","QuizCreationDate","QuizEdit","SeeStatistics");
	
} else { // they can only edit their own
	// get all the user's quizzes
//	$quizzes = $db->select("quiz", array("*"),"CreatorID=:quizID", array("quizID"=>getCurrentUserID()));

	$params["quizWhere"] = "CreatorID=:quizID"; 
	$params["quizParams"] = array("quizID"=>getCurrentUserID());

	// they are the creator, 
	// don't show the creator, as they are implied
	$params["tableColNames"] = array("QuizName","QuizCreationDate","QuizEdit","SeeStatistics");
}
// the See Statistics column
$params["tableColHead"]["SeeStatistics"] = "Statistics";

$params["tableColValues"]["SeeStatistics"] = "<a class=\"link-button\" href=\"index.php?action=statistics-show-quiz-%QuizID%\">See Statisitcs</a>";


// begin page
addQMContent("<h2>My Quizzes</h2>\n");
addQMContent("<p>Please select a quiz to edit.</p>");

// add the add quiz button if we can add quizzes
if(currentUserHasPrivilege("AddQuiz")) {
	addQMContent("<p><a class=\"link-button\" href=\"index.php?action=manager-show-addQuiz\">Add New Quiz</a></p>\n");
}

// now, create the table
addQMContent( getQuizzesTable($params));


?>