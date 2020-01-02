<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Vyhledávání';
  
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

auditTrail(12, 1, 0);
mainMenu (3);
$custom_Filter = custom_Filter(13);
sparklets ('<strong>vyhledávání</strong>','<a href="symbol_search.php">vyhledat symbol</a>');

// default SQL filters
$searchSecret = " AND 'secret'=0 ";
$searchDeleted = " AND 'deleted'=0 ";
if ($usrinfo['right_power'] > 0) { //utajeni overload
    $searchSecret = " AND 'secret'<=".$usrinfo['right_power']." ";
}
$searchContitions = $searchSecret.$searchDeleted;



//Zpracování filtru
if (!isset($custom_Filter['farchiv'])) {
    $farchiv = 0;
} else {
    $farchiv = 1;
}
/* Prevzit vyhledavane */
if (!isset($custom_Filter['search'])) {
    $searchedfor = NULL;
} elseif ($_POST['search']) {
    $searchedfor = $_POST['search'];
} else {
    $searchedfor = $custom_Filter['search'];
}

	//$search = $searchedfor;
		
	function filter ()
	{
	    global $farchiv,$searchedfor;
	    echo '<div id="filter-wrapper"><form action="search.php" method="get" id="filter">
	<fieldset>
	  <legend>Vyhledávání</legend>
	  <p>Zadejte vyhledávaný výraz.<br />
<input type="text" name="search" value="'.$searchedfor.'" />';
	    echo '
          <table class="filter"><tr>
          <td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv == 1) ? ' checked="checked"' : '').'> Zobrazit i archiv (uzavřené případy, archivovaná hlášení, mrtvé a archivované osoby).</td>
          </table>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Vyhledat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();

/* v pripade prazdneho vyhledavani nezobrazi vysledek */
        if (is_null($searchedfor)) {
            /* nothing to do */
        } elseif (mb_strlen($searchedfor) < 3) {
            /* v pripade vyrazu kratsiho nez 4 znaky zobrazi chybovou hlasku a preskoci zobrazeni vysledku */

            echo '<h2>Výraz "'.$searchedfor.'" je příliš krátký, zadejte výraz o délce alespoň 3 znaky.</h2>';
        } else {
            ?>


<div id="obsah">
    <h2>Výsledky hledání výrazu "<?php echo $searchedfor; ?>"</h2>

    <?php

$searchedfor = nocs($searchedfor);


            /* Případy */
            if ($farchiv == 0) {
                $fsql_archiv = ' AND '.DB_PREFIX.'case.status=0 ';
            } else {
                $fsql_archiv = '';
            }
            $sql = "
	SELECT ".DB_PREFIX."case.datum as date_changed, ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret'
    FROM ".DB_PREFIX."case
	WHERE (title LIKE '%$searchedfor%' or contents LIKE  '%$searchedfor%')
    ".$fsql_archiv.$searchContitions."
	ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor') + MATCH(contents) AGAINST ('$searchedfor') DESC";
            $res = mysqli_query ($database,$sql); ?>
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
		$even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
	<td>'.webdate($rec['date_changed']).'</td>
	<td>'.(($rec['status'] == 0) ? 'Otevřený' : 'Uzavřený').''.(($rec['secret'] == 1) ? ', Tajný' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';
          
            /* Hlášení */
            if ($farchiv == 0) {
                $fsql_archiv = ' AND '.DB_PREFIX.'report.status<>3 ';
            } else {
                $fsql_archiv = '';
            }
            $sql = "
    SELECT ".DB_PREFIX."report.adatum as date_created, ".DB_PREFIX."report.datum as date_changed,  ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.status AS 'status', ".DB_PREFIX."report.secret AS 'secret'
    FROM ".DB_PREFIX."report
	WHERE (label LIKE '%$searchedfor%' or task LIKE  '%$searchedfor%' or summary LIKE  '%$searchedfor%' or impacts LIKE  '%$searchedfor%' or details LIKE  '%$searchedfor%')".$searchContitions.$fsql_archiv."
    ORDER BY 5 * MATCH(label) AGAINST ('$searchedfor')
    + 3 * MATCH(summary) AGAINST ('$searchedfor')
    + 2 * MATCH(task) AGAINST ('$searchedfor')
    + 2 * MATCH(impacts) AGAINST ('$searchedfor')
	+ MATCH(details) AGAINST ('$searchedfor') DESC";
            $res = mysqli_query ($database,$sql); ?>
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
		$even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td><a href="readactrep.php?rid='.$rec['id'].'&amp;hidenotes=0&amp;truenames=0">'.StripSlashes($rec['label']).'</a></td>
		<td>'.webdate($rec['date_created']).'</td>
		<td>'.webdate($rec['date_changed']).'</td>
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
                if ($rec['secret'] == 1) {
                    echo ', Tajné';
                }
                echo '</td></tr>';
		
                $even++;
            }
            echo '</tbody>
