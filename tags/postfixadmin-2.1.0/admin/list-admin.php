<?php
// 
// Postfix Admin 
// by Mischa Peters <mischa at high5 dot net>
// Copyright (c) 2002 - 2005 High5!
// License Info: http://www.postfixadmin.com/?file=LICENSE.TXT
//
// File: list-admin.php
//
// Template File: list-admin.tpl
//
// Template Variables:
//
// -none-
//
// Form POST \ GET Variables:
//
// -none-
//
require ("../variables.inc.php");
require ("../config.inc.php");
require ("../functions.inc.php");
include ("../languages/" . check_language () . ".lang");

$list_admins = list_admins ();
if ((is_array ($list_admins) and sizeof ($list_admins) > 0))
{
   for ($i = 0; $i < sizeof ($list_admins); $i++)
   {
      $admin_properties[$i] = get_admin_properties ($list_admins[$i]);
   }
}

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/admin_list-admin.tpl");
   include ("../templates/footer.tpl");
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/admin_list-admin.tpl");
   include ("../templates/footer.tpl");
}
?>
