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
 * File: list-virtual.php
 * List virtual users for a domain.
 *
 * Template File: list-virtual.php
 *
 * Template Variables:
 *
 * tMessage
 * tAlias
 * tMailbox
 *
 * Form POST \ GET Variables:
 *
 * fDomain
 * fDisplay
 */
require_once('common.php');


authentication_require_role('admin');

$fDomain = false;
$SESSID_USERNAME = authentication_get_username();

if (authentication_has_role('global-admin')) {
    $list_domains = list_domains ();
    $is_superadmin = 1;
} else {
    $list_domains = list_domains_for_admin(authentication_get_username());
    $is_superadmin = 0;
}

$tAlias = array();
$tMailbox = array();
$fDisplay = 0;
$page_size = $CONF['page_size'];

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
    if (isset ($_GET['domain'])) $fDomain = escape_string ($_GET['domain']);
    if (isset ($_GET['limit'])) $fDisplay = intval ($_GET['limit']);
}
else
{
    if (isset ($_POST['fDomain'])) $fDomain = escape_string ($_POST['fDomain']);
    if (isset ($_POST['limit'])) $fDisplay = intval ($_POST['limit']);
}

if (count($list_domains) == 0) {
    #   die("no domains");
    flash_error( $PALANG['invalid_parameter'] );
    header("Location: list-domain.php"); # no domains (for this admin at least) - redirect to domain list
    exit;
}

if ((is_array ($list_domains) and sizeof ($list_domains) > 0)) {
    if (empty ($fDomain)) {
        $fDomain = $list_domains[0];
    }
}

if(!in_array($fDomain, $list_domains)) {
    flash_error( $PALANG['invalid_parameter'] );
    header("Location: list-domain.php"); # invalid domain, or not owned by this admin
    exit;
}

if (!check_owner(authentication_get_username(), $fDomain)) { 
    flash_error( $PALANG['invalid_parameter'] . " If you see this message, please open a bugreport"); # this check is most probably obsoleted by the in_array() check above
    header("Location: list-domain.php"); # domain not owned by this admin
    exit(0);
}

// store fDomain in $_SESSION so after adding/editing aliases/mailboxes we can 
// take the user back to the appropriate domain listing. (see templates/menu.php)
if($fDomain) {
    $_SESSION['list_virtual_sticky_domain'] = $fDomain;
}

if (boolconf('alias_domain')) {
    # Alias-Domains
    # first try to get a list of other domains pointing
    # to this currently chosen one (aka. alias domains)
    $query = "SELECT $table_alias_domain.alias_domain,$table_alias_domain.target_domain,$table_alias_domain.modified,$table_alias_domain.active FROM $table_alias_domain WHERE target_domain='$fDomain' ORDER BY $table_alias_domain.alias_domain LIMIT $fDisplay, $page_size";
    if ('pgsql'==$CONF['database_type'])
    {
        $query = "SELECT alias_domain,target_domain,extract(epoch from modified) as modified,active FROM $table_alias_domain WHERE target_domain='$fDomain' ORDER BY alias_domain LIMIT $page_size OFFSET $fDisplay";
    }
    $result = db_query ($query);
    $tAliasDomains = array();
    if ($result['rows'] > 0)
    {
        while ($row = db_array ($result['result']))
        {
            if ('pgsql'==$CONF['database_type'])
            {
                $row['modified']=gmstrftime('%c %Z',$row['modified']);
                $row['active']=('t'==$row['active']) ? 1 : 0;
            }
            $tAliasDomains[] = $row;
        }
    } 
    # now let's see if the current domain itself is an alias for another domain
    $query = "SELECT $table_alias_domain.alias_domain,$table_alias_domain.target_domain,$table_alias_domain.modified,$table_alias_domain.active FROM $table_alias_domain WHERE alias_domain='$fDomain'";
    if ('pgsql'==$CONF['database_type'])
    {
        $query = "SELECT alias_domain,target_domain,extract(epoch from modified) as modified,active FROM $table_alias_domain WHERE alias_domain='$fDomain'";
    }
    $result = db_query ($query);
    $tTargetDomain = "";
    if ($result['rows'] > 0)
    {
        if($row = db_array ($result['result']))
        {
            if ('pgsql'==$CONF['database_type'])
            {
                $row['modified']=gmstrftime('%c %Z',$row['modified']);
                $row['active']=('t'==$row['active']) ? 1 : 0;
            }
            $tTargetDomain = $row;
        }
    }
}

$query = "SELECT $table_alias.address,
    $table_alias.goto,
    $table_alias.modified,
    $table_alias.active
    FROM $table_alias LEFT JOIN $table_mailbox ON $table_alias.address=$table_mailbox.username
    WHERE ($table_alias.domain='$fDomain' AND $table_mailbox.maildir IS NULL)
    ORDER BY $table_alias.address LIMIT $fDisplay, $page_size";
if ('pgsql'==$CONF['database_type'])
{
    $query = "SELECT address,
        goto,
        extract(epoch from modified) as modified,
        active
        FROM $table_alias
        WHERE domain='$fDomain' AND NOT EXISTS(SELECT 1 FROM $table_mailbox WHERE username=$table_alias.address)
        ORDER BY address LIMIT $page_size OFFSET $fDisplay";
}
$result = db_query ($query);
if ($result['rows'] > 0)
{
    while ($row = db_array ($result['result']))
    {
        if ('pgsql'==$CONF['database_type'])
        {
            //. at least in my database, $row['modified'] already looks like : 2009-04-11 21:38:10.75586+01, 
            // while gmstrftime expects an integer value. strtotime seems happy though.
            //$row['modified']=gmstrftime('%c %Z',$row['modified']);
            $row['modified'] = date('Y-m-d H:i', strtotime($row['modified']));
            $row['active']=('t'==$row['active']) ? 1 : 0;
        }
        $tAlias[] = $row;
    }
}

