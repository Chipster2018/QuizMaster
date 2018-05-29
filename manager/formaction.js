// function to repopulate all the select boxes once something has changed
function populateOutcomeLists() {
	var selects = document.getElementsByTagName("select");
	var outcomes = document.getElementsByName("outcome[]");
	
	for(var i = 0;i<selects.length;i++) {
		// clear all the options
		var v = selects[i].value;
		selects[i].options.length = 0;	
		
		// Repopulate
		for(var j = 0;j<outcomes.length;j++) {
			 var option = document.createElement('option');
			 option.text = outcomes.item(j).value;
			 option.value = j;
			 if(j == v) {
				option.selected = true; 
			 }
			 selects.item(i).add(option,selects.item(i).length);
		}	
	}
}

// Function to add possible outcome
function addOutcome() {
	var platform = document.getElementById("outcomePlatform");
	var numOutcomes = document.getElementsByName("numoutcomes").item(0).value++;
	var newOutcome = document.createElement("div");
	
	platform.insertBefore(newOutcome,platform.lastChild.previousSibling.previousSibling.previousSibling);
	
	newOutcome.outerHTML = "<div class=\"outcome\" id=\"outcome" + numOutcomes + "\">\n<label> Outcome " + (numOutcomes + 1) + "</label>\n<input name=\"outcome[]\" value=\"New Outcome " + (numOutcomes + 1) + "\" onchange=\"populateOutcomeLists()\"><BR><textarea name=\"outcomedesc[]\">New Outcome " + (numOutcomes + 1) + " Description</textarea>\n<input type=\"button\" value=\"Delete\" onClick=\"deleteOutcome(this.parentElement)\"></div>";

	populateOutcomeLists();
}

// Function to delete possible outcome
function deleteOutcome(outcome) {
	// delete the number of outcomes
	var platform = document.getElementById("outcomePlatform");
	var numOutcomes = document.getElementsByName("numoutcomes").item(0).value--;
	var outcomeLabels = platform.getElementsByTagName("label");
	var i;

	// don't delete if we only have one
	if(numOutcomes == 1) {
		// re-add the outcome to our list
		document.getElementsByName("numoutcomes").item(0).value++;
		// end early
		return;	
	}

	// delete the item
	outcome.parentElement.removeChild(outcome);
	
	// populate the outcome
	populateOutcomeLists();
	
	// get the list of outcomes again
	outcomeLabels = platform.getElementsByTagName("label");
	numOutcomes = document.getElementsByName("numoutcomes").item(0).value;
	
	// now, relable the answers
	for(i=0;i<numOutcomes;i++) {
		outcomeLabels[i].innerHTML = "New Outcome " + (i + 1);
	}
}

// Function to add possible outcome
function addAnswer(q) {
	var question = document.getElementById("question" + q);
	var numAnswers = document.getElementsByName("numanswers[]").item(q).value++;
	var newAnswer = document.createElement("div");
	
	question.insertBefore(newAnswer,question.lastChild.previousSibling.previousSibling);
	
	newAnswer.outerHTML = "<div><label> Answer " + (numAnswers + 1) + "</label>\n<input name=\"answerValue[" + q + "][]\" value=\"New Answer " + (numAnswers + 1)+ " \"> <select name=\"answerOutcome[" + q + "][]\"></select><input type=\"button\" value=\"Delete\" onClick=\"deleteAnswer(this.parentElement, " + q + ")\"></div>";

	populateOutcomeLists();

}

// Function to delete possible answer
function deleteAnswer(answer,q) {
	var question = document.getElementById("question" + q);
	var numAnswers = document.getElementsByName("numanswers[]").item(q).value--;
	var answerLabels = question.getElementsByTagName("label");
	var i;
	
	// don't delete if we only have one
	if(numAnswers == 1) {
		// readd the answer to the counter
		document.getElementsByName("numanswers[]").item(q).value++;
		// and end early
		return;	
	}
	// delete the answer
	answer.parentElement.removeChild(answer);
	
	// get the updated list of answers
	answerLabels = question.getElementsByTagName("label");
	numAnswers = document.getElementsByName("numanswers[]").item(q).value;
	
	// now relabel the answers
	for(i=0;i<numAnswers;i++) {
		answerLabels[i+1].innerHTML = "Answer " + (i + 1);
	}
}

