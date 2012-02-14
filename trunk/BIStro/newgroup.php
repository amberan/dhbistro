<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nová skupina');
	mainMenu (3);
	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>nová skupina</strong>');
?>
<form action="procgroup.php" method="post" id="inputform">
	<div>
	  <label for="title">Název:</label>
	  <input type="text" name="title" id="title" />
	</div>
	<div>
	  <label for="secret">Přísně tajné:</label>
		<select name="secret" id="secret">
		  <option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
	  <label for="contents">Popis:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="contents" id="contents"></textarea>
	</div>
	<div>
	  <input type="submit" name="insertgroup" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>