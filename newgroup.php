<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Nová skupina');
	mainMenu (3);
	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>nová skupina</strong>');
?>
<div id="obsah">
<form action="procgroup.php" method="post" id="inputform">
<fieldset><legend><h1>Nová skupina</h1></legend>
	<div id="info">
		<h3><label for="title">Název:</label></h3>
		<input type="text" name="title" id="title" />
		<div class="clear">&nbsp;</div>	
		<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
			<input type="radio" name="secret" value="0" checked="checked"/>ne<br />
			<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1"/>ano
		<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<h3><label for="notnew">Není&nbsp;nové</label></h3>
					<input type="checkbox" name="notnew"/>
				<div class="clear">&nbsp;</div>';
				}
?>			
	</div>
	<!-- end of #info -->
	
	<fieldset><legend><h2>Popis</h2></legend>
		  <textarea cols="80" rows="7" name="contents" id="contents"></textarea>
	</fieldset>
	
	<input type="submit" name="insertgroup" id="submitbutton" value="Vložit" />
</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
	pageEnd ();
?>