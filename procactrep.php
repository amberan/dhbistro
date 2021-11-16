<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Zobrazení symbolu';


if (isset($_POST['reportid'])) {
    $autharray = mysqli_fetch_assoc(mysqli_query($database, "SELECT iduser FROM ".DB_PREFIX."report WHERE id=".$_POST['reportid']));
    $author = $autharray['iduser'];
}
    if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
        auditTrail(4, 11, $_REQUEST['delete']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."report SET deleted=1 WHERE id=".$_REQUEST['delete']);
        deleteAllUnread($_REQUEST['table'], $_REQUEST['delete']);
        Header('Location: reports.php');
    }
    if (isset($_POST['insertrep']) && !preg_match('/^[[:blank:]]*$/i', $_POST['label']) && !preg_match('/^[[:blank:]]*$/i', $_POST['task']) && !preg_match('/^[[:blank:]]*$/i', $_POST['summary']) && !preg_match('/^[[:blank:]]*$/i', $_POST['impact']) && !preg_match('/^[[:blank:]]*$/i', $_POST['details']) && !preg_match('/^[[:blank:]]*$/i', $_POST['start']) && !preg_match('/^[[:blank:]]*$/i', $_POST['end']) && !preg_match('/^[[:blank:]]*$/i', $_POST['energy']) && !preg_match('/^[[:blank:]]*$/i', $_POST['inputs']) && is_numeric($_POST['secret']) && is_numeric($_POST['status']) && is_numeric($_POST['type'])) {
        $adatum = mktime(0, 0, 0, $_POST['adatummonth'], $_POST['adatumday'], $_POST['adatumyear']);
        $ures = mysqli_query($database, "SELECT id FROM ".DB_PREFIX."report WHERE UCASE(label)=UCASE('".$_POST['label']."')");
        if (mysqli_num_rows($ures)) {
            $latteParameters['title'] = 'Hlášení nepřidáno';


            mainMenu();
            sparklets('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
            echo '<div id="obsah"><p>Toto označení hlášení již existuje, změňte ho.</p></div>';
        } else {
            $latteParameters['title'] = 'Hlášení uloženo';


            mainMenu();
            mysqli_query($database, "INSERT INTO ".DB_PREFIX."report (label, datum, iduser, task, summary, impacts, details, secret, deleted, status, type, adatum, start, end, energy, inputs) VALUES('".$_POST['label']."','".Time()."','".$user['userId']."','".$_POST['task']."','".$_POST['summary']."','".$_POST['impact']."','".$_POST['details']."','".$_POST['secret']."','0','".$_POST['status']."','".$_POST['type']."','".$adatum."','".$_POST['start']."','".$_POST['end']."','".$_POST['energy']."','".$_POST['inputs']."')");
            $ridarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."report WHERE UCASE(label)=UCASE('".$_POST['label']."')"));
            $rid = $ridarray['id'];
            auditTrail(4, 3, $rid);
            if ($_POST['status'] <> 0) {
                unreadRecords(4, $rid);
            }
            sparklets('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení uloženo</strong>', '<a href="readactrep.php?rid='.$rid.'&hidenotes=0&truenames=0">zobrazit uložené</a>');
            echo '<div id="obsah"><p>Hlášení uloženo.</p></div>
			<hr />
			<form action="addp2ar.php" method="post" class="otherform">
			<div>
			<input type="hidden" name="rid" value="'.$rid.'" />
			<input type="submit" value="Přidat k hlášení přítomné osoby" name="setperson" class="submitbutton" />
			</div>
			</form>';
        }
        latteDrawTemplate("footer");
    } else {
        if (isset($_POST['insertrep'])) {
            $latteParameters['title'] = 'Hlášení nepřidáno!!!';


            mainMenu();
            sparklets('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
            echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva. Pamatujte, že všechna pole musí být vyplněná.</p></div>';
            latteDrawTemplate("footer");
        }
    }
    if (isset($_POST['reportid'], $_POST['editactrep']) && ($user['aclReport'] || $user['userId'] == $author) && !preg_match('/^[[:blank:]]*$/i', $_POST['label']) && !preg_match('/^[[:blank:]]*$/i', $_POST['task']) && !preg_match('/^[[:blank:]]*$/i', $_POST['summary']) && !preg_match('/^[[:blank:]]*$/i', $_POST['impacts']) && !preg_match('/^[[:blank:]]*$/i', $_POST['details']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
        auditTrail(4, 2, $_POST['reportid']);
        $latteParameters['title'] = 'Uložení změn';


        mainMenu();
        if ($_POST['status'] <> 0) {
            unreadRecords(4, $_POST['reportid']);
        }
        sparklets('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>', '<a href="readactrep.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
        $adatum = mktime(0, 0, 0, $_POST['adatummonth'], $_POST['adatumday'], $_POST['adatumyear']);
        $ures = mysqli_query($database, "SELECT id FROM ".DB_PREFIX."report WHERE UCASE(label)=UCASE('".$_POST['label']."') AND id<>".$_POST['reportid']);
        if (mysqli_num_rows($ures)) {
            echo '<div id="obsah"><p>Toto označení již existuje, změňte ho.</p></div>';
        } else {
            mysqli_query($database, "UPDATE ".DB_PREFIX."report SET label='".$_POST['label']."', task='".$_POST['task']."', summary='".$_POST['summary']."', impacts='".$_POST['impacts']."', details='".$_POST['details']."', secret='".$_POST['secret']."', status='".$_POST['status']."', adatum='".$adatum."', start='".$_POST['start']."', end='".$_POST['end']."', energy='".$_POST['energy']."', inputs='".$_POST['inputs']."' WHERE id=".$_POST['reportid']);
            echo '<div id="obsah"><p>Hlášení upraveno.</p></div>';
        }
        latteDrawTemplate("footer");
    } else {
        if (isset($_POST['editactrep'])) {
            $latteParameters['title'] = 'Uložení změn';


            mainMenu();
            sparklets('<a href="/cases/">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn neúspěšné</strong>');
            echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva. Pamatujte, že žádné pole nesmí být prázdné.</p></div>';
            latteDrawTemplate("footer");
        }
    }
    if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['reportid']) && is_numeric($_POST['secret'])) {
        auditTrail(4, 4, $_POST['reportid']);
        $newname = Time().MD5(uniqid(Time().Rand()));
        move_uploaded_file($_FILES['attachment']['tmp_name'], './files/'.$newname);
        $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".Time()."','".$user['userId']."','4','".$_POST['reportid']."','".$_POST['secret']."')";
        mysqli_query($database, $sql);
        unreadRecords(4, $_POST['reportid']);
        Header('Location: '.$_POST['backurl']);
    } else {
        if (isset($_POST['uploadfile'])) {
            $latteParameters['title'] = 'Přiložení souboru';


            mainMenu();
            sparklets('<a href="/cases/">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>přiložení souboru</strong>');
            echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
            latteDrawTemplate("footer");
        }
    }
    if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
        auditTrail(4, 5, $_GET['reportid']);
        if ($user['aclReport']) {
            $fres = mysqli_query($database, "SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
            $frec = mysqli_fetch_assoc($fres);
            UnLink('./files/'.$frec['uniquename']);
            mysqli_query($database, "DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        }
        Header('Location: editactrep.php?rid='.$_GET['reportid']);
    }
