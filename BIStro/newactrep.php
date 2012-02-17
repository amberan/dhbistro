<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nové hlášení');
	mainMenu (4);
	sparklets ('<a href="./cases.php">hlášení</a> &raquo; <strong>nové hlášení</strong>');
?>
<form action="proccase.php" method="post" id="inputform">
	<div>
	  <label for="title">Označení výjezdu:</label>
	  <input type="text" name="title" id="title" />
	</div>
	<div>
	  <label for="task">Úkol:</label>
	  <input type="text" name="task" id="task" />
	</div>
	<div>
	  <label for="present">Přítomni:</label>
	  <input type="text" name="present" id="present" />
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
	  <label for="summary">Shrnutí:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="summary" id="summary"></textarea>
	</div>
	<div>
	  <label for="impact">Možné dopady:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="impact" id="impact"></textarea>
	</div>
	<div>
	  <label for="description">Podrobný popis průběhu:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="impact" id="impact"></textarea>
	</div>
	<div>
	  <input type="submit" name="insertcase" id="submitbutton" value="Vložit" />
	</div>	
</form>

<?php
	pageEnd ();
?>