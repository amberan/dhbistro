<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Hlášení';
    auditTrail(4, 1, 0);
    mainMenu();
        $customFilter = custom_Filter(4);
        if ($_SERVER["SERVER_NAME"] == 'enigma.dhbistro.cz') {
            sparklets('<strong>'.$text['hlaseniM'].'</strong>', '<a href="newactrep.php?type=1">nová zakázka na amulet</a>; <a href="newactrep.php?type=2">nová zakázka na informace</a>');
        } else {
            sparklets('<strong>'.$text['hlaseniM'].'</strong>', '<a href="newactrep.php?type=1">nové hlášení z výjezdu</a>; <a href="newactrep.php?type=2">nové hlášení z výslechu</a>');
        }
// zpracovani filtru
    if (!isset($customFilter['type'])) {
        $filterCat = 0;
    } else {
        $filterCat = $customFilter['type'];
    }
    if (!isset($customFilter['sort'])) {
        $filterSort = 6;
    } else {
        $filterSort = $customFilter['sort'];
    }
    if (!isset($customFilter['status'])) {
        $filterStat = 2;
    } else {
        $filterStat = $customFilter['status'];
    }
    if (!isset($customFilter['my'])) {
        $filterMine = 0;
    } else {
        $filterMine = 1;
    }
    if (!isset($customFilter['conn'])) {
        $filterConn = 0;
    } else {
        $filterConn = 1;
    }
    if (!isset($customFilter['sec'])) {
        $filterSec = 0;
    } else {
        $filterSec = 1;
    }
        if (!isset($customFilter['archiv'])) {
            $filterArchiv = 0;
        } else {
            $filterArchiv = 1;
        }
        if (!isset($customFilter['new'])) {
            $fNew = 0;
        } else {
            $fNew = 1;
            $filterUnread = ' AND '.DB_PREFIX.'unread.id is not null ';
        }
    switch ($filterCat) {
      case 0: $filterSqlCat = ''; break;
      case 1: $filterSqlCat = ' AND '.DB_PREFIX.'report.type=1 '; break;
      case 2: $filterSqlCat = ' AND '.DB_PREFIX.'report.type=2 '; break;
      default: $filterSqlCat = '';
    }
    switch ($filterSort) {
      case 1: $filterSqlSort = ' '.DB_PREFIX.'report.datum DESC '; break;
      case 2: $filterSqlSort = ' '.DB_PREFIX.'report.datum ASC '; break;
      case 3: $filterSqlSort = ' '.DB_PREFIX.'user.userName ASC '; break;
      case 4: $filterSqlSort = ' '.DB_PREFIX.'user.userName DESC '; break;
      case 5: $filterSqlSort = ' '.DB_PREFIX.'report.adatum ASC, '.DB_PREFIX.'report.start ASC '; break;
      case 6: $filterSqlSort = ' '.DB_PREFIX.'report.adatum DESC, '.DB_PREFIX.'report.start DESC '; break;
      default: $filterSqlSort = ' '.DB_PREFIX.'report.adatum DESC ';
    }
    switch ($filterStat) {
        case 0: $fsql_stat = ''; break;
        case 1: $fsql_stat = ' AND '.DB_PREFIX.'report.status=0 '; break;
        case 2: $fsql_stat = ' AND '.DB_PREFIX.'report.status=1 '; break;
        case 3: $fsql_stat = ' AND '.DB_PREFIX.'report.status=2 '; break;
        default: $fsql_stat = ' AND '.DB_PREFIX.'report.status=1 ';
    }
    switch ($filterMine) {
        case 0: $filterSqlMine = ''; break;
        case 1: $filterSqlMine = ' AND '.DB_PREFIX.'report.iduser='.$user['userId'].' '; break;
        default: $filterSqlMine = '';
    }
    switch ($filterConn) {
        case 0: $fsql_conn = ''; $fsql_conn2 = ''; break;
        case 1: $fsql_conn = ' AND '.DB_PREFIX.'ar2c.idreport IS NULL '; $fsql_conn2 = ' LEFT JOIN '.DB_PREFIX.'ar2c ON '.DB_PREFIX.'report.id='.DB_PREFIX.'ar2c.idreport '; break;
        default: $fsql_conn = ''; $fsql_conn2 = '';
    }
    switch ($filterSec) {
        case 0: $fsql_sec = ''; break;
        case 1: $fsql_sec = ' AND '.DB_PREFIX.'report.secret>0 '; break;
        default: $fsql_sec = '';
    }
        switch ($filterArchiv) {
        case 0: $fsql_archiv = ' AND '.DB_PREFIX.'report.status<>3 '; break;
        case 1: $fsql_archiv = ''; break;
        default: $fsql_archiv = '';
    }
    // filtr samotny
    function filter(): void
    {
        global $filterCat, $user, $filterStat, $filterMine, $filterConn, $filterSec, $fNew, $filterArchiv, $user, $text, $filterSort;
        echo '<div id="filter-wrapper"><form action="reports.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="status">
	<option value="0"'.($filterStat == 0 ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.($filterStat == 1 ? ' selected="selected"' : '').'>rozpracovaná</option>
	<option value="2"'.($filterStat == 2 ? ' selected="selected"' : '').'>dokončená</option>
	<option value="3"'.($filterStat == 3 ? ' selected="selected"' : '').'>analyzovaná</option>
</select> '.$text['hlaseniM'].' <select name="type">
	<option value="0"'.($filterCat == 0 ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.($filterCat == 1 ? ' selected="selected"' : '').'>z výjezdu</option>
	<option value="2"'.($filterCat == 2 ? ' selected="selected"' : '').'>z výslechu</option>
</select> a seřadit je podle <select name="sort" >
	<option value="1"'.($filterSort == 1 ? ' selected="selected"' : '').'>data '.$text['hlaseniM'].' sestupně</option>
	<option value="2"'.($filterSort == 2 ? ' selected="selected"' : '').'>data '.$text['hlaseniM'].' vzestupně</option>
	<option value="3"'.($filterSort == 3 ? ' selected="selected"' : '').'>jména autora vzestupně</option>
	<option value="4"'.($filterSort == 4 ? ' selected="selected"' : '').'>jména autora sestupně</option>
	<option value="5"'.($filterSort == 5 ? ' selected="selected"' : '').'>data výjezdu vzestupně</option>
	<option value="6"'.($filterSort != 6 ? '' : ' selected="selected"').'>data výjezdu sestupně</option>
</select>.</p>
        <table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="my" value="my" class="checkbox"'.($filterMine == 1 ? ' checked="checked"' : '').' /> Jen moje.</td>
        <td class="filter"><input type="checkbox" name="new" value="new" class="checkbox"'.($fNew == 1 ? ' checked="checked"' : '').' /> Jen nové.</td>
	<td class="filter"><input type="checkbox" name="conn" value="conn" class="checkbox"'.($filterConn == 1 ? ' checked="checked"' : '').' /> Jen nepřiřazené.</td>
        </tr>
        <tr class="filter">
        <td class="filter"><input type="checkbox" name="archiv" value="archiv" class="checkbox"'.($filterArchiv == 1 ? ' checked="checked"' : '').' /> I archiv.</td>';
        if ($user['aclSecret']) {
            echo '<td class="filter"><input type="checkbox" name="sec" value="sec" class="checkbox"'.($filterSec == 1 ? ' checked="checked"' : '').' /> Jen tajné.</td></tr></table>';
        } else {
            echo '</tr></table>';
        }
        echo '<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
    }
    filter();

    // if (isset($_GET['sort'])) {
    //     sortingSet('report', $_GET['sort'], 'report');
    // }
            $sqlFilter = DB_PREFIX."report.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."report.secret<=".$user['aclSecret'];

    $sql = "SELECT
                ".DB_PREFIX."unread.id AS 'unread',
                ".DB_PREFIX."report.id,
                ".DB_PREFIX."report.datum,
                ".DB_PREFIX."report.adatum,
                ".DB_PREFIX."report.label,
                ".DB_PREFIX."report.task,
                ".DB_PREFIX."report.status,
                ".DB_PREFIX."user.userName AS 'autor',
                ".DB_PREFIX."report.iduser AS 'riduser',
				".DB_PREFIX."report.type,
				".DB_PREFIX."report.start,
				".DB_PREFIX."report.end
                    FROM ".DB_PREFIX."report ".$fsql_conn2."
                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."report.iduser = ".DB_PREFIX."user.userId
                    LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."report.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 4 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
                    WHERE $sqlFilter AND ".DB_PREFIX."report.iduser=".DB_PREFIX."user.userId ".$filterSqlCat.$fsql_sec.$fsql_stat.$filterSqlMine.$fsql_conn.$filterUnread.
                    $fsql_archiv." GROUP BY ".DB_PREFIX."report.id ORDER BY ".$filterSqlSort;
                    //sortingGet('report');

    $res = mysqli_query($database, $sql);

    while ($rec = mysqli_fetch_assoc($res)) {
        echo '<div class="news_div '.($rec['type'] == 1 ? 'game_news' : 'system_news').($rec['unread'] ? ' unread_record' : '').'">
                <div class="news_head"><strong><a href="readactrep.php?rid='.$rec['id'].'&amp;hidenotes=0&amp;truenames=0">'.stripslashes($rec['label']).'</a></strong>';
        if ($user['aclReport'] || ($user['userId'] == $rec['riduser'] && $rec['status'] < 1)) {
            echo '	 | <td><a href="editactrep.php?rid='.$rec['id'].'">upravit</a> | <a href="procactrep.php?delete='.$rec['id'].'&amp;table=4" onclick="'."return confirm('Opravdu smazat &quot;".$text['hlaseniM']."&quot; &quot;".stripslashes($rec['label'])."&quot;?');".'">smazat</a></td>';
        } else {
            echo '   | <td><a href="newnote.php?rid='.$rec['id'].'&idtable=8">přidat poznámku</a></td>';
        }
        echo '</span>
                <p>['.webdatetime($rec['datum']).']  '.$rec['autor'].', Datum akce: ['.webdate($rec['adatum']).' - '.$rec['start'].'-'.$rec['end'].']<br /> <strong>Úkol: </strong>'
                .stripslashes($rec['task']).'&nbsp; <strong>Stav:</strong> ';
        if ($rec['status'] == '0') {
            echo 'Rozpracované';
        }
        if ($rec['status'] == '1') {
            echo 'Dokončené';
        }
        if ($rec['status'] == '2') {
            echo 'Analyzované';
        }
        if ($rec['status'] == '3') {
            echo 'Archivované';
        }
        echo '</p></div></div>';
    }
    latteDrawTemplate("footer");
