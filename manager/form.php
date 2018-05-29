<?php
// check for an action
if(isset($_REQUEST["action"])) {
	require("../index.php");
	// index should handle everything else, so we can actually exit
	exit();
}

//page scripts and styles
$QM_PAGE["scripts"][] = "manager/formaction.js";
$QM_PAGE["styles"][] = "manager/manager-display.css";

// the body tag
$QM_PAGE["bodyTag"] = "onLoad=\"populateOutcomeLists()\"";

// a page heading
if($action == "manager-addQuiz") {
	addQMContent("<h2>New Quiz</h2>\n");
} else if(beginsWith("manager-editQuiz",$action)) {
	addQMContent("<h2>Edit Quiz " . $quizName . "</h2>\n");
}

// the form
addQMContent("<form method=\"post\" action=\"#\">\n");

// quiz name
addQMContent("\t<div class=\"outline-div\">
		<h3>Quiz Options</h3>
		<label> Name:</label>
	    <input name=\"quizName\" value=\"" . $quizName . "\">		
	</div>\n");

// the outcomes
addQMContent("\t<div id=\"outcomePlatform\">\n");
addQMContent("\t\t<h3>Outcomes</h3>\n");
// for every outcome
for($i=0;$i<$numoutcomes;$i++) {
	// add the outcome to the page
	addQMContent("\t\t<div class=\"outcome\" id=\"outcome" . ($i) . "\">
	    	<label> Outcome " . ($i+1) . "</label>
		    <input name=\"outcome[]\" value=\"" .  $outcomes[$i] . "\" onChange=\"populateOutcomeLists()\"><BR>
            <textarea name=\"outcomedesc[]\">" . $outcomedescription[$i] . "</textarea>
            <input type=\"button\" value=\"Delete\" onClick=\"deleteOutcome(this.parentElement)\">

        </div>");
} // end for every outcome

// an add outcome button		
addQMContent("<input type=\"button\" value=\"Add\" onClick=\"addOutcome()\">\n\n");

//hidden content
addQMContent("\t\t<input type=\"hidden\" name=\"numoutcomes\" value=\"" .  $numoutcomes . "\">\n\n");

// end div        
addQMContent("\t</div>\n");

// questions
addQMContent("\t<div id=\"questionPlatform\">
        <h3>Questions</h3>
		<input type=\"hidden\" name=\"numquestions\" value=\"" . $numquestions . "\">\n");

// for every question		
for($i=0;$i<$numquestions;$i++) {
	// add the question
	addQMContent("\t\t<div class=\"question\" id=\"question" . ($i) . "\">
	    	<h4> Question " . ($i+1) . "</h4>
			<label>Name:</label>
        	<input name=\"questionValue[]\" value=\"" . $questionValue[$i] . "\"><br>\n");
            // for every answer
            for($j=0;$j<$numanswers[$i];$j++) {
				// add the answers
				addQMContent("\t\t<div>
				<label> Answer " . ($j+1) . "</label>
				<input name=\"answerValue[" . ($i) . "][]\" value=\"" . $answerValue[$i][$j] . "\"> <select name=\"answerOutcome[" .  ($i) . "][]\"></select><input type=\"button\" value=\"Delete\" onClick=\"deleteAnswer(this.parentElement, " . ($i) . ")\"></div>\n");
			} // end for every answer
			
			//add the hidden values, and buttons
			addQMContent("\t\t<input type=\"hidden\" name=\"numanswers[]\" value=\"" . $numanswers[$i] . "\">
			<input type=\"button\" value=\"Add Answer\" onClick=\"addAnswer(" . ($i) . ")\"><input type=\"button\" value=\"Delete Question\" onClick=\"deleteQuestion(this.parentElement)\">
        </div>\n");
		}
		addQMContent("\t<input type=\"button\" value=\"Add Question\" onClick=\"addQuestion()\">
	</div>

	<input type=\"hidden\" name=\"quizID\" value=\"" . $quizID . "\">
	<input type=\"hidden\" name=\"action\" value=\"" . $action . "\">
	<input type=\"submit\" value=\"" .  $submitName . "\">
</form>\n");