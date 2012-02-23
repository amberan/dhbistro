<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nové hlášení');
	mainMenu (4);
	$type=mysql_real_escape_string($_GET['type']); // nacitani typu hlaseni z prikazove radky prohlizece (zakladni ochrana proti SQL injection)
	if($type==='1'){sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>nové hlášení z výjezdu</strong>');}
	else if($type==='2'){sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>nové hlášení z výslechu</strong>');}
	else { ?>
<h1>Požadovaný typ hlášení neexistuje - vraťte se prosím <a href="./reports.php" title="">zpět &raquo;</a></h1>
<?php pageEnd ();exit; }; ?>
<form action="procactrep.php" method="post" id="inputform"><?php
	switch ($type){		
		// default situace by nemela nikdy nastat, zande nove hlaseni by nemelo mit typ 0 (nula);
		case 1: ?><input type="hidden" name="type" value="1" /><?php break; // výjezd
		case 2: ?><input type="hidden" name="type" value="2" /><?php break; // výslech
		default:?><input type="hidden" name="type" value="0" /><?php  break; }; // tato moznost je zahrnuta pouze jako pojistka  ?>
	<div>
	  <label for="label">Označení <?php if($type==='1'){ ?>výjezdu<?php }else if($type==='2'){ ?>výslechu<?php }; ?>:</label>
	  <input type="text" name="label" id="label" />
	</div>
	<div>
	  <label for="task"><?php if($type==='1'){ ?>Úkol<?php }else if($type==='2'){ ?>Předmět výslechu<?php }; ?>:</label>
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