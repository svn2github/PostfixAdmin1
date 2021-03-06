<?php
//
// File: edit-active.php
//
// Template File: message.tpl
//
// Template Variables:
//
// tMessage
//
// Form POST \ GET Variables:
//
// fUsername
//
require ("../variables.inc.php");
require ("../config.inc.php");
require ("../functions.inc.php");
include ("../languages/" . $CONF['language'] . ".lang");

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   if (isset ($_GET['username'])) $fUsername = $_GET['username'];
   
   $result = db_query ("UPDATE admin SET active=1-active WHERE username='$fUsername'");
   if ($result['rows'] != 1)
   {
      $error = 1;
      $tMessage = $PALANG['pAdminEdit_admin_result_error'];
   }
   
   if ($error != 1)
   {
      header ("Location: list-admin.php");
      exit;
   }
   
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/message.tpl");
   include ("../templates/footer.tpl");
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/message.tpl");
   include ("../templates/footer.tpl");
}
?>
