<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Nový symbol');
	mainMenu (5);
	sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>nový symbol</strong>');
?>
<div id="obsah">
	<fieldset><h1><legend>Nový symbol</legend></h1>
	<p id="top-text">Symboly nahrávejte pokud možno ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen symboly jasně rozeznatelné, rozmazané fotky použijte třeba jako přílohu. <br />
	Pokud zadáváte hodnoty pro čáry, křivky, body, geometrické tvary, písma a speciální znaky, hodnota nabývá velikosti 0 až 10.</p>
	<form action="procother.php" method="post" id="inputform" enctype="multipart/form-data">
	    	<datalist id=hodnoty>
				<option>0</option>
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
				<option>6</option>
				<option>7</option>
				<option>8</option>
				<option>9</option>
				<option>10</option>
			</datalist>
		<fieldset><h2><legend>Základní údaje</legend></h2>
			<div id="info">
				<h3><label for="symbol">Symbol:</label></h3><input type="file" name="symbol" id="symbol" /><br />	        	
				<h3><label for="liner">Čáry:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="liner" id="liner" list=hodnoty /><br />
				<h3><label for="curver">Křivky:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="curver" id="curver" list=hodnoty /><br />
				<h3><label for="pointer">Body:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="pointer" id="pointer" list=hodnoty /><br />
				<h3><label for="geometrical">Geom. tvary:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="geometrical" id="geometrical" list=hodnoty /><br />
				<h3><label for="alphabeter">Písma:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="alphabeter" id="alphabeter" list=hodnoty /><br />
				<h3><label for="specialchar">Spec. znaky:</label></h3><input type="range" value="0" min="0" max="10" step="1" name="specialchar" id="specialchar" list=hodnoty /><br />
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
		<fieldset><h2><legend>Poznámky k symbolu</legend></h2>
			<div class="field-text">
				<textarea cols="80" rows="15" name="contents" id="contents">Doplnit.</textarea>
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