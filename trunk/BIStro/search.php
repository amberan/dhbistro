<?php
require_once ('./inc/func_main.php');
auditTrail(3, 1, 0);
pageStart ('Vyhledávání');
mainMenu (3);
sparklets ('<strong>vyhledávání</strong>');
$searchedfor="upir";
/* Vyměnit "léčitelka" za $_GET["search"] */
$search = mysql_real_escape_string($searchedfor);
?>

<div id="obsah">
<h2>Výsledky hledání výrazu "<?php echo $searchedfor; ?>"</h2>

<?php

/* Případy */
$res = mysql_query("
    SELECT ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id'
    FROM ".DB_PREFIX."cases
    WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
    ORDER BY 5 * MATCH(title) AGAINST ('$search') + MATCH(contents) AGAINST ('$search') DESC
");
?>
<h3>Případy</h3>
<table>
<thead>
	<tr>
	  <th>ID</th>
	  <th>Název</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td>'.$rec['id'].'</td>
	<td><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
        </tr>';
                $even++;
                }
	  echo '</tbody>
</table>';
          
/* Hlášení */
$res = mysql_query("
    SELECT ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id'
    FROM ".DB_PREFIX."reports
    WHERE MATCH(label, task, summary, impacts, details) AGAINST ('$search' IN BOOLEAN MODE)
    ORDER BY 5 * MATCH(label) AGAINST ('$search')
    + 3 * MATCH(summary) AGAINST ('$search')
    + 2 * MATCH(task) AGAINST ('$search')
    + 2 * MATCH(impacts) AGAINST ('$search')
    + MATCH(details) AGAINST ('$search') DESC
");
?>
<h3>Hlášení</h3>
<table>
<thead>
	<tr>
	  <th>ID</th>
	  <th>Název</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td>'.$rec['id'].'</td>
	<td><a href="readactrep.php?rid='.$rec['id'].'&amp;hidenotes=0&amp;truenames=0">'.StripSlashes($rec['label']).'</a></td>
        </tr>';
		
                $even++;
                }
	  echo '</tbody>
</table>';
          
/* Osoby */
$res = mysql_query("
    SELECT ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name'
    FROM ".DB_PREFIX."persons
    WHERE MATCH(surname, name, contents) AGAINST ('$search' IN BOOLEAN MODE)
    ORDER BY 5 * MATCH(surname) AGAINST ('$search')
    + 3 * MATCH(name) AGAINST ('$search')
    + MATCH(contents) AGAINST ('$search') DESC
");
?>
<h3>Osoby</h3>
<table>
<thead>
	<tr>
	  <th>ID</th>
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>

<?php
                $even=0;
                while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td>'.$rec['id'].'</td>
	<td><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['surname']).' '.StripSlashes($rec['name']).'</a></td>
        </tr>';
		$even++;
                }
	  echo '</tbody>
</table>';          

/* Skupiny */
$res = mysql_query("
    SELECT ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id'
    FROM ".DB_PREFIX."groups
    WHERE MATCH(title, contents) AGAINST ('$search' IN BOOLEAN MODE)
    ORDER BY 5 * MATCH(title) AGAINST ('$search')
    + MATCH(contents) AGAINST ('$search') DESC
");
?>
<h3>Skupiny</h3>
<table>
<thead>
	<tr>
	  <th>ID</th>
	  <th>Název</th>
	</tr>
</thead>
<tbody>

<?php
		$even=0;
                while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td>'.$rec['id'].'</td>
	<td><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
        </tr>';
		$even++;
                }
	  echo '</tbody>
</table>'; 
          
/* Poznámky */
$res = mysql_query("
    SELECT ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id', ".DB_PREFIX."notes.idtable AS 'idtable', ".DB_PREFIX."notes.iditem AS 'iditem'
    FROM ".DB_PREFIX."notes
    WHERE MATCH(title, note) AGAINST ('$search' IN BOOLEAN MODE)
    ORDER BY 5 * MATCH(title) AGAINST ('$search')
    + MATCH(note) AGAINST ('$search') DESC
");
?>
<h3>Poznámky</h3>
<table>
<thead>
	<tr>
	  <th>ID</th>
	  <th>Název</th>
          <th>Typ</th>
	</tr>
</thead>
<tbody>

<?php
		
                    $even=0;
                    while ($rec=MySQL_Fetch_Assoc($res)) {
                    switch ($rec['idtable']) {
                        case 1:
                            $res_note = mysql_query("
                                SELECT ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name'
                                FROM ".DB_PREFIX."persons
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=MySQL_Fetch_Assoc($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['surname']." ".$rec_note['name'];
                                $type = "Osoba";
                                $linktype = "readperson.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 2:
                            $res_note = mysql_query("
                                SELECT ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id'
                                FROM ".DB_PREFIX."groups
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=MySQL_Fetch_Assoc($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Skupina";
                                $linktype = "readgroup.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 3:
                            $res_note = mysql_query("
                                SELECT ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id'
                                FROM ".DB_PREFIX."cases
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=MySQL_Fetch_Assoc($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Případ";
                                $linktype = "readcase.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 4:
                            $res_note = mysql_query("
                                SELECT ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id'
                                FROM ".DB_PREFIX."reports
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note=MySQL_Fetch_Assoc($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['label'];
                                $type = "Hlášení";
                                $linktype = "readactrep.php?rid=".$rec_note['id']."&amp;hidenotes=0&amp;truenames=0";
                            }
                            break;
                        default :
                                $noteid = $rec['id'];
                                $notetitle = $rec['title'];
                                $type = "Jiná";
                            break;
                    }

                echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td>'.$noteid.'</td>
	<td><a href="'.$linktype.'">'.StripSlashes($notetitle).'</a></td>
        <td>'.StripSlashes($type).'</td>
        </tr>';
		
                $even++;
                }
	  echo '</tbody>
</table>'; 
          
?>