<?php
	require_once ('./inc/func_main.php');
	auditTrail(7, 1, 0);
	pageStart ('Symboly');
	mainMenu (5);
	deleteUnread (7,'none');
	sparklets ('<a href="persons.php">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>','<a href="newsymbol.php">nový symbol</a>');
	
	// symbolu
	$sql="SELECT * FROM ".DB_PREFIX."symbols WHERE ".DB_PREFIX."symbols.deleted=0 ORDER BY ".DB_PREFIX."symbols.created DESC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
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
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.((searchRecord(7,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">
		  <td><img src="getportrait.php?nrid='.$rec['id'].'" alt="symbol chybí" /></td>
		  <td>'.(StripSlashes($rec['desc'])).'</td>
		  <td>';
		  // generování seznamu přiřazených případů
		  if ($usrinfo['right_power']) {
		  	$sql_s="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 ORDER BY ".DB_PREFIX."cases.title ASC";
		  } else {
		  	$sql_s="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".DB_PREFIX."cases.title ASC";
		  }
		  $pers=MySQL_Query ($sql_s);
		  	
		  $i=0;
		  while ($perc=MySQL_Fetch_Assoc($pers)) {
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
			$pers=MySQL_Query ($sql_s);
			 
			$i=0;
			while ($perc=MySQL_Fetch_Assoc($pers)) {
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
		  echo '</td>
'.(($usrinfo['right_text'])?'	<td><a href="editsymbol.php?rid='.$rec['id'].'">upravit</a> | <a href="procother.php?sdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat tento symbol?".'">smazat</a> | přiřadit k osobě</td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	pageEnd ();
?>