# TODO: reduce number of different queries by not depending on too much config options
#       (it probably won't hurt to include a field in the resultset that is not displayed later)
if ($CONF['vacation_control_admin'] == 'YES')
{
    if (boolconf('used_quotas'))
    {
        if (boolconf('new_quota_table'))
        {
            $query = "SELECT $table_mailbox.*, $table_vacation.active AS v_active, $table_quota2.bytes as current FROM $table_mailbox
                LEFT JOIN $table_vacation ON $table_mailbox.username=$table_vacation.email
                LEFT JOIN $table_quota2 ON $table_mailbox.username=$table_quota2.username
                WHERE $table_mailbox.domain='$fDomain'
                ORDER BY $table_mailbox.username LIMIT $page_size OFFSET $fDisplay";
        }
        else
        {
            $query = "SELECT $table_mailbox.*, $table_vacation.active AS v_active, $table_quota.current FROM $table_mailbox 
                LEFT JOIN $table_vacation ON $table_mailbox.username=$table_vacation.email 
                LEFT JOIN $table_quota ON $table_mailbox.username=$table_quota.username 
                WHERE $table_mailbox.domain='$fDomain' AND 
                    ( $table_quota.path='quota/storage' OR  $table_quota.path IS NULL )
                ORDER BY $table_mailbox.username LIMIT $page_size OFFSET $fDisplay";
        }
    }
    else # $CONF[used_quotas] = NO
    {
        $query = "SELECT $table_mailbox.*, $table_vacation.active AS v_active FROM $table_mailbox 
        LEFT JOIN $table_vacation ON $table_mailbox.username=$table_vacation.email 
        WHERE $table_mailbox.domain='$fDomain' ORDER BY $table_mailbox.username LIMIT $page_size OFFSET $fDisplay";
    }
}
else # $CONF['vacation_control_admin'] == 'NO'
{
    if (boolconf('used_quotas'))
    {
        if (boolconf('new_quota_table'))
        {
            $query = "SELECT $table_mailbox.*, $table_quota2.bytes as current FROM $table_mailbox
                LEFT JOIN $table_quota2 ON $table_mailbox.username=$table_quota2.username
                WHERE $table_mailbox.domain='$fDomain' ORDER BY $table_mailbox.username LIMIT $page_size OFFSET $fDisplay";
        }
        else
        {
            $query = "SELECT $table_mailbox.*, $table_quota.current FROM $table_mailbox
                LEFT JOIN $table_quota ON $table_mailbox.username=$table_quota.username
                WHERE $table_mailbox.domain='$fDomain' AND
                    ( $table_quota.path='quota/storage' OR  $table_quota.path IS NULL )
                ORDER BY $table_mailbox.username LIMIT $page_size OFFSET $fDisplay";
        }
	}
    else # $CONF[used_quotas] = NO
    {
            $query = "SELECT * FROM $table_mailbox WHERE domain='$fDomain' ORDER BY username LIMIT $page_size OFFSET $fDisplay";
    }
}
$result = db_query ($query);
if ($result['rows'] > 0)
{
    while ($row = db_array ($result['result']))
    {
        if ('pgsql'==$CONF['database_type'])
        {
            // XXX
            $row['modified'] = date('Y-m-d H:i', strtotime($row['modified']));
            $row['created'] = date('Y-m-d H:i', strtotime($row['created']));
            $row['active']=('t'==$row['active']) ? 1 : 0;
            if($row['v_active'] == NULL) { 
                $row['v_active'] = 'f';
            }
            $row['v_active']=('t'==$row['v_active']) ? 1 : 0; 
        }
        $tMailbox[] = $row;
    }
}

$tCanAddAlias = false;
$tCanAddMailbox = false;

$limit = get_domain_properties($fDomain);
if (isset ($limit)) {
    if ($fDisplay >= $page_size) {
        $tDisplay_back_show = 1;
        $tDisplay_back = $fDisplay - $page_size;
    }
    if (($limit['alias_count'] > $page_size) or ($limit['mailbox_count'] > $page_size)) {
        $tDisplay_up_show = 1;
    }
    if ((($fDisplay + $page_size) < $limit['alias_count']) or 
        (($fDisplay + $page_size) < $limit['mailbox_count'])) 
    {
        $tDisplay_next_show = 1;
        $tDisplay_next = $fDisplay + $page_size;
    }

    if($limit['aliases'] == 0) {
        $tCanAddAlias = true;
    }
    elseif($limit['alias_count'] < $limit['aliases']) {
        $tCanAddAlias = true;
    }
    if($limit['mailboxes'] == 0) {
        $tCanAddMailbox = true;
    }
    elseif($limit['mailbox_count'] < $limit['mailboxes']) {
        $tCanAddMailbox = true;
    }
}

// this is why we need a proper template layer.
$fDomain = htmlentities($fDomain, ENT_QUOTES);
include ("templates/header.php");
include ("templates/menu.php");
include ("templates/list-virtual.php");
include ("templates/footer.php");

/* vim: set expandtab softtabstop=4 tabstop=4 shiftwidth=4: */
?>