</table>';
          
            /* Osoby */
            if ($farchiv == 0) {
                $fsql_archiv = ' AND '.DB_PREFIX.'person.archiv=0  AND '.DB_PREFIX.'person.dead=0';
            } else {
                $fsql_archiv = '';
            }
            $sql = "
        SELECT ".DB_PREFIX."person.regdate as date_created, ".DB_PREFIX."person.datum as date_changed, ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.archiv AS 'archiv', ".DB_PREFIX."person.dead AS 'dead', ".DB_PREFIX."person.secret AS 'secret'
        FROM ".DB_PREFIX."person
		WHERE (surname LIKE '%$searchedfor%' or name LIKE  '%$searchedfor%' or contents LIKE  '%$searchedfor%')
        ".$searchContitions.$fsql_archiv."
        ORDER BY 5 * MATCH(surname)   AGAINST ('+(>$searchedfor)' IN BOOLEAN MODE) 
        + 3 * MATCH(name) AGAINST ('$searchedfor')
        + MATCH(contents) AGAINST ('$searchedfor') DESC
	";
            $res = mysqli_query ($database,$sql); ?>
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
                $even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['surname']).' '.StripSlashes($rec['name']).'</a></td>
	<td>'.webdate($rec['date_created']).'</td>
	<td>'.webdate($rec['date_changed']).'</td>

	<td>'.(($rec['archiv'] == 1) ? 'Archivovaný' : 'Aktivní').''.(($rec['dead'] == 1) ? ', Mrtvý' : '').''.(($rec['secret'] == 1) ? ', Tajný' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Skupiny */
            //TODO skupiny nemaji timestamp pro vytvoreni
            if ($farchiv == 0) {
                $fsql_archiv = ' AND '.DB_PREFIX.'group.archived=0 ';
            } else {
                $fsql_archiv = '';
            }
            $sql = "
    SELECT  ".DB_PREFIX."group.datum as date_changed, ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.archived AS 'archived'
    FROM ".DB_PREFIX."group
	WHERE (title LIKE '%$searchedfor%' or contents LIKE  '%$searchedfor%')
    ".$searchContitions.$fsql_archiv."
    ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor')
    + MATCH(contents) AGAINST ('$searchedfor') DESC
    ";
            $res = mysqli_query ($database,$sql); ?>
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
		$even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></td>
	<td>'.webdate($rec['date_changed']).'</td>

	<td>'.(($rec['secret'] == 1) ? 'Tajná' : '').''.(($rec['archived'] == 1) ? ' Archivovaná' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Symboly */
            /* Není tu ošetřené, aby to nevyhazovalo symboly od tajných osob. Nutno v budoucnu ošetřit. */
            $sql = "SELECT ".DB_PREFIX."symbol.created as date_created, ".DB_PREFIX."symbol.modified as date_changed,  ".DB_PREFIX."symbol.id AS 'id', ".DB_PREFIX."symbol.assigned AS 'assigned', ".DB_PREFIX."symbol.secret AS 'secret'
		FROM ".DB_PREFIX."symbol
		WHERE (".DB_PREFIX."symbol.desc LIKE '%$searchedfor%')
        ".$searchContitions."
        ORDER BY 5 * MATCH(`desc`) AGAINST ('$searchedfor') DESC
    ";
            $res = mysqli_query ($database,$sql); ?>
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
		$even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td><a href="readsymbol.php?rid='.$rec['id'].'&amp;hidenotes=0">'.($rec['id']).'</a></td>
	<td>'.webdate($rec['date_created']).'</td>
	<td>'.webdate($rec['date_changed']).'</td>

	<td>'.(($rec['assigned'] == 1) ? 'Přiřazený' : 'Nepřiřazený').''.(($rec['secret'] == 1) ? ', Tajný' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';
          
            /* Poznámky */
            /* POZOR, tady bude hrozny opich udelat ten join pro zobrazeni jen poznamek k nearchivovanym vecem */
            $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.id AS 'id', ".DB_PREFIX."note.idtable AS 'idtable', ".DB_PREFIX."note.iditem AS 'iditem', ".DB_PREFIX."note.secret AS 'secret'
		FROM ".DB_PREFIX."note
		WHERE (title LIKE '%$searchedfor%' or note LIKE '%$searchedfor%')		
		".$searchContitions."
		ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor')
        + MATCH(note) AGAINST ('$searchedfor') DESC
    ";

            $res = mysqli_query ($database,$sql); ?>
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
		
                    $even = 0;
            while ($rec = mysqli_fetch_assoc ($res)) {
                switch ($rec['idtable']) {
                        case 1:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.secret AS 'secret'
                                FROM ".DB_PREFIX."person
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note = mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['surname']." ".$rec_note['name'];
                                $type = "Osoba";
                                $linktype = "readperson.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 2:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.secret AS 'secret'
                                FROM ".DB_PREFIX."group
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note = mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Skupina";
                                $linktype = "readgroup.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 3:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.secret AS 'secret'
                                FROM ".DB_PREFIX."case
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note = mysqli_fetch_assoc ($res_note)) {
                                $noteid = $rec_note['id'];
                                $notetitle = $rec_note['title'];
                                $type = "Případ";
                                $linktype = "readcase.php?rid=".$rec_note['id']."&amp;hidenotes=0";
                                $secret = $rec_note['secret'];
                            }
                            break;
                        case 4:
                            $res_note = mysqli_query ($database,"
                                SELECT ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.secret AS 'secret'
                                FROM ".DB_PREFIX."report
                                WHERE id = ".$rec['iditem']);
                            while ($rec_note = mysqli_fetch_assoc ($res_note)) {
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
                //TODO chybi zobrazovani priznaku utajeni VSUDE krom poznamek
                if ($usrinfo['right_power']) {
                    echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
                <td><a href="readnote.php?rid='.$rec['id'].'&idtable='.$rec['idtable'].'">'.StripSlashes($rec['title']).'</a></td>
                <td><a href="'.$linktype.'">'.StripSlashes($notetitle).'</a></td>
				<td>'.StripSlashes($type).'</td>
				<td>'.webdate($rec['date_created']).'</td>
                <td>'.(($rec['secret'] == 1) ? 'Tajná' : '').'</td>
                </tr>';
		
                    $even++;
                } else {
                    if ($secret == 0) {
                        echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
                <td><a href="readnote.php?rid='.$rec['id'].'&idtable='.$rec['idtable'].'">'.StripSlashes($rec['title']).'</a></td>
                <td><a href="'.$linktype.'">'.StripSlashes($notetitle).'</a></td>
				<td>'.StripSlashes($type).'</td>
				<td>'.webdate($rec['date_created']).'</td>
                <td>'.(($rec['secret'] == 1) ? 'Tajná' : '').'</td>
                </tr>';
		
                        $even++;
                    }
                }
            }
            echo '</tbody>
</table>';
        }
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>
