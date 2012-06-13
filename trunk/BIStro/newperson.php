<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nová osoba');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>nová osoba</strong>');
?>
<div id="obsah">
	<h1>Nová osoba</h1>
	<p>Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách.</p>
	<form action="procperson.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><h2>Základní údaje</h2></legend>
			<div id="info">
				<h3><label for="name" class="required">Jméno:</label></h3><input type="text" name="name" id="name" />	
				<div class="clear">&nbsp;</div>	
				<h3><label for="surname">Příjmení:</label></h3><input type="text" name="surname" id="surname" />
				<div class="clear">&nbsp;</div>
				<h3><label for="side">Strana:</label></h3>
					<select name="side" id="side">
						<option value="0" selected="selected">neznámá</option>
						<option value="1">světlo</option>
						<option value="2">tma</option>
						<option value="3">člověk</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="power">Síla:</label></h3>
					<select name="power" id="power">
						<option value="0" selected="selected">neznámá</option>
						<option value="1">1. kategorie</option>
						<option value="2">2. kategorie</option>
						<option value="3">3. kategorie</option>
						<option value="4">4. kategorie</option>
						<option value="5">5. kategorie</option>
						<option value="6">6. kategorie</option>
						<option value="7">7. kategorie</option>
						<option value="8">mimo kategorie</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="spec">Specializace:</label></h3>
					<select name="spec" id="spec">
						<option value="0" selected="selected">neznámá</option>
						<option value="1">bílý mág</option>
						<option value="2">černý mág</option>
						<option value="3">léčitel</option>
						<option value="4">obrateň</option>
						<option value="5">upír</option>
						<option value="6">vlkodlak</option>
						<option value="7">vědma</option>
						<option value="8">zaříkávač</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="phone">Telefon:</label></h3><input type="text" name="phone" id="phone" />
				<div class="clear">&nbsp;</div>
				<h3><label for="portrait">Portrét:</label></h3><input type="file" name="portrait" id="portrait" />
				<div class="clear">&nbsp;</div>
				<h3><label for="secret">Přísně tajné:</label></h3>
					<input type="radio" name="secret" id="secret" value="0" checked/>ne<br/>
					<h3><label>&nbsp;</label></h3><input type="radio" name="secret" id="secret" value="1">ano
				<div class="clear">&nbsp;</div>
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><h2>Popis osoby</h2></legend>
			<div class="field-text">
				<textarea cols="80" rows="7" name="contents" id="contents"></textarea>
			</div>
		</fieldset>
		<input type="submit" name="insertperson" id="submitbutton" value="Vložit" />
	</form>
</div>
<!-- end of #obsah -->
<?php
	pageEnd ();
?>