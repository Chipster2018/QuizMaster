<?php
// Make sure we have database access
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}
//require_once($_SERVER['DOCUMENT_ROOT'] . "/quizmaster/database/load_db.php");

// set the action if we don't have it already
/*if( isset($_REQUEST["action"]) ) {
	$action = $_REQUEST["action"];
} else {
	$action = "manager-show-addQuiz";	
}*/
$action = getQMAction("manager-manage");
setQMAction($action); // set it to s=whatever we are doing

//var_dump($_REQUEST);

// a switch statement could work her, but I decided to use an if-elseif here instead
if($action == "manager-addQuiz") { // if we are adding a quiz
	$quizName = filter_input(INPUT_POST,"quizName");
	$outcomeValue = $_POST["outcome"];
	$outcomeDescription = $_POST["outcomedesc"];
	$numoutcomes = filter_input(INPUT_POST,"numoutcomes");
	$questionValue = $_POST["questionValue"];
	$answerValue = $_POST["answerValue"];
	$answerOutcome = $_POST["answerOutcome"];
	$numanswers = $_POST["numanswers"];
	$numquestions = filter_input(INPUT_POST,"numquestions");	
		
	// Insert the quiz name
	$db->insert("quiz",array("QuizName","CreatorID"), 
					array(":quizName",":creatorID"),
					array( "quizName" => $quizName,
					"creatorID" => getCurrentUserID()));	
	// get the ID for later use	
	$quizID = $db->queryDB("SELECT last_insert_id()");
//	echo "QuizID: " . var_dump($quizID) . "<br>\n";
	$quizID = $quizID[0][0];
//	$quizID = mysql_insert_id();
	// for every outcome
	for($i=0;$i<$numoutcomes;$i++) {
		// Insert it into the db
		$db->insert("outcomes",
			array("QuizID","OutcomeValue","OutcomeDescription"), 
			array($quizID,":outcomeValue",":outcomeDescription"),
			array( "outcomeValue" => $outcomeValue[$i],
					"outcomeDescription" => $outcomeDescription[$i]));	
	
		// get the ID and store it for later use	
		$outcomeIDs[$i] = $db->queryDB("SELECT last_insert_id()");
		$outcomeIDs[$i] = $outcomeIDs[$i][0][0];
	}
	
	// for every question
	for($i=0;$i<$numquestions;$i++) {
		// Insert it into the db
		$db->insert("question",
			array("QuizID","QuestionPosition","QuestionValue"), 
			array($quizID,$i,":questionValue"),
			array( "questionValue" => $questionValue[$i]));	
		// get the ID and store it for later use	
		$questionIDs[$i] = $db->queryDB("SELECT last_insert_id()");
		$questionIDs[$i] = $questionIDs[$i][0][0];
	
		// add the answers
		for($j=0;$j<$numanswers[$i];$j++) {
			// Insert it into the db
			$db->insert("answer",
				array("QuestionID","OutcomeID","AnswerValue"), 
				array($questionIDs[$i],$outcomeIDs[$answerOutcome[$i][$j]],":answerValue"),
				array( "answerValue" => $answerValue[$i][$j]));	
		}
	}
	// finally, it would be nice to see the edit page again
	
	// first, make sure no action is set
	unset($_REQUEST["action"]);
	// then include form
	include($QM_root . "/manager/editQuiz.php");

} else if($action == "manager-editQuiz") {// if we are editing a quiz
	// get the input data
	$quizID = filter_input(INPUT_POST,"quizID", FILTER_VALIDATE_INT);
	$quizName = filter_input(INPUT_POST,"quizName");
	$outcomeValue = $_POST["outcome"];
	$outcomeDescription = $_POST["outcomedesc"];
	$numoutcomes = filter_input(INPUT_POST,"numoutcomes");
	$questionValue = $_POST["questionValue"];
	$answerValue = $_POST["answerValue"];
	$answerOutcome = $_POST["answerOutcome"];
	$numanswers = $_POST["numanswers"];
	$numquestions = filter_input(INPUT_POST,"numquestions");	
		
	// First, Update the quiz name
	$db->update("quiz",array("QuizName"=>":quizName"),
					"QuizID=:quizID",
					array( "quizName" => $quizName,
							"quizID" => $quizID ));	
		
	// Now, remove	old outcomes				
	$db->delete("outcomes", "QuizID=:quizID", array("quizID" => $quizID));

	// get a list of questionIDs where we are the quizID
	
	$removablequestions = $db->select("question", array("QuestionID"), "QuizID=:quizID", array("quizID" => $quizID));
	// now delete all the removable questions
	foreach($removablequestions as $r) {
		$db->delete("answer", "QuestionID=:questionID", array("questionID" => $r["QuestionID"]));
	
	}
	$db->delete("question", "QuizID=:quizID", array("quizID" => $quizID));

	// re-add the question

	for($i=0;$i<$numoutcomes;$i++) {
		// Insert it into the db
		$db->insert("outcomes",
			array("QuizID","OutcomeValue","OutcomeDescription"), 
			array($quizID,":outcomeValue",":outcomeDescription"),
			array( "outcomeValue" => $outcomeValue[$i],
					"outcomeDescription" => $outcomeDescription[$i]));	
	
		// get the ID and store it for later use	
		$outcomeIDs[$i] = $db->queryDB("SELECT last_insert_id()");
		$outcomeIDs[$i] = $outcomeIDs[$i][0][0];
	}
	
	// for every question
	for($i=0;$i<$numquestions;$i++) {
		// Insert it into the db
		$db->insert("question",
			array("QuizID","QuestionPosition","QuestionValue"), 
			array($quizID,$i,":questionValue"),
			array( "questionValue" => $questionValue[$i]));	
		// get the ID and store it for later use	
		$questionIDs[$i] = $db->queryDB("SELECT last_insert_id()");
		$questionIDs[$i] = $questionIDs[$i][0][0];
	//	echo $questionIDs[$i] . "<br>\n";
		// add the answers
		for($j=0;$j<$numanswers[$i];$j++) {
			// Insert it into the db
			$db->insert("answer",
				array("QuestionID","OutcomeID","AnswerValue"), 
				array($questionIDs[$i],$outcomeIDs[$answerOutcome[$i][$j]],":answerValue"),
				array( "answerValue" => $answerValue[$i][$j]));	
		}
	}
	// finally, it would be nice to see the edit page again
	// first, make sure no action is set
	unset($_REQUEST["action"]);
	// then include form
	include($QM_root . "/manager/editQuiz.php");
	
} else if($action == "manager-show-addQuiz") { // show the add quiz page
	// first, make sure no action is set
	//unset($_REQUEST["action"]);
	unsetQMAction();// then include form

	include($QM_root . "/manager/addQuiz.php");
} else if(beginsWith("manager-show-editQuiz",$action)) { // show the edit page
	// get the quizID
	$quizID = substr(strrchr($action,'-'),1);
	
	// unset the QM action
	unsetQMAction();
	
	// include the edit page
	require($QM_root . "/manager/editQuiz.php");
} else if($action == "manager-manage") {
	// unset the QM action
	unsetQMAction();
	
	// include the edit page
	require($QM_root . "/manager/manage.php");	
}else {
	// if the action is not for us, then let's
	// see who it is for
	include($QM_root . "/index.php");	
}
getQMTemplate();
//var_dump($_REQUEST);
?>