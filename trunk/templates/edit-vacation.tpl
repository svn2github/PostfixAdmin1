<script type="text/javascript">
function newLocation()
{
window.location="<?php print $fCanceltarget; ?>"

}
</script>
<div id="edit_form">

<form name="edit-vacation" method="post">
<table>
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pUsersVacation_welcome']; ?></h3></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pUsersLogin_username'] . ":"; ?></td>
      <td><?php print $tUseremail; ?></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pUsersVacation_subject'] . ":"; ?></td>
      <td><textarea class="flat" cols="60" name="fSubject" ><?php print $tSubject; ?></textarea></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pUsersVacation_body'] . ":"; ?></td>
      <td><textarea class="flat" rows="10" cols="60" name="fBody" ><?php print htmlentities($tBody,ENT_QUOTES); ?></textarea></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td colspan="3" class="hlp_center">

      <input class="button" type="submit" name="fChange" value="<?php print $PALANG['pEdit_vacation_set']; ?>" />
      <input class="button" type="submit" name="fBack" value="<?php print $PALANG['pEdit_vacation_remove']; ?>" />
      <input class="button" type="button" name="fCancel" value="<?php print $PALANG['exit']; ?>" onclick="newLocation()" />
      </td>
   </tr>

   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
