<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Osoby';

    // DELETE
    if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete']) && $usrinfo['right_text']) {
        auditTrail(1, 11, $_REQUEST['delete']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."person SET deleted=1 WHERE id=".$_REQUEST['delete']);
        deleteAllUnread(1, $_REQUEST['delete']);
    }
    // NEW
    if (isset($_POST['insertperson']) && !preg_match('/^[[:blank:]]*$/i', $_POST['name']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
        if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
            $file = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['portrait']['tmp_name'], './files/'.$file.'tmp');
            $sdst = imageResize('./files/'.$file.'tmp', 100, 130);
            imagejpeg($sdst, './files/portraits/'.$file);
            unlink('./files/'.$file.'tmp');
        } else {
            $file = '';
        }
        if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
            $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
            $sdst = imageResize('./files/'.$sfile.'tmp', 100, 130);
            imagejpeg($sdst, './files/symbols/'.$sfile);
            unlink('./files/'.$sfile.'tmp');
            $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".time()."', '".$user['userId']."', '".time()."', '".$user['userId']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
            mysqli_query($database, $sql_sy);
            $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
            $syid = $syidarray['id'];
        } else {
            $sfile = '';
            $syid = '';
        }
        if ($_POST['personRoof'] > null) {
            $updateRoof = 'current_timestamp';
        } else {
            $updateRoof = 'null';
        }
        $updateDate = $rdatum = time();
        if ($user['aclGamemaster'] == 1) {
            $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
            $updateDate = rand($rdatum, time());
        }


        $sql_p = "INSERT INTO ".DB_PREFIX."person (roof, name, surname, phone, datum, iduser, contents, secret, deleted, portrait, side, power, spec, symbol, dead, archived, regdate, regid)
            VALUES(".$updateRoof.",'".$_POST['name']."','".$_POST['surname']."','".$_POST['phone']."','".$updateDate."','".$user['userId']."','".$_POST['contents']."','".$_POST['secret']."','0','".$file."', '".$_POST['side']."', '".$_POST['power']."', '".$_POST['spec']."', '".$syid."','0',null,'".$rdatum."','".$user['userId']."')";
        mysqli_query($database, $sql_p);
        $pidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."person WHERE UCASE(surname)=UCASE('".$_POST['surname']."') AND UCASE(name)=UCASE('".$_POST['name']."') AND side='".$_POST['side']."'"));
        $pid = $pidarray['id'];
        if (!isset($_POST['notnew'])) {
            unreadRecords(1, $pid);
        }
        auditTrail(1, 3, $pid);
        $_SESSION['message'] = 'Osoba vytvořena.';
    } else {
        if (isset($_POST['insertperson'])) {
            $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
        }
    }
    //EDIT
    if (isset($_POST['personid'], $_POST['editperson']) && $usrinfo['right_text'] && !preg_match('/^[[:blank:]]*$/i', $_POST['name']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
        auditTrail(1, 2, $_POST['personid']);
        if (!isset($_POST['notnew'])) {
            unreadRecords(1, $_POST['personid']);
        }
        if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
            $ps = mysqli_query($database, "SELECT portrait FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
            if ($pc = mysqli_fetch_assoc($ps)) {
                unlink('./files/portraits/'.$pc['portrait']);
            }
            $file = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['portrait']['tmp_name'], './files/'.$file.'tmp');
            $dst = imageResize('./files/'.$file.'tmp', 100, 130);
            imagejpeg($dst, './files/portraits/'.$file);
            unlink('./files/'.$file.'tmp');
            mysqli_query($database, "UPDATE ".DB_PREFIX."person SET portrait='".$file."' WHERE id=".$_POST['personid']);
        }
        if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
            $sps = mysqli_query($database, "SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
            if ($spc = mysqli_fetch_assoc($sps)) {
                $prsn_res = mysqli_query($database, "SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
                $prsn_rec = mysqli_fetch_assoc($prsn_res);
                $sdate = "<p>".date("j/m/Y H:i:s", time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
                mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
            }
            $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
            $sdst = imageResize('./files/'.$sfile.'tmp', 100, 100);
            imagejpeg($sdst, './files/symbols/'.$sfile);
            unlink('./files/'.$sfile.'tmp');
            $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".time()."', '".$user['userId']."', '".time()."', '".$user['userId']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
            mysqli_query($database, $sql_sy);
            $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
            $syid = $syidarray['id'];
            mysqli_query($database, "UPDATE ".DB_PREFIX."person SET symbol='".$syid."' WHERE id=".$_POST['personid']);
        }
        personCheckboxUpdate($_POST['personid'], 'archived', $_POST['archiv']);
        personCheckboxUpdate($_POST['personid'], 'roof', $_POST['personRoof']);
        $sqlPlayer = '';
        if ($user['aclGamemaster'] != 1) {
            $sqlPlayer = "datum='".time()."', iduser='".$user['userId']."',";
        }
        $update = "UPDATE ".DB_PREFIX."person SET name='".$_POST['name']."', surname='".$_POST['surname']."', phone='".$_POST['phone']."', ".$sqlPlayer."
        contents='".$_POST['contents']."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."',
        dead='".(isset($_POST['dead']) ? '1' : '0')."' WHERE id=".$_POST['personid'];

        Debugger::log('DEBUG '.$config['version'].': '.$update);

        mysqli_query($database, $update);

        $_SESSION['message'] = 'Osoba upravena.';
        header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
    } else {
        if (isset($_POST['editperson'])) {
            $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
        }
    }
    //ANTIDATING registration
    if ((isset($_POST['personid'])) && $user['aclGamemaster'] == 1 && is_numeric($_POST['rdatumday']) && is_numeric($_POST['regusr'])) {
        auditTrail(1, 10, $_POST['personid']);
        $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."person SET regdate='".$rdatum."', regid='".$_POST['regusr']."' WHERE id=".$_POST['personid']);
        $_SESSION['message'] = 'Osoba upravena.';
        header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
    } else {
        if (isset($_POST['orgperson'])) {
            $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
        }
    }
    if (isset($_POST['setgroups'])) {
        auditTrail(1, 6, $_POST['personid']);
        mysqli_query($database, "DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".$_POST['personid']);
        $group = $_POST['group'];
        $_SESSION['message'] = 'Skupiny pro uživatele uloženy.';
        for ($i = 0; $i < count($group); $i++) {
            mysqli_query($database, "INSERT INTO ".DB_PREFIX."g2p VALUES('".$_POST['personid']."','".$group[$i]."','".$user['userId']."')");
        }
        header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
    }
    if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
        auditTrail(1, 4, $_POST['personid']);
        $newname = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['attachment']['tmp_name'], './files/'.$newname);
        $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".time()."','".$user['userId']."','1','".$_POST['personid']."','".$_POST['secret']."')";
        mysqli_query($database, $sql);
        if (!isset($_POST['fnotnew'])) {
            unreadRecords(1, $_POST['personid']);
        }
    } else {
        if (isset($_POST['uploadfile'])) {
            $_SESSION['message'] = 'Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.';
        }
    }
    if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
        auditTrail(1, 5, $_POST['personid']);
        if ($usrinfo['right_text']) {
            $fres = mysqli_query($database, "SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
            $frec = mysqli_fetch_assoc($fres);
            unlink('./files/'.$frec['uniquename']);
            mysqli_query($database, "DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        }
        header('Location: editperson.php?rid='.$_GET['personid']);
    }
    if (isset($_GET['deletesymbol'])) {
        auditTrail(1, 2, $_GET['personid']);
        if ($usrinfo['right_text']) {
            $sps = mysqli_query($database, "SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
            $spc = mysqli_fetch_assoc($sps);
            $prsn_res = mysqli_query($database, "SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
            $prsn_rec = mysqli_fetch_assoc($prsn_res);
            $sdate = "<p>".date("j/m/Y H:i:s", time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
            mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
            mysqli_query($database, "UPDATE ".DB_PREFIX."person SET symbol='' WHERE id=".$_GET['personid']);
        }
        header('Location: editperson.php?rid='.$_GET['personid']);
    }

    auditTrail(1, 1, 0);
    mainMenu();
        $customFilter = custom_Filter(1);
    sparklets('<strong>osoby</strong>', '<a href="newperson.php">přidat osobu</a>; <a href="symbols.php" '.(searchTable(7) ? ' class="unread"' : '').'>nepřiřazené symboly</a>; <a href="symbol_search.php">vyhledat symbol</a>');
    // zpracovani filtru
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
    if (!isset($customFilter['sec'])) {
        $filterSec = 0;
    } else {
        $filterSec = 1;
    }
        if (!isset($customFilter['new'])) {
            $fNew = 0;
        } else {
            $fNew = 1;
            $filterUnread = ' AND '.DB_PREFIX.'unread.id is not null ';
        }
    if (!isset($customFilter['fspec'])) {
        $fspec = 0;
    } else {
        $fspec = $customFilter['fspec'];
    }
    if (!isset($customFilter['fside'])) {
        $fside = 0;
    } else {
        $fside = $customFilter['fside'];
    }
    if (!isset($customFilter['fpow'])) {
        $fpow = 0;
    } else {
        $fpow = $customFilter['fpow'];
    }
    switch ($filterSec) {
        case 0: $fsql_sec = ''; break;
        case 1: $fsql_sec = ' AND '.DB_PREFIX.'person.secret>0 '; break;
        default: $fsql_sec = '';
    }
    switch ($fdead) {
        case 0: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 '; break;
        case 1: $fsql_dead = ''; break;
        default: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
    }
    switch ($farchiv) {
        case 0: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  '; break;
        case 1: $fsql_archiv = ''; break;
        default: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1)) ';
    }
        switch ($fspec) {
        case 0: $fsql_fspec = ''; break;
        case 1: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=0 '; break;
        case 2: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=1 '; break;
        case 3: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=2 '; break;
        case 4: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=3 '; break;
        case 5: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=4 '; break;
        case 6: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=5 '; break;
        case 7: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=6 '; break;
        case 8: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=7 '; break;
        case 9: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=8 '; break;
        case 10: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=9 '; break;
        case 11: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=10 '; break;
        default: $fsql_fspec = '';
    }
    switch ($fside) {
        case 0: $fsql_fside = ''; break;
        case 1: $fsql_fside = ' AND '.DB_PREFIX.'person.side=0 '; break;
        case 2: $fsql_fside = ' AND '.DB_PREFIX.'person.side=1 '; break;
        case 3: $fsql_fside = ' AND '.DB_PREFIX.'person.side=2 '; break;
        case 4: $fsql_fside = ' AND '.DB_PREFIX.'person.side=3 '; break;
        default: $fsql_fside = '';
    }
    switch ($fpow) {
        case 0: $fsql_fpow = ''; break;
        case 1: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=0 '; break;
        case 2: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=1 '; break;
        case 3: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=2 '; break;
        case 4: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=3 '; break;
        case 5: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=4 '; break;
        case 6: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=5 '; break;
        case 7: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=6 '; break;
        case 8: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=7 '; break;
        case 9: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=8 '; break;
        default: $fsql_fpow = '';
    }
    // formular filtru
    function filter(): void
    {
        global $sportraits, $ssymbols, $filterSec, $fNew, $fdead, $farchiv, $user, $fspec, $fside, $fpow;
        echo '<div id="filter-wrapper"><form action="persons.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	<p> Strana:
	<select name="fside">
	<option value="0"'.($fside == 0 ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.($fside == 1 ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.($fside == 2 ? ' selected="selected"' : '').'>světlo</option>
	<option value="3"'.($fside == 3 ? ' selected="selected"' : '').'>tma</option>
	<option value="4"'.($fside == 4 ? ' selected="selected"' : '').'>člověk</option>
	</select>
	 Specializace:
	<select name="fspec">
	<option value="0"'.($fspec == 0 ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.($fspec == 1 ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.($fspec == 2 ? ' selected="selected"' : '').'>bílý mág</option>
	<option value="3"'.($fspec == 3 ? ' selected="selected"' : '').'>černý mág</option>
	<option value="4"'.($fspec == 4 ? ' selected="selected"' : '').'>léčitel</option>
	<option value="5"'.($fspec == 5 ? ' selected="selected"' : '').'>obrateň</option>
	<option value="6"'.($fspec == 6 ? ' selected="selected"' : '').'>upír</option>
	<option value="7"'.($fspec == 7 ? ' selected="selected"' : '').'>vlkodlak</option>
	<option value="8"'.($fspec == 8 ? ' selected="selected"' : '').'>vědma</option>
	<option value="9"'.($fspec == 9 ? ' selected="selected"' : '').'>zaříkávač</option>
	<option value="10"'.($fspec == 10 ? ' selected="selected"' : '').'>vykladač</option>
	<option value="11"'.($fspec == 11 ? ' selected="selected"' : '').'>jasnovidec</option>
	</select>
	 Kategorie:
	<select name="fpow">
	<option value="0"'.($fpow == 0 ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.($fpow == 1 ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.($fpow == 2 ? ' selected="selected"' : '').'>první</option>
	<option value="3"'.($fpow == 3 ? ' selected="selected"' : '').'>druhá</option>
	<option value="4"'.($fpow == 4 ? ' selected="selected"' : '').'>třetí</option>
	<option value="5"'.($fpow == 5 ? ' selected="selected"' : '').'>čtvrtá</option>
	<option value="6"'.($fpow == 6 ? ' selected="selected"' : '').'>pátá</option>
	<option value="7"'.($fpow == 7 ? ' selected="selected"' : '').'>šestá</option>
	<option value="8"'.($fpow == 8 ? ' selected="selected"' : '').'>sedmá</option>
	<option value="9"'.($fpow == 9 ? ' selected="selected"' : '').'>mimo kategorie</option>
	</select></p>

	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.($sportraits ? ' checked="checked"' : '').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.($fdead == 1 ? ' checked="checked"' : '').'> Zobrazit i mrtvé.</td>
        <td class="filter"><input type="checkbox" name="new" value="1"'.($fNew == 1 ? ' checked="checked"' : '').'> Zobrazit jen nové.</td>
	</tr>
        <tr class="filter">
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.($ssymbols ? ' checked="checked"' : '').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.($farchiv != null ? ' checked="checked"' : '').'> Zobrazit i archiv.</td>';
        if ($user['aclSecret']) {
            echo '<td class="filter"><input type="checkbox" name="sec" value="sec" class="checkbox"'.($filterSec == 1 ? ' checked="checked"' : '').' /> Jen tajné.</td></tr></table>';
        } else {
            echo '</tr></table>';
        }
        echo '
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
    }
    filter();

if (isset($_GET['sort'])) {
    sortingSet('person', $_GET['sort'], 'person');
}

    $sql = "SELECT ".DB_PREFIX."unread.id as unread, ".DB_PREFIX."person.regdate as date_created, ".DB_PREFIX."person.datum as date_changed, ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.archived, ".DB_PREFIX."person.dead AS 'dead', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol'
    FROM ".DB_PREFIX."person
    LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."person.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 1 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    WHERE ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret<=".$user['aclSecret'].$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow.$filterUnread." GROUP BY ".DB_PREFIX."person.id ".sortingGet('person');

    $res = mysqli_query($database, $sql);
    //echo mysqli_num_rows($res);
    if (mysqli_num_rows($res)) {
        echo '<div id="obsah">
<table>
<thead>
	<tr>
'.($sportraits ? '<th>Portrét</th>' : '').
($ssymbols ? '<th>Symbol</th>' : '').'
	  <th>Jméno <a href="persons.php?sort=surname">&#8661;</a></th>
	  <th>Telefon</th>
	  <th>Vytvořeno <a href="persons.php?sort=regdate">&#8661;</a>/ Změněno <a href="persons.php?sort=datum">&#8661;</a></th>
      <th style="min-width:100px">Status</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
//            if ($fNew == 0 || ($fNew == 1 && searchRecord(1, $rec['id']))) {
            echo '<tr class="'.($rec['unread'] ? ' unread_record' : ($even % 2 == 0 ? 'even' : 'odd')).'">
                        '.($sportraits ? '<td><img src="file/portrait/'.$rec['id'].'" alt="" /></td>' : '').'
                        '.($ssymbols ? '<td><img src="file/symbol/'.$rec['symbol'].'" alt="" /></td>' : '').'
                        <td>'.($rec['secret'] ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a>').'</td>
						<td><a href="tel:'.str_replace(' ', '', $rec['phone']).'">'.$rec['phone'].'</a></td>
						<td>'.webdate($rec['date_created']).' / '.webdate($rec['date_changed']).'</td>
                        <td>'.($rec['archived'] > 2 ? 'Archivovaný' : '').''.($rec['dead'] == 1 ? ' Mrtvý' : '').''.($rec['secret'] == 1 ? ' Tajný' : '').'</td>
                        '.($usrinfo['right_text'] ? '	<td><a href="editperson.php?rid='.$rec['id'].'">upravit</a> | <a href="persons.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat osobu &quot;".implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])])."&quot;?');".'">smazat</a></td>' : '<td><a href="newnote.php?rid='.$rec['id'].'&idtable=5">přidat poznámku</a>').'
                        </tr>';
            $even++;
        }
//        }
        echo '</tbody>
</table>
</div>';
    }
    latteDrawTemplate("footer");
