<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Nová aktualita');
	mainMenu (1);
	sparklets ('<a href="./index.php">aktuality</a> &raquo; <strong>nová aktualita</strong>');
?>

<div id="obsah">
<fieldset><legend><strong>Nová aktualita</strong></legend>
	<form action="procnews.php" method="post" id="inputform">
	<div id="info">
		<h3><label for="nadpis">Nadpis:</label></h3>
		<input type="text" name="nadpis" id="nadpis" />
		<div class="clear">&nbsp;</div>
		<h3><label for="kategorie">Kategorie:</label></h3>
			<select name="kategorie" id="kategorie">
				<option value="1" selected="selected">herní</option>
				<option value="2">systémová</option>
			</select>
		<div class="clear">&nbsp;</div>
		<fieldset><legend><strong>Obsah</strong></legend>
  		<div class="field-text">
		<textarea cols="140" rows="50" name="obsah" id="obsah"></textarea>
		</div>
		</fieldset>
	  	<input type="submit" name="insertnews" id="submitbutton" value="Vložit" />
	</div>
</form>
</fieldset>
</div>
<?php
	pageEnd ();
?>