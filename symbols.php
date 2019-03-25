<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Symboly' ;
  
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

	auditTrail(7, 1, 0);
	mainMenu (5);
	deleteUnread (7,'none');
	sparklets ('<a href="persons.php">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>','<a href="newsymbol.php">nový symbol</a>; <a href="symbol_search.php">vyhledat symbol</a>');
	
	// symbolu
	$sql="SELECT * FROM ".DB_PREFIX."symbols WHERE ".DB_PREFIX."symbols.deleted=0 AND ".DB_PREFIX."symbols.assigned=0 ORDER BY ".DB_PREFIX."symbols.created DESC";
	$res=mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr><th>Symbol</th>
	  <th>Poznámky</th>
	  <th>Výskyt</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=mysqli_fetch_assoc ($res)) {
		  echo '<tr class="'.((searchRecord(7,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">
		  <td><a href="readsymbol.php?rid='.$rec['id'].'"><img src="getportrait.php?nrid='.$rec['id'].'" alt="symbol chybí" /></a></td>
		  <td>'.(StripSlashes($rec['desc'])).'<br />';
			// generování poznámek
		  echo '<br /><strong>Poznámky:</strong>';
		  $backurl='symbols.php';
		  if ($usrinfo['right_power']) {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$rec['id']." AND ".DB_PREFIX."notes.idtable=7 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$rec['id']." AND ".DB_PREFIX."notes.idtable=7 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		  		$res_n=mysqli_query ($database,$sql_n);
		  		$i=0;
		  		while ($rec_n=mysqli_fetch_assoc ($res_n)) {
		  		$i++;
		  		if($i==1){ ?>
		  	<div id="poznamky"><?php
		  			}
		  			if($i>1){?>
		  		<?php
		  			} ?>
		  		<div class="poznamka">
		  			<h4><?php echo(StripSlashes($rec_n['title'])).' - '.(StripSlashes($rec_n['user']));?><?php
		  			if ($rec_n['secret']==0) echo ' (veřejná)';
		  			if ($rec_n['secret']==1) echo ' (tajná)';
		  			if ($rec_n['secret']==2) echo ' (soukromá)';
		  			?></h4>
		  			<div><?php echo(StripSlashes($rec_n['note'])); ?></div>
		  		</div>
		  		<!-- end of .poznamka -->
		  			<?php }
		  		if($i<>0){ ?>
		  	</div>
		  	<!-- end of #poznamky -->
		  	<?php }
		  	// konec poznámek
		  
		  
		  
		  echo '</td>
		  <td>';
		  // generování seznamu přiřazených případů
		  if ($usrinfo['right_power']) {
		  	$sql_s="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 ORDER BY ".DB_PREFIX."cases.title ASC";
		  } else {
		  	$sql_s="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".DB_PREFIX."cases.title ASC";
		  }
		  $pers=mysqli_query ($database,$sql_s);
		  	
		  $i=0;
		  while ($perc=mysqli_fetch_assoc ($pers)) {
		  	$i++;
		  				if($i==1){ ?>
		  		<strong>Případy:</strong>
		  		<ul id=""><?php
		  				}
		  				 ?>
		  			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		  		<?php }
		  			if($i<>0){ ?>
		  		</ul>
		  		<!-- end of # -->
		  		<?php 
		  			}else{?>
		  		<em>Symbol nebyl přiřazen žádnému případu.</em><br /><?php
		  			}
		  // konec seznamu přiřazených případů
		// generování seznamu přiřazených hlášení
			if ($usrinfo['right_power']) {
				$sql_s="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=4 ORDER BY ".DB_PREFIX."reports.label ASC";
			} else {
				$sql_s="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."reports.secret=0 ORDER BY ".DB_PREFIX."reports.label ASC";
			}
			$pers=mysqli_query ($database,$sql_s);
			 
			$i=0;
			while ($perc=mysqli_fetch_assoc ($pers)) {
				$i++;
				if($i==1){ ?>
		  		<strong>Hlášení:</strong>
		  		<ul id=""><?php
				}
				 ?>
		  		<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		  		<?php }
		  		if($i<>0){ ?>
		  		</ul>
		  		<!-- end of # -->
		  	    <?php 
		   			}else{?>
		   		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
		   			}
		   	// konec seznamu přiřazených případů
		   	?>
		   	

			<?php		   			
		  echo '</td>
'.(($usrinfo['right_text'])?'	<td><a href="addsy2p.php?rid='.$rec['id'].'">přiřadit</a> <a href="editsymbol.php?rid='.$rec['id'].'">upravit</a> <a href="procother.php?sdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat tento symbol?');".'">smazat</a></td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>