<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nastavení');
	mainMenu (6);
	sparklets ('<strong>nastavení</strong>');
?>
<div id="obsah"><strong>Nepoužívejte svá obvyklá hesla</strong>, protože v tomto systému se hesla ukládají nezakódovaná. <strong>Dají se v databázi přímo přečíst</strong>, takže si vymyslete něco čistě pro DI.<p></p></div>
<form action="procsettings.php" method="post" id="inputform">
	<div>
	<label for="heslo">Heslo:</label>
  	<input type="text" name="heslo" id="heslo" value="<?php echo StripSlashes($usrinfo['pwd'])?>"/>
	</div>
	<div>
	  <label for="plan">Aktuální plán:</label>
	</div>
	<div>
	  <textarea cols="140" rows="300" name="plan" id="plan"><?php echo StripSlashes($usrinfo['plan'])?></textarea>
	</div>
	<div>
	  <input type="submit" name="editsettings" id="submitbutton" value="Vložit" />
	</div>
</form>
<?php
	pageEnd ();
?>
