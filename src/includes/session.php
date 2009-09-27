<?php
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
	header("Location: ".URL_PREFIX."login.php?url=".URL_PREFIX."index.php");
}
