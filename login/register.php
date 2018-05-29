<?php
// Make sure we have have booted
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}
if(getQMAction("browse-quizzes") != "user-register") {
	setQMAction(getQMAction("user-show-register"));
}
// page title
$QM_PAGE["title"] = "Quizmaster - Register";

$type = "register";

$action = "user-show-register";
$formAction = "user-register";
$redirect_action = "browse-quizzes";

include("form.php");
setQMAction("user-register");
getQMTemplate();
?>