<h1 class="center">Úpravit uživatele</h1>
<div class="table" id="user">
<?php
if (!$usrinfo['right_power']) {
	unauthorizedAccess(8, 1, 0, 0);
} elseif (is_numeric($_REQUEST['user_edit'])) {
	$res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE id=".$_REQUEST['user_edit']);
	if ($rec=mysqli_fetch_assoc ($res)) {
?>
	<form action="users.php" method="post" >
		<div>
			<span>Login:</span>
			<input type="text" name="login" id="login" value="<?php echo StripSlashes($rec['login']); ?>" />
		</div>
		<div>
			<span>Číslo osoby:</span>
			<input type="text" name="idperson" id="idperson" value="<?php echo StripSlashes($rec['idperson']); ?>" />
		</div>
		<div><b>Práva</b></div>
		<div>
			<span class="button">POWER USER</span>
			<select name="power" id="poweruser">
				<option value="0"<?php if ($rec['right_power']==0) { echo ' selected="selected"'; } ?>>ne</option>
				<option value="1"<?php if ($rec['right_power']==1) { echo ' selected="selected"'; } ?>>ano</option>
			</select>
		</div>	
		<div>
			<span class="button">EDITOR</span>
			<select name="texty" id="texty">
				<option value="0"<?php if ($rec['right_text']==0) { echo ' selected="selected"'; } ?>>ne</option>
				<option value="1"<?php if ($rec['right_text']==1) { echo ' selected="selected"'; } ?>>ano</option>
			</select>
		</div>	
<?php if ($usrinfo['right_aud']) { //pokud je uzivatel auditorem?>	
		<div>
			<span class="button">AUDITOR</span>
			<select name="auditor" id="auditor">
				<option value="0"<?php if ($rec['right_aud']==0) { echo ' selected="selected"'; } ?>>ne</option>
				<option value="1"<?php if ($rec['right_aud']==1) { echo ' selected="selected"'; } ?>>ano</option>
			</select>
		</div>
<?php }
	if ($usrinfo['right_org']) { //pokud je uzivatel organizatorem?>	
		<div>
			<span class="button">ORGANIZATOR</span>
			<select name="organizator" id="organizator">
				<option value="0"<?php if ($rec['right_org']==0) { echo ' selected="selected"'; } ?>>ne</option>
				<option value="1"<?php if ($rec['right_org']==1) { echo ' selected="selected"'; } ?>>ano</option>
			</select>
		</div>
<?php } ?>
		<div>
			<input type="hidden" name="userid" value="<?php echo $rec['id']; ?>" />
			<input type="submit" name="edituser" id="submitbutton" value="Uložit" />
		</div>
	</form>
	<div><b>Rozpracovaná nedokončená hlášení: <?php
	$hlaseni_sql=mysqli_query ($database,"SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.iduser=".$rec['id']." AND ".DB_PREFIX."reports.status=0 AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC");
	echo mysqli_num_rows ($hlaseni_sql);
	?></b></div>
		<ul>
	<?php
	if (mysqli_num_rows ($hlaseni_sql)) {
		while ($hlaseni=mysqli_fetch_assoc ($hlaseni_sql)) {
			echo '<li><a href="./readactrep.php?rid='.$hlaseni['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($hlaseni['label']).'</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>Uživatel nemá žádná nedokončená hlášení.</p>';
	} ?>
	<div><b>Přiřazené neuzavřené případy: <?php
	$pripady_sql=mysqli_query ($database,"SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$rec['id']." ORDER BY ".DB_PREFIX."cases.title ASC");
	echo mysqli_num_rows ($pripady_sql);
	?></b></div>
		<ul>
	<?php
	if (mysqli_num_rows ($pripady_sql)) {
		while ($pripady=mysqli_fetch_assoc ($pripady_sql)) {
			echo '<li><a href="./readcase.php?rid='.$pripady['id'].'&hidenotes=0">'.StripSlashes ($pripady['title']).'</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>Uživatel nemá žádný přiřazený neuzavřený případ.</p>';
	} ?>

	<div><b>Nedokončené úkoly: <?php
	$ukoly_sql=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."tasks WHERE ".DB_PREFIX."tasks.iduser=".$rec['id']." AND ".DB_PREFIX."tasks.status=0 ORDER BY ".DB_PREFIX."tasks.created ASC");
	echo mysqli_num_rows ($ukoly_sql);
	?></b></div>
		<ul>
	<?php
	if (mysqli_num_rows ($ukoly_sql)) {
		while ($ukoly=mysqli_fetch_assoc ($ukoly_sql)) {
			echo '<li><a href="./readcase.php?rid='.$ukoly['id'].'&hidenotes=0">'.StripSlashes ($ukoly['title']).'</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>Uživatel nemá žádné nedokončené úkoly.</p>';
	}

} else { echo '<div id="obsah"><p>Uživatel neexistuje.</p></div>';}
} else { echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>'; }?>
</div>