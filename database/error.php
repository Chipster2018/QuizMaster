<?php
require($_SERVER['DOCUMENT_ROOT'] . "/quizmaster/hideBackend.php");
// If we are the main, exit
hideIfMain(__FILE__);

?>
<html>
<body>
<p>Error! <?php echo $err_msg; ?></p>


</body>
</html>