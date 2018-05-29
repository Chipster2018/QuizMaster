<?php
/************************/
/* This file creates all the functions related to statistics 
	*/

// function to get quiz statistics
function getQuizStatistics($quizID) {
	// vars
	global $db;
	$result = array();
	$outcomeIDs = array();
	
	// first, get the outcomes used by the given quiz
	$outcomes = $db->select("outcomes", array("*"), "QuizID=:quizID", array("quizID" => $quizID));
	
	foreach($outcomes as $outcome) {
//		var_dump($outcome);
		$outcomeIDs[] = $outcome["OutcomeID"];	
	}
	
	// set the outcome data
	$result["outcomes"] = $outcomes;
	
	// get the statistics by
	$statistics = $db->select("statistics",array("*"),
		"OutcomeID in (" . implode(", ",$outcomeIDs) . ")",
		array());
		
	$result["statistics"] = $statistics;

	// now, loop through the statistics and organize them
	foreach($result["statistics"] as $stat) {
		// let's add the stat to an array
		// based off of the user
		$result["byUser"][$stat["UserID"]][] = $stat;

	}
	
	// get the statistics by outcomeID
	$statistics = $db->select("statistics",array("*"),
		"OutcomeID in (" . implode(", ",$outcomeIDs) . ") ORDER BY OutcomeID",
		array());
		
	// now, loop through the statistics and organize them
	foreach($statistics as $stat) {
		// let's add the stat to an array
		// based off of the user
//		echo $stat["OutcomeID"] . "<BR>\n";
		$result["byOutcome"][$stat["OutcomeID"]][] = $stat;

	}	
	// and, since we stored the outcomes in a nice format,
	// it's only fair to store the users as well
	if(isset($result["byUser"])) {
		foreach($result["byUser"] as $u => $stats) {
			// if the user exists,
			if(userExists($u)) {
				// add it to the array
				$result["users"][$u] = getUserData($u);	
			} else {
				$result["users"][$u] = "Unknown";
			}
		}
	} else {
		$result["byUser"] = array();
	}
	//var_dump($statistics);
	
	return $result;
}

// function to group statistics
function groupQuizStatistics($stat, $groups = array(), $mode="byUser") {
	// vars
	$result = $stat;
	
	// add a group to the statistics
	foreach($groups as $key => $group) {
		// set the name of the group
		$result[$mode . "Group"][$key]["name"] = $group[0];
		
		// now, add it to the groups
		if($mode=="byUser") {
			// keep a running total
			$sum = 0;
			// if we have users
			if(isset($stat["users"])) {
				// for every user
				foreach($stat["users"] as $k => $userData) {
					// if we're in the group
					if(($group[2] == "in" && in_array($k,$group[1])) || ($group[2] == "not-in" && !in_array($k,$group[1]))) {
						// add ourselves to the total sum
						$sum += count($stat["byUser"][$k]);
					}
				}
			}
			// finally, add the number to the group
			$result[$mode . "Group"][$key]["sum"] = $sum;
		} else if($mode=="byUserGroupOutcome") {
		//	echo "Hello, World!\n";
			// if we have users
			if(isset($stat["users"])) {
				// for every user
				foreach($stat["users"] as $k => $userData) {
					// if we're in the group
					if(($group[2] == "in" && in_array($k,$group[1])) || ($group[2] == "not-in" && !in_array($k,$group[1]))) {	
						// for every outcome
						foreach($stat["outcomes"] as $outcome) {
							// keep a running total
							$sum = 0;
						
							// for every outcome by this iser
							foreach($stat["byUser"][$k] as $userOutcome) {
								// if we are the same as the current oucome
								if($userOutcome["OutcomeID"] == $outcome["OutcomeID"]) {
									// add 1 to the total
									$sum += 1;		
								}
							}// end for every user outcome
							// finally, add the number to the group
							$result[$mode . "Group"][$key][$outcome["OutcomeValue"]] = $sum;

						
						} // end for every outcome
					}// end if we're in the group
				} // end for every user 
			} // end if we have users			
		} // end if mode
		
	}
	
	
	return $result;
}

