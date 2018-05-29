<?php
// Make sure we have have booted
if(!defined("QM_BOOT")) {
 require_once("boot.php");
}

// first, define the action to user-logout
setQMAction("user-logout");

// now require the index, as that will take care of the logout
require_once("index.php");

?>