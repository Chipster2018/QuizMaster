<?php
$QM_root = $_SERVER['DOCUMENT_ROOT'] . "/quizmaster";

// Define boot string
define("QM_BOOT","booted");


// Backend hiding
require_once($QM_root . "/hideBackend.php");

// misc functions
require_once($QM_root . "/functions.php");

// Database use
require_once ($QM_root . "/database/load_db.php");

// user authentication

//**/ var configuring
$QM_Guest_User = 1; // the ID of the dummy/default guest user
$QM_Max_Retries = 3; // the maximum number of retries the user is allowed to make before a lockout
$QM_Retry_Timeout = 5; // the amount of time in minutes that the user is locked out of any

					// this is used when no one is logged
					// in and is primarily used to lock 
					// unregistered users out of places 
					// they shouldn't be

//**/ actual module loding
require_once ($QM_root . "/login/auth.php");

// quiz handling
require_once ($QM_root . "/quiz/quiz.php");

// statistics handling
require_once ($QM_root . "/statistics/statistics.php");

// Now, the template defaults
$QM_PAGE["title"] = "Quizmaster";
$QM_PAGE["scripts"] = array();
$QM_PAGE["styles"] = array("main.css");
$QM_PAGE["bodyTag"] = "";
$QM_PAGE["menu"] = array(array("Home","index.php"));
/*$QM_PAGE["menu"] = array(array("Home","index.php"),
						array("My Quizzes","index.php?action=manager-manage"),
						array("Statistics","index.php?action=statistics-show-table"));*/

$QM_PAGE["content"] = "";





