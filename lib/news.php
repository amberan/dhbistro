<?php

function newsRead($newsId)
{
    global $database, $user,$text;
    if (!isset($user['aclRoot'])) {
        $sqlwhere = " AND deleted = 0";
    }
    $sql = 'SELECT ' . DB_PREFIX . 'news.* FROM ' . DB_PREFIX . 'news
        WHERE ' . DB_PREFIX . 'news.id=' . $newsId . @$sqlwhere;
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        $news = mysqli_fetch_assoc($query);
        $news['newsTitle'] = $news['nadpis'];
        $news['newsBody'] = stripslashes($news['obsahMD']);
        $news['newsCreated'] = webDateTime($news['datum']);
        $news['category'] = $news['kategorie'];
        $news['newsCreatedBy'] = AuthorDB($news['iduser']);
    } else {
        $news = $text['notificationRecordNotFound'];
    }
    return $news;
}

function newsList()
{
    global $database, $user;
    if (!isset($user['aclRoot'])) {
        $sqlwhere = " AND deleted = 0";
    }
    $sql = 'SELECT ' . DB_PREFIX . 'news.* FROM ' . DB_PREFIX . 'news
        WHERE 1' . @$sqlwhere . '
        ORDER BY datum desc';
    $query = mysqli_query($database, $sql);
    $newsList = [];
    if (mysqli_num_rows($query) > 0) {
        while ($news = mysqli_fetch_assoc($query)) {
            $news['newsTitle'] = $news['nadpis'];
            $news['newsBody'] = stripslashes($news['obsahMD']);
            $news['newsCreated'] = webDateTime($news['datum']);
            $news['category'] = $news['kategorie'];
            $news['newsCreatedBy'] = AuthorDB($news['iduser']);
            $newsList[] = $news;
            deleteUnread('news', $news['id']);
        }
        return $newsList;
    }
}

function newsDelete($newsId)
{
    global $database,$text,$URL;
    $sqlNewsDelete = 'UPDATE ' . DB_PREFIX . 'news set deleted=1 where id=' . $newsId;
    mysqli_query($database, $sqlNewsDelete);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess('news', 'delete', $newsId);
        unset($newsId, $URL[3]);
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

function newsAdd($title, $body, $category)
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

function newsEdit($title, $body, $category)
{
    global $database,$text,$URL,$user;
    $sqlNewsEdit = 'UPDATE ' . DB_PREFIX . 'news SET nadpis="' . $title . '", obsahMD="' . $body . '", kategorie ="' . $category . '", iduser=' . $user['userId'] .'
                        WHERE id=' . $URL[2];
    mysqli_query($database, $sqlNewsEdit);
    if (mysqli_affected_rows($database) == 1) {
        authorizedAccess('news', 'delete', $URL[2]);
        unreadRecords(5, $URL[2]);
        return $text['notificationUpdated'];
    } else {
        return $text['notificationNotUpdated'];
    }
}
