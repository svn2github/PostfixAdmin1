<?php
//
// File: main.php
//
// Template File: main.tpl
//
// Template Variables:
//
// -none-
//
// Form POST \ GET Variables:
//
// -none-
//
require ("./config.inc.php");
require ("./functions.inc.php");
include ("./languages/" . $CONF['language'] . ".lang");

$SESSID_USERNAME = check_session ();

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
   include ("./templates/header.tpl");
   include ("./templates/menu.tpl");
   include ("./templates/main.tpl");
   include ("./templates/footer.tpl");
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   include ("./templates/header.tpl");
   include ("./templates/menu.tpl");
   include ("./templates/main.tpl");
   include ("./templates/footer.tpl");
}
?>
