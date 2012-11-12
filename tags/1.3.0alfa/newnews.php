<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nová aktualita');
	mainMenu (1);
	sparklets ('<a href="./index.php">aktuality</a> &raquo; <strong>nová aktualita</strong>');
?>
<form action="procnews.php" method="post" id="inputform">
	<div>
	  <label for="nadpis">Nadpis:</label>
	  <input type="text" name="nadpis" id="nadpis" />
	</div>
	<div>
	  <label for="kategorie">Kategorie:</label>
		<select name="kategorie" id="kategorie">
		  <option value="1">herní</option>
			<option value="2">systémová</option>
		</select>
	</div>
	<div>
	  <label for="obsah">Obsah:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="obsah" id="obsah"></textarea>
	</div>
	<div>
	  <input type="submit" name="insertnews" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>