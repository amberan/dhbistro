<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Úprava hlášení';
mainMenu ();
        $customFilter = custom_Filter(15);
	sparklets ('<a href="/cases/">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přidání osob</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
	    if ($rec = mysqli_fetch_assoc ($res)) {
	        ?>

<div id="obsah">
    <p>
        K případu můžete přiřadit osoby, kterých se týká nebo kterých by se týkat mohl.
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
	  case 1: $filterSqlSort = ' '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.name ASC '; break;
	  case 2: $filterSqlSort = ' '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.name DESC '; break;
	  default: $filterSqlSort = ' '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.name ASC ';
	}
	        switch ($fdead) {
		case 0: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 '; break;
		case 1: $fsql_dead = ''; break;
		default: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
	}
	        switch ($farchiv) {
		case 0: $fsql_archiv = ' AND '.DB_PREFIX.'person.archiv=0 '; break;
		case 1: $fsql_archiv = ''; break;
		default: $fsql_archiv = ' AND '.DB_PREFIX.'person.archiv=0 ';
	}
	        // formular filtru
	        function filter ()
	        {
	            global $filterSort, $sportraits, $ssymbols, $farchiv, $fdead;
	            echo '<form action="addp2c.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>příjmení a jména sestupně</option>
	</select>.</p>
	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.(($sportraits) ? ' checked="checked"' : '').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.(($fdead == 1) ? ' checked="checked"' : '').'> Zobrazit i mrtvé.</td>
	</tr>
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.(($ssymbols) ? ' checked="checked"' : '').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv == 1) ? ' checked="checked"' : '').'> Zobrazit i archiv.</td>
	</tr>
	</table>
	<div id="filtersubmit"><input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" /><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addpersons.php" method="post" class="otherform">';
	        }
	        filter();
	        // vypis osob
	        if ($user['aclDirector']) {
	            $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."person LEFT JOIN ".DB_PREFIX."c2p ON ".DB_PREFIX."c2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." WHERE ".DB_PREFIX."person.deleted=0 ".$fsql_dead.$fsql_archiv." ORDER BY ".$filterSqlSort;
	        } else {
	            $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."person LEFT JOIN ".DB_PREFIX."c2p ON ".DB_PREFIX."c2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." WHERE ".DB_PREFIX."person.deleted=0 ".$fsql_dead.$fsql_archiv." AND ".DB_PREFIX."person.secret=0 ORDER BY ".$filterSqlSort;
	        }
	        $res = mysqli_query ($database,$sql); ?>

    <div style="padding-top: 0px; padding-bottom: 0px;" id="in-form-table">

        <?php
	if (mysqli_num_rows ($res)) {
	    echo '<table>
<thead>
	<tr>
	<th>#</th>
'.(($sportraits) ? '<th>Portrét</th>' : '').(($ssymbols) ? '<th>Symbol</th>' : '').'
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>
';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'"><td><input type="checkbox" name="person[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser']) ? ' checked="checked"' : '').' /></td>
'.(($sportraits) ? '<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>' : '').(($ssymbols) ? '<td><img src="getportrait.php?nrid='.$rec['symbol'].'" alt="symbol chybí" /></td>' : '').'
	<td>'.(($rec['secret']) ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a>').'</td>
	</tr>';
	        $even++;
	    }
	    echo '</tbody>
</table>';
	} ?>
        <input type="hidden" name="fdead" value="<?php echo $fdead; ?>" />
        <input type="hidden" name="farchiv" value="<?php echo $farchiv; ?>" />
        <input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />

        <input id="button-floating-uloz" type="submit" value="Uložit změny" name="addtocase" class="submitbutton" title="Uložit změny" />
    </div>
    <!-- end of #obsah -->
    </form>

</div>
<!-- end of #obsah -->
<?php
	    } else {
	        echo '<div id="obsah"><p>Případ neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
	    }
	} else {
	    echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	latteDrawTemplate("footer");
?>
