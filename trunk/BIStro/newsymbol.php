<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nový symbol');
	mainMenu (5);
	sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>nový symbol</strong>');
?>
<div id="obsah">
	<fieldset><legend><h1>Nový symbol</h1></legend>
	<p id="top-text">Symboly nahrávejte pokud možno ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen symboly jasně rozeznatelné, rozmazané fotky použijte třeba jako přílohu.</p>
	<form action="procother.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><h2>Základní údaje</h2></legend>
			<div id="info">
				<h3><label for="symbol">Symbol:</label></h3><input type="file" name="symbol" id="symbol" />
				<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
				}
?>					
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- násedují poznámky k symbolu -->
		<fieldset><legend><h2>Poznámky k symbolu</h2></legend>
			<div class="field-text">
				<textarea cols="80" rows="+ř" name="contents" id="contents">Doplnit.</textarea>
			</div>
		</fieldset>
		<input type="submit" name="insertsymbol" id="submitbutton" value="Vložit" title="Vložit symbol"/>
	</form>
	</fieldset>
</div>
<!-- end of #obsah -->
<?php
	pageEnd ();
?>