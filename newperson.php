<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Nová osoba';

mainMenu();
    sparklets('<a href="/persons/">osoby</a> &raquo; <strong>nová osoba</strong>');
?>
<div id="obsah">
	<fieldset><legend><strong>Nová osoba</strong></legend>
	<form action="/persons/" method="post" id="inputform" enctype="multipart/form-data">
<?php  if ($user['aclGamemaster'] == 1) {
    $sql = 'SELECT '.DB_PREFIX.'person.name, '.DB_PREFIX.'person.surname, '.DB_PREFIX.'user.userName , '.DB_PREFIX.'user.userId
                    FROM '.DB_PREFIX.'user
                    JOIN '.DB_PREFIX.'person ON '.DB_PREFIX.'user.personId = '.DB_PREFIX.'person.id
                    ORDER BY '.DB_PREFIX.'user.userName ASC';
    $res = mysqli_query($database, $sql); ?>
    <fieldset><legend><strong>Organizační úprava osoby</strong></legend>
			<div id="info">
				<div class="clear">&nbsp;</div>
                    <div>
                    <h3><label for="rdatum">Vytvořeno:</label></h3>

				<?php echo date_picker("rdatum", 1970); //org?>
                    </div>
                <div class="clear">&nbsp;</div>
				<div>
				<h3><label for="regusr">Vytvořil:</label></h3>
				<select name="regusr" id="regusr">
<?php           while ($rec = mysqli_fetch_assoc($res)) {
        echo '<option value="'.$rec['userId'].'" '.($rec['userId'] == $rec_p['regid'] ? ' selected' : '').'>'.stripslashes($rec['userName']).'       -      '.stripslashes($rec['surname']).', '.stripslashes($rec['name']).'</option>';
    } ?>
				</select>
				</div>
				<div class="clear">&nbsp;</div>
  				<div>
	  			<h3><label for="notnew">organizační změna: <br> (není nové)</label></h3>
                  <input type=checkbox name=notnew checked >
	  			</div>

			</div>
		</fieldset>
<?php
} ?>
	<p id="top-text">Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, symboly ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách. Symboly rovněž nahrávejte jasně rozeznatelné.</p>

    <fieldset><legend><strong>Základní údaje</strong></legend>
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
						<option value="9">vykladač</option>
						<option value="10">jasnovidec</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="phone">Telefon:</label></h3><input type="text" name="phone" id="phone" />
				<div class="clear">&nbsp;</div>
				<h3><label for="portrait">Portrét:</label></h3><input type="file" name="portrait" id="portrait" />
				<div class="clear">&nbsp;</div>
				<h3><label for="symbol">Symbol:</label></h3><input type="file" name="symbol" id="symbol" />
				<div class="clear">&nbsp;</div>
				<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
					<input type="radio" name="secret" value="0" checked="checked"/>ne<br/>
					<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1">ano
				<div class="clear">&nbsp;</div>
				<div class="clear">&nbsp;</div>
				<h3><label for="personRoof">strop</label></h3><input type="checkbox" name="personRoof"/>
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><strong>Popis osoby</strong></legend>
			<div class="field-text">
				<textarea cols="80" rows="7" name="contents" id="contents">Doplnit.</textarea>
			</div>
		</fieldset>
		<input type="submit" name="insertperson" id="submitbutton" value="Vložit" title="Vložit osobu"/>
	</form>
	</fieldset>
</div>
<!-- end of #obsah -->
<?php
    latteDrawTemplate("footer");
?>
