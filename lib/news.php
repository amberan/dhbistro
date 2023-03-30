<?php


function newsRead($newsId)
{
    global $database, $user,$text;
    if (!isset($user['aclRoot'])) {
        $sqlwhere = " AND deleted = 0";
    }
    $sql = 'SELECT '.DB_PREFIX.'news.*, '.DB_PREFIX.'user.userName, concat( '.DB_PREFIX.'person.name, " ", '.DB_PREFIX.'person.surname) as person FROM '.DB_PREFIX.'news
        left join '.DB_PREFIX.'user on '.DB_PREFIX.'news.iduser = '.DB_PREFIX.'user.userId
        left join '.DB_PREFIX.'person on '.DB_PREFIX.'user.personId = '.DB_PREFIX.'person.id
        WHERE '.DB_PREFIX.'news.id='.$newsId.@$sqlwhere;
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        $news = mysqli_fetch_assoc($query);
        $news['newsTitle'] = $news['nadpis'];
        $news['newsBody'] = stripslashes($news['obsahMD']);
        $news['newsCreated'] = webDateTime($news['datum']);
        $news['category'] = $news['kategorie'];
        $news['newsCreatedBy'] = Author($news['userName'],$news['person']);
    } else {
        $news = $text['notificationRecordNotFound'];
    }
    return $news;
}

function newsList()
{
    global $database, $user,$text;
    if (!isset($user['aclRoot'])) {
        $sqlwhere = " AND deleted = 0";
    }
    $sql = 'SELECT '.DB_PREFIX.'news.*, '.DB_PREFIX.'user.userName, concat( '.DB_PREFIX.'person.name, " ", '.DB_PREFIX.'person.surname) as person FROM '.DB_PREFIX.'news
        left join '.DB_PREFIX.'user on '.DB_PREFIX.'news.iduser = '.DB_PREFIX.'user.userId
        left join '.DB_PREFIX.'person on '.DB_PREFIX.'user.personId = '.DB_PREFIX.'person.id
        WHERE 1'.@$sqlwhere.'
        ORDER BY datum desc';
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        while ($news = mysqli_fetch_assoc($query)) {
            $news['newsTitle'] = $news['nadpis'];
            $news['newsBody'] = stripslashes($news['obsahMD']);
            $news['newsCreated'] = webDateTime($news['datum']);
            $news['category'] = $news['kategorie'];
            $news['newsCreatedBy'] = Author($news['userName'],$news['person']);
            $newsList[] = $news;
            deleteUnread(5, $news['id']);
        }
        return $newsList;
    }
}

function newsDelete($newsId)
{
    global $database,$text,$URL;
    $sqlNewsDelete = 'UPDATE ' . DB_PREFIX . 'news set deleted=1 where id=' . $URL[2];
    mysqli_query($database, $sqlNewsDelete);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess('news', 'delete', $URL[2]);
        unset($URL[2], $URL[3]);
        return $text['notificationDeleted'];
    } else {
        return $text['notificationNotDeleted'];
    }
}

function newsRestore($newsId)
{
    global $database,$text,$URL;
    $sqlNewsRestore = 'UPDATE ' . DB_PREFIX . 'news set deleted=0 where id=' . $URL[2];
    mysqli_query($database, $sqlNewsRestore);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess('news', 'restore', $URL[2]);
        return $text['notificationRestored'];
    } else {
        return $text['notificationNotRestored'];
    }
}

function newsAdd($title,$body,$category)
{
    global $database,$text,$URL,$user;
    $sqlNewsCreate = "INSERT INTO " . DB_PREFIX . "news ( datum, iduser, kategorie, nadpis, obsahMD, deleted)
                                VALUES('" . time() . "','" . $user['userId'] . "','" . $category . "','" . $title . "','" . $body . "',0)";
    mysqli_query($database, $sqlNewsCreate);
    $URL[2] = mysqli_insert_id($database);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess('news', 'new', $URL[2]);
        unreadRecords(5, $URL[2]);
        return $text['notificationCreated'];
    } else {
        return $text['notificationNotCreated'];
    }
}

function newsEdit($title,$body,$category)
{
    global $database,$text,$URL,$user;
    $sqlNewsEdit = 'UPDATE ' . DB_PREFIX . 'news SET nadpis="' . $title . '", obsahMD="' . $body . '", kategorie ="' . $category . '"
                        WHERE id=' . $URL[2];
    mysqli_query($database, $sqlNewsEdit);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess(5, 11, $URL[2]);
        unreadRecords(5, $URL[2]);
        return $text['notificationUpdated'];
    } else {
        return $text['notificationNotUpdated'];
    }
}
