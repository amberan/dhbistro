<?php

$menu[] = [$text['menuNews'], "/", unreadItems(5)];
$menu[] = [$text['menuDashboard'], "/board/", unreadItems(6)];
$menu[] = [$text['menuReports'], "/reports/", unreadItems(4)];
$menu[] = [$text['menuPersons'], "/persons/", unreadItems(1)];
$menu[] = [$text['menuSymbols'], "/symbols/", unreadItems(7)];
$menu[] = [$text['menuCases'], "/cases/", unreadItems(3)];
$menu[] = [$text['menuGroups'], "/groups/", unreadItems(2)];

$menuSub[] = [$text['menuPoints'], "/evilpoints.php", 0];
if (isset($user) && $user['aclUser'] > 0) {
    $menuSub[] = [$text['menuUsers'], "/users", 0];
}
if (isset($user) && $user['aclUser'] > 0) {
    $menuSub[] = [$text['menuDoodle'], "/doodle.php", 0];
} else {
    $doodle = mysqli_fetch_assoc(mysqli_query($database, "SELECT link FROM " . DB_PREFIX . "doodle ORDER BY id desc LIMIT 0,1"));
    $menuSub[] = [$text['menuDoodle'], @$doodle['link'], 0];
}
if (isset($user) && $user['aclAudit'] > 0) {
    $menuSub[] = [$text['menuAudit'], "/audit.php", 0];
}
$menuSub[] = [$text['menuSettings'], "/settings", 0];
if (isset($user) && $user['aclRoot'] > 0) {
    $menuSub[] = [$text['menuBackups'], '/backup', 0];
}
$menuSub[] = [$text['menuLogout'], "/logout", 0];

$menuLinks[] = [$text['menuForum'], "http://www.prazskahlidka.cz/forums/index.php", 0, "images/icons/Icon_forum.svg"];
$menuLinks[] = [$text['menuBank'], "http://banka.prazskahlidka.cz", 0, "images/icons/Icon_bank.svg"];
$menuLinks[] = [$text['menuGameWebsite'], "http://prazskahlidka.cz", 0, "images/icons/Icon_web.svg"];
