<?php
// 
// Postfix Admin 
// by Mischa Peters <mischa at high5 dot net>
// Copyright (c) 2002 - 2005 High5!
// License Info: http://www.postfixadmin.com/?file=LICENSE.TXT
//
// File: logout.php
//
// Template File: -none-
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

$SESSID_USERNAME = check_session ();

session_unset ();
session_destroy ();

header ("Location: login.php");
exit;
?>
