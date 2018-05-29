<?php
$title = "Login";
$type = "login";

$formAction = "user-login";
if(!isset($redirect_action)) {
//	var_dump($_REQUEST);
	$redirect_action = "browse-quizzes";
}

include("form.php");

// get the template
getQMTemplate();
?>