// function to put per quiz values into the string
function getUserStatisticsTableFormat($statistics, $u, $current ,$format,$params = array()) {
	// vars
	$result = $format;	
	$numPlayed = count($statistics["statistics"]);

	// by user mode
	if($params["mode"] == "byUser") {
	 	// if we are not unknown
		if($statistics["users"][$u] != "Unknown") {
		 	// replace the group name with the current Username
			$result = str_replace("%UserGroupName%",$statistics["users"][$u]["UserUsername"],$result);
			
		} else {
			// if we are unknown
			// replace the group name with the "Unknown"
			$result = str_replace("%UserGroupName%",$statistics["users"][$u],$result);
		}
		
		// replace the value with the current group value
		$result = str_replace("%UserGroupValue%",sprintf("%0.2f",(count($current)/$numPlayed)*100),$result);
		
	// by user group mode
	} else if($params["mode"] == "byUserGroup") {
		// replace the group name
		$result = str_replace("%UserGroupName%",$statistics["byUserGroup"][$u]["name"],$result);
		
		// let's make sure we actually have a number
		// to divide by
		if(isset($numPlayed) && $numPlayed != 0) {
			// replace the value with the current value
			$result = str_replace("%UserGroupValue%",sprintf("%0.2f",($statistics["byUserGroup"][$u]["sum"]/$numPlayed)*100),$result);
		} else {
			// since the current group hasn't played 
			// at all...
			
			// replace the group value with 0
			$result = str_replace("%UserGroupValue%","0",$result);	
		}
	// by outcome mode
	} else if($params["mode"] == "byOutcome") {
		/*var_dump($statistics[$params["mode"]]);
		echo "<BR>\n";//*/

		//addQMContent($u . "<BR>\n");

		// for every outcome
		foreach($statistics["outcomes"] as $outcome) {
			// if its ID matches ours, 
			if($outcome["OutcomeID"] == $u) {
				$result = str_replace("%OutcomeName%",$outcome["OutcomeValue"],$result);
				
			}
		}
		if(isset($numPlayed) && $numPlayed != 0) {
			// replace the value with the current value
			$result = str_replace("%OutcomeValue%",sprintf("%0.2f",(count($statistics["byOutcome"][$u])/$numPlayed)*100),$result);
			
		} else {
						$result = str_replace("%OutcomeValue%","0",$result);
		}
		
	} else if($params["mode"] == "byUserGroupOutcomeGroup") {
		// replace the group name
		$result = str_replace("%UserGroupName%",$statistics["byUserGroupOutcomeGroup"][$u]["name"],$result);
		// for every column in the current
		foreach($current as $col => $value) {
			// don't include the name,
			// we already did that
			if($col != "name") {
				// for every body else,
				//replace the item
				$result = str_replace("%$col%",sprintf("%0.2f",($value/$numPlayed)*100),$result);
				
			}
		}
		// for every column
		foreach($statistics["outcomes"] as $clearOutcome) {
			// clear it if it hasn't been cleared for some reason
			$result = str_replace("%" . $clearOutcome["OutcomeValue"] . "%","0",$result);
			
		}
	} // end mode delibration
	
	return $result;
}
	
	
// function to create a table of quizzes
function getUserStatisticsTable($statistics, $params = array()) {
	// vars
	global $db;

	// set up default params
	
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
				case "OutcomeName":
					// show the name
					$params["tableColValues"][$col] = "%OutcomeName%";
					break;
				case "OutcomeValue":
					// show the name
					$params["tableColValues"][$col] = "%OutcomeValue%";
					break;
					
				case "UserGroupName":
					// show the name
					$params["tableColValues"][$col] = "%UserGroupName%";
					break;
				case "UserGroupValue":
					// show the name
					$params["tableColValues"][$col] = "%UserGroupValue%%";
			}
		}
	}

	// default tags
	foreach($params["tableColNames"] as $col) {
		// check to see if our col value is set
		if(!isset($params["tableColTags"][$col])) {
			switch($col) {
				case "OutcomeName":
					// show the name
					$params["tableColTags"][$col] = "class=\"left\"";
					break;
				case "OutcomeValue":
					// show the name
					$params["tableColTags"][$col] = "class\"right\"";
					break;
					
				case "UserGroupName":
					// show the name
					$params["tableColTags"][$col] = "class=\"left\"";
					break;
				case "UserGroupValue":
					// show the name
					$params["tableColTags"][$col] = "class=\"right\"";
			}
		}
	}
	
	// begin table
	$result = "<table " . $params["tableTag"] . ">\n";
	
	// add the thead if we are suppoed to use it
	if($params["tableThead"]) {
		// set up thead
		$result .= "\t<thead>\n";
	} else {
		$result .= "\t<tr>\n";	
	}

	// set up the headers for each supported column
	foreach($params["tableColNames"] as $col) {
		$result .= "\t\t<td>";
		// switch the column
		switch($col) {
				case "OutcomeName":
					// show the name
					$result .= "Outcome";
					break;
				case "OutcomeValue":
					// show the name
					$result .= "Value";
					break;
					
				case "UserGroupName":
					// show the name
					$result .= "Name";
					break;
				case "UserGroupValue":
					// show the name
					$result .= "Value";
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
		$result .= "\t<tbody>\n";
	} else {
		$result .= "\t</tr>";	
	}

	// for every quiz, show it in the table
	if(isset($statistics[$params["mode"]]) && count($statistics[$params["mode"]]) > 0) {
		// for every play in our mode
		foreach($statistics[$params["mode"]] as $key => $current) {
			// begin row
			$result .= "\t<tr>";	

			// now, add the value for each supported column
			foreach($params["tableColNames"] as $col) {
				$result .= "\t\t<td " . $params["tableColTags"][$col] . ">";
				// switch the column
				switch($col) { 
					case "OutcomeName":
					case "OutcomeValue":
					case "UserGroupName":
					case "UserGroupValue":
						// all of these columns have a specific col 
						// value format that we can use
						// all we need to do is get it and use the function
						// to convert it.
						$result .= getUserStatisticsTableFormat($statistics, $key, $current ,$params["tableColValues"][$col],$params);
						break;
				} // end switch
				$result .= "</td>\n";
			}// end colum creation

			// end row
			$result .= "\t</tr>";
		}// end for every play
	} else {
		// no data to show
		// make an error message
		$result .= "<TR><TD colspan=\"" . count($params["tableColNames"]) . "\">No data to show</TD></TR>";
	}

	// end the tbody if it exists
	if($params["tableThead"]) {
		$result .= "\t</tbody>\n";
	}

	// end table
	$result .= "</table>\n";
	
	return $result;
}	
	
