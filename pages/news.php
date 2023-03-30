<?php

if (isset($URL[1],$URL[2],$URL[3]) && $URL['1'] == 'news' && is_numeric($URL[2])) {
    if ($user['aclNews'] || $user['aclRoot']) {
        if ($URL[2] == 0 && isset($_POST['newsTitle'], $_POST['newsBody']) && !preg_match('/^[[:blank:]]*$/i', $_POST['newsTitle']) && !preg_match('/^[[:blank:]]*$/i', $_POST['newsBody'])) {
            $latteParameters['message'] = newsAdd($_POST['newsTitle'], $_POST['newsBody'], $_POST['newsCategory']);
        } elseif ($URL[3] == 'edit' && isset($_POST['newsTitle'],$_POST['newsBody']) && !preg_match('/^[[:blank:]]*$/i', $_POST['newsTitle']) && !preg_match('/^[[:blank:]]*$/i', $_POST['newsBody'])) {
            $latteParameters['message'] = newsEdit($_POST['newsTitle'], $_POST['newsBody'], $_POST['newsCategory']);
            $latteParameters['newsEdit'] = newsRead($URL[2]);
        } elseif ($URL[3] == 'delete') {
            $latteParameters['message'] = newsDelete($URL[2]);
        } elseif ($URL[3] == 'restore') {
            $latteParameters['message'] = newsRestore($URL[2]);
        }
        if ($URL[3] == 'new') {
            $latteParameters['subtitle'] = $text['subtitleNewsAdd'];
        } elseif ($URL[3] == 'edit') {
            $latteParameters['subtitle'] = $text['subtitleNewsEdit'];
        }
        if ($URL[2] > 0) {
            $latteParameters['newsEdit'] = newsRead($URL[2]);
        }
        latteDrawTemplate('sparklet');
        latteDrawTemplate('news_edit');
    } else {
        $latteParameters['message'] = $text['notificationHttp401'];
        unauthorizedAccess('news', 'unauthorizedAccess', $URL[2]);
    }
} else {
    $latteParameters['news_array'] = newsList();
    latteDrawTemplate('sparklet');
    latteDrawTemplate('dashboard');
    latteDrawTemplate('news');
}
