<div id="edit_form">
<form name="alias" method="post">
<table>
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pPassword_welcome']; ?></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pPassword_admin'] . ":"; ?></td>
      <td><?php print $USERID_USERNAME; ?></td>
      <td><?php print $pPassword_admin_text; ?></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pPassword_password_current'] . ":"; ?></td>
      <td><input class="flat" type="password" name="fPassword_current" ></td>
      <td><?php print $pPassword_password_current_text; ?></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pPassword_password'] . ":"; ?></td>
      <td><input class="flat" type="password" name="fPassword" ></td>
      <td><?php print $pPassword_password_text; ?></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pPassword_password2'].":" ?></td>
      <td><input class="flat" type="password" name="fPassword2" /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td colspan="3" class="hlp_center"><input type="submit" name="submit" value="<?php print $PALANG['pPassword_button']; ?>" /></td>
   </tr>
   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
