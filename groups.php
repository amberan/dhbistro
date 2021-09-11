<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Skupiny';

   if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
       auditTrail(2, 11, $_GET['delete']);
       mysqli_query($database,"UPDATE ".DB_PREFIX."group SET deleted=1 WHERE id=".$_GET['delete']);
       deleteAllUnread(2,$_GET['delete']);
   }
   if (isset($_GET['undelete']) && is_numeric($_GET['undelete'])) {
       auditTrail(2, 11, $_GET['delete']);
       mysqli_query($database,"UPDATE ".DB_PREFIX."group SET deleted=0 WHERE id=".$_GET['undelete']);
   }
    if (isset($_GET['archive']) && is_numeric($_GET['archive'])) {
        auditTrail(2, 2, $_GET['archive']);
        mysqli_query($database,"UPDATE ".DB_PREFIX."group SET archived=1 WHERE id=".$_GET['archive']);
    }
    if (isset($_GET['dearchive']) && is_numeric($_GET['dearchive'])) {
        auditTrail(2, 2, $_GET['dearchive']);
        mysqli_query($database,"UPDATE ".DB_PREFIX."group SET archived=0 WHERE id=".$_GET['dearchive']);
    }

        if (isset($_POST['insertgroup']) && !preg_match('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
            $ures = mysqli_query($database,"SELECT id FROM ".DB_PREFIX."group WHERE UCASE(title)=UCASE('".$_POST['title']."')");
            if (mysqli_num_rows($ures)) {
                echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
            } else {
                mysqli_query($database,"INSERT INTO ".DB_PREFIX."group ( title, contents, datum, iduser, deleted, secret, archived, groupCreated) VALUES('".$_POST['title']."','".$_POST['contents']."','".time()."','".$user['userId']."','0','".$_POST['secret']."',0,CURRENT_TIMESTAMP)");
                $gidarray = mysqli_fetch_assoc(mysqli_query($database,"SELECT id FROM ".DB_PREFIX."group WHERE UCASE(title)=UCASE('".$_POST['title']."')"));
                $gid = $gidarray['id'];
                auditTrail(2, 3, $gid);
                if (!isset($_POST['notnew'])) {
                    unreadRecords(2,$gid);
                }
                echo '<div id="obsah"><p>Skupina vytvořena.</p></div>';
            }
        } else {
            if (isset($_POST['insertgroup'])) {
                echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
            }
        }
    if (isset($_POST['groupid'], $_POST['editgroup']) && $usrinfo['right_text'] && !preg_match('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match('/i^[[:blank:]]*$/i',$_POST['contents'])) {
        auditTrail(2, 2, $_POST['groupid']);
        $ures = mysqli_query($database,"SELECT id FROM ".DB_PREFIX."group WHERE UCASE(title)=UCASE('".$_POST['title']."') AND id<>".$_POST['groupid']);
        if (mysqli_num_rows($ures)) {
            echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
        } else {
            $sqlGroupUpdate = "UPDATE ".DB_PREFIX."group SET datum='".time()."', title='".$_POST['title']."', contents='".$_POST['contents']."', archived='".(isset($_POST['archived']) ? '1' : '0')."', secret='".(isset($_POST['secret']) ? '1' : '0')."' WHERE id=".$_POST['groupid'];
            mysqli_query($database,$sqlGroupUpdate);
            if (!isset($_POST['notnew'])) {
                unreadRecords(2,$_POST['groupid']);
            }
            $_SESSION['message'] = 'Skupina upravena.';
        }
    } else {
        if (isset($_POST['editgroup'])) {
            $latteParameters['title'] = 'Uložení změn';
            echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
        }
    }
    if (isset($_POST['setperson'])) {
        header('Location: editgroup.php?rid='.$_POST['groupid']);
    }
    if (isset($_GET['delperson']) && is_numeric($_GET['delperson'])) {
        header('Location: editgroup.php?rid='.$_GET['groupid']);
    }
    if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['groupid']) && is_numeric($_POST['secret'])) {
        auditTrail(2, 4, $_POST['groupid']);
        $newname = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['attachment']['tmp_name'],'./files/'.$newname);
        $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".time()."','".$user['userId']."','2','".$_POST['groupid']."','".$_POST['secret']."')";
        mysqli_query($database,$sql);
        if (!isset($_POST['fnotnew'])) {
            unreadRecords(2,$_POST['groupid']);
        }
        header('Location: '.$_POST['backurl']);
    } else {
        if (isset($_POST['uploadfile'])) {
            echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
            header('Location: '.$_POST['backurl']);
        }
    }
    if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
        auditTrail(2, 5, $_GET['groupid']);
        if ($usrinfo['right_text']) {
            $fres = mysqli_query($database,"SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
            $frec = mysqli_fetch_assoc($fres);
            unlink('./files/'.$frec['uniquename']);
            mysqli_query($database,"DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        }
        header('Location: editgroup.php?rid='.$_GET['groupid']);
    }

auditTrail(3, 1, 0);
mainMenu();

sparklets('<strong>skupiny</strong>','<a href="newgroup.php">přidat skupinu</a>');

if (isset($_GET['sort'])) {
    sortingSet('group',$_GET['sort'],'group');
}
if (isset($_POST['filter'])) {
    filterSet('group',@$_POST['filter']);
}
$filter = filterGet('group');
$sqlFilter = DB_PREFIX."group.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."group.secret<=".$user['aclSecret'];
switch (@$filter['archived']) {
    case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'group.archived in (0,1)'; break;
    default: $sqlFilter .= ' AND '.DB_PREFIX.'group.archived=0 ';
}
if (@$filter['new']) {
    $sqlFilter .= ' AND '.DB_PREFIX.'unread.id is not null ';
}
$latteParameters['filter'] = $filter;

?>


<form action="/groups.php" method="POST" id="filter">
<input type="hidden" name="filter[placeholder]"  />
<input type="checkbox" name="filter[archived]" <?php if (isset($filter['archived']) and $filter['archived'] == 'on') {
    echo " checked";
}?> onchange="this.form.submit()"/><?php echo $text['iarchiv']; ?>
<input type="checkbox" name="filter[new]" <?php if (isset($filter['new']) and $filter['new'] == 'on') {
    echo " checked";
}?> onchange="this.form.submit()"/><?php echo $text['jennove']; ?>
</form>



<?php

    $sql = "SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title , ".DB_PREFIX."group.id , ".DB_PREFIX."group.archived , ".DB_PREFIX."group.datum as groupEdited, ".DB_PREFIX."group.groupCreated, ".DB_PREFIX."group.deleted,  ".DB_PREFIX."unread.id as unread
    FROM ".DB_PREFIX."group
    LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."group.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 2 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    WHERE ".$sqlFilter.sortingGet('group');
    $res = mysqli_query($database,$sql);
    if (mysqli_num_rows($res)) {
        echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název <a href="groups.php?sort=title">&#8661;</a></th>
      <th>Status</th>
      <th>Vytvoreno <a href="groups.php?sort=groupCreated">&#8661;</a></th>
      <th>Zmeneno <a href="groups.php?sort=datum">&#8661;</a></th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            if (@$filter['new'] == 0 || (isset($filter['new']) && $filter['new'] == 'on' && searchRecord(2,$rec['id']))) { //TODO fNew = filter NEW
                echo '<tr class="'.(searchRecord(2,$rec['id']) ? ' unread_record' : ($even % 2 == 0 ? 'even' : 'odd')).'">
                        <td>'.($rec['secret'] ? '<span class="secret"><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.stripslashes($rec['title']).'</a></span>' : '<a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.stripslashes($rec['title']).'</a>').'</td>';
                echo '<td>';
                if ($rec['deleted']) {
                    echo $text['smazany'];
                }
                if ($rec['secret']) {
                    echo $text['utajeno'];
                }
                if ($rec['archived']) {
                    echo $text['archivovano'];
                }
                echo '</td><td>';
                if ($rec['groupCreated']) {
                    echo $rec['groupCreated'];
                } else {
                    echo $text['neznamo'];
                }
                echo '</td><td>'.webDateTime($rec['groupEdited']).'</td>';
                if ($usrinfo['right_text']) {
                    echo '<td><a href="editgroup.php?rid='.$rec['id'].'">upravit</a> | ';
                    if ($rec['archived']) {
                        echo '<a href="groups.php?dearchive='.$rec['id'].'" onclick="'."return confirm('Opravdu vyjmout z archivu skupinu &quot;".stripslashes($rec['title'])."&quot;?');".'">z archivu</a>';
                    } else {
                        echo '<a href="groups.php?archive='.$rec['id'].'" onclick="'."return confirm('Opravdu archivovat skupinu &quot;".stripslashes($rec['title'])."&quot;?');".'">archivovat</a>';
                    }
                    echo ' | ';
                    if ($rec['deleted'] && $user['aclRoot']) {
                        echo '<a href="groups.php?undelete='.$rec['id'].'" onclick="'."return confirm('Opravdu obnovit smazanou skupinu &quot;".stripslashes($rec['title'])."&quot;?');".'">obnovit</a>';
                    } elseif ($rec['deleted'] == 0) {
                        echo '<a href="groups.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat skupinu &quot;".stripslashes($rec['title'])."&quot;?');".'">smazat</a>';
                    }
                    echo ' | <a href="newnote.php?rid='.$rec['id'].'&idtable=6">přidat poznámku</a></td>';
                }
                echo '</tr>';

                $even++;
            }
        }
        echo '</tbody>
</table>
</div>
';
    } else {
        echo '<div id="obsah"><p>Žádné skupiny neodpovídají výběru.</p></div>';
    }
latteDrawTemplate("footer");
