<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate(header);

$latteParameters['title'] = 'Nové hlášení';

	mainMenu ();
	$type = $_GET['type']; // nacitani typu hlaseni z prikazove radky prohlizece (zakladni ochrana proti SQL injection)
	if ($type === '1') {
	    sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>nové hlášení z výjezdu</strong>');
	} else {
	    if ($type === '2') {
	        sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>nové hlášení z výslechu</strong>');
	    } else { ?>
<h1>Požadovaný typ hlášení neexistuje - vraťte se prosím <a href="./reports.php" title="">zpět &raquo;</a></h1>
<?php latteDrawTemplate(footer);exit; }
	};

?>
<div id="obsah">
<form action="procactrep.php" method="post" id="inputform">
<fieldset id="ramecek"><legend><strong>Nové hlášení z <?php echo (($type == 1) ? 'výjezdu' : (($type == 2) ? 'výslechu' : '#&*'));?></strong></legend>
	<fieldset><legend><strong>Základní údaje</strong></legend>
		<div id="info"><?php
	switch ($type) {
		// default situace by nemela nikdy nastat, zadne nove hlaseni by nemelo mit typ 0 (nula);
		case 1: ?><input type="hidden" name="type" value="1" /><?php break; // výjezd
		case 2: ?><input type="hidden" name="type" value="2" /><?php break; // výslech
		default:?><input type="hidden" name="type" value="0" /><?php  break; }; // tato moznost je zahrnuta pouze jako pojistka  ?>
			<h3><label for="label">Označení <?php if ($type === '1') { ?>výjezdu<?php } else {
		    if ($type === '2') { ?>výslechu<?php }
		}; ?>:</label></h3>
	  		<input type="text" size="80" name="label" id="label" />
	  		<div class="clear">&nbsp;</div>
			<h3><label for="task"><?php if ($type === '1') { ?>Úkol<?php } else {
		    if ($type === '2') { ?>Předmět výslechu<?php }
		}; ?>:</label></h3>
	  		<input type="text" size="80" name="task" id="task" />
	  		<div class="clear">&nbsp;</div>
			<h3><label for="adatum"><?php if ($type === '1') { ?>Datum akce<?php } else {
		    if ($type === '2') { ?>Datum výslechu<?php }
		}; ?>:</label></h3>
	  		<?php echo date_picker("adatum")?>
	  		<div class="clear">&nbsp;</div>
			<h3><label for="start">Začátek:</label></h3>
	  		<input type="text" name="start" id="start" />
	  		<div class="clear">&nbsp;</div>
			<h3><label for="end">Konec:</label></h3>
			<input type="text" name="end" id="end" />
	  		<div class="clear">&nbsp;</div>
			<h3><label for="secret">Přísně tajné:</label></h3>
			<select name="secret" id="secret">
				<option value="0">ne</option>
				<option value="1">ano</option>
			</select>
	  		<div class="clear">&nbsp;</div>
			<h3><label for="status">Stav:</label></h3>
			<select name="status" id="status">
				<option value="0">rozpracované</option>
				<option value="1">dokončené</option>
			</select>
			<div class="clear">&nbsp;</div>			
		</div>
		<!-- end of #info -->
	</fieldset>

	<fieldset><legend><strong>Shrnutí:</strong></legend>
		<textarea cols="80" rows="7" name="summary" id="summary">doplnit</textarea>
	</fieldset>
	
	<fieldset><legend><strong>Možné dopady:</strong></legend>
		<textarea cols="80" rows="7" name="impact" id="impact">doplnit</textarea>
	</fieldset>
	
	<fieldset><legend><strong>Podrobný popis průběhu:</strong></legend>
		<textarea cols="80" rows="30" name="details" id="details">doplnit</textarea>
	</fieldset>
	
	<fieldset><legend><strong>Energetická náročnost:</strong></legend>
		<textarea cols="80" rows="7" name="energy" id="energy">kouzla, vstupy do Šera, amulety, artefakty</textarea>
	</fieldset>
	
	<fieldset><legend><strong>Počáteční vstupy:</strong></legend>
		<textarea cols="80" rows="7" name="inputs" id="inputs">info z analytického atd.</textarea>
	</fieldset>
	
	<input type="submit" name="insertrep" id="submitbutton" value="Vložit" title="Vložit" />

</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
	latteDrawTemplate(footer);
?>