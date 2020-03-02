<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate(header);

$latteParameters['title'] = 'Symboly';
	auditTrail(7, 1, 0);
	mainMenu ();
	deleteUnread (7,'none');
	sparklets ('<a href="persons.php">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>','<a href="newsymbol.php">nový symbol</a>; <a href="symbol_search.php">vyhledat symbol</a>');
	
	// symbolu
	$sql = "SELECT * FROM ".DB_PREFIX."symbol WHERE ".DB_PREFIX."symbol.deleted=0 AND ".DB_PREFIX."symbol.assigned=0 ORDER BY ".DB_PREFIX."symbol.created DESC";
	$res = mysqli_query ($database,$sql);
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
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        echo '<tr class="'.((searchRecord(7,$rec['id'])) ? ' unread_record' : (($even % 2 == 0) ? 'even' : 'odd')).'">
		  <td><a href="readsymbol.php?rid='.$rec['id'].'"><img src="getportrait.php?nrid='.$rec['id'].'" alt="symbol chybí" /></a></td>
		  <td>'.(StripSlashes($rec['desc'])).'<br />';
	        // generování poznámek
	        echo '<br /><strong>Poznámky:</strong>';
	        $backurl = 'symbols.php';
	        if ($usrinfo['right_power']) {
	            $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$rec['id']." AND ".DB_PREFIX."note.idtable=7 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
	        } else {
	            $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$rec['id']." AND ".DB_PREFIX."note.idtable=7 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
	        }
	        $res_n = mysqli_query ($database,$sql_n);
	        $i = 0;
	        while ($rec_n = mysqli_fetch_assoc ($res_n)) {
	            $i++;
	            if ($i == 1) { ?>
		  	<div id="poznamky"><?php
		  			}
	            if ($i > 1) {?>
		  		<?php
		  			} ?>
		  		<div class="poznamka">
		  			<h4><?php echo StripSlashes($rec_n['title']).' - '.StripSlashes($rec_n['user']);
	            if ($rec_n['secret'] == 0) {
	                echo ' (veřejná)';
	            }
	            if ($rec_n['secret'] == 1) {
	                echo ' (tajná)';
	            }
	            if ($rec_n['secret'] == 2) {
	                echo ' (soukromá)';
	            } ?></h4>
		  			<div><?php echo StripSlashes($rec_n['note']); ?></div>
		  		</div>
		  		<!-- end of .poznamka -->
		  			<?php
	        }
	        if ($i <> 0) { ?>
		  	</div>
		  	<!-- end of #poznamky -->
		  	<?php }
	        // konec poznámek
		  
		  
		  
	        echo '</td>
		  <td>';
	        // generování seznamu přiřazených případů
	        if ($usrinfo['right_power']) {
	            $sql_s = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 ORDER BY ".DB_PREFIX."case.title ASC";
	        } else {
	            $sql_s = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."case.secret=0 ORDER BY ".DB_PREFIX."case.title ASC";
	        }
	        $pers = mysqli_query ($database,$sql_s);
		  	
	        $i = 0;
	        while ($perc = mysqli_fetch_assoc ($pers)) {
	            $i++;
	            if ($i == 1) { ?>
		  		<strong>Případy:</strong>
		  		<ul id=""><?php
		  				} ?>
		  			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		  		<?php
	        }
	        if ($i <> 0) { ?>
		  		</ul>
		  		<!-- end of # -->
		  		<?php
		  			} else {?>
		  		<em>Symbol nebyl přiřazen žádnému případu.</em><br /><?php
		  			}
	        // konec seznamu přiřazených případů
	        // generování seznamu přiřazených hlášení
	        if ($usrinfo['right_power']) {
	            $sql_s = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=4 ORDER BY ".DB_PREFIX."report.label ASC";
	        } else {
	            $sql_s = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."report.secret=0 ORDER BY ".DB_PREFIX."report.label ASC";
	        }
	        $pers = mysqli_query ($database,$sql_s);
			 
	        $i = 0;
	        while ($perc = mysqli_fetch_assoc ($pers)) {
	            $i++;
	            if ($i == 1) { ?>
		  		<strong>Hlášení:</strong>
		  		<ul id=""><?php
				} ?>
		  		<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		  		<?php
	        }
	        if ($i <> 0) { ?>
		  		</ul>
		  		<!-- end of # -->
		  	    <?php
		   			} else {?>
		   		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
		   			}
	        // konec seznamu přiřazených případů ?>
		   	

			<?php
		  echo '</td>
'.(($usrinfo['right_text']) ? '	<td><a href="addsy2p.php?rid='.$rec['id'].'">přiřadit</a> <a href="editsymbol.php?rid='.$rec['id'].'">upravit</a> <a href="procother.php?sdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat tento symbol?');".'">smazat</a></td>' : '<td><a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a>').'
</tr>';
	        $even++;
	    }
	    echo '</tbody>
</table>
</div>';
	}
	latteDrawTemplate(footer);
?>