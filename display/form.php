<?php
// check for an action
if(isset($_REQUEST["action"])) {
	require("../index.php");
	// index should handle everything else, so we can actually exit
	exit();
}
addQMContent("<form method=\"post\" action=\"#\">\n" .
  "<h2>" . $quizName . "</h2>\n");

// the message
if(isset($message)) {
	addQMContent("<p>$message</p>\n");
}
  
  
addQMContent("<div id=\"questionPlatform\">
        <input type=\"hidden\" name=\"numquestions\" value=\"" .
		 $numquestions . "\">");

		for($i=0;$i<$numquestions;$i++) {
		addQMContent("<div class=\"question\" id=\"question" . ($i) . "\">
	    	<h3>Question " . ($i+1) . ":" . $questionValue[$i] . "</h3>\n");

            for($j=0;$j<$numanswers[$i];$j++) {
                addQMContent("\t\t\t\t<div>
					<input type=\"radio\" name=\"answer[" . $i ."]\" id=\"quiz_" . $i . "_" . $j . "\" value=\""
						. $answerOutcome[$i][$j] . "\">\n					<label for=\"quiz_" . $i . "_" . $j . "\"");
				
				// if we are the second one
				if(($j % 2) == 0 ){//|| $j == ($numanswers[$i] -1)) {
					addQMContent(" class=\"clear-float\"");	
				}
						
				addQMContent(">" . $answerValue[$i][$j] . "</label>
                </div>");
			}
			addQMContent("<input type=\"hidden\" name=\"numanswers[]\" value=\""
				. $numanswers[$i] . "\">
        </div>");
		}
	addQMContent("</div>
	<div class=\"block clear-float\">
		<input type=\"hidden\" name=\"quizID\" value=\"" . $quizID . "\">
		<input type=\"hidden\" name=\"action\" value=\"" . $action . "\">
		<BR><BR><input type=\"submit\" value=\"" . $submitName . "\">
		</div>
</form>");
