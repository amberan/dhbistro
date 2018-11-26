<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
auditTrail(12, 1, 0);
pageStart ('Vyhledávání');
mainMenu (3);
$custom_Filter = custom_Filter(13);
sparklets ('<strong>vyhledávání</strong>','<a href="symbol_search.php">vyhledat symbol</a>');
//Zpracování filtru
if (!isset($custom_Filter['farchiv'])) {
	$farchiv=0;
} else {
	$farchiv=1;
}
/* Prevzit vyhledavane */
if (!isset($custom_Filter['search'])) {
	  $searchedfor=NULL;
	} else {
	  $searchedfor=$custom_Filter['search'];
	}

$search = $searchedfor;
?>


<?php
	function filter () {
	  global $database,$usrinfo, $farchiv;
	  echo '<div id="filter-wrapper"><form action="search.php" method="get" id="filter">
	<fieldset>
	  <legend>Vyhledávání</legend>
	  <p>Zadejte vyhledávaný výraz.<br />
<input type="text" name="search" value="" />';
	echo '
          <table class="filter">
          <td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv==1)?' checked="checked"':'').'> Zobrazit i archiv (uzavřené případy, archivovaná hlášení, mrtvé a archivované osoby).</td>
          </table>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Vyhledat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
        }
	filter();

/* v pripade prazdneho vyhledavani nezobrazi vysledek */        
        if (is_null($searchedfor)) {
/* nothing to do */
		} elseif (strlen($searchedfor) < 3) {
/* v pripade vyrazu kratsiho nez 4 znaky zobrazi chybovou hlasku a preskoci zobrazeni vysledku */

    echo '<h2>Výraz "'.$searchedfor.'" je příliš krátký, zadejte výraz o délce alespoň 3 znaky.</h2>';
    
} else {
        
?>       


<div id="obsah">
<h2>Výsledky hledání výrazu "<?php echo $searchedfor; ?>"</h2>

<?php

/* Případy */
if ($farchiv==0) {
    $fsql_archiv=' AND '.DB_PREFIX.'cases.status=0 ';
} else {
    $fsql_archiv='';
}
if ($usrinfo['right_power']) {
    $sql = "
        SELECT ".DB_PREFIX."cases.datum as date_changed, ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret'
        FROM ".DB_PREFIX."cases
        WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."cases.deleted=0".$fsql_archiv."
        ORDER BY 5 * MATCH(title) AGAINST ('$search') + MATCH(contents) AGAINST ('$search') DESC
    ";
    $res = mysqli_query ($database,$sql);
} else {
    $sql = "
        SELECT ".DB_PREFIX."cases.datum as date_changed, ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret'
        FROM ".DB_PREFIX."cases
        WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."cases.deleted=0 AND ".DB_PREFIX."cases.secret=0".$fsql_archiv."
        ORDER BY 5 * MATCH(title) AGAINST ('$search') + MATCH(contents) AGAINST ('$search') DESC
    ";
    $res = mysqli_query ($database,$sql);
}

?>
<h3>Případy</h3>
<table>
<thead>
	<tr>
	  <th width="50%">Název</th>
	  <th width="15%">Změněno</th>
	  <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
	<td>'.(Date ('d. m. Y',$rec['date_changed'])).'</td>
	<td>'.(($rec['status']==0)?'Otevřený':'Uzavřený').''.(($rec['secret']==1)?', Tajný':'').'</td>
        </tr>';
                $even++;
                }
	  echo '</tbody>
</table>';
          
