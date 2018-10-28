<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	auditTrail(6, 1, 0);
	pageStart ('Nástěnka');
	mainMenu (1);
	deleteUnread (6,0);
	sparklets ('<strong>nástěnka</strong>',(($usrinfo['right_power'])?'<a href="index.php">zobrazit aktuality</a>; <a href="editdashboard.php">upravit nástěnku</a>':'<a href="index.php">zobrazit aktuality</a>'));

// dashboard
?>
<div id="dashboard">
<fieldset><legend><h2>Osobní nástěnka</h2></legend>
	<table><tr><td>
	<h3>Rozpracovaná nedokončená hlášení: <?php
				$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.iduser=".$usrinfo['id']." AND ".DB_PREFIX."reports.status=0 AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC";
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
					echo 'Nemáte žádná nedokončená hlášení.';
				} ?></p>
	<div class="clear">&nbsp;</div>
				<h3>Přiřazené neuzavřené případy: <?php
			$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." AND ".DB_PREFIX."cases.status<>1 AND ".DB_PREFIX."cases.deleted=0 ORDER BY ".DB_PREFIX."cases.title ASC";
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
			echo ((implode($cases, '<br />')<>"")?implode($cases, '<br />'):'<em>Nemáte žádný přiřazený neuzavřený případ.</em>');
			?></p>
	</td>
	<td>
	<h3>Nedokončené úkoly: <?php
			$sql_r="SELECT * FROM ".DB_PREFIX."tasks WHERE ".DB_PREFIX."tasks.iduser=".$usrinfo['id']." AND ".DB_PREFIX."tasks.status=0 ORDER BY ".DB_PREFIX."tasks.created ASC";
			$res_r=mysqli_query ($database,$sql_r);
			$rec_count = mysqli_num_rows ($res_r);
			echo $rec_count
			?>
			</h3><p>
			<?php
			if (mysqli_num_rows ($res_r)) {
				$tasks=Array();
				while ($rec_r=mysqli_fetch_assoc ($res_r)) {
					$tasks[]=StripSlashes ($rec_r['task']).' ('.getAuthor($rec_r['created_by'],0).') | <a href="procother.php?fnshtask='.$rec_r['id'].'">hotovo</a>';
				}
				echo implode ($tasks,'<br />');
			} else {
				echo 'Nemáte žádné nedokončené úkoly.';
			} ?></p>
	</td>
	</tr></table>
	<div class="clear">&nbsp;</div>
</fieldset>
<?php 
	$res_d=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
	if ($rec_d=mysqli_fetch_assoc ($res_d)) {
		?>
		<fieldset><legend>
		<h2>Veřejná nástěnka</h2>
		<strong>Poslední změna:</strong> <?php echo(Date ('d. m. Y',$rec_d['created'])); ?>
				<strong>Změnil:</strong> <?php 
				$name=getAuthor($rec_d['iduser'],0);
				echo $name; ?> 
		</legend>
		<p>
		<?php if (isset($rec_d['content'])) {
			echo StripSlashes($rec_d['content']);
		} else {
			echo 'Veřejná nástěnka nemá žádný obsah.';
		} ?></p>
		<div class="clear">&nbsp;</div>
		</fieldset>
		</div>
	<?php 
	} else {
		?>
		<fieldset><legend><h2>Veřejná nástěnka</h2></legend>
		<p>Veřejná nástěnka nemá žádný obsah.</p>
		<div class="clear">&nbsp;</div>
		</fieldset>		
	<?php } ?>

<?php
	pageEnd ();
?>