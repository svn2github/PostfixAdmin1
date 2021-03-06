<?php
// 
// Postfix Admin 
// by Mischa Peters <mischa at high5 dot net>
// Copyright (c) 2002 - 2005 High5!
// License Info: http://www.postfixadmin.com/?file=LICENSE.TXT
//
// File: sendmail.php
//
// Template File: sendmail.tpl
//
// Template Variables:
//
// tMessage
// tFrom
// tSubject
// tBody
//
// Form POST \ GET Variables:
//
// fTo
// fSubject
// fBody
//
require ("./variables.inc.php");
require ("./config.inc.php");
require ("./functions.inc.php");
include ("./languages/" . check_language () . ".lang");

$SESSID_USERNAME = check_session ();

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   include ("./templates/header.tpl");
   include ("./templates/menu.tpl");
   include ("./templates/sendmail.tpl");
   include ("./templates/footer.tpl");
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   $fTo = escape_string ($_POST['fTo']);
   $fFrom = $SESSID_USERNAME;
   $fHeaders = "To: " . $fTo . "\n";
   $fHeaders .= "From: " . $fFrom . "\n";
   
   if (!empty ($PALANG['charset']))
   {
      $fHeaders .= "Subject: " . encode_header (escape_string ($_POST['fSubject']), $PALANG['charset']) . "\n";
      $fHeaders .= "MIME-Version: 1.0\n";
      $fHeaders .= "Content-Type: text/plain; charset=" . $PALANG['charset'] . "\n";
      $fHeaders .= "Content-Transfer-Encoding: 8bit\n";
   }
   else
   {
      $fHeaders .= "Subject: " . escape_string ($_POST['fSubject']) . "\n\n";
   }
   
   $fHeaders .= escape_string ($_POST['fBody']);

   if (empty ($fTo) or !check_email ($fTo))
	{
      $error = 1;
      $tTo = escape_string ($_POST['fTo']);
      $tSubject = escape_string ($_POST['fSubject']);
      $tBody = escape_string ($_POST['fBody']);
      $tMessage = $PALANG['pSendmail_to_text_error'];
	}

   if ($error != 1)
   {
      if (!smtp_mail ($fTo, $fFrom, $fHeaders))
      {
         $tMessage .= $PALANG['pSendmail_result_error'];
      }
      else
      {
         $tMessage .= $PALANG['pSendmail_result_succes'];
      }
   }

   include ("./templates/header.tpl");
   include ("./templates/menu.tpl");
   include ("./templates/sendmail.tpl");
   include ("./templates/footer.tpl");
}
?>
