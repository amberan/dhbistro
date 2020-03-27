<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

	if (is_numeric($_REQUEST['rid'])) {
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."group WHERE id=".$_REQUEST['rid']);
	    if ($rec_g = mysqli_fetch_assoc ($res)) {
	        if (($rec_g['secret'] > $usrinfo['right_power']) || $rec_g['deleted'] == 1) {
	            unauthorizedAccess(2, $rec_g['secret'], $rec_g['deleted'], $_REQUEST['rid']);
	        }
	        auditTrail(2, 1, $_REQUEST['rid']);

	        $latteParameters['title'] = StripSlashes($rec_g['title']);

	        mainMenu ();
	        $customFilter = custom_Filter(14, $_REQUEST['rid']);
	        if (!isset($_REQUEST['hidenotes'])) {
	            $hn = 0;
	        } else {
	            $hn = $_REQUEST['hidenotes'];
	        }
	        if ($hn == 0) {
	            $hidenotes = '&amp;hidenotes=1">skrýt poznámky</a>';
	            $backurl = 'readgroup.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
	        } else {
	            $hidenotes = '&amp;hidenotes=0">zobrazit poznámky</a>';
	            $backurl = 'readgroup.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
	        }
	        if ($usrinfo['right_text']) {
	            $editbutton = '; <a href="editgroup.php?rid='.$_REQUEST['rid'].'">upravit skupinu</a>';
	        } else {
	            $editbutton = '';
	        }
	        deleteUnread (2,$_REQUEST['rid']);
	        sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>'.StripSlashes($rec_g['title']).'</strong>','<a href="readgroup.php?rid='.$_REQUEST['rid'].$hidenotes.$editbutton); ?>
<?php // zpracovani filtru
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
	        if (!isset($customFilter['sec'])) {
	            $filterSec = 0;
	        } else {
	            $filterSec = 1;
	        }
	        switch ($filterSort) {
	  case 1: $filterSqlSort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC '; break;
	  case 2: $filterSqlSort = ' '.DB_PREFIX.'person.surname DESC, '.DB_PREFIX.'person.name DESC '; break;
	  default: $filterSqlSort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC ';
	}
	        switch ($filterSec) {
		case 0: $fsql_sec = ''; break;
		case 1: $fsql_sec = ' AND '.DB_PREFIX.'person.secret=1 '; break;
		default: $fsql_sec = '';
	}
	        //
	        function filter ()
	        {
	            global $filterSort, $sportraits;
	            echo '<div id="filter-wrapper"><form action="readgroup.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Členy skupiny řadit podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>příjmení a jména sestupně</option>
</select>.</p>
		<p><input type="checkbox" name="sportraits" value="1"'.(($sportraits) ? ' checked="checked"' : '').'> Zobrazit portréty.</p>';
	            echo '
	  <input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	        }
	        filter(); ?>
<div id="obsah">
    <h1><?php echo StripSlashes($rec_g['title']); ?></h1>
    <fieldset>
        <legend><strong>Obecné informace</strong></legend>
        <div id="info"><?php
		if ($rec_g['secret'] == 1) { ?>
            <h2>TAJNÉ</h2><?php }
	        if ($rec_g['archived'] == 1) { ?>
            <h2>ARCHIV</h2><?php }
	        if ($rec_g['deleted'] == 1) { ?>
            <h2>SMAZANÝ ZÁZNAM</h2><?php } ?>
            <h3>Členové: </h3>
            <p><?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."person, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."person.deleted=0 ORDER BY ".$filterSqlSort;
		} else {
		    $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."person, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0 ORDER BY ".$filterSqlSort;
		}
	        $res = mysqli_query ($database,$sql);
	        if (mysqli_num_rows ($res)) {
	            echo '<div id=""><!-- je treba dostylovat -->
<table>
<thead>
	<tr>
'.(($sportraits) ? '<th>Portrét</th>' : '').'
	  <th>Jméno</th>
	  <th>Telefon</th>
	</tr>
</thead>
<tbody>
';
	            $even = 0;
	            while ($rec = mysqli_fetch_assoc ($res)) {
	                echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
'.(($sportraits) ? '<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>' : '').'
	<td>'.(($rec['secret']) ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a>').'</td>
	<td><a href="tel:'.str_replace(' ', '',$rec['phone']).'">'.$rec['phone'].'</a></td>
</tr>';
	                $even++;
	            }
	            echo '</tbody>
