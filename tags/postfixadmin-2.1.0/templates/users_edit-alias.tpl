<div id="edit_form">
<form name="alias" method="post">
<table>
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pEdit_alias_welcome']; ?></h3></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pEdit_alias_address'] . ":"; ?></td>
      <td><?php print $USERID_USERNAME; ?></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pEdit_alias_goto'] . ":"; ?></td>
<td><textarea class="flat" rows="4" cols="30" name="fGoto">
<?php
$array = preg_split ('/,/', $tGoto);

for ($i = 0 ; $i < sizeof ($array) ; $i++)
{
   if (empty ($array[$i])) continue;
   if ($array[$i] == $USERID_USERNAME) continue;
   if ($array[$i] == "$USERID_USERNAME@$vacation_domain")
   {
      $vacation = "YES";
      continue;
   }
   print "$array[$i]\n";
}
?>
</textarea>
      <input type="hidden" name="fVacation" value="<?php print $vacation; ?>">
      </td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td colspan="3" class="hlp_center"><input class="button" type="submit" name="submit" value="<?php print $PALANG['pEdit_alias_button']; ?>"></td>
   </tr>
   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
