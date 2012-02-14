<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nová osoba');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>nová osoba</strong>');
?>
<div id="obsah"><p>Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran,
	ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách.</p></div>
<form action="procperson.php" method="post" id="inputform" enctype="multipart/form-data">
	<div>
	  <label for="name" class="required">Jméno:</label>
	  <input type="text" name="name" id="name" />
	</div>
	<div>
	  <label for="surname">Příjmení:</label>
	  <input type="text" name="surname" id="surname" />
	</div>
	<div>
	  <label for="side">Strana:</label>
		<select name="side" id="side">
			<option value="0" selected="selected">neznámá</option>
			<option value="1">světlo</option>
			<option value="2">tma</option>
			<option value="3">člověk</option>
		</select>
	</div>
	<div>
	  <label for="power">Síla:</label>
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
	</div>
		<div>
	  <label for="spec">Specializace:</label>
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
	</div>
	<div>
	  <label for="phone">Telefon:</label>
	  <input type="text" name="phone" id="phone" />
	</div>
	<div>
	  <label for="portrait">Portrét:</label>
	  <input type="file" name="portrait" id="portrait" />
	</div>
	<div>
	  <label for="secret">Přísně tajné:</label>
		<select name="secret" id="secret">
		  <option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
	  <label for="contents" class="required">Popis:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="contents" id="contents"></textarea>
	</div>
	<div>
	  <input type="submit" name="insertperson" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>
