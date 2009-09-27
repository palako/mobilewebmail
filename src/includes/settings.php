<?php
/*
//Testing set 1
define('IMAP_HOST', 'www.ardeenelinfierno.com');
define('IMAP_PORT', '993');
define('IMAP_SERVICE', '/imap/ssl/novalidate-cert');
define('IMAP_FOLDER', 'INBOX');
define('URL_PREFIX', '');
//Testing set 2
*/
define('IMAP_HOST', 'localhost');
define('IMAP_PORT', '143');
define('IMAP_SERVICE', '');
define('IMAP_FOLDER', 'INBOX');
define('URL_PREFIX', '/');

$error_messages = array(array('title' => 'Error', 'msg' => 'Please, select one or more messages to perform that action.'),
						array('title' => 'Info', 'msg' => 'Please, select an action from the list.'));
