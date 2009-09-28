<?php
require('./includes/settings.php');
require('./includes/session.php');
require('./includes/util.php');
require('./classes/Message.php');

	
$errMsg=null;
//postback send message action
if(isset($_POST) && count($_POST)>0) {
	if(isset($_POST['cancel'])) {
		header("Location: ".URL_PREFIX."index.php");
		die();
	}
	if ($_POST['from'] != '' && $_POST['to'] != '' && $_POST['subject'] != '') {
		$from = $_POST['from'];
		$to = $_POST['to'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];
		if($_POST['cc'] != '') {
			$cc = $_POST['cc'];
		} else {
			$cc = null;
		}
		
		$additional_headers = "From: " . $from . "\r\n" .
							  "Reply-To: " . $from . "\r\n" .
							  "Cc: " . $cc;
		if(mail($to, $subject, $body, $additional_headers)) {
			header("Location: /index.php");
			die();
		} else {
			$errMsg = 'An error happened while sending the message';		
		}
	} else {
		$errMsg = 'Please complete the \'To\' and \'Subject\' fields.';
	}
}

if(isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = 'new';
}

if(isset($_SESSION['currentMessage'])) {
	$message = unserialize($_SESSION['currentMessage']);
} else {
	$message = new Message();
}

switch($action) {
	case 'reply':
		$message->setTo($message->getFrom(true));
		$message->setCc('');
		break;
	case 'replyall':
		$message->setTo($message->getFrom(true));
		break;
	case 'forward':
		$message->setTo('');
		$message->setCc('');
		break;
}

header( "Content-Type: application/x-blueprint+xml" );
header( "Cache-Control: no-cache" );
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<page style="list">
	<models>
		<model>
			<instance>
				<data>
					<selected-tab>write</selected-tab>
				</data>
			</instance>
		</model>
		<model id="compose">
			<instance>
				<data xmlns="">
					<from><?php echo $_SESSION['email']; ?></from>
					<to><?php echo htmlspecialchars($message->getTo()); ?></to>
					<cc><?php echo htmlspecialchars($message->getCc()); ?></cc>
					<subject><?php echo htmlspecialchars($message->getSubject()); ?></subject>
					<body><?php echo htmlspecialchars($message->getBody()); ?></body>
				</data>
			</instance>
			<submission id="compose" resource="composemessage.php"
				method="urlencoded-post" />
		</model>
	</models>
	<page-header>
		<tabs ref="selected-tab">
			<tab id='read'>
				<label>Messages</label>
				<load-page event="activate" page="index.php" />
			</tab>
			<tab id='write'>
				<label>Compose</label>
				<load-page event="activate" page="composemessage.php" />
			</tab>
		</tabs>
	</page-header>
	<content>
		<?php if($errMsg != null) { ?>
		<placard class="callout strong" layout="card">
			<layout-items>
				<image size="small" resource="check"/>
				<block class="description">
					<strong><?php echo $errMsg; ?></strong>
				</block>
			</layout-items>
		</placard>
		<?php } ?>
		<module>
			<input ref="to" model="compose" appearance="full">
				<label>To:</label>
			</input>
			<input ref="cc" model="compose" appearance="full">
				<label>Cc:</label>
			</input>
			<input ref="subject" model="compose" appearance="full">
				<label>Subject:</label>
			</input>
			<textarea ref="body" model="compose">
				<label />
			</textarea>
			<submit appearance="button" model="compose" id="send">
				<label>Send</label>
			</submit>
			<submit appearance='button' model='compose' id='cancel'>
				<label>Cancel</label>
			</submit>
			<group>
				<block>
					<br />
				</block>
			</group>
			<submit appearance="button" model="compose" id="save">
				<label>Save draft</label>
			</submit>
		</module>
	</content>
</page> 