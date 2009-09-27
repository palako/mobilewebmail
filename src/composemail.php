<?php
require('./includes/settings.php');
require('./includes/session.php');
$errMsg=null;
if(isset($_POST) && count($_POST)>0) {
	if(isset($_POST['cancel'])) {
		header("Location: /index.php");
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
					<to></to>
					<cc></cc>
					<subject></subject>
					<body></body>
				</data>
			</instance>
			<submission id="compose" resource="composemail.php"
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
				<load-page event="activate" page="composemail.php" />
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