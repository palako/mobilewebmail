<?php

	function getCurrentFolder() {
		$imap_folder = IMAP_FOLDER;
		
		if(isset($_GET['mbox'])) {
			$imap_folder = $_GET['mbox'];
		}
		else if(isset($_SESSION['mbox_saved'])) {
			$imap_folder = trim($_SESSION['mbox_saved']);
		}
		
		return $imap_folder;
	}
	
	function getServiceString() {
		$imap_host = IMAP_HOST;
		$imap_port= IMAP_PORT;
		$imap_service = IMAP_SERVICE;
		$imap_folder = getCurrentFolder();
		
		return "{" . $imap_host . ":" 
					. $imap_port . "" . $imap_service . "}" . $imap_folder;
	}
	
	function getMbox() {
		$imap_user = $_SESSION['email'];
		$imap_password = $_SESSION['password'];
		
		try {
			$service_string = getServiceString();
			
			$mbox = @imap_open($service_string,
					 $imap_user, 
					 $imap_password) 
					 or 
					 die(imap_last_error()."<br>Connection Faliure!");
		} catch (Exception $e) {
			error_log($e);
		}
		
		return $mbox;
	}
?>