<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

    if (is_numeric($_REQUEST['rid'])) {
        $res = mysqli_query($database, "SELECT
				".DB_PREFIX."note.id AS 'id',
				".DB_PREFIX."note.title AS 'title',
				".DB_PREFIX."note.note AS 'note',
				".DB_PREFIX."note.secret AS 'secret',
				".DB_PREFIX."note.iduser AS 'iduser',
				".DB_PREFIX."note.deleted AS 'deleted',
				".DB_PREFIX."note.datum as date_created,
				".DB_PREFIX."user.userName AS 'nuser',
                ".DB_PREFIX."note.idtable,
                ".DB_PREFIX."note.iditem
				 FROM ".DB_PREFIX."note, ".DB_PREFIX."user
				 WHERE ".DB_PREFIX."note.id=".$_REQUEST['rid']."
				AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId");
        if ($rec = mysqli_fetch_assoc($res)) {
            if ((($rec['secret'] <= $user['aclSecret']) || $rec['iduser'] == $user['userId']) && !$rec['deleted'] == 1) {
                $latteParameters['title'] = StripSlashes($rec['title']);
                mainMenu(0);
                switch ($_REQUEST['idtable']) {
                    case 1: $sourceurl = "readperson.php?rid=".$rec['iditem']."&hidenotes=0"; $sourcename = "osoby"; break;
                    case 2: $sourceurl = "readgroup.php?rid=".$rec['iditem']."&hidenotes=0"; $sourcename = "skupiny"; break;
                    case 3: $sourceurl = "readcase.php?rid=".$rec['iditem']."&hidenotes=0"; $sourcename = "případy"; break;
                    case 4: $sourceurl = "/reports/".$rec['iditem']; $sourcename = "hlášení"; break;
                    default: $sourceurl = ""; $sourcename = ""; break;
                }
                if ($user['aclUser']) { //TODO NOTE permission
                    $editbutton = '; <a href="editnote.php?rid='.$_REQUEST['rid'].'&amp;idtable='.$_REQUEST['idtable'].'">upravit poznámku</a>';
                } else {
                    $editbutton = '';
                }
                sparklets('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>zobrazení poznámky</strong>', $editbutton);
                echo '<h1>'.StripSlashes($rec['title']).'</h1>
				<h3>'.StripSlashes($rec['nuser']).' ['.webdate($rec['date_created']).']'.'</h3>';
                if ($rec['secret'] == 0) {
                    echo '<h4>veřejná</h4>';
                }
                if ($rec['secret'] == 1) {
                    echo '<h4>tajná</h4>';
                }
                if ($rec['secret'] == 2) {
                    echo '<h4>soukromá</h4>';
                }
                echo '<div id="obsah">'.StripSlashes($rec['note']).'</div>';
            } else {
                $_SESSION['message'] = $text['accessdeniedrecorded'];
                Header('location: index.php');
            }
        } else {
            $_SESSION['message'] = "Poznámka neexistuje!";
            Header('location: index.php');
        }
    } else {
        echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
    }
    latteDrawTemplate("footer");
