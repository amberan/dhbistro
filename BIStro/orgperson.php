<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava osoby');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>úprava osoby</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec_p=MySQL_Fetch_Assoc($res)) {

	// kalendář
	function date_picker($name, $rdate, $startyear=NULL, $endyear=NULL)
	{
	if($startyear==NULL) $startyear = date("Y")-10;
	if($endyear==NULL) $endyear=date("Y")+5;

	$cday = StrFTime("%d", $rdate);
	$cmonth = StrFTime("%m", $rdate);
	$cyear = StrFTime("%Y", $rdate);

	$months=array('','Leden','Únor','Březen','Duben','Květen',
		'Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec');

	// roletka dnů
	$html="<select class=\"day\" name=\"".$name."day\">";
	for($i=1;$i<=31;$i++)
	{
		$html.="<option value='$i'".(($i==$cday)?'selected="selected"':'').">$i</option>";
	}
	$html.="</select> ";
	
	// roletka měsíců
	$html.="<select class=\"month\" name=\"".$name."month\">";
	
	for($i=1;$i<=12;$i++)
	{
		$html.="<option value='$i'".(($i==$cmonth)?'selected="selected"':'').">$months[$i]</option>";
	}
	$html.="</select> ";
	
	// roletka let
	$html.="<select class=\"year\" name=\"".$name."year\">";
	
	for($i=$startyear;$i<=$endyear;$i++)
	{
		$html.="<option value='$i'".(($i==$cyear)?'selected="selected"':'').">$i</option>";
	}
	$html.="</select> ";

	return $html;
	}
			
?>
<div id="obsah">
<fieldset><legend><h1>Organizační úprava osoby: <?php echo(StripSlashes($rec_p['surname']).', '.StripSlashes($rec_p['name'])); ?></h1></legend>
	<form action="procperson.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><h2>Základní údaje</h2></legend>
			<div id="info">
				<div class="clear">&nbsp;</div>
				<div>
	  			<h3><label for="rdatum">Registrace:</label></h3>
	  			</div>
				<?php echo date_picker("rdatum",$rec_p['regdate'])?>
				<div class="clear">&nbsp;</div>
			</div>
			<!-- end of #info -->
		</fieldset>
		<fieldset><legend><h2>Registroval</h2></legend>
		<?php
			$sql="SELECT ".DB_PREFIX."users.login AS 'login', ".DB_PREFIX."users.id AS 'id' FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.deleted=0 ORDER BY ".DB_PREFIX."users.login ASC";
			$res=MySQL_Query ($sql);
			while ($rec=MySQL_Fetch_Assoc($res)) {
				echo '<div>
				<input type="radio" name="regusr" value="'.$rec['id'].'" class="checkbox"'.(($rec['id']==$rec_p['iduser'])?' checked="checked"':'').' />
				<label>'.StripSlashes ($rec['login']).'</label>
				</div>';
			}
		?>
		</fieldset>
		<input type="hidden" name="personid" value="<?php echo $rec_p['id']; ?>" />
		<input type="submit" name="orgperson" id="submitbutton" value="Uložit" title="Uložit změny"/>
	</form>

</fieldset>
</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Osoba neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>