</table>
</div>';
	        } else { ?>
                <em>Do skupiny nejsou přiřazeny žádné osoby.</em><?php
		} ?></p>
        </div>
        <!-- end of #info -->
    </fieldset>

    <fieldset>
        <legend><strong>Popis</strong></legend>
        <div class="field-text"><?php echo StripSlashes($rec_g['contents']); ?></div>
    </fieldset>

    <!-- následuje seznam přiložených souborů -->
    <?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
		    $sql = "SELECT mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2 ORDER BY ".DB_PREFIX."file.originalname ASC";
		} else {
		    $sql = "SELECT mime,  ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
		}
	        $res = mysqli_query ($database,$sql);
	        $i = 0;
	        while ($rec = mysqli_fetch_assoc ($res)) {
	            $i++;
	            if ($i == 1) { ?>
    <fieldset>
        <legend><strong>Přiložené soubory</strong></legend>
        <ul id="prilozenadata">
            <?php } //zobrazovani obrazku i jako obrazky
	            if (in_array($rec['mime'],$config['mime-image'])) { ?>
            <li><a href="getfile.php?idfile=<?php echo $rec['id']; ?>"><img width="300px" alt="<?php echo StripSlashes($rec['title']); ?>" src="getfile.php?idfile=<?php echo $rec['id']; ?>"></a></li>
            <?php		} else { ?>
            <li><?php echo $rec['mime']?><a href="getfile.php?idfile=<?php echo $rec['id']; ?>"><?php echo StripSlashes($rec['title']); ?></a></li>
            <?php } ?>
            <?php
	        }
	        if ($i <> 0) { ?>
        </ul>
        <!-- end of #prilozenadata -->
    </fieldset>
    <?php
		}
	        // konec seznamu přiložených souborů ?>

    <?php //skryti poznamek
if ($hn != 1) { ?>
    <!-- následuje seznam poznámek -->
    <?php // generování poznámek
		if ($usrinfo['right_power']) {
		    $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		}
		$res_n = mysqli_query ($database,$sql_n);
		$i = 0;
		while ($rec_n = mysqli_fetch_assoc ($res_n)) {
		    $i++;
		    if ($i == 1) { ?>
    <fieldset>
        <legend><strong>Poznámky</strong></legend>
        <div id="poznamky"><?php
			}
		    if ($i > 1) {?>
            <hr /><?php
			} ?>
            <div class="poznamka">
                <h4><?php echo StripSlashes($rec_n['title']).' - '.StripSlashes($rec_n['user']).' ['.webdate($rec_n['date_created']).']';
		    if ($rec_n['secret'] == 0) {
		        echo ' (veřejná)';
		    }
		    if ($rec_n['secret'] == 1) {
		        echo ' (tajná)';
		    }
		    if ($rec_n['secret'] == 2) {
		        echo ' (soukromá)';
		    } ?></h4>
                <div><?php echo StripSlashes($rec_n['note']); ?></div>
                <span
                      class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_text'])) {
			    echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit</span></a> ';
			}
		    if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_power'])) {
		        echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
		    } ?>
                </span>
            </div>
            <!-- end of .poznamka -->
            <?php
		}
		if ($i <> 0) { ?>
        </div>
        <!-- end of #poznamky -->
    </fieldset>
    <?php }
	// konec poznámek
	?>
    <?php } ?>
</div>
<!-- end of #obsah -->
<?php
	    } else {
	        $_SESSION['message'] = "Skupina neexistuje!";
	        Header ('location: index.php');
	    }
	} else {
	    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	    Header ('location: index.php');
	}
	latteDrawTemplate("footer");
?>
