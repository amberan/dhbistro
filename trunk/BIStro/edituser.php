<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava uživatele');
	mainMenu (2);
	sparklets ('<a href="./users.php">uživatelé</a> &raquo; <strong>úprava uživatele</strong>');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."users WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>
<form action="procuser.php" method="post" id="inputform">
	<div>
	  <label for="login">Login:</label>
	  <input type="text" name="login" id="login" value="<?php echo StripSlashes($rec['login']); ?>" />
	</div>
	<div>
	  <label for="power">Power user:</label>
		<select name="power" id="power">
			<option value="0"<?php if ($rec['right_power']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec['right_power']==1) { echo ' selected="selected"'; } ?>>ano</option>
		</select>
	</div>
	<div>
	  <label for="texty">Editace hlavních textů:</label>
		<select name="texty" id="texty">
			<option value="0"<?php if ($rec['right_text']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec['right_text']==1) { echo ' selected="selected"'; } ?>>ano</option>
		</select>
	</div>
	<div>
	  <input type="hidden" name="userid" value="<?php echo $rec['id']; ?>" />
	  <input type="submit" name="edituser" id="submitbutton" value="Uložit" />
	</div>
</form>
<?php
		} else {
		  echo '<div id="obsah"><p>Uživatel neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>
