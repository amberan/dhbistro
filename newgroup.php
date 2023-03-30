<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;


latteDrawTemplate("header");

$latteParameters['title'] = 'Nová skupina';

    mainMenu();
    sparklets('<a href="./groups/">skupiny</a> &raquo; <strong>nová skupina</strong>');
?>
<div id="obsah">
<form action="groups/" method="post" id="inputform">
<fieldset><legend><strong>Nová skupina</strong></legend>
	<div id="info">
		<h3><label for="title">Název:</label></h3>
		<input type="text" name="title" id="title" />
		<div class="clear">&nbsp;</div>
		<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
			<input type="radio" name="secret" value="0" checked="checked"/>ne<br />
			<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1"/>ano
		<div class="clear">&nbsp;</div>
<?php 			if ($user['aclGamemaster'] == 1) {
    echo '
				<h3><label for="notnew">Není&nbsp;nové</label></h3>
					<input type="checkbox" name="notnew"/>
				<div class="clear">&nbsp;</div>';
}
?>
	</div>
	<!-- end of #info -->

	<fieldset><legend><strong>Popis</strong></legend>
		  <textarea cols="80" rows="7" name="contents" id="contents"></textarea>
	</fieldset>

	<input type="submit" name="insertgroup" id="submitbutton" value="Vložit" />
</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
    latteDrawTemplate("footer");
?>
