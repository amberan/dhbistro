<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Nový uživatel');
	mainMenu (2);
	sparklets ('<a href="./users.php">uživatelé</a> &raquo; <strong>nový uživatel</strong>');
?>
<form action="users.php" method="post" id="inputform">
	<div>
	  <label for="login">Login:</label>
	  <input type="text" name="login" id="login" />
	</div>
	<div>
	  <label for="heslo">Heslo:</label>
	  <input type="text" name="heslo" id="heslo" />
	</div>
	<div>
	<span class="powerlevel">POWER USER</span>
		<select name="power" id="power">
			<option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
	<span class="powerlevel">EDITOR</span>
		<select name="texty" id="texty">
			<option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
	  <input type="submit" name="insertuser" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>
