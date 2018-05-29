<?php
/********************/
/* This file handles some quiz organization functions
	*/

// function to put per quiz values into the string
function getQuizTableFormat($quiz,$format,$dateFormat = "F j, Y") {
	$result = $format;	
	
	
	// replace the quizID
	$result = str_replace("%QuizID%",$quiz["QuizID"],$result);

	// replace the quiz name
	$result = str_replace("%QuizName%",$quiz["QuizName"],$result);
	
	// replace the creation date
	$date = new DateTime($quiz["QuizDateCreated"]);
	//var_dump($date);
	$result = str_replace("%QuizCreationDate%",$date->format($dateFormat),$result);
	// convert the creator ID into a username
	if(userExists($quiz["CreatorID"])) {
		$userData = getUserData($quiz["CreatorID"]);
			$result = str_replace("%CreatorUsername%",$userData["UserUsername"],$result);
	} else {
		$result = str_replace("%CreatorUsername%","Unknown",$result);
	}
	
	// the popularity
	$statistics = getQuizStatistics($quiz["QuizID"]);
	$result = str_replace("%QuizPopularity%",count($statistics["statistics"]),$result);
	
	
	return $result;
}
	
	
// function to create a table of quizzes
function getQuizzesTable($params = array()) {
	global $db;
	// set up default params
	
	// DB
	if(!isset($params["quizWhere"])) {
		$params["quizWhere"] = "";	
	}
	if(!isset($params["quizParams"])) {
		$params["quizParams"] = array();	
	}
	// table
		if(!isset($params["tableThead"])) {
		$params["tableThead"] = true;	
	}
	if(!isset($params["tableTag"])) {
		$params["tableTag"] = "";	
	}
	if(!isset($params["tableColNames"])) {
	$params["tableColNames"] = array("QuizName",
									"QuizCreatorName",
									"QuizCreationDate");
	}
	// now this one is a bit tricky,
	// this one we need to set the default
	// for every column we have
	foreach($params["tableColNames"] as $col) {
		// check to see if our col value is set
		if(!isset($params["tableColValues"][$col])) {
			switch($col) {
				case "QuizPopularity":
					// show the name
					$params["tableColValues"][$col] = "%QuizPopularity%";
					break;
				case "QuizName":
					// show the name
					$params["tableColValues"][$col] = "%QuizName%";
					break;
				case "QuizCreatorName":
					// convert the ID into a username
					$params["tableColValues"][$col] = "%CreatorUsername%";
					break;
				case "QuizCreationDate":
					$params["tableColValues"][$col]["tableFormat"] = "%QuizCreationDate%";
					$params["tableColValues"][$col]["dateFormat"] = "F j, Y";
					break;
				case "QuizEdit":
					$params["tableColValues"][$col] = "<a class=\"link-button\" href=\"index.php?action=manager-show-editQuiz-%QuizID%\">Edit</a>";
					break;	
				default:
					$params["tableColValues"][$col] ="%QuizName%";
					break;
			}
		}
	}

	// this one, we want the tag for each
	foreach($params["tableColNames"] as $col) {
		// check to see if our col value is set
		if(!isset($params["tableColTag"][$col])) {
			switch($col) { 
				case "QuizPopularity":
					// show the name
					$params["tableColTag"][$col] = "class=\"center\"";
					break;
				case "QuizName":
					// show the name
					$params["tableColTag"][$col] = "class=\"left\"";
					break;
				case "QuizCreatorName":
					// show the name
					$params["tableColTag"][$col] = "class=\"center\"";
					break;
				case "QuizCreationDate":
					// show the name
					$params["tableColTag"][$col] = "class=\"center\"";
					break;
				case "QuizEdit":
					// show the name
					$params["tableColTag"][$col] = "class=\"center\"";
					break;	
				default:
					$params["tableColTag"][$col] = "class=\"center\"";
					break;
			}
		}
	}

	// first, get all the quizzes
	$quizzes = $db->select("quiz",array("*"),$params["quizWhere"],$params["quizParams"]);
	
	// begin table
	$result = "<table " . $params["tableTag"] . ">\n";
	if($params["tableThead"]) {
		// set up thead
		$result .= "\t<thead>\n";
	} else {
		$result .= "\t<tr>\n";	
	}

	// set up the headers for each supported column
	foreach($params["tableColNames"] as $col) {
		$result .= "\t\t<td>";
		switch($col) {
			case "QuizName":
				$result .= "Name";
				break;
			case "QuizCreatorName":
				$result .= "Creator";
				break;
			case "QuizCreationDate":
				$result .= "Date Created";
				break;
			case "QuizEdit":
				$result .= "Edit";
				break;
			case "QuizPopularity":
				$result .= "Popularity";
				break;
			default:
				// pick whatever was sent
				$result .= $params["tableColHead"][$col];
				break;
		}

		$result .= "</td>\n";

	}

	// end thead if it exists
	if($params["tableThead"]) {
		$result .= "\t</thead>\n";
		// now, create the tbody
		$result .= "\t</tbody>\n";
	} else {
		$result .= "\t</tr>";	
	}

	if(count($quizzes) != 0) {
		// for every quiz, show it in the table
		foreach($quizzes as $quiz) {
			$result .= "\t<tr>";	
			// now, add the value for each supported column
			foreach($params["tableColNames"] as $col) {
				$result .= "\t\t<td " .$params["tableColTag"][$col] . ">";
				switch($col) { 
					case "QuizName":
					case "QuizCreatorName":
					case "QuizEdit":
					case "QuizPopularity":
						// all of these columns have a specific col 
						// value format that we can use
						// all we need to do is get it and use the function
						// to convert it.
						$result .= getQuizTableFormat($quiz,$params["tableColValues"][$col]);
						break;
					case "QuizCreationDate":
						// this one is special, as we want to do something different with it
						$result .= getQuizTableFormat($quiz,$params["tableColValues"][$col]["tableFormat"],$params["tableColValues"][$col]["dateFormat"]);
//						$result .= new DateTime($quiz["QuizDateCreated"]).format($params["tableColValues"][$col]);
						break;
					default:
						// use the value format that was passed
						// to us 
						// all we need to do is get it and use the function
						// to convert it.
						$result .= getQuizTableFormat($quiz,$params["tableColValues"][$col]);
						break;
				} // end switch
				$result .= "</td>\n";
			}// end colum creation
			$result .= "\t</tr>";
		}// end for every quiz
	} else { // if we don't have any data to show
		// no data to show
		// make an error message
		$result .= "<TR><TD class=\"center\" colspan=\"" . count($params["tableColNames"]) . "\">No quizzes to show</TD></TR>";
	}
	
	// end the tbody if it exists
	if($params["tableThead"]) {
		$result .= "\t</tbody>\n";
	}
	// end table
	$result .= "</table>\n";
	
	return $result;
}

?>