<?php

	require('./includes/settings.php');
	require('./includes/session.php');
	
	$_SESSION['mbox_saved'] = $_GET['mbox'];
	$page = intval($_GET['page']);
	if(!isset($page)) {
		$page = 0;
	}
	
	header("Location: ".URL_PREFIX."index.php?page=$page");

?>