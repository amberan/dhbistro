<?php
	require_once ('./inc/func_main.php');
        if (!$usrinfo['right_power']) {
            unauthorizedAccess(8, 1, 0, 0);
        }
	pageStart ('Úprava uživatele');
	mainMenu (2);
	sparklets ('<a href="./users.php">uživatelé</a> &raquo; <strong>úprava uživatele</strong>');
	if (is_numeric($_REQUEST['rid'])) {
		$res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE id=".$_REQUEST['rid']);
		if ($rec=mysqli_fetch_assoc ($res)) {
?>
<div id="obsah">
<form action="procuser.php" method="post" id="inputform" class="inputform">
	<fieldset><legend><h2>Základní údaje</h2></legend>
	<div>
	  <h3><label for="login" id="login">Login:</label></h3>
	  <input type="text" name="login" id="login" value="<?php echo StripSlashes($rec['login']); ?>" />
	</div>
	<div>
	  <h3><label for="power" id="poweruser">Power user:</label></h3>
		<select name="power" id="poweruser">
			<option value="0"<?php if ($rec['right_power']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec['right_power']==1) { echo ' selected="selected"'; } ?>>ano</option>
		</select>
	</div>
	<div>
	  <h3><label for="texty" id="texty">Editace textů:</label></h3>
		<select name="texty" id="texty">
			<option value="0"<?php if ($rec['right_text']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec['right_text']==1) { echo ' selected="selected"'; } ?>>ano</option>
		</select>
	</div>
	<div>
	  <h3><label for="idperson"  id="persnum">Číslo osoby:</label></h3>
	  <input type="text" name="idperson" id="idperson" value="<?php echo StripSlashes($rec['idperson']); ?>" />
	</div>
	<div>
	  <input type="hidden" name="userid" value="<?php echo $rec['id']; ?>" />
	  <input type="submit" name="edituser" id="submitbutton" value="Uložit" />
	</div>
	</fieldset>
</form>

<fieldset><legend><h2>Nedodělky</h2></legend>
	<h3>Rozpracovaná nedokončená hlášení: <?php
				$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.iduser=".$rec['id']." AND ".DB_PREFIX."reports.status=0 AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC";
				$res_r=mysqli_query ($database,$sql_r);
				$rec_count = mysqli_num_rows ($res_r);
				echo $rec_count
				?>
				</h3><p>
				<?php
				if (mysqli_num_rows ($res_r)) {
					$reports=Array();
					while ($rec_r=mysqli_fetch_assoc ($res_r)) {
						$reports[]='<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a>';
					}
					echo implode ($reports,'<br />');
				} else {
					echo 'Uživatel nemá žádná nedokončená hlášení.';
				} ?></p>
	<div class="clear">&nbsp;</div>
				<h3>Přiřazené neuzavřené případy: <?php
			$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$rec['id']." ORDER BY ".DB_PREFIX."cases.title ASC";
			$pers=mysqli_query ($database,$sql);
			$rec_count = mysqli_num_rows ($pers);
			echo $rec_count
			?>
			</h3><p>
			<?php
			$cases=Array();
			while ($perc=mysqli_fetch_assoc ($pers)) {
				$cases[]='<a href="./readcase.php?rid='.$perc['id'].'&hidenotes=0">'.StripSlashes ($perc['title']).'</a>';
			}
			echo ((implode($cases, '<br />')<>"")?implode($cases, '<br />'):'<em>Uživatel nemá žádný přiřazený neuzavřený případ.</em>');
			?></p>
	<div class="clear">&nbsp;</div>
			<h3>Nedokončené úkoly: <?php
			$sql_r="SELECT * FROM ".DB_PREFIX."tasks WHERE ".DB_PREFIX."tasks.iduser=".$rec['id']." AND ".DB_PREFIX."tasks.status=0 ORDER BY ".DB_PREFIX."tasks.created ASC";
			$res_r=mysqli_query ($database,$sql_r);
			$rec_count = mysqli_num_rows ($res_r);
			echo $rec_count
			?>
			</h3><p>
			<?php
			if (mysqli_num_rows ($res_r)) {
				$tasks=Array();
				while ($rec_r=mysqli_fetch_assoc ($res_r)) {
					$tasks[]=StripSlashes ($rec_r['task']).' ('.getAuthor($rec_r['created_by'],2).')';
				}
				echo implode ($tasks,'<br />');
			} else {
				echo 'Uživatel nemá žádné nedokončené úkoly.';
			} ?></p>
	<div class="clear">&nbsp;</div>
</fieldset>
</div>

<?php
		} else {
		  echo '<div id="obsah"><p>Uživatel neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>
