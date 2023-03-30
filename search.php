<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;


latteDrawTemplate("header");

$latteParameters['title'] = 'Vyhledávání';
authorizedAccess(12, 1, 0);
mainMenu();
sparklets('<strong>vyhledávání</strong>', '<a href="symbol_search.php">vyhledat symbol</a>');


$sqlFilter = $sqlFilterReport = "";

//TODO SORTING created/modified/title/status/type

if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('search', $_POST['filter']);
}
$filter = filterGet('search');
if (!isset($filter['secret'])) {
    $sqlFilter .= ' AND secret = 0 AND secret <= '.$user['aclSecret'];
    $sqlFilterReport .= ' AND reportSecret = 0 AND reportSecret <= '.$user['aclSecret'];
}
if ((!isset($filter['deleted']) && $user['aclRoot']) || (!$user['aclRoot'])) {
    $sqlFilter .= ' AND deleted = 0 ';
    $sqlFilterReport .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1))';
}

//TODO search legacy overload
$searchedfor = $filter['search'];
if (isset($_GET['search'])) {
    $searchedfor = $filter['search'] = $_GET['search'];
}

$latteParameters['filter'] = $filter;
?>
<div id="filter-wrapper">
<form action="/search.php" method="post" id="filter">
<input type="hidden" name="filter[placeholder]"  />
	<fieldset>
	  <legend>Vyhledávání</legend>
	  <p>Zadejte vyhledávaný výraz.<br />
<input type="text" name="filter[search]" value="<?php  echo $filter['search']; ?>" />

<input type="checkbox" name="filter[archived]" <?php if (isset($filter['archived']) and $filter['archived'] == 'on') { echo " checked"; } ?> onchange="this.form.submit()"/>Zobrazit i archiv (uzavřené případy, archivovaná hlášení, mrtvé a archivované osoby).
<?php

if ($user['aclSecret']) {
    echo '<input type="checkbox" name="filter[secret]" ';
    if (isset($filter['secret']) and $filter['secret'] == 'on') {echo " checked ";}
    echo 'onchange="this.form.submit()"/>Zobrazit i Utajeno.';
}
if ($user['aclRoot']) {
    echo '<input type="checkbox" name="filter[deleted]" ';
    if (isset($filter['deleted']) and $filter['deleted'] == 'on') {echo " checked ";}
    echo 'onchange="this.form.submit()"/>Zobrazit i smazané.';
}
?>
<div id="filtersubmit"><input type="submit" name="filter[submit]" value="Vyhledat" onclick="this.form.submit()"/></div>
	</fieldset>
</form>
</div>

