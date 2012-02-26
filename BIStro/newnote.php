<?php
require_once ('./inc/func_main.php');
pageStart ('Nová poznámka');
mainMenu (4);
sparklets ('<a href="./'.$_REQUEST['sourcepage'].'">osoby</a> &raquo; <strong>nová poznámka</strong>');
?>
<form action="procperson.php" method="post" class="otherform">
	<p>K osobě si můžete připsat kolik chcete poznámek.</p>
	<p>Nová poznámka:</p>
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" />
	</div>
	<div>
	  <label for="nsecret">Utajení:</label>
		<select name="secret" id="nsecret">
		  <option value="0">veřejná</option>
		  <option value="1">tajná</option>
		  <option value="2">soukromá</option>
		</select>
	</div>
	<div>
		<label for="notebody">Tělo poznámka:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
	</div>
	<div>
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="persons.php" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form>
<?php
pageEnd ();
?>