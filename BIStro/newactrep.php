<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nové hlášení');
	mainMenu (4);
	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>nové hlášení o výjezdu</strong>');
?>
<form action="procactrep.php" method="post" id="inputform">
	<div>
	  <label for="label">Označení výjezdu:</label>
	  <input type="text" name="label" id="label" />
	</div>
	<div>
	  <label for="task">Úkol:</label>
	  <input type="text" name="task" id="task" />
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
		  <option value="0">rozpracované</option>
			<option value="1">dokončené</option>
		</select>
	</div>
	<div>
	  <label for="summary">Shrnutí:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="summary" id="summary">doplnit</textarea>
	</div>
	<div>
	  <label for="impact">Možné dopady:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="impact" id="impact">doplnit</textarea>
	</div>
	<div>
	  <label for="details">Podrobný popis průběhu:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="details" id="details">doplnit</textarea>
	</div>
	<div>
	  <input type="submit" name="insertrep" id="submitbutton" value="Vložit" />
	</div>	
</form>

<?php
	pageEnd ();
?>