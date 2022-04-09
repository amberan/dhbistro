<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

    // následuje načtení dat reportu a jejich uložení do vybranných proměných
    $reportarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT * FROM ".DB_PREFIX."report WHERE id=".$_REQUEST['rid'])); // načte data z DB
    $type = intval($reportarray['type']); // určuje typ hlášení
        $typestring = $type == 1 ? 'výjezd' : ($type == 2 ? 'výslech' : '?'); //odvozuje slovní typ hlášení
    $author = $reportarray['iduser']; // určuje autora hlášení
    $label = ($reportarray['label'] ?? ''); // nadpis hlášení, ke kterému je přiřazováno

if ($label != '') {
    $latteParameters['title'] .= $label.' ('.$typestring.')'; // specifikace TITLE
}

$latteParameters['title'] = 'Prirazeni osob k  hlášení';
mainMenu();
        $customFilter = custom_Filter(17);
    sparklets('<a href="/reports/">hlášení</a> &raquo; <strong>úprava hlášení</strong>'.($label != '' ? ' - "'.$label.' ('.$typestring.')"' : ''));
    if (is_numeric($_REQUEST['rid']) && ($user['aclReport'] || $user['userId'] == $author)) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."report WHERE id=".$_REQUEST['rid']);
        if ($rec = mysqli_fetch_assoc($res)) {
            ?>

<div id="obsah">
    <p>
        K hlášení můžete přiřadit osoby, kterých se týká nebo kterých by se týkat mohl.
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
        case 0: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  '; break;
        case 1: $fsql_archiv = ''; break;
        default: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
    }
            // formular filtru
            function filter(): void
            {
                global $filterSort, $sportraits, $ssymbols, $farchiv, $fdead;
                echo '<form action="addp2ar.php" method="post" id="filter">
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
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.($ssymbols ? ' checked="checked"' : '').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.($farchiv == 1 ? ' checked="checked"' : '').'> Zobrazit i archiv.</td>
	</tr>
	</table>
	  <div id="filtersubmit"><input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" /><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form name="addpersons" action="addpersons.php" method="post" class="otherform">';
            }
            filter();
            // vypis osob
            $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol', ".DB_PREFIX."ar2p.role AS 'role', ".DB_PREFIX."ar2p.iduser
                FROM ".DB_PREFIX."person
                LEFT JOIN ".DB_PREFIX."ar2p ON ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']."
                WHERE $sqlFilter ".$fsql_dead.$fsql_archiv." ORDER BY ".$filterSqlSort;
            $res = mysqli_query($database, $sql); ?>
    <div id="in-form-table">
        <?php
    if (mysqli_num_rows($res)) {
        echo '<table>
<thead>
	<tr>
	<th>#</th>
	<th>Úloha</th>
'.($sportraits ? '<th>Portrét</th>' : '').($ssymbols ? '<th>Symbol</th>' : '').'
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>
';
        $even = 0;
        $iterator = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            echo '<script type="text/javascript" language="JavaScript">
			<!--
			function NameChanger'.$iterator.'()
			{
				if(document.addpersons.isthere'.$iterator.'.checked == true) {
					document.addpersons.role'.$iterator.'.name = "role[]";
				}
				if(document.addpersons.isthere'.$iterator.'.checked == false) {
					document.addpersons.role'.$iterator.'.name = "norole[]";
				}
				return true;
			}
			// -->
			</script>';

            echo '<tr class="'.($even % 2 == 0 ? 'even' : 'odd').'"><td><input type="checkbox" id="isthere'.$iterator.'" name="person[]" value="'.$rec['id'].'" class="checkbox"'.($rec['iduser'] ? ' checked="checked"' : '').' onClick="NameChanger'.$iterator.'();"/></td>
	<td><select type="role" id="role'.$iterator.'" '.($rec['iduser'] ? ' name="role[]' : 'name="norole[]').'">
			<option value="0">osoba přítomná</option>'
            .($type == 1 ? '
			<option value="4"'.($rec['role'] == 4 ? ' selected="selected"' : '').'>velitel zásahu</option>
			<option value="3"'.($rec['role'] == 3 ? ' selected="selected"' : '').'>zatčený</option>' : '')
            .($type == 2 ? '
			<option value="1"'.($rec['role'] == 1 ? ' selected="selected"' : '').'>vyslýchaný</option>
			<option value="2"'.($rec['role'] == 2 ? ' selected="selected"' : '').'>vyslýchající</option>' : '').'
		</select></td>
'.($sportraits ? '<td><img src="file/portrait/'.$rec['id'].'" alt="portrét chybí" /></td>' : '').($ssymbols ? '<td><img src="file/symbol/'.$rec['symbol'].'" alt="symbol chybí" /></td>' : '').'
	<td>'.($rec['secret'] ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a>').'</td>
	</tr>';
            $even++;
            $iterator++;
        }
        echo '</tbody>
</table>';
    } ?>
        <input type="hidden" name="fdead" value="<?php echo $fdead; ?>" />
        <input type="hidden" name="farchiv" value="<?php echo $farchiv; ?>" />
        <input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
        <input id="button-floating-uloz" type="submit" value="Uložit změny" name="addtoareport" class="submitbutton" title="Uložit změny" />
    </div>
    <!-- end of #obsah -->
    </form>

</div>
<!-- end of #obsah -->
<?php
        } else {
            echo '<div id="obsah"><p>Hlášení neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
        }
    } else {
        echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
    }
    latteDrawTemplate("footer");
?>
