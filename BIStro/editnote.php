<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava poznámky');
	mainMenu (2);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>úprava uživatele</strong>');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."notes WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>

<form action="procnotes.php" method="post" class="otherform">
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" value="<?php echo StripSlashes($rec['title']); ?>"/>
	</div>
	<div>
	  <label for="nsecret">Utajení:</label>
		<select name="secret" id="nsecret">
		  <option value="0"<?php if ($rec['secret']==0) { echo ' selected="selected"'; } ?>>veřejná</option>
		  <option value="1"<?php if ($rec['secret']==1) { echo ' selected="selected"'; } ?>>tajná</option>
		  <option value="2"<?php if ($rec['secret']==2) { echo ' selected="selected"'; } ?>>soukromá</option>
		</select>
	</div>
	<div>
		<label for="notebody">Tělo poznámka:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"><?php echo StripSlashes($rec['note']); ?></textarea>
	</div>
	<div>
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editnote.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form>
<?php
	pageEnd ();
?>
