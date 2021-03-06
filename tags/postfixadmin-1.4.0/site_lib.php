<?php
//
// If site_lib.php is called directly, redirect to login.php
//
if (ereg("site_lib.php", $PHP_SELF)) {
	header("Location: login.php");
}

// login information for the database
$db_host = "localhost";
$db_name = "postfix";
$db_user = "postfixadmin";
$db_pass = "postfixadmin";

// title used for all pages except login
$title = "Mail Admin";

// title used for login
$welcome_title = ":: Welcome to Mail Admin ::";

// header used for login
$welcome_header = "Welcome to Mail Admin";

// footer used for all pages
$version = "Built on Postfix Admin v1.4.0<br>\n2003 &copy; High5!";



//
// print_menu
// Action: Prints out the requirement menu bar
// Call: print_menu()
//
function print_menu() {
	print "<table>\n";
	print "<tr>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "<td class=menu>\n";
	print "<a target=_top href=main.php?" . session_name() . "=" . session_id() . ">Overview</a>";
	print "</td>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "<td class=menu>\n";
	print "<a target=_top href=alias.php?" . session_name() . "=" . session_id() . ">Add Alias</a>";
	print "</td>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "<td class=menu>\n";
	print "<a target=_top href=mailbox.php?" . session_name() . "=" . session_id() . ">Add Mailbox</a>";
	print "</td>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "<td class=menu>\n";
	print "<a target=_top href=passwd.php?" . session_name() . "=" . session_id() . ">Passwd</a>";
	print "</td>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "<td class=menu>\n";
	print "<a target=_top href=logout.php?" . session_name() . "=" . session_id() . ">Logout</a>";
	print "</td>\n";
	print "<td width=8>&nbsp;</td>\n";
	print "</tr>\n";
	print "</table>\n";
}
?>
