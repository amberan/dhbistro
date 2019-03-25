<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Nastavení';
  
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);


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
<form action="settings.php" method="post" name="edituser" id="inputform" onSubmit="return pwdcheck(this);">
<fieldset id="ramecek"><legend><strong>Uživatel: <?php echo $usrinfo['login']; ?></strong></legend>
	<fieldset><legend><strong>Základní&nbsp;nastavení</strong></legend>
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

	<fieldset><legend><strong>Aktuální plán:</strong></legend>
		<textarea cols="140" rows="300" name="plan" id="plan"><?php echo StripSlashes($usrinfo['plan'])?></textarea>
	</fieldset>

	<input type="submit" name="editsettings" id="submitbutton" value="Vložit"  title="Vložit"/>
</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>
