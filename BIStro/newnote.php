<?php
require_once ('./inc/func_main.php');
pageStart ('Nová poznámka');
mainMenu (4);
switch ($_REQUEST['idtable']) {
				case 1: $sourceurl1="editperson.php"; $sourceurl2="editperson.php"; $sourcename="osoba"; $idtable=1; break;
				case 2: $sourceurl1="editgroup.php"; $sourceurl2="editgroup.php"; $sourcename="skupina"; $idtable=2;  break;
				case 3: $sourceurl1="editcase.php"; $sourceurl2="editcase.php"; $sourcename="případ"; $idtable=3; break;
				case 4: $sourceurl1="editactrep.php"; $sourceurl2="editactrep.php"; $sourcename="hlášení"; $idtable=4; break;
				case 5: $sourceurl1="readperson.php?rid=".$_REQUEST['rid']."&hidenotes=0"; $sourceurl2="persons.php"; $sourcename="osoby"; $idtable=1; break;
				case 6: $sourceurl1="readgroup.php?rid=".$_REQUEST['rid']."&hidenotes=0"; $sourceurl2="groups.php"; $sourcename="skupiny"; $idtable=2; break;
				case 7: $sourceurl1="readcase.php?rid=".$_REQUEST['rid']."&hidenotes=0"; $sourceurl2="cases.php"; $sourcename="případy"; $idtable=3; break;
				case 8: $sourceurl1="readactrep.php?rid=".$_REQUEST['rid']."&hidenotes=0"; $sourceurl2="reports.php"; $sourcename="hlášení"; $idtable=4; break;
				default: $sourceurl=""; $sourcename=""; break;
			}
sparklets ('<a href="./'.$sourceurl2.'">'.$sourcename.'</a> &raquo; <strong>nová poznámka</strong>');
if (is_numeric($_REQUEST['rid'])) {
	?>
<div id="obsah">
	<form action="procnote.php" method="post" class="otherform">
		<p>K osobě si můžete připsat kolik chcete poznámek.</p>
		<p>Nová poznámka:</p>
		<div>
			<label for="notetitle">Nadpis:</label>
			<input type="text" name="title" id="notetitle" />
		</div>
		<div>
		  <label for="nsecret">Utajení:</label>
			<select name="secret" id="nsecret">
			  <?php if ($_REQUEST['s']==0) {
			  	echo '<option value="0">veřejná</option>';
			  } ?>
			  <option value="1">tajná</option>
			  <option value="2">soukromá</option>
			</select>
		</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<div>
				<label for="nnotnew">Není nové</label>
					<input type="checkbox" name="nnotnew"/><br/>
				</div>';
				}
?>		
		<div>
			<label for="notebody">Tělo poznámka:</label>
			<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
		</div>
		<div>
			<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="hidden" name="backurl" value="<?php echo $sourceurl1; ?>?rid=<?php echo $_REQUEST['rid']; ?>" />
			<input type="hidden" name="tableid" value="<?php echo $idtable; ?>" />
			<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
		</div>
	</form>
</div>
<!-- end of #obsah -->
<?php
	}else{
		echo '<div id="obsah"><p>Tohle nezkoušejte.';
	}
pageEnd ();
?>