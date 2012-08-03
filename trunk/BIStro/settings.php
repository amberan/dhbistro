<?php
	require_once ('./inc/func_main.php');
	pageStart ('Nastavení');
	mainMenu (6);
	sparklets ('<strong>nastavení</strong>');
//	if (!isset ($hiddenpwd)) {
//		$hiddenpwd='hidden';
//	}
?>
<script type="text/javascript" language="JavaScript">
<!--
function pwdcheck(form)
{
	if (form.heslo.value != form.heslo2.value) {
		alert ('Hesla nejsou stejná.');
		return false
	}
	else return true;
}
-->
</script>
<div id="obsah"><strong>Nepoužívejte svá obvyklá hesla</strong>, protože v tomto systému se hesla ukládají nezakódovaná. <strong>Dají se v databázi přímo přečíst</strong>, takže si vymyslete něco čistě pro hru.<p></p></div>
<form action="procsettings.php" method="post" name="inputform" id="inputform" onSubmit="return pwdcheck(this);">
	<div>
	<label for="heslo">Heslo:</label>
  	<input type="password" name="heslo" id="heslo" value="<?php echo StripSlashes($usrinfo['pwd'])?>"/>
	</div>
	<div>
	<label for="heslo2">Ověření:</label>
  	<input type="password" name="heslo2" id="heslo2" value="<?php echo StripSlashes($usrinfo['pwd'])?>"/>
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
