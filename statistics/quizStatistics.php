<?php
// first, boot
if(!defined("QM_BOOT")) {
 require_once("boot.php");
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


// get the quiz
$quiz = $db->select("quiz",array("*"),
	"QuizID=:quizID",array("quizID" => $quizID));
$quiz = $quiz[0];



	
// get the statistics
$stat = getQuizStatistics($quizID);
$userGroups = array(array("Users",
						array(NULL, "0",$QM_Guest_User), "not-in"),
					array("Guests",
						array(NULL, "0",$QM_Guest_User), "in"));
$stat = groupQuizStatistics($stat, $userGroups, "byUser");
$stat = groupQuizStatistics($stat, $userGroups, "byUserGroupOutcome");

$timesPlayed = count($stat["statistics"]);

/**************************/
// begin page

// Headding
addQMContent("<h2>Statistics for quiz: " . 
		$quiz["QuizName"] . "*</h2>
<HR class=\"spaced-hr\">\n");

/****/
// the general section
addQMContent("<h2>General</h2>\n");

addQMContent("Number of times Played: " . $timesPlayed . "<BR>\n
<HR class=\"spaced-hr\">
<h2>Outcomes</h2>
");

/* addQMContent("<h3>Outcomes of all plays<h3>\n");

foreach($stat["outcomes"] as $o => $outcomeStats) {
	// if we have data for our current outcome
	if( isset($stat["byOutcome"][$outcomeStats["OutcomeID"]])) {
		// display the data
		addQMContent( $outcomeStats["OutcomeValue"] . ": " . (count($stat["byOutcome"][$outcomeStats["OutcomeID"]])/$timesPlayed)*100 . "%<BR>\n");
	} else {
		// no data
		// that meant no one has gotten it before
		addQMContent( $outcomeStats["OutcomeValue"] . ": 0%<BR>\n");
	}
} //*/
$params["tableColNames"] = array("OutcomeName","OutcomeValue");
$params["mode"] = "byOutcome";

addQMContent("<h3>Outcomes of all plays</h3>\n");

addQMContent(getOutcomeStatisticsTable($stat,$params));

/**************************************/
// Users
addQMContent("<hr class=\"spaced-hr\">\n<h2>Users</h2>");
$params = array();
$params["tableColNames"] = array("UserGroupName","UserGroupValue");
$params["mode"] = "byUser";

addQMContent("<h3>Users as a percent of all plays</h3>\n");

addQMContent(getUserStatisticsTable($stat,$params));

addQMContent("<h3>Users vs. Guests as a percent of all plays</h3>\n");

$params["mode"] = "byUserGroup";

addQMContent(getUserStatisticsTable($stat,$params));


unset($params["tableColNames"]);
$params["tableMode"] = "groupOutcomes";
$params["mode"] = "byUserGroupOutcomeGroup";

addQMContent("<h3>User of all plays</h3>\n");

addQMContent(getOutcomeStatisticsTable($stat,$params));

addQMContent("<p>* Statistics since last edit</p>\n");

?>