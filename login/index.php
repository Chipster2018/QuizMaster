<?php
// Make sure we have have booted
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}
// check for the action
$action = getQMAction("user-show-login");

if($action == "user-login") {
	// login code 
	global $QM_Max_Retries, $QM_Retry_Timeout;
	
	// get the username and password in question
	$username = filter_input(INPUT_POST,"username");
	$pwd = filter_input(INPUT_POST,"pwd");

	// if we are not timed out
	if(!issetQMCookie("login_timeout")) {
		// if the user exists
		if (userExists($username,"Username")) {
			// good, now let's check the password
			if(authUser($username,$pwd)) {
				// our password is correct
				// now, let's set the correct session vars
	
				// get authenticated user data
				$userData = getUserData($username, "Username");
				// set its ID as the current user
				setCurrentUserID($userData["UserID"]);
				
				// set redirection
				setQMAction(filter_input(INPUT_POST,"redirect-action"));
				
				require($QM_root . "/index.php");
				exit();
			} else {
				// password is wrong
				// retry code
				if(issetQMCookie("retries")) {
					// add 1 to the retry counter
					setQMCookie("retries",(getQMCookie("retries")) + 1, new DateInterval("PT" . $QM_Retry_Timeout . "M"));
							
					// now, see if the user has maxed out their retries
					if(getQMCookie("retries") >= $QM_Max_Retries) {
						// set the timed out cookie
						setQMCookie("login_timeout", 1, new DateInterval("PT" . $QM_Retry_Timeout . "M"));
					}

					
				} else {
					// set the retry counter to 1
					setQMCookie("retries", 1, new DateInterval("PT" . $QM_Retry_Timeout . "M"));		
				}
				
				
				
				// tell them an error has ocurred
				$message = "Error! Username or password is incorrect! Please try again.";
			
				setQMAction("user-show-login");
				require($QM_root . "/login/login.php");
			}
			
		} else { // if the user doesn't exist
			$message = "Error! Username or password is incorrect! Please try again.";
			
			setQMAction("user-show-login");
			require($QM_root . "/login/login.php");
		}
	} else { // if the use has timed out
			$message = "You have exceded the maximum attempts to login. Please try again in " . $QM_Retry_Timeout . " minutes.";
			
			setQMAction("user-show-login");
			require($QM_root . "/login/login.php");
	}
	
} else if($action == "user-logout") {
	// logout code
	// first, empty the QM variables
	emptyQMVars();
	
	// destroy the session
	session_destroy();
	
	// and recreate the session
	session_start();
	 
	// setup the new action

	setQMAction("user-show-login");
	
	// set the message to tell the user they have logged out
	$message = "You have sucessfully logged out.<BR>\n";

	require($QM_root . "/login/login.php");
	
} else if($action == "user-register") {
	// registration code 
	$validNewUser = TRUE; // flag for whether data is valid
	// get the user data
	$userData = $_REQUEST["user"];

	// registar as a regular user
	$userData["typeID"] = 2;
	
	// validate everything
	if(in_array("",$userData)) { // is any field left blank?
		$message = "Error! You are missing a required field!<BR>\n";
		$validNewUser = FALSE;
	} else if(userExists($userData["Username"], "Username")) { // Does the user already exist		

		$message = "Error: Username already exists!<BR>\n" .
					"Please try something else.<BR>\n";
		$validNewUser = FALSE;
	} else if (userExists($userData["Email"], "Email")) { // is that email already taken
		$message = "Error! Email already registered!<BR>\n";
		$validNewUser = FALSE;		
	} else if (!preg_match("/^.*@.*\\..*$/",$userData["Email"])) { // is an invalid email taken
		$message = "Error! Invalid Email!<BR>\n";
		$validNewUser = FALSE;		
	} else if (!preg_match("/^\d{5}$/",$userData["Zip"])) { // is the zipcode invalid?
		$message = "Error! Invalid Zip Code!<BR>\n";
		$validNewUser = FALSE;		
	}
	
	// password match
	
	// if the passwords do match
	if( $userData["Password"] == $userData["Password2"]) {
		// unset the password confirmation, just so our
		// userdata var is clean
		unset($userData["Password2"]);
		
	} else {
		// otherwise,
		
		// send a message telling the user that the passwords
		// don't match
		$message = "Error! Passwords do not match!<BR>\n";
		$validNewUser = FALSE;		
		
	}
	
	
	// if the user has passed validation...
	if($validNewUser) {
		try {
			// try to add the user
			addUser($userData);
		} catch (PDOException $e) {
			$message = "An error occured!<BR>\n" . 
						"Please contact your administrator!<BR>\n";	
			setQMAction("user-show-register");
		
			require($QM_root . "/login/register.php");
			exit();
		}
		
		// now, log in the user automatically
		
		// first, re-get user data from the server
		$userData = getUserData($userData["Username"], "Username");
		
		// and set its ID to the current one
		setCurrentUserID($userData["UserID"]);
		
		// unset the action
		setQMAction(filter_input(INPUT_POST,"redirect-action"));
		
		// include the main index
		require($QM_root . "/index.php");
	} else {
		// an error occured...
		
		// send the message and re-show the form
		setQMAction("user-show-register");
			
		require($QM_root . "/login/register.php");
		exit();
	}
} else if($action == "user-show-login") {
	// login form code 
	require($QM_root . "/login/login.php");
} else if($action == "user-show-register") {
	// registration form code 
	require($QM_root . "/login/register.php");
} else {
	// unrecognized action
	// send to parent module
	require($QM_root . "/index.php");
}


getQMTemplate();
?>