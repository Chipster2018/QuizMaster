<?php
require_once("boot.php");

if(issetQMAction()) {
	include($QM_root . "/index.php");	
}

// ensure that the user has the privileges to  add quizzes
ensureCurrentUserCan("AddQuiz", "manager-show-addQuiz");


$title = "Add Quiz";
$quizName = "New Quiz";

$numoutcomes=1;
$outcomes[0]="New Outcome 1";
$outcomedescription[0] = "New Outcome 1 Description";

$numquestions=1;
$questionValue[0]="What do you want to ask?";

$numanswers[0]=1;
$answerValue[0][0]="New Answer 1";

$quizID = 0;
$action = "manager-addQuiz";
$submitName = "Add Quiz";


require($QM_root . "/manager/form.php");
?>