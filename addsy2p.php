<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';


latteDrawTemplate("header");

$latteParameters['title'] = 'Přiřazení symbolu osobě';
mainMenu();
$customFilter = custom_Filter(20);
sparklets('<a href="/persons/">osoby</a> &raquo; <a href="/symbols">nepřiřazené symboly</a>');
// Overeni, zda dany symbol existuje, a uzivatel ma dostatecna prava na jeho upravu
if (is_numeric($_REQUEST['rid']) && ($user['aclPerson'] || $user['aclSymbol'])) {
    $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."symbol WHERE id=".$_REQUEST['rid']);
    if ($rec = mysqli_fetch_assoc($res)) {
        ?>

<div id="obsah">

    <p>
        Přiřazení symbolu osobě, které patří.
    </p>

    <?php
    // zpracovani filtru
    if (!isset($customFilter['sort'])) {
        $filterSort = 1;
    } else {
        $filterSort = $customFilter['sort'];
    }
        if (!isset($customFilter['sportraits'])) {
            $sportraits = false;
        } else {
            $sportraits = $customFilter['sportraits'];
        }
        if (!isset($customFilter['ssymbols'])) {
            $ssymbols = false;
        } else {
            $ssymbols = $customFilter['ssymbols'];
        }
        if (!isset($customFilter['fdead'])) {
            $fdead = 0;
        } else {
            $fdead = 1;
        }
        if (!isset($customFilter['farchiv'])) {
            $farchiv = 0;
        } else {
            $farchiv = 1;
        }
        switch ($filterSort) {
            case 1: $filterSqlSort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC ';
                break;
            case 2: $filterSqlSort = ' '.DB_PREFIX.'person.surname DESC, '.DB_PREFIX.'person.name DESC ';
                break;
            default: $filterSqlSort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC ';
        }
        switch ($fdead) {
            case 0: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
                break;
            case 1: $fsql_dead = '';
                break;
            default: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
        }
        switch ($farchiv) {
            case 0: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
                break;
            case 1: $fsql_archiv = '';
                break;
            default: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
        }
        // formular filtru
            echo '<form action="addsy2p.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.($filterSort == 1 ? ' selected="selected"' : '').'>příjmení a jména vzestupně</option>
	<option value="2"'.($filterSort == 2 ? ' selected="selected"' : '').'>příjmení a jména sestupně</option>
	</select>.</p>
	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.($sportraits ? ' checked="checked"' : '').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.($fdead == 1 ? ' checked="checked"' : '').'> Zobrazit i mrtvé.</td>
	</tr>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.($farchiv == 1 ? ' checked="checked"' : '').'> Zobrazit i archiv.</td>
	</tr>
	</table>
	<div id="filtersubmit"><input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" /><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
        </form>

        <form action="addsymbols.php" method="post" class="otherform">';
        // vypis osob
        $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
        $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol'
                FROM ".DB_PREFIX."person
                LEFT JOIN ".DB_PREFIX."symbol2all ON ".DB_PREFIX."symbol2all.idrecord=".DB_PREFIX."person.id AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']."
                WHERE $sqlFilter AND ".DB_PREFIX."person.symbol < 1 ".$fsql_dead.$fsql_archiv." ORDER BY ".$filterSqlSort;
        $res = mysqli_query($database, $sql); ?>
    <div id="in-form-table">
        <?php
    if (mysqli_num_rows($res)) {
        echo '<table>
<thead>
	<tr>
	<th>#</th>
'.($sportraits ? '<th>Portrét</th>' : '').($ssymbols ? '<th>Symbol</th>' : '').'
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>
';
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            echo '<tr class="'.($even % 2 == 0 ? 'even' : 'odd').'"><td><input type="radio" name="person" value="'.$rec['id'].'" class="checkbox"'.($rec['iduser'] ? ' checked="checked"' : '').' /></td>
'.($sportraits ? '<td><img  loading="lazy" src="file/portrait/'.$rec['id'].'" alt="portrét chybí" /></td>' : '').($ssymbols ? '<td><img  loading="lazy" src="file/symbol/'.$rec['symbol'].'" alt="symbol chybí" /></td>' : '').'
	<td>'.($rec['secret'] ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ', [stripslashes($rec['surname'].' '), stripslashes($rec['name'].' ')]).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ', [stripslashes($rec['surname'].' '), stripslashes($rec['name'].' ')]).'</a>').'</td>
	</tr>';
            $even++;
        }
        echo '</tbody>
</table>';
    } ?>
        <input type="hidden" name="fdead" value="<?php echo $fdead; ?>" />
        <input type="hidden" name="farchiv" value="<?php echo $farchiv; ?>" />
        <input type="hidden" name="symbolid" value="<?php echo $_REQUEST['rid']; ?>" />
        <input id="button-floating-uloz" type="submit" value="Uložit změny" name="addsymb2pers" class="submitbutton" title="Uložit změny" />
    </div>
    <!-- end of #obsah -->
    </form>

</div>
<!-- end of #obsah -->
<?php
    } else {
        echo '<div id="obsah"><p>Symbol neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
    }
} else {
    echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
}
latteDrawTemplate("footer");
?>
