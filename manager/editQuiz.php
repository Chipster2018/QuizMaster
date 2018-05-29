<?php
// make sure we have database access
require_once("boot.php");
//var_dump($_REQUEST);
//var_dump(getCurrentUserPrivileges());

if(issetQMAction()) {
	include($QM_root . "/index.php");	
}


// set the quiz ID
if (isset($quizID)) { // if we already have a $quizID
	// trust the file that included us.
	// this does nothing, other then make it apparent that we aren't changing the quizID id we already have one
	
} else if (isset($_REQUEST["quizID"])) {
	// if we have a $_REQUEST, then let's use it
	$quizID = $_REQUEST["quizID"];
} else {
	// otherwise, default to quiz 1. Not neccessarily the bet, but it will work for now
	$quizID = 1;
}
// first, make sure the user can even edit quizzes
ensureCurrentUserCan("EditQuiz","manager-show-editQuiz-$quizID");

// get the quiz
$quiz = $db->select("quiz",array("*"),"QuizID=:quizID",array("quizID" => $quizID));

// if we are the creator,
if(getCurrentUserID() == $quiz[0]["CreatorID"]
	){//&& getQMAction("") != "user-login") {
	// then do nothing, or essentially
	// allow the user to continue editing
} else { // if we didn't create this
	// then make sure we can edit all quizzes
	ensureCurrentUserCan("EditAllQuizzes","manager-show-editQuiz-$quizID");


}

$title = "Edit Quiz";


// $quizName = "New Quiz";
$quizName = $quiz[0]["QuizName"];

$quizOutcomes = $db->select("outcomes",array("*"),"QuizID=:quizID",array("quizID" => $quizID));

$numoutcomes=count($quizOutcomes);
for($i=0;$i<$numoutcomes;$i++) {
	$outcomes[$i]=$quizOutcomes[$i]["OutcomeValue"];
	$outcomedescription[$i]=$quizOutcomes[$i]["OutcomeDescription"];
}

$quizQuestion = $db->select("question",array("*"),"QuizID=:quizID ORDER BY QuestionPosition",array("quizID" => $quizID));

$numquestions=count($quizQuestion);
for($i=0;$i<$numquestions;$i++) {
	$questionValue[$i]=$quizQuestion[$i]["QuestionValue"];


	$quizAnswer = $db->select("answer",array("*"),"QuestionID=:questionID",array("questionID" => $quizQuestion[$i]["QuestionID"]));

	$numanswers[$i]=count($quizAnswer);
	for($j=0;$j<$numanswers[$i];$j++) {
		$answerValue[$i][$j]=$quizAnswer[$j]["AnswerValue"];
	}

}

$action = "manager-editQuiz";
$submitName = "Save Quiz";


require($QM_root . "/manager/form.php");
?>