<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nový případ');
	mainMenu (4);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>nový případ</strong>');
?>
<form action="proccase.php" method="post" id="inputform">
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
	  <label for="status">Stav:</label>
		<select name="status" id="status">
		  <option value="0">otevřený</option>
			<option value="1">uzavřený</option>
		</select>
	</div>
	<div>
	  <label for="contents">Popis:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="contents" id="contents"></textarea>
	</div>
	<div>
	  <input type="submit" name="insertcase" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>