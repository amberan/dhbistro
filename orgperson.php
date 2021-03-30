<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Úprava osoby';
    mainMenu();
    sparklets('<a href="./persons.php">osoby</a> &raquo; <strong>úprava osoby</strong>');
    if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
        $res = mysqli_query($database,"SELECT * FROM ".DB_PREFIX."person WHERE id=".$_REQUEST['rid']);
        if ($rec_p = mysqli_fetch_assoc($res)) {
            ?>
<div id="obsah">
<fieldset><legend><strong>Organizační úprava osoby: <?php echo stripslashes($rec_p['surname']).', '.stripslashes($rec_p['name']); ?></strong></legend>
	<form action="persons.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><strong>Základní údaje</strong></legend>
			<div id="info">
				<div class="clear">&nbsp;</div>
				<div>
	  			<h3><label for="rdatum">Vytvořeno:</label></h3>
	  			</div>
				<?php echo date_picker("rdatum",$rec_p['regdate']); ?>
				<div class="clear">&nbsp;</div>
				<div>
				<h3><label for="regusr">Vytvořil:</label></h3>
				<select name="regusr" id="regusr">
				<?php
                    $sql = "SELECT ".DB_PREFIX."user.userName AS 'login', ".DB_PREFIX."user.userId AS 'id' FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.userDeleted=0 ORDER BY ".DB_PREFIX."user.userName ASC";
            $res = mysqli_query($database,$sql);
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<div>
						<option value="'.$rec['id'].'" "'.($rec['id'] == $rec_p['iduser'] ? ' checked="checked"' : '').'>'.stripslashes($rec['login']).'</option>
						</div>';
            } ?>
				</select>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			<!-- end of #info -->
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
    latteDrawTemplate("footer");
?>