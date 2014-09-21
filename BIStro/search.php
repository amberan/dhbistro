<?php
require_once ('./inc/func_main.php');
$searchedfor="léčitelka";
/* Vyměnit "Studijní" za $_GET["search"] */
$search = mysql_real_escape_string($searchedfor);

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
		while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr><td>'.$rec['id'].'</td>
	<td>'.StripSlashes($rec['title']).'</td>
        </tr>';
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
		while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr><td>'.$rec['id'].'</td>
	<td>'.StripSlashes($rec['label']).'</td>
        </tr>';
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
		while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr><td>'.$rec['id'].'</td>
	<td>'.StripSlashes($rec['surname']).' '.StripSlashes($rec['name']).'</td>
        </tr>';
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
		while ($rec=MySQL_Fetch_Assoc($res)) {
                echo '<tr><td>'.$rec['id'].'</td>
	<td>'.StripSlashes($rec['title']).'</td>
        </tr>';
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
                            }
                            break;
                        default :
                                $noteid = $rec['id'];
                                $notetitle = $rec['title'];
                                $type = "Jiná";
                            break;
                    }

                echo '<tr><td>'.$noteid.'</td>
	<td>'.StripSlashes($notetitle).'</td>
        <td>'.StripSlashes($type).'</td>
        </tr>';
		}
	  echo '</tbody>
</table>'; 
          
?>