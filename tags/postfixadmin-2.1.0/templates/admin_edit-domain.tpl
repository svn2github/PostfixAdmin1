<div id="edit_form">
<form name="edit_domain" method="post">
<table>
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pAdminEdit_domain_welcome']; ?></h3></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_domain'] . ":"; ?></td>
      <td><?php print $domain; ?></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_description'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fDescription" value="<?php print htmlspecialchars($tDescription, ENT_QUOTES); ?>" /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_aliases'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fAliases" value="<?php print $tAliases; ?>" /></td>
      <td><?php print $PALANG['pAdminEdit_domain_aliases_text']; ?></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_mailboxes'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fMailboxes" value="<?php print $tMailboxes; ?>" /></td>
      <td><?php print $PALANG['pAdminEdit_domain_mailboxes_text']; ?></td>
   </tr>
   <?php if ($CONF['quota'] == 'YES') { ?>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_maxquota'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fMaxquota" value="<?php print $tMaxquota; ?>" /></td>
      <td><?php print $PALANG['pAdminEdit_domain_maxquota_text']; ?></td>
   </tr>
   <?php } if ($CONF['transport'] == 'YES') { ?>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_transport'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fTransport" value="<?php print $tTransport; ?>" /></td>
      <td><?php $PALANG['pAdminEdit_domain_transport_text']; ?></td>
   </tr>
   <?php } ?>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_backupmx'] . ":"; ?></td>
      <td><?php $checked = (!empty ($tBackupmx)) ? 'checked' : ''; ?>
      <input class="flat" type="checkbox" name="fBackupmx" <?php print $checked; ?> /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pAdminEdit_domain_active'] . ":"; ?></td>
      <td><?php $checked = (!empty ($tActive)) ? 'checked' : ''; ?>
      <input class="flat" type="checkbox" name="fActive" <?php print $checked; ?> /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td colspan="3" class="hlp_center"><input type="submit" class="button" name="submit" value="<?php print $PALANG['pAdminEdit_domain_button']; ?>" /></td>
   </tr>
   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
