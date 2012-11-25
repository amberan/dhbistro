<?php
	require_once ('./inc/func_main.php');
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
<script type="text/javascript">
<!--
function MrFixit (sonny, daddy){ //both parameters must be ID
	// Mr.Fixit v1.0
	// requires jQuery to work !!!
	var dBorder = ($("#"+daddy).outerWidth(false)-$("#"+daddy).innerWidth())/2;
	var topStandart = $("#"+sonny).offset().top;
	var topBuff = topStandart - $("#"+daddy).offset().top - dBorder;
	
	var sHeight = $("#"+sonny).outerHeight(false);
	var dInnerHeight = $("#"+daddy).innerHeight();
	var maxScrollTop = $("#"+daddy).offset().top + dBorder + dInnerHeight - sHeight - 2*topBuff;
	var absInnerPos = $("#"+daddy).innerHeight() - sHeight - topBuff;

	$(window).scroll(function(e){	
		if(($(window).scrollTop() > (topStandart-topBuff)) && ($(window).scrollTop() < maxScrollTop)){
			$("#"+sonny).css({
				position: 'fixed',
				top: topBuff
			});
		}else if($(window).scrollTop() >= maxScrollTop){
			$("#"+sonny).css({
				position: 'absolute',
				top: absInnerPos
			});
		}else {
			$("#"+sonny).css({
				position: 'static',
				top: topBuff
			});
		}
	});	
	 $("#"+daddy).click(function(){ alert (maxScrollTop + "px :-[" +"\n"+ $(window).scrollTop() + "px :-]"+"\n"+absInnerPos); });
}
window.onload=function(){
	MrFixit('submitbutton', 'ramecek');
};
-->
</script>

<div id="obsah">
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

	<input type="submit" name="editsettings" id="submitbutton" value="Vložit" />
</fieldset>
</form>
</div>
<!-- end of #obsah -->
<?php
	pageEnd ();
?>