// Function to add possible outcome
function addQuestion() {
	var question = document.getElementById("questionPlatform");
	var numAnswers = document.getElementsByName("numquestions").item(0).value++;
	var newAnswer = document.createElement("div");
	
	question.insertBefore(newAnswer,question.lastChild.previousSibling);
	
	newAnswer.outerHTML = "<div class=\"question\" id=\"question" + numAnswers + "\">\n<label> Question " + (numAnswers+1) + 
		"</label>\n<input name=\"questionValue[]\" value=\"What do you want to ask?\"><br>\n" +
		"<div>\n<label> Answer 1</label>\n<input name=\"answerValue[" + numAnswers + "][]\" value=\"New Answer 1\"> " + 
		"<select name=\"answerOutcome[" + numAnswers + "][]\"></select><input type=\"button\" value=\"Delete\" onClick=\"deleteAnswer(this.parentElement, " + numAnswers + ")\"></div>\n<input type=\"hidden\" name=\"numanswers[]\" " +
		"value=\"1\"><input type=\"button\" value=\"Add\" onclick=\"addAnswer(" + numAnswers + ")\"><input type=\"button\" value=\"Delete Question\" onClick=\"deleteQuestion(this.parentElement)\">\n</div>";

	populateOutcomeLists();

}

// Function to delete possible question
function deleteQuestion(q) {
	var questions = document.getElementById("questionPlatform");
	var numQuestions = document.getElementsByName("numquestions").item(0).value--;
	var questionLabels = questions.getElementsByTagName("div");
	var numAnswers;
	var i = 0;;
	var j = 0;
	// don't delete if we only have one
	if(numQuestions == 1) {
		// re-add the outcome to our list
		document.getElementsByName("numquestions").item(0).value++;
		// end early
		return;	
	}

	// delete the item
	q.parentElement.removeChild(q);
	
	// populate the outcome
	populateOutcomeLists();
	
	// get the list of outcomes again
	questionLabels = questions.getElementsByTagName("div");
	numQuestions = document.getElementsByName("numquestions").item(0).value;
	
	j = 0;
	currentQuestion = questions.firstElementChild.nextElementSibling;
	// now, relable the answers
	for(i=0;i<numQuestions;i++) {
//		var numAnswers = document.getElementsByName("numanswers[]").item(i).value;
//		console.log("I: " + i);
//		console.log("numAnswers:");
//		console.log(numAnswers);
//		console.log(currentQuestion);
	//	j += 1 + numAnswers;
		// redo the ID
		currentQuestion.id = "question" + i;
		
		// redo the label
		currentQuestion.getElementsByTagName("label")[0].innerHTML = "Question " + (i + 1);

		// edit the add button
		currentQuestion.querySelectorAll("input[value='Add']")[0].setAttribute("onClick", "addAnswer(" + i + ")");
//		j = j + 1 + numAnswers;
		var deleteButtons = currentQuestion.querySelectorAll("input[value='Delete']");
		for(j = 0;j<deleteButtons.length;j++) {
			deleteButtons[j].setAttribute("onClick", "deleteAnswer(this.parentElement, " + i + ")");
		}
		var answerValueInputs = currentQuestion.querySelectorAll("input[name^='answerValue']");
		for(j = 0;j<answerValueInputs.length;j++) {
			answerValueInputs[j].name = "answerValue[" + i + "][]";
		}
		
		var answerOutcomeInputs = currentQuestion.querySelectorAll("select[name^='answerOutcome']");
		for(j = 0;j<answerOutcomeInputs.length;j++) {
			answerOutcomeInputs[j].name = "answerOutcome[" + i + "][]";
		}
		currentQuestion = currentQuestion.nextElementSibling;
		
	}
	
	
}