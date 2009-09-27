<?php
if(isset($_POST['email']) && isset($_POST['password'])){
	$email = $_POST['email'];
	$password = $_POST['password'];
	$mbox = imap_open("{localhost:143}INBOX", $email, $password);
	if ($mbox !== false) {
		session_start();
		$_SESSION['email'] = $email;
		$_SESSION['password'] = $password; 	
		if(isset($_POST['url'])) {
			header("Location: " . $_POST['url']);
		} else {
			header("Location: /index.php");
		}
}
if(isset($_GET['url'])) {
	$url = $_GET['url'];
} else {
	$url = '/index.php';
}

header( "Content-Type: application/x-blueprint+xml" );
header( "Cache-Control: no-cache" );
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<page style="collection">
	<models>
		<model id="LoginModel">
			<instance>
				<data xmlns="">
					<email/>
					<password/>
					<url><?php echo($url); ?></url>
				</data>
			</instance>
			<submission id="LoginSubmit" resource="login.php" method="urlencoded-post" secure="false" />
		</model>
	</models>
	<page-header>
		<page-title>Sign in</page-title>
	</page-header>
    <content>
		<module>
			<group class="normal" appearance="compact">
				<input ref="email" model="LoginModel" appearance="full">
					<label>Email Address</label>
				</input>
				<secret ref="password" model="LoginModel" appearance="full">
					<label>Password</label>
				</secret>
				<block>
		        	<br />
				</block>
		        <submit appearance="button" model="LoginModel">
		        	<label>Sign in</label>
				</submit>
			</group>
	    </module>
	</content>
</page>