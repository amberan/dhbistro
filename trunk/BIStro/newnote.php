<?php
require_once ('./inc/func_main.php');
pageStart ('Nová poznámka');
mainMenu (4);
switch ($_REQUEST['idtable']) {
				case 1: $sourceurl="editperson.php"; $sourcename="osoby"; break;
				case 2: $sourceurl="editgroup.php"; $sourcename="skupiny"; break;
				case 3: $sourceurl="editcase.php"; $sourcename="případy"; break;
				case 4: $sourceurl="editreport.php"; $sourcename="hlášení"; break;
				default: $sourceurl=""; $sourcename=""; break;
			}
sparklets ('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>nová poznámka</strong>');
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
			  <option value="0">veřejná</option>
			  <option value="1">tajná</option>
			  <option value="2">soukromá</option>
			</select>
		</div>
		<div>
			<label for="notebody">Tělo poznámka:</label>
			<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
		</div>
		<div>
			<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="hidden" name="backurl" value="<?php echo $sourceurl; ?>?rid=<?php echo $_REQUEST['rid']; ?>" />
			<input type="hidden" name="tableid" value="<?php echo $_REQUEST['idtable']; ?>" />
			<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
		</div>
	</form>
</div>
<!-- end of #obsah -->
<?php
	}else{
		echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
pageEnd ();
?>