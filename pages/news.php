<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

if (isset($URL['3']) and $URL['1'] == "news" and ($user['aclNews'] > 0) and $URL['2'] == "delete") { // DELETE
    mysqli_query($database, "UPDATE ".DB_PREFIX."news set deleted=1 where id='".$URL['3']."'");
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess(5, 11, $URL['3']);
        $latteParameters['message'] = $text['aktualitaodebrana'];
    } else {
        $latteParameters['message'] = $text['aktualitaneodebrana'];
    }
} elseif (isset($_GET['newsdelete'])) {
    $latteParameters['message'] = $text['http401'];
    unauthorizedAccess(5, 11, $URL[3]);
}

if (isset($URL['3']) and $URL['1'] == "news" and ($user['aclNews'] > 0) and $URL['2'] == "restore") { // DELETE
    mysqli_query($database, "UPDATE ".DB_PREFIX."news set deleted=0 where id='".$URL['3']."'");
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess(5, 11, $URL['3']);
        $latteParameters['message'] = $text['aktualitaobnovena'];
    } else {
        $latteParameters['message'] = $text['aktualitaneobnovena'];
    }
} elseif (isset($_GET['newsdelete'])) {
    $latteParameters['message'] = $text['http401'];
    unauthorizedAccess(5, 11, $URL[3]);
}

if ($URL['1'] == "news" and ($user['aclNews'] > 0) and isset($_POST['news_new'])) { // ADD
    if ($_POST['insertnews'] && !preg_match('/^[[:blank:]]*$/i', $_POST['nadpis']) && !preg_match('/^[[:blank:]]*$/i', $_POST['news_new']) && is_numeric($_POST['kategorie'])) {
        mysqli_query($database, "INSERT INTO ".DB_PREFIX."news ( datum, iduser, kategorie, nadpis, obsahMD, deleted) VALUES('".time()."','".$user['userId']."','".$_POST['kategorie']."','".$_POST['nadpis']."','".$_POST['news_new']."',0)");
        if (mysqli_affected_rows($database) == 1) {
            authorizedAccess(5, 3, 0);
            $latteParameters['message'] = $text['aktualitavlozena'];
            unreadRecords(5, 0);
        } else {
            $latteParameters['message'] = $text['aktualitanevlozena'];
        }
    } else {
        $latteParameters['message'] = $text['nevytvoreno'];
    }
}

deleteUnread(5, 0);
$sql_news = "SELECT ".DB_PREFIX."news.* , ".DB_PREFIX."user.userName AS 'author'
FROM ".DB_PREFIX."news JOIN ".DB_PREFIX."user ON ".DB_PREFIX."news.iduser = ".DB_PREFIX."user.userId
WHERE ".$user['sqlDeleted']." ORDER BY ".DB_PREFIX."news.datum DESC LIMIT 10";
$news_query = mysqli_query($database, $sql_news);
if (mysqli_num_rows($news_query)) {
    while ($news_record = mysqli_fetch_assoc($news_query)) {
        if (isset($news_record['obsahMD']) && strlen($news_record['obsahMD']) > 0) {
            $news_record['datum'] = webdatetime($news_record['datum']);
            //$news_record['id'] = $news_record['id'];
            //$news_record['nadpis'] = $news_record['nadpis'];
//            $news_record['obsahMD'] = $converter->convertToHtml($news_record['obsahMD']);
            $news_record['category'] = $news_record['kategorie'];
            //$news_record['author'] = $news_record['author'];
            //$news_record['deleted'] = $news_record['deleted'];
            $news_array[] = $news_record;
        }
    }
    $latteParameters['news_array'] = $news_array;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('dashboard');
latteDrawTemplate('news');
