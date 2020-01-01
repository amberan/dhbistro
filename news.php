<?php   
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);


if (($URL['1']) == "news" AND ($usrinfo['right_power']>0 AND ($URL['2'] == "delete")) AND isset($URL['3']) ) { // DELETE
    mysqli_query ($database,"UPDATE ".DB_PREFIX."news set deleted=1 where id='".$URL['3']."'");
    if (mysqli_affected_rows($database) == 1) {
        auditTrail(5, 11, $URL['3']);
        Debugger::log("NEWS DELETED");
        $latteParameters['message'] = $text['aktualitaodebrana'];
    } else {
        $latteParameters['message'] = $text['aktualitaneodebrana'];
    }
} elseif (isset($_GET['newsdelete'])) {
    $latteParameters['message'] = $text['http401'];
    unauthorizedAccess(5, 0, 0, $URL[3]);
}

if ($URL['1'] == "news" AND $usrinfo['right_power']>0 AND isset($_POST['news_new'])) { // ADD  
    if ($_POST['insertnews'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['nadpis']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['news_new']) && is_numeric($_POST['kategorie'])) {
        mysqli_query ($database,"INSERT INTO ".DB_PREFIX."news ( datum, iduser, kategorie, nadpis, obsah, obsah_md, deleted) VALUES('".Time()."','".$usrinfo['id']."','".$_POST['kategorie']."','".$_POST['nadpis']."','','".$_POST['news_new']."',0)");
        if (mysqli_affected_rows($database) == 1) {
            auditTrail(5, 3, 0);
            Debugger::log("NEWS INSERTED");
            $latteParameters['message'] = $text['aktualitavlozena'];
            unreadRecords (5,0);
        } else {
            $latteParameters['message'] = $text['aktualitanevlozena'];
        }
    } else {
        $latteParameters['message'] = $text['neytvoreno'];
    }
}

deleteUnread (5,0);
$sql_news="SELECT ".DB_PREFIX."news.* , ".DB_PREFIX."user.login AS 'author'
FROM ".DB_PREFIX."news JOIN ".DB_PREFIX."user ON ".DB_PREFIX."news.iduser = ".DB_PREFIX."user.id
WHERE ".DB_PREFIX."news.deleted = 0 ORDER BY ".DB_PREFIX."news.datum DESC LIMIT 10";
$news_query = mysqli_query ($database,$sql_news);
if (mysqli_num_rows ($news_query)) {
	while ($news_record=mysqli_fetch_assoc($news_query)) { 	
        $news_record['datum'] = webdatetime($news_record['datum']);
        $news_record['id'] = $news_record['id'];
        $news_record['nadpis'] = $news_record['nadpis'];
        $news_record['obsah_md'] = $news_record['obsah_md'];
        $news_record['category'] = $news_record['kategorie'];
        $news_record['author'] = $news_record['author'];
        $news_array[] = $news_record;
    }
    $latteParameters['news_array'] = $news_array;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'dashboard.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'news.latte', $latteParameters);


?>
