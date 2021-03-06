<?php
//
// File: edit-alias.php
//
// Template File: edit-alias.tpl
//
// Template Variables:
//
// tMessage
// tGoto
//
// Form POST \ GET Variables:
//
// fAddress
// fDomain
// fGoto
//
require ("../variables.inc.php");
require ("../config.inc.php");
require ("../functions.inc.php");
include ("../languages/" . check_language () . ".lang");

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   $fAddress = $_GET['address'];
   $fDomain = $_GET['domain'];
   
   $result = db_query ("SELECT * FROM alias WHERE address='$fAddress' AND domain='$fDomain'");
   if ($result['rows'] == 1)
   {
      $row = db_array ($result['result']);
      $tGoto = $row['goto'];
   }
   else
   {
      $tMessage = $PALANG['pEdit_alias_address_error'];
   }
   
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/edit-alias.tpl");
   include ("../templates/footer.tpl");
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   $pEdit_alias_goto = $PALANG['pEdit_alias_goto'];
   
   $fAddress = $_GET['address'];
   $fAddress = strtolower ($fAddress);
   $fDomain = $_GET['domain'];
   $fGoto = $_POST['fGoto'];
   $fGoto = strtolower ($fGoto);

   if (empty ($fGoto))
   {
      $error = 1;
      $tGoto = $fGoto;
      $tMessage = $PALANG['pEdit_alias_goto_text_error1'];
   }

	$goto = preg_replace ('/\r\n/', ',', $fGoto);
	$goto = preg_replace ('/[\s]+/i', '', $goto);
	$goto = preg_replace ('/\,*$/', '', $goto);
	$array = preg_split ('/,/', $goto);

	for ($i = 0; $i < sizeof ($array); $i++) {
		if (in_array ("$array[$i]", $CONF['default_aliases'])) continue;
		if (empty ($array[$i])) continue;
		if (!check_email ($array[$i]))
		{
   		$error = 1;
   		$tGoto = $goto;
   		$tMessage = $PALANG['pEdit_alias_goto_text_error2'] . "$array[$i]</div>";
	   }
   }
   
   if ($error != 1)
   {
      $result = db_query ("UPDATE alias SET goto='$goto',modified=NOW() WHERE address='$fAddress' AND domain='$fDomain'");
      if ($result['rows'] != 1)
      {
         $tMessage = $PALANG['pEdit_alias_result_error'];
      }
      else
      {
         db_log ($CONF['admin_email'], $fDomain, "edit alias", "$fAddress -> $goto");
               
         header ("Location: list-virtual.php?domain=$fDomain");
         exit;
      }
   }
   
   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/edit-alias.tpl");
   include ("../templates/footer.tpl");
}
?>
