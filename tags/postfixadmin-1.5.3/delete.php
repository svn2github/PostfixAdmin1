<?php
require "config.inc.php";
require "my_lib.php";

$sessid = check_session();
$sessid_domain = $sessid['domain'];
$sessid_username = $sessid['username'];

$table = $_GET['table'];
$where = $_GET['where'];
$delete = $_GET['delete'];
$url = "main.php?" . session_name() . "=" . session_id();

if ($table == "alias") {
	$query = "DELETE FROM alias WHERE address='$delete' AND domain='$sessid_domain'";
	$result = db_query ("$query");
	if ($result['rows'] != 1) {
		print_error ("Unable to delete entry <b>$delete</b> from the $table table!", "YES", "MENU");
	}

	$query = "SELECT * FROM mailbox WHERE username='$delete' AND domain='$sessid_domain'";
	$result = db_query ("$query");
	if ($result['rows'] == 1) {

		$query = "DELETE FROM mailbox WHERE username='$delete' AND domain='$sessid_domain'";
		$result = db_query ("$query");
		if ($result['rows'] == 1) {
			header("Location: $url");
		} else {
			print_error ("Unable to delete entry <b>$delete</b> from the $table table!", "YES", "MENU");
		}
	}
	header("Location: $url");
}

if ($table == "mailbox") {
	$query = "DELETE FROM mailbox WHERE username='$delete' AND domain='$sessid_domain'";
	$result = db_query ("$query");
	if ($result['rows'] != 1) {
		print_error ("Unable to delete entry <b>$delete</b> from the $table table!", "YES", "MENU");
	}

	$query = "DELETE FROM alias WHERE address='$delete' AND domain='$sessid_domain'";
	$result = db_query ("$query");
	if ($result['rows'] == 1) {
		header("Location: $url");
	} else {
		print_error ("Unable to delete entry <b>$delete</b> from the $table table!", "YES", "MENU");
	}
}
?>
