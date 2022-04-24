<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

        // Přidání symbolu
    if (isset($_POST['insertsymbol'])) {
        if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
            $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
            $sdst = imageResize('./files/'.$sfile.'tmp', 100, 100);
            imagejpeg($sdst, './files/symbols/'.$sfile);
            unlink('./files/'.$sfile.'tmp');
        } else {
            $sfile = '';
        }
        $time = time();
        $sql_p = "INSERT INTO ".DB_PREFIX."symbol (symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret)  VALUES( '".$sfile."', '".$_POST['contents']."', '0', '".$time."', '".$user['userId']."', '".$time."', '".$user['userId']."', 0, '0', '".$_POST['liner']."', '".$_POST['curver']."', '".$_POST['pointer']."', '".$_POST['geometrical']."', '".$_POST['alphabeter']."', '".$_POST['specialchar']."', 0)";
        mysqli_query($database, $sql_p);
        $sql_f = "SELECT id FROM ".DB_PREFIX."symbol WHERE created='".$time."' AND created_by='".$user['userId']."' AND modified='".$time."' AND modified_by='".$user['userId']."'";
        $pidarray = mysqli_fetch_assoc(mysqli_query($database, $sql_f));
        $pid = $pidarray['id'];
        authorizedAccess(7, 3, $pid);
        if (!isset($_POST['notnew'])) {
            unreadRecords(7, $pid);
        }
        $_SESSION['message'] = 'Symbol vložen.';
    } elseif (isset($_POST['insertperson'])) {
        $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
        // Vymazani symbolu
    if (isset($_REQUEST['sdelete']) && is_numeric($_REQUEST['sdelete']) && $user['aclSymbol']>1) {
        authorizedAccess(7, 11, $_REQUEST['sdelete']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET deleted=1 WHERE id=".$_REQUEST['sdelete']);
        deleteAllUnread(7, $_REQUEST['sdelete']);
        $_SESSION['message'] = 'Symbol smazan.';
    }
        // Obnoveni symbolu
    if (isset($_REQUEST['undelete']) && is_numeric($_REQUEST['undelete']) && $user['aclRoot']) {
        authorizedAccess(7, 11, $_REQUEST['undelete']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET deleted=0 WHERE id=".$_REQUEST['undelete']);
        $_SESSION['message'] = 'Symbol obnoven.';
    }

        // Uprava symbolu
    if (isset($_POST['symbolid'], $_POST['editsymbol']) && $user['aclSymbol']) {
        authorizedAccess(7, 2, $_POST['symbolid']);
        if (!isset($_POST['notnew'])) {
            unreadRecords(7, $_POST['symbolid']);
        }
        if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
            $sps = mysqli_query($database, "SELECT symbol FROM ".DB_PREFIX."symbol WHERE id=".$_POST['symbolid']);
            if ($spc = mysqli_fetch_assoc($sps)) {
                unlink('./files/symbols/'.$spc['symbol']);
            }
            $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
            move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
            $sdst = imageResize('./files/'.$sfile.'tmp', 100, 100);
            imagejpeg($sdst, './files/symbols/'.$sfile);
            unlink('./files/'.$sfile.'tmp');
            mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET symbol='".$sfile."' WHERE id=".$_POST['symbolid']);
        }
        if ($user['aclGamemaster'] == 1) {
            $sql = "UPDATE ".DB_PREFIX."symbol SET `desc`='".$_POST['desc']."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
            mysqli_query($database, $sql);
        } else {
            $sql = "UPDATE ".DB_PREFIX."symbol SET `desc`='".$_POST['desc']."', modified='".time()."', modified_by='".$user['userId']."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
            mysqli_query($database, $sql);
        }
        $_SESSION['message'] = 'Symbol upraven.';
    } elseif (isset($_POST['editsymbol'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
      // archivace symbolu
    if (isset($_GET['archive']) && is_numeric($_GET['archive']) && $user['aclSymbol']) {
        authorizedAccess(7, 11, $_GET['archive']);
        mysqli_query($database, 'UPDATE '.DB_PREFIX.'symbol SET archived=CURRENT_TIMESTAMP WHERE id='.$_GET['archive']);
        $_SESSION['message'] = 'Symbol archivovan.';
    }
      // odarchivace symbolu
    if (isset($_GET['unarchive']) && is_numeric($_GET['unarchive']) && $user['aclSymbol']) {
        authorizedAccess(7, 11, $_GET['unarchive']);
        mysqli_query($database, 'UPDATE '.DB_PREFIX.'symbol SET archived=0 WHERE id='.$_GET['unarchive']);
        $_SESSION['message'] = 'Symbol odarchivovan.';
    }

latteDrawTemplate("header");

$latteParameters['title'] = 'Symboly';
    authorizedAccess(7, 1, 0);
    mainMenu();
    deleteUnread(7, 'none');
    sparklets('<a href="/persons/">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>', '<a href="newsymbol.php">nový symbol</a>; <a href="symbol_search.php">vyhledat symbol</a>');

    if (sizeof(@$_POST['filter']) > 0) {
        filterSet('symbol', @$_POST['filter']);
    }
$filter = filterGet('symbol');
$sqlFilter = DB_PREFIX.'symbol.deleted in (0,'.$user['aclRoot'].') ';

switch (@$filter['archived']) {
   case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'symbol.archived >= 0 '; break;
   default: $sqlFilter .= ' AND '.DB_PREFIX.'symbol.archived = 0 ';
}
switch (@$filter['deleted']) {
   case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'symbol.deleted>=0 '; break;
   default: $sqlFilter .= ' AND '.DB_PREFIX.'symbol.deleted=0 ';
}

?>

<form action="symbols.php" method="POST" id="filter">
<input type="hidden" name="filter[placeholder]"  />
<input type="checkbox" name="filter[archived]" <?php if (isset($filter['archived']) and $filter['archived'] == 'on') { ?> checked <?php } ?> onchange="this.form.submit()"/>i archivovane
<?php if ($user['aclRoot'] > 0) { ?> <input type="checkbox" name="filter[deleted]" <?php if (isset($filter['deleted']) and $filter['deleted'] == 'on') { ?> checked <?php } ?> onchange="this.form.submit()"/>i smazane <?php } ?>
</form>
<?php

    $sql = "SELECT * FROM ".DB_PREFIX."symbol
        WHERE ".$sqlFilter." AND ".DB_PREFIX."symbol.assigned=0
        ORDER BY ".DB_PREFIX."symbol.created DESC";
    $res = mysqli_query($database, $sql);
    if (mysqli_num_rows($res)) {
        echo '<div id="obsah">
<table>
<thead>
	<tr><th>Symbol</th>
	  <th>Poznámky</th>
	  <th>Výskyt</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>';
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            echo '<tr class="'.(searchRecord(7, $rec['id']) ? ' unread_record' : ($even % 2 == 0 ? 'even' : 'odd')).'">
		  <td><a href="readsymbol.php?rid='.$rec['id'].'"><img src="file/symbol/'.$rec['id'].'" alt="symbol chybí" /></a></td>
		  <td>'.stripslashes($rec['desc']).'<br />';
            // generování poznámek
            echo '<br /><strong>Poznámky:</strong>';
            $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
            $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
            FROM ".DB_PREFIX."note, ".DB_PREFIX."user
            WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$rec['id']." AND ".DB_PREFIX."note.idtable=7
            ORDER BY ".DB_PREFIX."note.datum DESC";
            $res_n = mysqli_query($database, $sql_n);
            $i = 0;
            while ($rec_n = mysqli_fetch_assoc($res_n)) {
                $i++;
                if ($i == 1) { ?>
		  	<div id="poznamky"><?php
                    }
                if ($i > 1) {?>
		  		<?php
                    } ?>
		  		<div class="poznamka">
		  			<h4><?php echo stripslashes($rec_n['title']).' - '.stripslashes($rec_n['user']);
                if ($rec_n['secret'] == 0) {
                    echo ' (veřejná)';
                }
                if ($rec_n['secret'] == 1) {
                    echo ' (tajná)';
                }
                if ($rec_n['secret'] == 2) {
                    echo ' (soukromá)';
                } ?></h4>
		  			<div><?php echo stripslashes($rec_n['note']); ?></div>
		  		</div>
		  		<!-- end of .poznamka -->
		  			<?php
            }
            if ($i != 0) { ?>
		  	</div>
		  	<!-- end of #poznamky -->
		  	<?php }
            // konec poznámek

            echo '</td>
		  <td>';
            // generování seznamu přiřazených případů
            $sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
            $sql_s = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title'
            FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case
            WHERE $sqlFilter AND  ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=3
            ORDER BY ".DB_PREFIX."case.title ASC";
            $pers = mysqli_query($database, $sql_s);

            $i = 0;
            while ($perc = mysqli_fetch_assoc($pers)) {
                $i++;
                if ($i == 1) { ?>
		  		<strong>Případy:</strong>
		  		<ul id=""><?php
                        } ?>
		  			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		  		<?php
            }
            if ($i != 0) { ?>
		  		</ul>
		  		<!-- end of # -->
		  		<?php
                    } else {?>
		  		<em>Symbol nebyl přiřazen žádnému případu.</em><br /><?php
                    }
            // konec seznamu přiřazených případů
            // generování seznamu přiřazených hlášení
            if ($user['aclRoot'] < 1) {
                $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
            }

            $sqlFilter .= " AND ".DB_PREFIX."report.reportSecret<=".$user['aclSecret'];
            $sql_s = "SELECT ".DB_PREFIX."report.reportId AS 'id', ".DB_PREFIX."report.reportName AS 'label'
            FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report
            WHERE $sqlFilter AND ".DB_PREFIX."report.reportId=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$rec['id']." AND ".DB_PREFIX."symbol2all.table=4
            ORDER BY ".DB_PREFIX."report.reportName ASC";
            $pers = mysqli_query($database, $sql_s);

            $i = 0;
            while ($perc = mysqli_fetch_assoc($pers)) {
                $i++;
                if ($i == 1) { ?>
		  		<strong>Hlášení:</strong>
		  		<ul id=""><?php
                } ?>
		  		<li><a href="/reports/<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		  		<?php
            }
            if ($i != 0) { ?>
		  		</ul>
		  		<!-- end of # -->
		  	    <?php
                    } else {?>
		   		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
                    }
            // konec seznamu přiřazených případů?>
		   	</td>
            <td>
			<?php
if ($user['aclSymbol']) {
                echo '	<a href="addsy2p.php?rid='.$rec['id'].'">přiřadit </a> <a href="editsymbol.php?rid='.$rec['id'].'">upravit </a> <a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku </a>';
                if ($rec['archived'] > 0) {
                    echo '<a href="symbols.php?unarchive='.$rec['id'].'" onclick="'."return confirm('Opravdu vyjmout z archivu tento symbol?');".'">odarchivovat </a>';
                } else {
                    echo '<a href="symbols.php?archive='.$rec['id'].'" onclick="'."return confirm('Opravdu archivovat tento symbol?');".'">archivovat </a>';
                }
                if ($rec['deleted'] == 0) {
                    echo '<a href="symbols.php?sdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat tento symbol?');".'">smazat </a>';
                }
            }
            if ($user['aclRoot'] && $rec['deleted'] > 0) {
                echo '<a href="symbols.php?undelete='.$rec['id'].'" onclick="'."return confirm('Opravdu obnovit tento symbol?');".'">obnovit </a>';
            } ?>
            </td>
        </tr>
<?php
            $even++;
        } ?>
        </tbody>
</table>
</div>
<?php
    }

latteDrawTemplate("footer");
?>