/* Hlášení */
if ($farchiv==0) {
    $fsql_archiv=' AND '.DB_PREFIX.'reports.status<>3 ';
} else {
    $fsql_archiv='';
}          
if ($usrinfo['right_power']) {          
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."reports.adatum as date_created, ".DB_PREFIX."reports.datum as date_changed,  ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.status AS 'status', ".DB_PREFIX."reports.secret AS 'secret'
        FROM ".DB_PREFIX."reports
        WHERE MATCH(label, task, summary, impacts, details) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."reports.deleted=0".$fsql_archiv."
        ORDER BY 5 * MATCH(label) AGAINST ('$search')
        + 3 * MATCH(summary) AGAINST ('$search')
        + 2 * MATCH(task) AGAINST ('$search')
        + 2 * MATCH(impacts) AGAINST ('$search')
        + MATCH(details) AGAINST ('$search') DESC
    ");
} else {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."reports.adatum as date_created, ".DB_PREFIX."reports.datum as date_changed,  ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.status AS 'status', ".DB_PREFIX."reports.secret AS 'secret'
        FROM ".DB_PREFIX."reports
        WHERE MATCH(label, task, summary, impacts, details) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret=0".$fsql_archiv."
        ORDER BY 5 * MATCH(label) AGAINST ('$search')
        + 3 * MATCH(summary) AGAINST ('$search')
        + 2 * MATCH(task) AGAINST ('$search')
        + 2 * MATCH(impacts) AGAINST ('$search')
        + MATCH(details) AGAINST ('$search') DESC
    ");    
}    
?>
<h3>Hlášení</h3>
<table>
<thead>
	<tr>
	  <th width="50%">Název</th>
	  <th width="15%">Vytvořeno</th>
	  <th width="15%">Změněno</th>
	  <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td><a href="readactrep.php?rid='.$rec['id'].'&amp;hidenotes=0&amp;truenames=0">'.StripSlashes($rec['label']).'</a></td>
		<td>'.(Date ('d. m. Y',$rec['date_created'])).'</td>
		<td>'.(Date ('d. m. Y',$rec['date_changed'])).'</td>
        <td>';
        switch ($rec['status']) {
                case 0:
                    echo 'Rozpracované';
                    break;
                case 1:
                    echo 'Dokončené';
                    break;
                case 2:
                    echo 'Analyzované';
                    break;
                case 3:
                    echo 'Archivované';
        }
        if ($rec['secret']==1) {
                echo ', Tajné';
        }
        echo '</td></tr>';
		
                $even++;
                }
	  echo '</tbody>
</table>';
          
