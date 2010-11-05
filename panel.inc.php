<?php $_SESSION['user']->select(); ?>
<div class="adminsection">
<h3>Neue Schicht eintragen</h3>
<table>
<form action="admin.php" method="POST">
<input type="hidden" name="action" value="new_shift" />
<tr>
<td>Tag</td>
<td><select name="day">
<option value="0">Montag</option>
<option value="1">Dienstag</option>
<option value="2">Mittwoch</option>
<option value="3">Donnerstag</option>
<option value="4">Freitag</option>
</select></td>
</tr>
<tr>
<td>Von</td>
<td>
<select name="starth">
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
</select>
:
<select name="startm">
<option value="00">00</option>
<option value="15">15</option>
<option value="30">30</option>
<option value="45">45</option>
</select>
</td>
</tr>
<tr>
<td>Bis</td>
<td>
<select name="endh">
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
</select>
:
<select name="endm">
<option value="00">00</option>
<option value="15">15</option>
<option value="30">30</option>
<option value="45">45</option>
</select>
</td>
</tr>
<tr>
<td><input type="submit" value="Hinzufuegen" colspan="2"/></td>
</tr>
</form>
</table>
</div>

<div class="adminsection">
<h3>Schicht loeschen</h3>
<table>
<form action="admin.php" method="POST">
<input type="hidden" name="action" value="delete_shift" />
<tr>
<td valign="top">Schicht</td>
<td><select name="sid" size="4">
<?php 
foreach($_SESSION['user']->shifts as $shift){
	printf("<option value=\"%s\">%s, %s-%s</option>\n", $shift->sid, substr($weekdays[$shift->day], 0, 2), string_splice($shift->start,2,0,":"),string_splice($shift->end,2,0,":"));
}
?>
</select></td>
</tr>
<tr>
<td><input type="submit" value="Loeschen" colspan="2"/></td>
</tr>
</form>
</table>
</div>

<div class="adminsection">
<h3>Benutzer hinzufuegen</h3>
<table>
<form action="admin.php" method="POST">
<input type="hidden" name="action" value="add_user" />
<tr>
<td>Name</td>
<td><input type="text" name="name" /></td>
</tr>
<tr>
<td>Telefon</td>
<td><input type="text" name="fon" /></td>
</tr>
<tr>
<td>Email</td>
<td><input type="text" name="email" /></td>
</tr>
<tr>
<td>Passwort</td>
<td><input type="password" name="password" /></td>
</tr>
<tr>
<tr>
<td><input type="submit" value="Hinzufuegen" colspan="2"/></td>
</tr>
</form>
</table>
</div>

<form action="admin.php" method="POST"><input type="submit" name="action" value="logout" /></form>
