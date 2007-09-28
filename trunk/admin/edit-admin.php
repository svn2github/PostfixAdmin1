<?php
// 
// Postfix Admin 
// by Mischa Peters <mischa at high5 dot net>
// Copyright (c) 2002 - 2005 High5!
// Licensed under GPL for more info check GPL-LICENSE.TXT
//
// File: edit-admin.php
//
// Template File: admin_edit-admin.tpl
//
// Template Variables:
//
// tAllDomains
// tDomains
// tActive
// tSadmin
//
// Form POST \ GET Variables:
//
// fDescription
// fAliases
// fMailboxes
// fMaxquota
// fActive
//

require_once('../common.php');

authentication_require_role('global-admin');


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   $fPassword = 'x';
   $fPassword = 'y';
   if(isset ($_GET['username'])) $username = escape_string ($_GET['username']);

   if(isset ($_POST['fPassword'])) $fPassword = escape_string ($_POST['fPassword']);
   if(isset ($_POST['fPassword2'])) $fPassword2 = escape_string ($_POST['fPassword2']);

   $fActive=(isset($_POST['fActive'])) ? escape_string ($_POST['fActive']) : FALSE;
   $fSadmin=(isset($_POST['fSadmin'])) ? escape_string ($_POST['fSadmin']) : FALSE;

   $fDomains = false;
   if (isset ($_POST['fDomains'])) $fDomains = $_POST['fDomains'];

   $tAllDomains = list_domains ();

   $result = db_query("SELECT * FROM $table_admin WHERE username = '$username'");
   if($result['rows'] != 1) {
      die("Invalid username for admin user");
   }
   $admin_details = db_array($result['result']);
   $originalPassword = $admin_details['password'];
   // has the password changed?
   if($fPassword != $originalPassword) { 
      if(!empty($_POST['fPassword2'])) {
         if ($fPassword != $fPassword2)
         {
            $error = 1;
            $pAdminEdit_admin_password_text = $PALANG['pAdminEdit_admin_password_text_error'];
         }
         else {
            $fPassword = pacrypt($fPassword);
         }
      }
   }

   $tActive = $fActive;
   $tDomains = escape_string ($_POST['fDomains']);
   if ($error != 1)
   {
      if ($fActive == "on")  {
         $sqlActive = db_get_boolean(True);
      }
      else {
         $sqlActive = db_get_boolean(False);
      }


      $result = db_query ("UPDATE $table_admin SET modified=NOW(),active='$sqlActive', password='$fPassword' WHERE username='$username'");

      if ($fSadmin == "on") $fSadmin = 'ALL';

      // delete everything, and put it back later on..
      db_query("DELETE FROM $table_domain_admins WHERE username = '$username'");
      if($fSadmin == 'ALL') {
         $fDomains = array('ALL');
      }

      foreach($fDomains as $domain) 
      {
         $result = db_query ("INSERT INTO $table_domain_admins (username,domain,created) VALUES ('$username','$domain',NOW())");
      }
      flash_info($PALANG['pAdminEdit_admin_result_success']);
   }
   else {
      flash_error($PALANG['pAdminEdit_admin_result_error']);
   }
   header("Location: list-admin.php");
}
else { // GET request.
   if (isset($_GET['username'])) $username = escape_string ($_GET['username']);

   $tAllDomains = list_domains();
   $tDomains = list_domains_for_admin ($username);

   $tSadmin = '0';
   $result = db_query ("SELECT * FROM $table_domain_admins WHERE username='$username'");
   // could/should be multiple matches to query; 
   if ($result['rows'] >= 1) {
      $result = $result['result'];
      while($row = db_array($result)) {
          if ($row['domain'] == 'ALL') {
              $tSadmin = '1';
              $tDomains = array(); /* empty the list, they're an admin */
          }
      }
   }

   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/admin_edit-admin.tpl");
   include ("../templates/footer.tpl");
}