/* Osoby */
if ($farchiv==0) {
    $fsql_archiv=' AND '.DB_PREFIX.'persons.archiv=0  AND '.DB_PREFIX.'persons.dead=0';
} else {
    $fsql_archiv='';
} 
if ($usrinfo['right_power']) {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."persons.regdate as date_created, ".DB_PREFIX."persons.datum as date_changed, ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.archiv AS 'archiv', ".DB_PREFIX."persons.dead AS 'dead', ".DB_PREFIX."persons.secret AS 'secret'
        FROM ".DB_PREFIX."persons
        WHERE MATCH(surname, name, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."persons.deleted=0".$fsql_archiv."
        ORDER BY 5 * MATCH(surname) AGAINST ('$search')
        + 3 * MATCH(name) AGAINST ('$search')
        + MATCH(contents) AGAINST ('$search') DESC
    ");
} else {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."persons.regdate as date_created, ".DB_PREFIX."persons.datum as date_changed, ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.archiv AS 'archiv', ".DB_PREFIX."persons.dead AS 'dead', ".DB_PREFIX."persons.secret AS 'secret'
        FROM ".DB_PREFIX."persons
        WHERE MATCH(surname, name, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0".$fsql_archiv."
        ORDER BY 5 * MATCH(surname) AGAINST ('$search')
        + 3 * MATCH(name) AGAINST ('$search')
        + MATCH(contents) AGAINST ('$search') DESC
    ");    
}
?>
<h3>Osoby</h3>
<table>
<thead>
	<tr>
	  <th width="50%">Jméno</th>
	  <th width="15%">Vytvořeno</th>
	  <th width="15%">Změněno</th>
	  <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
                $even=0;
                while ($rec=mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['surname']).' '.StripSlashes($rec['name']).'</a></td>
	<td>'.(Date ('d. m. Y',$rec['date_created'])).'</td>
	<td>'.(Date ('d. m. Y',$rec['date_changed'])).'</td>

	<td>'.(($rec['archiv']==1)?'Archivovaný':'Aktivní').''.(($rec['dead']==1)?', Mrtvý':'').''.(($rec['secret']==1)?', Tajný':'').'</td>
        </tr>';
		$even++;
                }
	  echo '</tbody>
</table>';          

/* Skupiny */
if ($farchiv==0) {
    $fsql_archiv=' AND '.DB_PREFIX.'groups.archived=0 ';
} else {
    $fsql_archiv='';
} 
if ($usrinfo['right_power']) {
    $res = mysqli_query ($database,"
        SELECT  ".DB_PREFIX."groups.datum as date_changed, ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.archived AS 'archived'
        FROM ".DB_PREFIX."groups
        WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."groups.deleted=0".$fsql_archiv."
        ORDER BY 5 * MATCH(title) AGAINST ('$search')
        + MATCH(contents) AGAINST ('$search') DESC
    ");
} else {
    $res = mysqli_query ($database,"
        SELECT  ".DB_PREFIX."groups.datum as date_changed, ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.archived AS 'archived'
        FROM ".DB_PREFIX."groups
        WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."groups.deleted=0 AND ".DB_PREFIX."groups.secret=0".$fsql_archiv."
        ORDER BY 5 * MATCH(title) AGAINST ('$search')
        + MATCH(contents) AGAINST ('$search') DESC
    ");
}
?>
<h3>Skupiny</h3>
<table>
<thead>
	<tr>
	  <th width="50%">Název</th>
	  <th width="15%">Změněno</th>
	  <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
	<td>'.(Date ('d. m. Y',$rec['date_changed'])).'</td>

	<td>'.(($rec['secret']==1)?'Tajná':'').''.(($rec['archived']==1)?' Archivovaná':'').'</td>
        </tr>';
		$even++;
                }
	  echo '</tbody>
</table>'; 

/* Symboly */
/* Není tu ošetřené, aby to nevyhazovalo symboly od tajných osob. Nutno v budoucnu ošetřit. */          
if ($usrinfo['right_power']) {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."symbols.created as date_created, ".DB_PREFIX."symbols.modified as date_changed,  ".DB_PREFIX."symbols.id AS 'id', ".DB_PREFIX."symbols.assigned AS 'assigned', ".DB_PREFIX."symbols.secret AS 'secret'
        FROM ".DB_PREFIX."symbols
        WHERE MATCH(`desc`) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."symbols.deleted=0
        ORDER BY 5 * MATCH(`desc`) AGAINST ('$search') DESC
    ");
} else {
    $res = mysqli_query ($database,"
        SELECT  ".DB_PREFIX."symbols.created as date_created, ".DB_PREFIX."symbols.modified as date_changed, ".DB_PREFIX."symbols.id AS 'id', ".DB_PREFIX."symbols.assigned AS 'assigned', ".DB_PREFIX."symbols.secret AS 'secret'
        FROM ".DB_PREFIX."symbols
        WHERE MATCH(`desc`) AGAINST ('$search' IN BOOLEAN MODE)
        AND ".DB_PREFIX."symbols.deleted=0 AND ".DB_PREFIX."symbols.secret=0
        ORDER BY 5 * MATCH(`desc`) AGAINST ('$search') DESC
    ");
}
?>
<h3>Symboly</h3>
<table>
<thead>
	<tr>
	  <th width="50%">ID</th>
	  <th width="15%">Vytvořeno</th>
	  <th width="15%">Změněno</th>
	  <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td><a href="readsymbol.php?rid='.$rec['id'].'&amp;hidenotes=0">'.($rec['id']).'</a></td>
	<td>'.(Date ('d. m. Y',$rec['date_created'])).'</td>
	<td>'.(Date ('d. m. Y',$rec['date_changed'])).'</td>

	<td>'.(($rec['assigned']==1)?'Přiřazený':'Nepřiřazený').''.(($rec['secret']==1)?', Tajný':'').'</td>
        </tr>';
		$even++;
                }
	  echo '</tbody>
</table>'; 
          
/* Poznámky */
/* POZOR, tady bude hrozny opich udelat ten join pro zobrazeni jen poznamek k nearchivovanym vecem */
if ($usrinfo['right_power']) {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."notes.datum as date_created, ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id', ".DB_PREFIX."notes.idtable AS 'idtable', ".DB_PREFIX."notes.iditem AS 'iditem', ".DB_PREFIX."notes.secret AS 'secret'
        FROM ".DB_PREFIX."notes
        WHERE MATCH(title, note) AGAINST ('$search' IN BOOLEAN MODE) AND ".DB_PREFIX."notes.secret<2
        ORDER BY 5 * MATCH(title) AGAINST ('$search')
        + MATCH(note) AGAINST ('$search') DESC
    ");
} else {
    $res = mysqli_query ($database,"
        SELECT ".DB_PREFIX."notes.datum as date_created, ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id', ".DB_PREFIX."notes.idtable AS 'idtable', ".DB_PREFIX."notes.iditem AS 'iditem', ".DB_PREFIX."notes.secret AS 'secret'
        FROM ".DB_PREFIX."notes
        WHERE MATCH(title, note) AGAINST ('$search' IN BOOLEAN MODE) AND ".DB_PREFIX."notes.secret=0
        ORDER BY 5 * MATCH(title) AGAINST ('$search')
        + MATCH(note) AGAINST ('$search') DESC
    ");
}
?>
<h3>Poznámky</h3>
<table>
<thead>
	<tr>
	  <th width="30%">Název poznámky</th>
          <th width="30%">Komentuje</th>
          <th width="15%">Typ</th>
		  <th width="15%">Vytvořeno</th>
          <th width="15%">Status</th>
	</tr>
</thead>
<tbody>

<?php
		
                    $even=0;
                    while ($rec=mysqli_fetch_assoc ($res)) {
                    switch ($rec['idtable']) {
                        case 1:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.secret AS 'secret'
                                FROM ".DB_PREFIX."persons
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['surname']." ".$rec_note['name'];
                                $type = "Osoba";
                                $linktype = "readperson.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 2:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."groups.secret AS 'secret'
                                FROM ".DB_PREFIX."groups
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Skupina";
                                $linktype = "readgroup.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 3:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.secret AS 'secret'
                                FROM ".DB_PREFIX."cases
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Případ";
                                $linktype = "readcase.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 4:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.secret AS 'secret'
                                FROM ".DB_PREFIX."reports
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['label'];
                                $type = "Hlášení";
                                $linktype = "readactrep.php?rid=".$rec_note['id']."&amp;hidenotes=0&amp;truenames=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        default :
                                $noteid = $rec['id'];
                                $notetitle = $rec['title'];
                                $type = "Jiná";
                            break;
                    }
                
        if ($usrinfo['right_power']) {        
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
                <td><a href="readnote.php?rid='.$rec['id'].'&idtable='.$rec['idtable'].'">'.StripSlashes($rec['title']).'</a></td>
                <td><a href="'.$linktype.'">'.StripSlashes($notetitle).'</a></td>
				<td>'.StripSlashes($type).'</td>
				<td>'.(Date ('d. m. Y',$rec['date_created'])).'</td>
                <td>'.(($rec['secret']==1)?'Tajná':'').'</td>
                </tr>';
		
                $even++;
        } else {
            if ($secret==0) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'">
                <td><a href="readnote.php?rid='.$rec['id'].'&idtable='.$rec['idtable'].'">'.StripSlashes($rec['title']).'</a></td>
                <td><a href="'.$linktype.'">'.StripSlashes($notetitle).'</a></td>
				<td>'.StripSlashes($type).'</td>
				<td>'.(Date ('d. m. Y',$rec['date_created'])).'</td>
                <td>'.(($rec['secret']==1)?'Tajná':'').'</td>
                </tr>';
		
                $even++;               
            }
        }
                }
	  echo '</tbody>
</table>'; 

			}
pageEnd();
?>