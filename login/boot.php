<?php
// dummy file to boot QM no matter what our sub-directory name is

// if we haven't booted yet
if(!defined("QM_BOOT")) {
	// try the boot file of the directory above us
	require_once("../boot.php");
}