<?php
/* v pripade prazdneho vyhledavani nezobrazi vysledek */
        if ($filter['search'] === null) {
            /* nothing to do */
        } elseif (mb_strlen($filter['search']) < 3) {
            /* v pripade vyrazu kratsiho nez 4 znaky zobrazi chybovou hlasku a preskoci zobrazeni vysledku */

            echo '<h2>Výraz "'.$filter['search'].'" je příliš krátký, zadejte výraz o délce alespoň 3 znaky.</h2>';
        } else {
            ?>

<div id="obsah">
    <h2>Výsledky hledání výrazu "<?php echo $filter['search']; ?>"</h2>

    <?php
    $filter['search'] = nocs($filter['search']);
            /* Případy */
            $fsql_archiv = '';
            if ($filter['archived'] != 'on') {
                $fsql_archiv = ' AND '.DB_PREFIX.'case.status=0 ';
            }
           $sql = "
                SELECT
                    ".DB_PREFIX."case.datum as date_changed,
                    ".DB_PREFIX."case.title ,
                    ".DB_PREFIX."case.id ,
                    ".DB_PREFIX."case.status ,
                    ".DB_PREFIX."case.secret ,
                    ".DB_PREFIX."case.deleted ,
                    ".DB_PREFIX."case.caseCreated
                FROM ".DB_PREFIX."case
                WHERE 1 ".@$sqlFilter."
                    AND (title LIKE '%$searchedfor%' or contents LIKE  '%$searchedfor%')
                    ".$fsql_archiv."
                ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor') + MATCH(contents) AGAINST ('$searchedfor') DESC";
            $res = mysqli_query($database, $sql);
            $caseCount = mysqli_num_rows($res);
            ?>
    <h3>Případy (<?php echo $caseCount; ?>)</h3>
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
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>';
    if ($rec['deleted']) { echo stripslashes($rec['title']); } else { echo '<a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.stripslashes($rec['title']).'</a>'; } echo '</td>
    <td>'.$rec['caseCreated'].'</td>
    <td>'.webdate($rec['date_changed']).'</td>
	<td>'.($rec['status'] === 0 ? 'Otevřený' : 'Uzavřený').($rec['secret'] > 0 ? ', Utajeno ['.$rec['secret'].']' : '').($rec['deleted'] > 0 ? ', Smazané' : '').($rec['status'] > 0 ? ', Archiv ' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Hlášení */
            $fsql_archiv = '';
            if ($filter['archived'] != 'on') {
                $fsql_archiv = ' AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1)) ';
            }
         $sql = "
                SELECT ".DB_PREFIX."report.reportCreated as date_created, ".DB_PREFIX."report.reportModified as date_changed,  ".DB_PREFIX."report.reportName , ".DB_PREFIX."report.reportId, ".DB_PREFIX."report.reportStatus,
                ".DB_PREFIX."report.reportSecret, ".DB_PREFIX."report.reportDeleted,
                 CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN 'False' ELSE 'True' END AS deleted
                FROM ".DB_PREFIX."report
                WHERE 1 ".@$sqlFilterReport." AND (reportTask LIKE  '%".$searchedfor."%' or reportDetail LIKE  '%".$searchedfor."%' or reportImpact LIKE  '%".$searchedfor."%' or reportInput LIKE  '%".$searchedfor."%'  or reportSummary LIKE  '%".$searchedfor."%' )"
                .$fsql_archiv." ORDER BY 5 * MATCH(reportInput) AGAINST ('".$searchedfor."')
                + 3 * MATCH(reportSummary) AGAINST ('".$searchedfor."')
                + 2 * MATCH(reportTask) AGAINST ('".$searchedfor."')
                + 2 * MATCH(reportImpact) AGAINST ('".$searchedfor."')
                + MATCH(reportDetail) AGAINST ('".$searchedfor."') DESC";
            $res = mysqli_query($database, $sql);
            $reportCount = mysqli_num_rows($res);
          ?>
            <h3>Hlášení  (<?php echo $reportCount; ?>)</h3>
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
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>';
    if ($rec['deleted'] == 'False') { echo '<a href="/reports/'.$rec['reportId'].'">'.stripslashes($rec['reportName']).'</a>'; } else { echo stripslashes($rec['reportName']); } echo'</td>
		<td>'.date('d.m.Y', strtotime($rec['date_created'])).'</td>
		<td>'.date('d.m.Y', strtotime($rec['date_changed'])).'</td>
        <td>';
                switch ($rec['reportStatus']) { //webdate
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
                    // no break
                default:
                ;
        }
                if ($rec['reportSecret'] > 0) {
                    echo ', Utajeno ['.$rec['reportSecret'].']';
                }
                if ($rec['reportDeleted'] > 0) {
                    echo ', Smazané ';
                }
                echo '</td></tr>';

                $even++;
            }
            echo '</tbody>
</table>';

            /* Osoby */
            $fsql_archiv = '';
            if ($filter['archived'] != 'on') {
                $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1)) ';
            }
    $sql = "
        SELECT ".DB_PREFIX."person.regdate as date_created, ".DB_PREFIX."person.datum as date_changed, ".DB_PREFIX."person.surname , ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name , ".DB_PREFIX."person.archived , ".DB_PREFIX."person.dead , ".DB_PREFIX."person.secret , ".DB_PREFIX."person.deleted
        FROM ".DB_PREFIX."person
		WHERE 1 ".@$sqlFilter." AND (surname LIKE '%$searchedfor%' or name LIKE  '%$searchedfor%' or contents LIKE  '%$searchedfor%')
        ".$fsql_archiv."
        ORDER BY 5 * MATCH(surname)   AGAINST ('+(>$searchedfor)' IN BOOLEAN MODE)
        + 3 * MATCH(name) AGAINST ('$searchedfor')
        + MATCH(contents) AGAINST ('$searchedfor') DESC
	";
            $res = mysqli_query($database, $sql);
            $personCount = mysqli_num_rows($res);
            ?>
                    <h3>Osoby   (<?php echo $personCount; ?>)</h3>
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
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>';
    if ($rec['deleted']) { echo stripslashes($rec['surname']).' '.stripslashes($rec['name']); } else { echo '<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.stripslashes($rec['surname']).' '.stripslashes($rec['name']).'</a>'; } echo '</td>
	<td>'.webdate($rec['date_created']).'</td>
	<td>'.webdate($rec['date_changed']).'</td>

    <td>'.($rec['archived'] > 1 ? 'Archivovaný' : 'Aktivní').''.($rec['dead'] == 1 ? ', Mrtvý' : '').' '.($rec['secret'] > 0 ? ', Utajeno ['.$rec['secret'].']' : '').' '.($rec['deleted'] > 0 ? ', Smazané' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Skupiny */
            //TODO skupiny nemaji timestamp pro vytvoreni
            $fsql_archiv = '';
            if ($filter['archived'] != 'on') {
                $fsql_archiv = ' AND '.DB_PREFIX.'group.archived is null';
            }
           $sql = "
    SELECT  ".DB_PREFIX."group.datum as date_changed, ".DB_PREFIX."group.title, ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.secret, ".DB_PREFIX."group.archived,".DB_PREFIX."group.deleted, ".DB_PREFIX."group.groupCreated
    FROM ".DB_PREFIX."group
	WHERE 1 ".@$sqlFilter." AND (title LIKE '%$searchedfor%' or contents LIKE  '%$searchedfor%')
    ".$fsql_archiv."
    ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor')
    + MATCH(contents) AGAINST ('$searchedfor') DESC
    ";
            $res = mysqli_query($database, $sql);
            $groupCount = mysqli_num_rows($res);
            ?>
                            <h3>Skupiny (<?php echo $groupCount; ?>)</h3>
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
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>';
    if ($rec['deleted']) { echo stripslashes($rec['title']); } else { echo '<a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.stripslashes($rec['title']).'</a>'; } echo '</td>
    <td>'.$rec['groupCreated'].'</td>
    	<td>'.webdate($rec['date_changed']).'</td>

	<td>'.($rec['secret'] > 0 ? 'Utajeno ['.$rec['secret'].'] ' : '').' '.($rec['archived'] === 1 ? ' Archivovaná' : '').' '.($rec['deleted'] > 0 ? ' Smazané' : '').($rec['archived'] > 0 ? ' archiv' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Symboly */
            /* Není tu ošetřené, aby to nevyhazovalo symboly od Utajenoch osob. Nutno v budoucnu ošetřit. */
            $sql = "SELECT ".DB_PREFIX."symbol.desc, ".DB_PREFIX."symbol.created as date_created, ".DB_PREFIX."symbol.modified as date_changed,  ".DB_PREFIX."symbol.id AS 'id', ".DB_PREFIX."symbol.assigned , ".DB_PREFIX."symbol.secret, ".DB_PREFIX."symbol.deleted
		FROM ".DB_PREFIX."symbol
		WHERE 1 ".@$sqlFilter." AND (".DB_PREFIX."symbol.desc LIKE '%$searchedfor%')
        ORDER BY 5 * MATCH(`desc`) AGAINST ('$searchedfor') DESC
    ";
            $res = mysqli_query($database, $sql);
            $symbolCount = mysqli_num_rows($res);
            ?>
                                    <h3>Symboly (<?php echo $symbolCount; ?>)</h3>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="50%">Popis</th>
                                                <th width="15%">Vytvořeno</th>
                                                <th width="15%">Změněno</th>
                                                <th width="15%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
        $even = 0;
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>';
    if ($rec['deleted']) { echo strip_tags($rec['desc']); } else { echo '<a href="readsymbol.php?rid='.$rec['id'].'&amp;hidenotes=0">'.strip_tags($rec['desc']).'</a>'; } echo '</td>
	<td>'.webdate($rec['date_created']).'</td>
	<td>'.webdate($rec['date_changed']).'</td>

	<td>'.($rec['assigned'] === 1 ? 'Přiřazený' : 'Nepřiřazený').' '.($rec['secret'] > 0 ? ', Utajeno ['.$rec['secret'].']' : '').' '.($rec['deleted'] > 0 ? ', Smazané' : '').'</td>
        </tr>';
                $even++;
            }
            echo '</tbody>
</table>';

            /* Poznámky */
            /* POZOR, tady bude hrozny opich udelat ten join pro zobrazeni jen poznamek k nearchivovanym vecem */
     $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.title , ".DB_PREFIX."note.id AS 'id', ".DB_PREFIX."note.idtable , ".DB_PREFIX."note.iditem , ".DB_PREFIX."note.secret , ".DB_PREFIX."note.deleted
		FROM ".DB_PREFIX."note
		WHERE 1 ".@$sqlFilter." AND (title LIKE '%$searchedfor%' or note LIKE '%$searchedfor%')
		ORDER BY 5 * MATCH(title) AGAINST ('$searchedfor')
        + MATCH(note) AGAINST ('$searchedfor') DESC
    ";

//! secret == 2 > private note
//AND ".DB_PREFIX."note.secret <= ".$user['aclSecret']."
            $res = mysqli_query($database, $sql);
            $noteCount = mysqli_num_rows($res);
            ?>
                                            <h3>Poznámky (<?php echo $noteCount; ?>)</h3>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Název poznámky</th>
                                                        <th width="30%">Komentuje</th>
                                                        <th width="15%">Vytvořeno</th>
                                                        <th width="15%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php

                    $even = 0;
            while ($rec = mysqli_fetch_assoc($res)) {
                 $deleted =  $secret =               $notetitle = $type = $linktype = "";
                switch ($rec['idtable']) {
                        case 1:
                            $notePersonSql = 'SELECT '.DB_PREFIX.'person.surname AS surname, '.DB_PREFIX.'person.id AS id, '.DB_PREFIX.'person.name AS name, '.DB_PREFIX.'person.secret AS secret, deleted
                                FROM '.DB_PREFIX.'person
                                WHERE '.DB_PREFIX.'person.secret <= '.$user['aclSecret'].' AND id = '.$rec['iditem'].' AND deleted <= '.$user['aclRoot'].' ORDER BY surname';
                            $notePersonQuery = mysqli_query($database, $notePersonSql);
                            while ($notePerson = mysqli_fetch_assoc($notePersonQuery)) {
                                $notetitle = $notePerson['surname']." ".$notePerson['name'];
                                if ($notePerson['secret']) {  $secret = true;}
                                if ($notePerson['deleted']) {  $deleted = true;}
                                $type = "Osoba";
                                $linktype = "readperson.php?rid=".$notePerson['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 2:
                            $noteGroupSql = "SELECT ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.secret AS 'secret', deleted
                                FROM ".DB_PREFIX."group
                                WHERE ".DB_PREFIX."group.secret <= ".$user['aclSecret']." AND id = ".$rec['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY title";
                            $noteGroupQuery = mysqli_query($database, $noteGroupSql);
                            while ($noteGroup = mysqli_fetch_assoc($noteGroupQuery)) {
                                $notetitle = $noteGroup['title'];
                                if ($noteGroup['secret']) { $secret = true;}
                                if ($noteGroup['deleted']) {  $deleted = true;}
                                $type = "Skupina";
                                $linktype = "readgroup.php?rid=".$noteGroup['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 3:
                            $noteCaseSql = "SELECT ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.secret AS 'secret', deleted
                                FROM ".DB_PREFIX."case
                                WHERE ".DB_PREFIX."case.secret <= ".$user['aclSecret']." AND id = ".$rec['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY title";
                            $noteCaseQuery = mysqli_query($database, $noteCaseSql);
                            while ($noteCase = mysqli_fetch_assoc($noteCaseQuery)) {
                                $notetitle = $noteCase['title'];
                                if ($noteCase['secret']) { $secret = true;}
                                if ($noteCase['deleted']) {  $deleted = true;}
                                $type = "Případ";
                                $linktype = "readcase.php?rid=".$noteCase['id']."&amp;hidenotes=0";
                            }
                            break;
                        case 4:
                            $noteReportSql = 'SELECT '.DB_PREFIX.'report.reportName , '.DB_PREFIX.'report.reportId, '.DB_PREFIX.'report.reportSecret AS secret,
                                    CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN "False" ELSE "True" END AS deleted
                                FROM '.DB_PREFIX.'report
                                WHERE '.DB_PREFIX.'report.reportSecret <= '.$user['aclSecret'].' AND reportId = '.$rec['iditem'].@$sqlFilterReport.'
                                ORDER BY reportName';
                            $noteReportQuery = mysqli_query($database, $noteReportSql);
                            if (mysqli_num_rows($noteReportQuery) ) {
                                $noteReport = mysqli_fetch_assoc($noteReportQuery);
                                 $notetitle = $noteReport['reportName'];
                                 if ($noteReport['secret']) { $secret = true;}
                                 if ($noteReport['deleted'] != 'False') {  $deleted = true;}
                                 $type = "Hlášení";
                                 $linktype = "/reports/".$noteReport['reportId'];
                            } else {
                                $deleted = true;
                            }
                            break;
                        case 7:
                            $noteSymbolSql = "SELECT ".DB_PREFIX."symbol.desc , ".DB_PREFIX."symbol.id, ".DB_PREFIX."symbol.secret AS 'secret', ".DB_PREFIX."symbol.deleted
                                FROM ".DB_PREFIX."symbol
                                WHERE ".DB_PREFIX."symbol.secret <= ".$user['aclSecret']." AND id = ".$rec['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY created desc";
                            $noteSymbolQuery = mysqli_query($database, $noteSymbolSql);
                            if ($noteSymbol = mysqli_fetch_assoc($noteSymbolQuery)) {
                                $notetitle = strip_tags($noteSymbol['desc']);
                                if ($noteSymbol['secret'] ) { $secret = true;}
                                if ($noteSymbol['deleted']) {  $deleted = true;}
                                $type = "Symbol";
                                $linktype = "/readsymbol.php?rid=".$noteSymbol['id'];
                            } else { $deleted = true;}
                            break;
                        default:
                                $notetitle = $rec['title'];
                                $type = "Jiná";
                            break;
                    }
 if ((!$deleted || ($deleted && isset($filter['deleted']) && $filter['deleted'] == 'on' && $user['aclRoot']))
        &&
    (!$secret || ($secret && isset($filter['secret']) && $filter['secret'] == 'on' ))) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
                <td>';
                if ($rec['deleted'] || $deleted) { echo strip_tags($rec['title']); } else { echo '<a href="readnote.php?rid='.$rec['id'].'&idtable='.$rec['idtable'].'">'.stripslashes($rec['title']).'</a>'; } echo '</td>
                <td>'.stripslashes($type).': ';
                if ($deleted) { echo stripslashes($notetitle).' <span style="color: orange; font-weight: bold; float: right; margin: 0 2 0 2;">Smazané </span>'; } else { echo '<a href="'.$linktype.'">'.stripslashes($notetitle).'</a>';}
                if ($secret) { echo ' <span style="color: red; font-weight: bold; float: right; margin: 0 2 0 2;">Utajeno </span>'; }
                echo '</td>
				<td>'.webdate($rec['date_created']).'</td>
                <td>'.($rec['secret'] > 0 ? 'Utajeno ['.$rec['secret'].']' : '').($rec['deleted'] > 0 ? ' Smazané' : '').'</td>
                </tr>';

                $even++;
            }
        }
            echo '</tbody>
</table>';
        }
latteDrawTemplate("footer");
?>
