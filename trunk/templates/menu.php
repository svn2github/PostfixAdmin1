<?php if( !defined('POSTFIXADMIN') ) die( "This file cannot be used standalone." ); ?>
<?php
function _menulink ($href, $title, $submenu = "") {
   if ($submenu != "") $submenu = "<ul><li><a target='_top' href='$href'>$title</a>$submenu</li></ul>";
   return "<li><a target='_top' href='$href'>$title</a>$submenu</li>";
} 

authentication_has_role('global-admin');

echo "<div id='menu'>\n";
echo "<ul>\n";

$url = "create-mailbox.php"; if (isset ($_GET['domain'])) $url .= "?domain=" . $_GET['domain'];
$submenu_virtual = _menulink($url, $PALANG['pMenu_create_mailbox']);

$url = "create-alias.php"; if (isset ($_GET['domain'])) $url .= "?domain=" . $_GET['domain'];
$submenu_virtual .=  _menulink($url, $PALANG['pMenu_create_alias']);

$submenu_admin = _menulink("create-admin.php", $PALANG['pAdminMenu_create_admin']);

$submenu_fetchmail = _menulink("fetchmail.php?new=1", $PALANG['pFetchmail_new_entry']);


if (authentication_has_role('global-admin')) {
   $submenu_domain = _menulink("create-domain.php", $PALANG['pAdminMenu_create_domain']);
   $submenu_sendmail = _menulink("broadcast-message.php", $PALANG['pAdminMenu_broadcast_message']);
} else {
   $submenu_domain = "";
   $submenu_sendmail = "";
}

if (authentication_has_role('global-admin')) {
   print _menulink("list-admin.php", $PALANG['pAdminMenu_list_admin'], $submenu_admin);
} else {
   print _menulink("main.php", $PALANG['pMenu_main']);
}

print _menulink("list-domain.php", $PALANG['pAdminMenu_list_domain'], $submenu_domain);
print _menulink("list-virtual.php", $PALANG['pAdminMenu_list_virtual'], $submenu_virtual);

if ($CONF['fetchmail'] == 'YES') {
   print _menulink("fetchmail.php", $PALANG['pMenu_fetchmail'], $submenu_fetchmail);
}

if ($CONF['sendmail'] == 'YES') {
   print _menulink("sendmail.php", $PALANG['pMenu_sendmail'], $submenu_sendmail);
} 

# not really useful in the admin menu
#if ($CONF['vacation'] == 'YES') {
#   print _menulink("edit-vacation.php", $PALANG['pUsersMenu_vacation']);
#}

print _menulink("password.php", $PALANG['pMenu_password']);

if (authentication_has_role('global-admin') && 'pgsql'!=$CONF['database_type'] && $CONF['backup'] == 'YES') {
   print _menulink("backup.php", $PALANG['pAdminMenu_backup']);
}

print _menulink("viewlog.php", $PALANG['pMenu_viewlog']);

print _menulink("logout.php", $PALANG['pMenu_logout']);

echo "</ul>\n";
echo "</div>\n";

print "<br clear='all' /><br>"; # TODO

if (authentication_has_role('global-admin')) {
   $motd_file = "motd-admin.txt";
} else {
   $motd_file = "motd.txt";
}

if (file_exists (realpath ($motd_file)))
{
   print "<div id=\"motd\">\n";
   include ($motd_file);
   print "</div>";
}

/* vim: set ft=php expandtab softtabstop=3 tabstop=3 shiftwidth=3: */
?>
