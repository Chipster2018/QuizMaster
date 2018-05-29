<?php
global $QM_PAGE;
global $QM_Guest_User;
?>
<html>

<head>
    <title><?php echo $QM_PAGE["title"];?></title>
    <?php
	// add the scripts
	foreach($QM_PAGE["scripts"] as $script) {
		echo "<script src=\"$script\"></script>\n";
	}
	
	echo "\n";
	
	// add the scripts
	foreach($QM_PAGE["styles"] as $style) {
		echo "\t<link rel=\"stylesheet\" href=\"$style\">\n";
		//echo "<style src=\"$style\"></style>\n";
	}	
	?> 
    
</head>

<body <?php echo $QM_PAGE["bodyTag"];?>>
<div id="all">
	<!-- Header -->
	<div class="header">
		<img class="header-image" alt="Header Image" src="images/logo.png">
	    <ul class="top-menu">
    		<?php
			// use the list menu items
			foreach($QM_PAGE["menu"] as $m) {
				echo "\t\t<li><a href=\"" . $m[1] . 
					"\">" . $m[0] . "</a></li>\n";
			}
			// the sign-in button
			// if we are 1) a valid user, and 2) not a guest
			if(getCurrentUserID() != $QM_Guest_User &&
		 userExists(getCurrentUserID())) {
				$userData = getCurrentUserData();

				echo "\t\t<li class=\"sign-in dropdown\"><a>Welcome, "
					. $userData["UserUsername"] . 
					"</a>\n\t\t\t<ul class=\"dropdown-content\"> 
						<li>
							<a href=\"index.php?action=manager-manage\">My Quizzes</a>
						</li>
						<li>
							<a href=\"index.php?action=user-logout\">Sign Out</a>
						</li>
					</ul>" .
					"</li>\n";
			} else {
				echo "\t\t<li class=\"sign-in dropdown\"><a href=\"index.php?action=user-show-login\">Sign In</a>
				</li>\n";
			}

			?>
	    </ul>
	</div>    
	<div id="content">
	<?php echo $QM_PAGE["content"]; ?>
	</div>
</div>
</body>
</html>