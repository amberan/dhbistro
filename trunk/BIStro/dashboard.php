<?php
	require_once ('./inc/func_main.php');
	pageStart ('Aktuality');
	mainMenu (1);
	sparklets ('<strong>nástěnka</strong>',(($usrinfo['right_power'])?'<a href="index.php">zobrazit aktuality</a>; <a href="editdashboard.php">upravit nástěnku</a>':'<a href="index.php">zobrazit aktuality</a>'));

// dashboard
?>
<div id="dashboard">
<fieldset><legend><h2>Osobní nástěnka</h2></legend>
	<h3>Rozpracovaná nedokončená hlášení: <?php
				$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.iduser=".$usrinfo['id']." AND ".DB_PREFIX."reports.status=0 AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC";
				$res_r=MySQL_Query ($sql_r);
				$rec_count = MySQL_Num_Rows($res_r);
				if (MySQL_Num_Rows($res_r)) {
					$reports=Array();
					while ($rec_r=MySQL_Fetch_Assoc($res_r)) {
						$reports[]='<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a>';
					}
					echo $rec_count.'</h3><p>'.implode ($reports,'<br />');
				} else {
					echo $rec_count.'</h3><p>Nemáte žádná nedokončená hlášení.';
				} ?></p>
	<div class="clear">&nbsp;</div>
				<h3>Přiřazené neuzavřené případy: <?php
			$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." ORDER BY ".DB_PREFIX."cases.title ASC";
			$pers=MySQL_Query ($sql);
			$rec_count = MySQL_Num_Rows($pers);
			$cases=Array();
			while ($perc=MySQL_Fetch_Assoc($pers)) {
				$cases[]='<a href="./readcase.php?rid='.$perc['id'].'&hidenotes=0">'.StripSlashes ($perc['title']).'</a>';
			}
			echo $rec_count.'</h3><p>'.((implode($cases, '<br />')<>"")?implode($cases, '<br />'):'<em>Nemáte žádný přiřazený neuzavřený případ.</em>');
			?></p>
	<div class="clear">&nbsp;</div>
</fieldset>
<?php 
	$res_d=MySQL_Query ("SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
	if ($rec_d=MySQL_Fetch_Assoc($res_d)) {
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
		</div>
	<?php } ?>

<?php
	pageEnd ();
?>