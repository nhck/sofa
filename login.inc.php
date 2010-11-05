<div class="adminsection">
<h3>Login</h3>
<form action="admin.php" method="POST">
<table>
<input type="hidden" name="action" value="login" />
<tr>
<td>Name</td>
<td><input name="name" type="text" /></td>
</tr>
<tr>
<td>Login</td>
<td><input name="password" type="password" /></td>
</tr>
<tr>
<td colspan="2">
	<input name="submit" value="Login" type="submit" />
	<a href="?action=lost_passwd">Passwort vergessen</a>
</td>
</tr>
</table>
<form>
</div>
