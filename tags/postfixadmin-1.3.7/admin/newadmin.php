<?
include "my_lib.php";

print_header();

print_menu();
print "<hr>\n";

if (!empty($_POST[submit])) {
	$username = $_POST[username];
	$password = $_POST[password];
	$domain = $_POST[domain];

	$passwd = md5crypt ("$password");
	
	if (empty($username) or empty($password) or empty($domain)) {
		print "<p>\n";
		print "You will need to fill all fields.\n";
		print "<p>\n";
		print_footer();
		exit;
	}

	if (!check_email($username)) {
		print "<p>\n";
		print "The email address that you have supplied at <b>Email</b> is not a valid email address, please go back.\n";
		print "<p>\n";		
		print_footer();
		exit;
	}

	$result = db_query ("SELECT * FROM domain WHERE domain='$domain'");
	if ($result[rows] != 1) {
		print "<p>\n";
		print "There is no domain present in the transport table!\n";
		print "<p>\n";
		print_footer();
		exit;
	}

	$result = db_query ("SELECT * FROM admin WHERE username='$username'");
	if ($result[rows] == 1) {
		print "<p>\n";
		print "This email address already exists, please choose a different one.\n";
		print "<p>\n";
		print_footer();
		exit;
	}

	$result = db_query ("INSERT INTO admin (username,password,domain,create_date,change_date) VALUES('$username','$passwd','$domain',NOW(),NOW())");
	if ($result[rows] == 1) {
		print "<i>$username</i> has been <b>added</b> to the admin table!\n";
		print "<p>\n";
	} else {
		print "<b>Unable</b> to add: <i>$username</i> to the mailbox table!\n";
		print "<p>\n";
		print_footer();
		exit;
	}
}
?>

Create a new admin for a domain.
<p>
<form method=post>
<table class=form>
<tr><td>Email:</td><td><input type=text name=username></td></tr>
<tr><td>Passwd:</td><td><input type=text name=password></td></tr>
<tr><td>Domain:</td><td><input type=text name=domain></td><td></tr>
<tr><td colspan=3 align=center><input type=submit name=submit value='Add Entry'></td></tr>
</table>
</form>

<?
print "<p>\n";
print_footer();
?>
