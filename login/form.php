<?php

// Make sure we have have booted
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}

// check for an action
if(!beginsWith("user-show",getQMAction($action))) {
	require("../index.php");
	// index should handle everything else, so we can actually exit
	exit();
}
// set the action to the given action
//setQMAction($action);

if($type == "login") {
	?>
    <?php
	addQMContent("<h2>Login</h2>\n");

	 
	if(isset($message)) {
		addQMContent($message . "<BR>\n");
	}
    
addQMContent("\t<form action=\"#\" method=\"post\">
    <label>Username:</label>
    <input name=\"username\" maxlength=\"30\"><BR>
    <label>Password:</label>
    <input type=\"password\" name=\"pwd\" maxlength=\"30\">
    <p><p class=\"inline\"><input type=\"submit\" value=\"Log In\">	<a class=\"link-button\" href=\"index.php?action=user-show-register\">Register</a></p></p>
    
    <input type=\"hidden\" name=\"action\" value=\"" . $formAction . "\">
    <input type=\"hidden\" name=\"redirect-action\" value=\""
		. $redirect_action  . "\">
</form>");
	addQMContent("");

	
} else if ($type == "register") {
	addQMContent("<h2>Register</h2>\n");

	if(isset($message)) {
		addQMContent($message . "<BR>\n");
	}
    
addQMContent("\t<form action=\"#\" method=\"post\">
    <label>Email:</label>
    <input name=\"user[Email]\" maxlength=\"30\"><BR>
    <label>Username:</label>
    <input name=\"user[Username]\" maxlength=\"30\"><BR>
    <label>Password:</label>
    <input type=\"password\" name=\"user[Password]\" maxlength=\"30\"><BR>
    <label>Password Confirmation:</label>
    <input type=\"password\" name=\"user[Password2]\" maxlength=\"30\"><BR>
    <label>First Name:</label>
    <input name=\"user[FirstName]\" maxlength=\"30\"><BR>
    <label>Last Name:</label>
    <input name=\"user[LastName]\" maxlength=\"30\"><BR>
    <HR>
    <label>Address:</label>
    <input name=\"user[Address]\" maxlength=\"30\"><BR><label>City:</label>
    <input name=\"user[City]\" maxlength=\"30\">		
    <label>State:</label>
    <select name=\"user[State]\">");

	// get the states
	$states = getStates();
	// for every state
	foreach($states as $state) {
		addQMContent("\t\t<option value=\"$state\">$state</option>\n");
	}
	addQMContent("</select><BR>
    <label>Zip:</label>
    <input name=\"user[Zip]\" maxlength=\"30\"><BR><label>Phone:</label>
    <input name=\"user[Phone]\" maxlength=\"30\"><BR>
    <input type=\"submit\" value=\"Sign Up\">
    
    <input type=\"hidden\" name=\"action\" value=\"" . $formAction . "\">
    <input type=\"hidden\" name=\"redirect-action\" value=\"" 
		. $redirect_action . "\">
</form>");
}
