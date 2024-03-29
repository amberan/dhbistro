<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');


latteDrawTemplate("header");

$latteParameters['title'] = 'Uložení změn';

// úprava poznámky
if (isset($_POST['noteid'], $_POST['editnote']) &&
!preg_match('/^[[:blank:]]*$/i', $_POST['title']) && !preg_match('/^[[:blank:]]*$/i', $_POST['note']) && is_numeric($_POST['nsecret'])) {
    authorizedAccess($_POST['idtable'], 9, $_POST['itemid']);
    mainMenu();
    switch ($_POST['idtable']) {
        case 1: $sourceurl = "/persons/";
            $sourcename = "osoby";
            break;
        case 2: $sourceurl = "/groups/";
            $sourcename = "skupiny";
            break;
        case 3: $sourceurl = "/cases/";
            $sourcename = "případy";
            break;
        case 4: $sourceurl = "/reports/";
            $sourcename = "hlášení";
            break;
        case 10: $sourceurl = "/symbols/";
            $sourcename = "symboly";
            break;
        default: $sourceurl = "";
            $sourcename = "";
            break;
    }
    if (!isset($_POST['nnotnew'])) {
        unreadRecords($_POST['idtable'], $_POST['itemid']);
    }
    sparklets('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>úprava poznámky</strong> &raquo; <strong>uložení změn</strong>');
    $updateSql = "UPDATE " . DB_PREFIX . "note SET title='" . $_POST['title'] . "', datum='" . Time() . "', note='" . $_POST['note'] . "', secret='" . $_POST['nsecret'] . "', iduser='" . $_POST['nowner'] . "' WHERE id=" . $_POST['noteid'];
    mysqli_query($database, $updateSql);
    echo '<div id="obsah"><p>Poznámka upravena.</p></div>';
    latteDrawTemplate("footer");
} else {
    if (isset($_POST['editnote'])) {
        mainMenu();
        switch ($_REQUEST['idtable']) {
            case 1: $sourceurl = "/persons/";
                $sourcename = "osoby";
                break;
            case 2: $sourceurl = "/groups/";
                $sourcename = "skupiny";
                break;
            case 3: $sourceurl = "/cases/";
                $sourcename = "případy";
                break;
            case 4: $sourceurl = "/reports/";
                $sourcename = "hlášení";
                break;
            case 10: $sourceurl = "/symbols/";
                $sourcename = "symboly";
                break;
            default: $sourceurl = "";
                $sourcename = "";
                break;
        }
        sparklets('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>úprava poznámky</strong> &raquo; <strong>uložení změn</strong>');
        echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
        latteDrawTemplate("footer");
    }
}



// nová poznámka
if (isset($_POST['setnote'])) {
    if (!preg_match('/^[[:blank:]]*$/i', $_POST['note'])) {
        authorizedAccess($_POST['tableid'], 7, $_POST['itemid']);
        // $secret = 0;
        // if (isset($_POST['secret'])) {
        //     $secret = 1;
        // }
        // if (isset($_POST['private'])) {
        //     $secret = 2;
        // }
        $createSql = "INSERT INTO ".DB_PREFIX."note (note, title, datum, iduser, idtable, iditem, secret, deleted)
            VALUES('".$_POST['note']."','".$_POST['title']."','".Time()."','".$user['userId']."','".$_POST['tableid']."','".$_POST['itemid']."',".$_POST['secret'].",0)";
        mysqli_query($database, $createSql);
        $_SESSION['message'] = "Poznámka uložena";
        if (!isset($_POST['nnotnew'])) {
            unreadRecords($_POST['tableid'], $_POST['itemid']);
        }
    }
    Header('Location: '.$_POST['backurl']);
}

// vymazání poznámky
if (isset($_GET['deletenote'])) {
    mysqli_query($database, "UPDATE ".DB_PREFIX."note SET deleted=1 WHERE ".DB_PREFIX."note.id=".$_GET['deletenote']);
    Header('Location: '.URLDecode($_GET['backurl']));
}
