<center>
<form name="overview" method="post">
<select name="fDomain" onChange="this.form.submit()";>
<?php
for ($i = 0; $i < sizeof ($list_domains); $i++)
{
   if ($fDomain == $list_domains[$i])
   {
      print "<option value=\"$list_domains[$i]\" selected>$list_domains[$i]</option>\n";
   }
   else
   {
      print "<option value=\"$list_domains[$i]\">$list_domains[$i]</option>\n";
   }
}
?>
</select>
<input type="submit" name="go" value="<?php print $PALANG['pOverview_button']; ?>" />
</form>
<p />
<?php
print "<center>\n";
print "<table border=\"1\">\n";
print "   <tr class=\"header\">\n";
print "      <td>" . $PALANG['pOverview_get_domain'] . "</td>\n";
print "      <td>" . $PALANG['pOverview_get_aliases'] . "</td>\n";
print "      <td>" . $PALANG['pOverview_get_mailboxes'] . "</td>\n";
if ($CONF['quota'] == 'YES') print "      <td>" . $PALANG['pOverview_get_quota'] . "</td>\n";
print "   </tr>\n";

for ($i = 0; $i < sizeof ($list_domains); $i++)
{
   if ((is_array ($list_domains) and sizeof ($list_domains) > 0))
   {
      $limit = get_domain_properties ($list_domains[$i]);
      
      print "   <tr class=\"hilightoff\" onMouseOver=\"className='hilighton';\" onMouseOut=\"className='hilightoff';\">\n";
      print "      <td><a href=\"overview.php?domain=" . $list_domains[$i] . "\">" . $list_domains[$i] . "</a></td>\n";
      print "      <td>" . $limit['alias_count'] . " / " . $limit['aliases'] . "</td>\n";
      print "      <td>" . $limit['mailbox_count'] . " / " . $limit['mailboxes'] . "</td>\n";
      if ($CONF['quota'] == 'YES') print "      <td>" . $limit['maxquota'] . "</td>\n";
      print "   </tr>\n";
   }
}
print "</table>\n";
print "<p />\n";
?>
