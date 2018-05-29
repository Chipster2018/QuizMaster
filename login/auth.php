<?php
/*
 File to handle all user interactions

*/

// first, let's start a session, so we can use $_SESSION

session_start();

/**********************************************************/
/* User Handling functions */

/***********************************/
/* DB functions */

function userExists($userToGet, $mode = "ID") {
	global $db;
	// call the proper function of the database
	$user = $db->select("user",array("*"),"User$mode=:user$mode",array("user$mode" => $userToGet));
	
	// return whether we get a user
	return isset($user[0]);
}


// function to get a user
	
// (NOTE: this function is only designed to get a
// single user. $mode should only be a column that
// has a unique value, such as ID or Username)
function getUserData($userToGet, $mode = "ID") {
	global $db;
	// throw an exception if the user does not exist
	if(!userExists($userToGet,$mode)) {
		throw new Exception("ERROR! getUserData() called on a non-existant user. Please use userExists() before calling getUserData()");
	}
	
	// call the proper function of the database
	$user = $db->select("user",array("*"),"User$mode=:user$mode",array("user$mode" => $userToGet));
	
	// return the first user to match
	return $user[0];
}
// function to update a given user
function setUserData($userToSet, $values=array(), $mode = "ID") {
	global $db;
	
	// first, update all the $values keys to have their 
	// table column equivalent name
	foreach($values as $key => $value) {
		if(!is_numeric($key)) { // prevent [0],[1], etc. 
								// from being messed with,
								// as this is an ailas for	
								// non-numeric named keys
			// First, the $key to User$key
			$values["User$key"] = $values[$key];
			// now, get rid of the old one
			unset($values[$key]);
		}
	}
	// now, call the actual update function
	$db->update("user", $values,"User$mode=:user$mode",array("user$mode" => $userToSet));
	
}

// function to add a user
function addUser($userData) {
	global $db;
	$cols = array();
	$values = array();
	$params=array();
	
	// hash the password before we add it
	$userData["Password"] = encryptPWD($userData["Password"]);
	
	$i = 0; // loop counter
	// for every key in the array
	foreach($userData as $key=>$value) {
		if(!is_numeric($key)) { // prevent [0],[1], etc. 
								// from being messed with,
								// as this is an ailas for	
								// non-numeric named keys
			// split the $userData array into seperate arrays
			$cols[$i] = "User$key";
			$values[$i] = ":user$key";
			$params["user$key"] = $value;
					
			// add 1 to the counter so we can use the next
			// open spot in $cols and $values
			$i++;		
		}
	}
	
	// add the user
	$db->insert("user",$cols,$values,$params);
}

// function to get  the privileges of a given user type
function getUserTypePrivileges($typeID) {
	// vars
	global $db;
	$i = 0;
	$privileges = array();

	// get the list of privilege IDs for our usertype
	$usertypePrivilegeIDList = $db->select("usertypeprivilege",array("PrivilegeID"), "UsertypeID=:usertypeID", array("usertypeID"=>$typeID));
	
	// for every ID
	foreach($usertypePrivilegeIDList as $ID) {
		// the PrivilegeID of our current $ID
		$ourID=$ID["PrivilegeID"];
	
		// get the privilege name
		$name = $db->select("privilege", array("PrivilegeName"), "PrivilegeID=:privilegeID", array("privilegeID"=>$ourID));
		// and stick it in our list of privileges
		$privileges[$i] = $name[0]["PrivilegeName"];		
		// loop counter
		$i++;
	}
	
	// finally, return the list of our privileges
	return $privileges;
}

// function to get all possible states in an array
function getStates() {
	global $db;
	
	// set up params
	$params["field"] = "UserState";
	
	// get the field description from the database
	$field = $db->queryDB("SHOW COLUMNS FROM user WHERE Field=:field", $params);
	
	// we only care about the type
	$type = $field[0]["Type"];
	
	// get only the part we care about
	preg_match("/^enum\(\'(.*)\'\)$/",$type,$states);
	
	// turn it into an array
	$states = explode("','",$states[1]);
	
	return $states;
}
/***********************************/
/* Other Functions */


// get the privileges of a specific user
function getUserPrivileges($userToGet, $mode="ID") {
	// get the data for that user
	$userData = getUserData($userToGet,$mode);
	// and return the privileges for its user type
	return getUserTypePrivileges($userData["UsertypeID"]);
}

function userHasPrivilege($userToGet, $privilege, $mode="ID") {
	$userPrivileges = getUserPrivileges($userToGet,$mode);

	return in_array($privilege, $userPrivileges);
}

