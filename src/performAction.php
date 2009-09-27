<?php

	require('./includes/settings.php');
	require('./includes/session.php');
	require('./includes/util.php');
	require('./includes/imapConnection.php');

	$action = $_POST['action'];
	if(!isset($action)) {
		$action = $_GET['action'];
	}
	
	$url = $_GET['url'];
	if(!isset($url)) {
		$url = URL_PREFIX . 'index.php';
	}
	
	$current_page = intval($_GET['page']) >= 0 ? intval($_GET['page']) : 0;
	if($current_page == 0 && isset($_POST['page'])) {
		$current_page = intval($_POST['page']) >= 0 ? 
				intval($_POST['page']) : 0;	
	}
	
	$mbox_info = imap_status(getMbox(), getServiceString(), SA_ALL);
	$num_messages = $mbox_info->messages;
	$num_pages = round($num_messages / 10);
	if($current_page > $num_pages) {
		$current_page = 0;
	}
	
	$messagesIds = str_ireplace(' ', ',', $_POST['msgids']);
	
	$requiredMessageActions = array('read', 'unread', 'delete');
	if(in_array($action, $requiredMessageActions) && 
			strlen($messagesIds) == 0) {
		
		header("Location: $url?page=$current_page&error_message=0");
		die();
	}
	
	switch($action) {
		case 'read':
			readMessages($messagesIds);
			break;
		case 'unread':
			unreadMessages($messagesIds);
			break;
		case 'delete':
			deleteMessages($messagesIds);
			break;
		case 'selectall':
			$select_all = selectAll($_POST['page']);
			header("Location: $url?page=$current_page&select_all=$select_all");
			die();
			break;
		case 'nothing':
			header("Location: $url?page=$current_page&error_message=1");
			die();
			break;
		default:
			break;
	}
	
	header("Location: $url?page=$current_page");
	
	function readMessages($messages) {
		$mbox = getMbox();
		
		$messages = uidToSecuence($mbox, $messages);
		
		imap_setflag_full($mbox, $messages, '\\Seen');
		imap_close($mbox);
	}
	
	function unreadMessages($messages) {
		$mbox = getMbox();
		
		$messages = uidToSecuence($mbox, $messages);
		
		imap_clearflag_full($mbox, $messages, '\\Seen');
		imap_close($mbox);
	}

	function deleteMessages($messages) {
		$mbox = getMbox();
		
		$messages = uidToSecuence($mbox, $messages);
		
		imap_delete($mbox, $messages);
		imap_expunge($mbox);
		imap_close($mbox);
	}
	
	function selectAll($page) {
		$mbox = getMbox();
		
		$mbox_info = imap_status($mbox, getServiceString(), SA_ALL);
		$num_messages = $mbox_info->messages;
		
		$messages = array();
		
		for ($i = 0; $i < 10; $i++) {
			$current_message = $num_messages - ($page * 10 + $i);
			$messages[] = imap_uid($mbox, $current_message);
		}
		
		imap_close($mbox);
		
		return implode(' ', $messages);
	}
	
	function uidToSecuence($mbox, $uidList) {
		$uids = explode(',', $uidList);
		$secuence = array();
		foreach($uids as $uid) {
			$secuence[] = imap_msgno($mbox, $uid);
		}
		
		return implode(',', $secuence);
	}

?>