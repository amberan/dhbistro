<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Nastavení');
	mainMenu (6);
	sparklets ('<strong>nastavení</strong>');
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

<div id="obsah">
	<script type="text/javascript">
	<!--
	window.onload=function(){
		FixitRight('submitbutton', 'ramecek');
	};
	-->
	</script>
<form action="procsettings.php" method="post" name="inputform" id="inputform" onSubmit="return pwdcheck(this);">
<fieldset id="ramecek"><legend><h1>Uživatel: <?php echo $usrinfo['login']; ?></h1></legend>
	<p>
		<strong>Nepoužívejte svá obvyklá hesla</strong>, protože v tomto systému se hesla ukládají nezakódovaná.<br/>
		<strong>Dají se v databázi přímo přečíst</strong>, takže si vymyslete něco čistě pro hru.
	</p>
	<fieldset><legend><h2>Základní&nbsp;nastavení</h2></legend>
	<div id="info">
		<h3><label for="timeout">Timeout:</label></h3>	
	  	<input type="text" name="timeout" id="timeout" value="<?php echo $usrinfo['timeout']?>"/>
	  	Zadávejte ve vteřinách v rozmezí 30 - 1800.
		<div class="clear">&nbsp;</div>
		<h3><label for="soucheslo">Staré heslo:</label></h3>
	  	<input type="password" name="soucheslo" id="soucheslo" value=""/>
	  	<div class="clear">&nbsp;</div>
		<h3><label for="heslo">Nové heslo:</label></h3>
	  	<input type="password" name="heslo" id="heslo" value=""/>
	  	<div class="clear">&nbsp;</div>
		<h3><label for="heslo2">Nové znovu:</label></h3>
	  	<input type="password" name="heslo2" id="heslo2" value=""/>
	  	<div class="clear">&nbsp;</div>
	</div>
	<!-- end of #info -->
	</fieldset>

	<fieldset><legend><h2>Aktuální plán:</h2></legend>
		<textarea cols="140" rows="300" name="plan" id="plan"><?php echo StripSlashes($usrinfo['plan'])?></textarea>
	</fieldset>

	<input type="submit" name="editsettings" id="submitbutton" value="Vložit"  title="Vložit"/>
</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
	pageEnd ();
?>
