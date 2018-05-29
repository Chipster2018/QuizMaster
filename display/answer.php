<?php
// Make sure we have have booted
if(!defined("QM_BOOT")) {
	require_once("boot.php");
}

// get the quizID
$quizID = filter_input(INPUT_POST,"quizID");


// if we have data
if(isset($_POST["answer"])) {
	// first, get the answers
	$answers = $_POST["answer"];
} else {
	// we don't have data to work with
	// exit
	setQMAction("display-quiz-$quizID");
	
	$message = "You must answer at least 1 question to find out who you are."; 
	
	require("index.php");
	return;
}

// first, get loop through the answers, to
// fill out the answer matix
foreach($answers as $answer) {
	if(isset($answerMatrix[$answer])) {
		$answerMatrix[$answer] += 1;
	} else {
		$answerMatrix[$answer] = 1;
	}
	
}

//var_dump($answerMatrix);

// now, loop through the answer matrix to find the 
// winner
foreach($answerMatrix as $o => $score) {
	// if there is no outcomeID
	if(!isset($outcomeID)) {
	$outcomeID = $o;

	// otherwise, if its score is better than 
	// the current best
	} else if($answerMatrix[$outcomeID] <= $score) {
		// set the new best
		$outcomeID	= $o;
	}
	
}

//var_dump($outcomeID);

// now that we have the winner,

// record it in the the stistics table
$db->insert("statistics",
				array("UserID","OutcomeID"),
				array(":userID",":outcomeID"),
				array("userID" => getCurrentUserID(),
					  "outcomeID" => $outcomeID));

// display it
$outcome = $db->select("outcomes",array("*"),"OutcomeID=:outcomeID",array("outcomeID"=>$outcomeID));
$outcome = $outcome[0];

addQMContent("<h2>You are: " . $outcome["OutcomeValue"] . "</h2>\n");
addQMContent("<p>" . nl2br($outcome["OutcomeDescription"]) . "</p>\n");




?>