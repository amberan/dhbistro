<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Úprava poznámky';

mainMenu();
    switch ($_REQUEST['idtable']) {
        case 1: $sourceurl = "/persons/"; $sourcename = "osoby"; break;
        case 2: $sourceurl = "groups/"; $sourcename = "skupiny"; break;
        case 3: $sourceurl = "/cases/"; $sourcename = "případy"; break;
        case 4: $sourceurl = "reports.php"; $sourcename = "hlášení"; break;
        case 7: $sourceurl = "symbols.php"; $sourcename = "symboly"; break;
        default: $sourceurl = ""; $sourcename = ""; break;
    }
    sparklets('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>úprava poznámky</strong>');
    if (is_numeric($_REQUEST['rid'])) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."note WHERE id=".$_REQUEST['rid']);
        if ($rec = mysqli_fetch_assoc($res)) {
            if ((($rec['secret'] <= $user['aclSecret']) || $rec['iduser'] == $user['userId']) && !$rec['deleted'] == 1) {
                ?>
<div id="obsah">
<form action="procnote.php" method="post" class="otherform">
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" value="<?php echo StripSlashes($rec['title']); ?>"/>
	</div>
	<div>
	  <label for="nsecret">Utajení:</label>
		<select name="nsecret" id="nsecret">
		  <option value="0"<?php if ($rec['secret'] == 0) {
                    echo ' selected="selected"';
                } ?>>veřejná</option>
		  <option value="1"<?php if ($rec['secret'] == 1) {
                    echo ' selected="selected"';
                } ?>>tajná</option>
		  <option value="2"<?php if ($rec['secret'] == 2) {
                    echo ' selected="selected"';
                } ?>>soukromá</option>
		</select>
	</div>
	<?php
    if ($user['aclUser']) { //TODO PERMISSION NOTE?
        $sql = "SELECT id, login FROM ".DB_PREFIX."user WHERE deleted=0 ORDER BY login ASC";
        $res_n = mysqli_query($database, $sql);
        echo '<div>
		<label for="nowner">Vlastník:</label>
		<select name="nowner" id="nowner">';
        while ($rec_n = mysqli_fetch_assoc($res_n)) {
            echo '<option value="'.$rec_n['id'].'"'.(($rec_n['id'] == $user['userId']) ? ' selected="selected"' : '').'>'.$rec_n['login'].'</option>';
        };
        echo '</select>
			  </div>';
    } else {
        echo '<input type="hidden" name="nowner" value="'.$rec['iduser'].'" />';
    } ?>
<?php 			if ($user['aclGamemaster'] == 1) {
        echo '
				<div>
				<label for="nnotnew">Není nové</label>
					<input type="checkbox" name="nnotnew"/><br/>
				</div>';
    } ?>
	<div>
		<label for="notebody">Tělo poznámky:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"><?php echo StripSlashes($rec['note']); ?></textarea>
	</div>
	<div>
		<input type="hidden" name="noteid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editnote.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="hidden" name="idtable" value="<?php echo $_REQUEST['idtable']; ?>" />
		<input type="hidden" name="itemid" value="<?php echo $rec['iditem']; ?>" />
		<input type="submit" value="Uložit poznámku" name="editnote" class="submitbutton" />
	</div>
</form>
</div>
<!-- end of #obsah -->
<?php
            } else {
                echo '<h1>Nemáte práva</h1>
				<div id="obsah">Nemáte práva upravovat tuto poznámku.</div>';
            }
        } else {
            echo '<div id="obsah"><p>Poznámka neexistuje.</p></div>';
        }
    } else {
        echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
    }
    latteDrawTemplate("footer");
?>