// function to create a table of quizzes
function getOutcomeStatisticsTable($statistics, $params = array()) {
	// vars
	global $db;

	// set up default params
	if(!isset($params["tableMode"])) {
		$params["tableMode"] = "allOutcomes";	
	}

	
	// table
	if(!isset($params["tableThead"])) {
		$params["tableThead"] = true;	
	}
	if(!isset($params["tableTag"])) {
		$params["tableTag"] = "";	
	}
	if(!isset($params["tableColNames"])) {
		//echo $params["tableMode"] . "<BR>\n";
		// if we are not in all outcomes mode
		if($params["tableMode"] == "groupOutcomes") {
			// first, add user group name to the head
			$params["tableColNames"] = array();
			$params["tableColNames"][] = "UserGroupName";
			
			// for every outcome
			foreach($statistics["outcomes"] as $col) {
				// add it to the col list
				$params["tableColNames"][] = $col["OutcomeValue"];
				// and add it to the col head list
				$params["tableColHead"][$col["OutcomeValue"]] = $col["OutcomeValue"];
				// and set up the value
				$params["tableColValues"][$col["OutcomeValue"]] = "%" . $col["OutcomeValue"] . "%%";
			// DEBUG:
//			addQMContent($col["OutcomeValue"] . ":\t" . $params["tableColValues"][$col["OutcomeValue"]] . "<BR>\n");
			}

		} else { // if we are in all outcomes mode,
			// or some other mode we don't support
			$params["tableColNames"] = array("OutcomeName",
										"OutcomeValue");
		}
	}
	
	// now this one is a bit tricky,
	// this one we need to set the default
	// for every column we have
	foreach($params["tableColNames"] as $col) {
		//echo $col . "<BR>\n";
		// check to see if our col value is set
		if(!isset($params["tableColValues"][$col])) {
			switch($col) {
				case "OutcomeName":
					// show the name
					$params["tableColValues"][$col] = "%OutcomeName%";
					break;
				case "OutcomeValue":
					// show the name
					$params["tableColValues"][$col] = "%OutcomeValue%%";
					break;
				case "UserGroupName":
					// show the name
					$params["tableColValues"][$col] = "%UserGroupName%";
					break;
				default:
					$params["tableColValues"][$col] ="%OutcomeName%";
					break;
			} // end switch
		} // end if table column value is not set
	} // end for every column

		// default tags
	foreach($params["tableColNames"] as $col) {
		// check to see if our col value is set
		if(!isset($params["tableColTags"][$col])) {
			switch($col) {
				case "OutcomeName":
					// show the name
					$params["tableColTags"][$col] = "class=\"left\"";
					break;
				case "OutcomeValue":
					// show the name
					$params["tableColTags"][$col] = "class\"right\"";
					break;
					
				case "UserGroupName":
					// show the name
					$params["tableColTags"][$col] = "class=\"left\"";
					break;
				case "UserGroupValue":
					// show the name
					$params["tableColTags"][$col] = "class=\"right\"";
					break;
				default:
					$params["tableColTags"][$col] = "class=\"right\"";
					break;
			}
		}
	}

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
			case "OutcomeName":
				// show the name
				$result .= "Outcome";
				break;
			case "OutcomeValue":
				// show the name
				$result .= "Value";
				break;
					
			case "UserGroupName":
				// show the name
				$result .= "Name";
				break;
			case "UserGroupValue":
				// show the name
				$result .= "Value";
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
		$result .= "\t<tbody>\n";
	} else {
		$result .= "\t</tr>";	
	}

	// for every quiz, show it in the table
	if(isset($statistics[$params["mode"]]) && count($statistics[$params["mode"]]) > 0) {
		foreach($statistics[$params["mode"]] as $key => $current) {
			$result .= "\t<tr>\n";	
			// now, add the value for each supported column
			foreach($params["tableColNames"] as $col) {
				$result .= "\t\t<td " . $params["tableColTags"][$col] . ">";
				switch($col) {
					case "OutcomeName":
					case "OutcomeValue":
					case "UserGroupName":
					case "UserGroupValue":
						// all of these columns have a specific col 
						// value format that we can use
						// all we need to do is get it and use the function
						// to convert it.
						$result .= getUserStatisticsTableFormat($statistics, $key, $current ,$params["tableColValues"][$col],$params);
						break;
					default:										
						$result .= getUserStatisticsTableFormat($statistics, $key, $current ,$params["tableColValues"][$col],$params);
						break;
				} // end switch
				$result .= "</td>\n";
			}// end colum creation
			$result .= "\t</tr>\n";
		}// end for every quiz
	} else {
		// no data to show
		// make an error message
		$result .= "<TR><TD colspan=\"" . count($params["tableColNames"]) . "\">No data to show</TD></TR>";
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