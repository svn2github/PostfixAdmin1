<?php
/** 
 * Postfix Admin 
 * 
 * LICENSE 
 * This source file is subject to the GPL license that is bundled with  
 * this package in the file LICENSE.TXT. 
 * 
 * Further details on the project are available at : 
 *     http://www.postfixadmin.com or http://postfixadmin.sf.net 
 * 
 * @version $Id$ 
 * @license GNU GPL v2 or later. 
 * 
 * File: list-domain.php
 * List all domains as a quick overview.
 * Template File: admin_list-domain.php
 *
 * Template Variables:
 *
 * -none-
 *
 * Form POST \ GET Variables:
 *
 * fUsername
 */

require_once('common.php');

authentication_require_role('admin');

if (authentication_has_role('global-admin')) {
   $list_admins = list_admins ();
   $is_superadmin = 1;
   $fUsername = safepost('fUsername', safeget('username')); # prefer POST over GET variable
   if ($fUsername != "") $admin_properties = get_admin_properties($fUsername);
} else {
   $list_admins = array(authentication_get_username());
   $is_superadmin = 0;
   $fUsername = "";
}

if (isset($admin_properties) && $admin_properties['domain_count'] == 'ALL') { # list all domains for superadmins
   $list_domains = list_domains ();
} elseif (!empty($fUsername)) {
  $list_domains = list_domains_for_admin ($fUsername);
} elseif ($is_superadmin) {
   $list_domains = list_domains ();
} else {
   $list_domains = list_domains_for_admin(authentication_get_username());
}

if (!empty ($list_domains))
{
	for ($i = 0; $i < sizeof ($list_domains); $i++)
	{
		$domain_properties [$i] = get_domain_properties ($list_domains[$i]);

		$domain_properties [$i]['name']		= $list_domains [$i];
		$domain_properties [$i]['backupmx']	= ($domain_properties [$i]['backupmx'] == 1)	? $PALANG ['YES'] : $PALANG ['NO'];
		$domain_properties [$i]['active']	= ($domain_properties [$i]['active'] == 1)		? $PALANG ['YES'] : $PALANG ['NO'];
		
		$domain_properties [$i]['maxquota']		= eval_size ($domain_properties [$i]['maxquota']);
		$domain_properties [$i]['aliases']		= eval_size ($domain_properties [$i]['aliases']);
		$domain_properties [$i]['mailboxes']	= eval_size ($domain_properties [$i]['mailboxes']);
	}
}
#}

$smarty->assign ('domain_properties', $domain_properties);
if ($is_superadmin)
{
	$smarty->assign ('select_options', select_options ($list_admins, array ($fUsername)));
	$smarty->assign ('smarty_template', 'admin_list-domain');
}
else
{
	$smarty->assign ('select_options', select_options ($list_domains, array ($_GET['domain'])));
	$smarty->assign ('smarty_template', 'overview-get');
}

$smarty->display ('index.tpl');

/* vim: set expandtab softtabstop=3 tabstop=3 shiftwidth=3: */
?>
