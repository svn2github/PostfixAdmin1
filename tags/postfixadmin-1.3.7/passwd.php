<?
include "my_lib.php";

$sessid = check_session();

print_header();

print_menu();

print "<hr>\n";

if (!empty($_POST[submit])) {
        $form_passwd = $_POST[form_passwd];
        $form_new_passwd1 = $_POST[form_new_passwd1];
        $form_new_passwd2 = $_POST[form_new_passwd2];

	if (empty($form_passwd) or empty($form_new_passwd1) or empty($form_new_passwd2)) {
		print "<p class=error>\n";
		print "You will need to fill all fields!\n";
		print_footer();
		exit;
	}

	if ($form_new_passwd1 != $form_new_passwd2) {
		print "<p class=error>\n";
		print "The new passwords that you supplied don't match!\n";
		print_footer();
		exit;
	}

	
	$result = db_query ("SELECT password FROM admin WHERE username='$sessid[username]'");
	if ($result[rows] == 1) {
		$row = mysql_fetch_array($result[result]);
		$db_passwd = $row[password];
		$keys = preg_split('/\$/', $row[password]);
		$checked_passwd = md5crypt($form_passwd, $keys[2]);

		$result = db_query ("SELECT * FROM admin WHERE username='$sessid[username]' AND password='$checked_passwd' AND active='1'");
		if ($result[rows] != 1) {
			print "<p class=error>\n";
			print "The password that you have entered doesn't match your current password!\n";
			print_footer();
			exit;
		}

	}

	$new_passwd = md5crypt($form_new_passwd1);
	$result = db_query ("UPDATE admin SET password='$new_passwd',change_date=NOW() WHERE username='$sessid[username]'");
	if ($result[rows] == 1) {
		print "Your password has been updated!\n";
		session_unset();
		session_destroy();
		print "<p>\n";
		print "<a href=login.php>Login</a>\n";
		print_footer();
		exit;
	} else {
		print "<p class=error>\n";
		print "<b>Unable</b> to update your password!\n";
		print_footer();
		exit;
	}
} 

?>
Change your password.
<p>
<form name=passwd method=post>
<table class=form>
<tr><td>Login:</td><td><? print "$sessid[username]"; ?></td></tr>
<tr><td>Current Password:</td><td><input type=password name=form_passwd></td></tr>
<tr><td>New Password:</td><td><input type=password name=form_new_passwd1></td></tr>
<tr><td>New Password (again):</td><td><input type=password name=form_new_passwd2></td></tr>
<tr><td colspan=2 align=center><input type=submit name=submit value='Enter'></td></tr>
</table>
</form>
<?
print_footer();
?>