// function to make sure the current user
function ensureUserCan($userToGet, $privilege, $redirect="browse-quizzes", $mode="ID") {
	global $QM_root;
	// if the current user doesn't have the privileges to 
		// see the current page,
	if(!userHasPrivilege($userToGet,$privilege,$mode)) {
		
		// first, set redirection to the current action
		$redirect_action = $redirect;
//		var_dump($_REQUEST);
		// now, tell QM to show the login page
		setQMAction("user-show-login");
		
		// set $action is it is not set
		if(!isset($action)) {
			// we don't really care what it is
			$action = "";	
		}
		
		// send a message telling the user
		// they don't have the privileges to do the 
		// required action
		$message = "You do not have the privileges to do this action<BR>\n" . 
					"Please log in.";
		include($QM_root . "/login/login.php");
		// exit the program
		exit();
	}
	// otherwise, we can just return to the main code
}


/**********************************************************/
/* Session Handling Functions */

// functions to set and get qm session vars
function getQMVar($key) {
	/*
	echo "Key: " . $key . "<BR>\n";	
	echo '$_SESSION["QM"][$key]: ' . $_SESSION["QM"][$key] . "<BR>\n";//*/
	return $_SESSION["QM"][$key];	
}

function setQMVar($key,$value) {
	$_SESSION["QM"][$key] = $value;	
	/*
	echo "Key: " . $key . "<BR>\n";	
	echo "Value: " . $value . "<BR>\n";	
	echo '$_SESSION["QM"][$key]: ' . $_SESSION["QM"][$key] . "<BR>\n";//*/
}

// unsets the QM var
function unsetQMVar($key) {
	unset($_SESSION["QM"][$key]);	
}

// checks to see if the QM var is set
function issetQMVar($key) {
	return isset($_SESSION["QM"][$key]);	
}

// function to empty QM vars
function emptyQMVars() {
	$_SESSION["QM"] = array();
	unset($_SESSION["QM"]);
	
}
// cookie functions

// function to get a cookie
function getQMCookie($key) {
	// unserialize the cookie
	return $_COOKIE["QM_" . $key];
}
function setQMCookie($key,$value,$time) {
	// convert $time to seconds
	$today = new DateTimeImmutable;
	$todayAndInterval = $today->add($time);
	
	$seconds = $todayAndInterval->getTimestamp() - $today->getTimestamp();
		
	// set the cookie
	setcookie("QM_" . $key,$value,time()+$seconds); 
}
// unsets the key with the 
function unsetQMCookie($key) {
	// get the cookie
	unset($_COOKIE["QM_" . $key]);
	
}
// is the key set
function issetQMCookie($key) {
	// set the key
	return isset($_COOKIE["QM_" . $key]);
}

/***************/
// specific vars 
// functions for the action
function setQMAction($a) {
	$_REQUEST["action"] = $a;	
}
function getQMAction($default) {

	/*if(issetQMAction()) {
	//	echo "Session";
		return getQMVar("action");	
		// if our action is set
	} else*/ if(isset($_REQUEST["action"])) {
		//echo "Request";
		return $_REQUEST["action"];
	} else {
		//echo "Default";
		return $default;	
	}
}
function issetQMAction() {
	return isset($_REQUEST["action"]);
}
function unsetQMAction() {
	unset($_REQUEST["action"]);	
}

// functions to get and set the current user
function getCurrentUserID() {
	global $QM_Guest_User;
	// if we don't have  the var,
	if(!issetQMVar("userID")) {
		return $QM_Guest_User; // return the guest user
	} else {
		// otherwise, return whoever is logged in
		return getQMVar("userID");	
	}
}
function setCurrentUserID($userID) {
	 setQMVar("userID",$userID);	
}

/**********************************************************/
/* Current User Shortcut Functions */

// shortcut for getUserData(getCurrentUserID())
function getCurrentUserData() {
	return getUserData(getCurrentUserID());
}

// shortcut for getUserPrivileges(getCurrentUserID())
function getCurrentUserPrivileges() {
	return getUserPrivileges(getCurrentUserID());
}

// shortcut for userHasPrivilege(getCurrentUserID(), $privilege)
function currentUserHasPrivilege($privilege) {
	return userHasPrivilege(getCurrentUserID(), $privilege);
}

// shortcut for ensureUserCan(getCurrentUserID(), $privilege)
function ensureCurrentUserCan($privilege, $redirect="browse-quizzes") {
	return ensureUserCan(getCurrentUserID(), $privilege, $redirect);
}

/**********************************************************/
/* Misc Functions */

// function to hash a user password
function encryptPWD($pwd) {
	$result = "";
	
	// reverse the characters of the password
	for($i =0;$i<strlen($pwd);$i++){ 
		$result[$i] = $pwd[strlen($pwd)-$i-1];
	}

	// this is not a terribly good encryption scheme
	// but it wull work for now
	
	return $result;	
}

// function to check a username and password pair
//  TRUE if they match, FALSE if they don't
function authUser($username, $pwd) {
	// first, get the user datafor the current username
	$userData = getUserData($username, "Username");	
	
	// now compare the hash of our current password
		// to the one on file
	return encryptPWD($pwd) == $userData["UserPassword"];
	
}



?>