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
 * File: login.php
 * Authenticates a user, and populates their $_SESSION as appropriate.
 * Template File: login.tpl
 *
 * Template Variables:
 *
 *  none
 *
 * Form POST \ GET Variables:
 *
 *  fUsername
 *  fPassword
 *  lang
 */

require_once('common.php');

if($CONF['configured'] !== true) {
    print "Installation not yet configured; please edit config.inc.php";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
    $fUsername = '';
    $fPassword = '';
    if (isset ($_POST['fUsername'])) $fUsername = escape_string ($_POST['fUsername']);
    if (isset ($_POST['fPassword'])) $fPassword = escape_string ($_POST['fPassword']);


    $result = db_query ("SELECT password FROM $table_admin WHERE username='$fUsername' AND active='1'");
    if ($result['rows'] == 1)
    {
        $row = db_array ($result['result']);
        $password = pacrypt ($fPassword, $row['password']);
        $result = db_query ("SELECT * FROM $table_admin WHERE username='$fUsername' AND password='$password' AND active='1'");
        if ($result['rows'] != 1)
        {
            $error = 1;
            flash_error($PALANG['pLogin_failed']);
        }
    }
    else
    {
        $error = 1;
        flash_error($PALANG['pLogin_failed']);
    }

    if ($error != 1)
    {
        $row = db_row ($result['result']);
        unset($row['password'])
        session_regenerate_id();
        $_SESSION['sessid'] = array();
        $_SESSION['sessid']['username'] = $fUsername;
        $_SESSION['sessid']['roles'] = array();
        $_SESSION['sessid']['locale'] = $row['locale'];
        $_SESSION['sessid']['roles'][] = 'admin';

        // they've logged in, so see if they are a domain admin, as well.
        $result = db_query ("SELECT * FROM $table_domain_admins WHERE username='$fUsername' AND domain='ALL' AND active='1'");
        if ($result['rows'] == 1)
        {
            $_SESSION['sessid']['roles'][] = 'global-admin';
            #            header("Location: admin/list-admin.php");
            #            exit(0);
        }
        header("Location: main.php");
        exit(0);
    }
}

$smarty->assign ('logintype', 'admin');
$smarty->assign ('smarty_template', 'login');
$smarty->display ('index.tpl');

/* vim: set expandtab softtabstop=4 tabstop=4 shiftwidth=4: */
?>
