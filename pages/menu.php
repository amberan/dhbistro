<?php

$menu[] = [$text['aktuality'], "/", searchTable(5)];
$menu[] = [$text['nastenka'], "/board/", searchTable(6)];
$menu[] = [$text['hlaseni'], "/reports/", searchTable(4)];
$menu[] = [$text['osoby'], "/persons/", searchTable(1) + searchTable(7)];
$menu[] = [$text['pripady'], "/cases/", searchTable(3)];
$menu[] = [$text['skupiny'], "/groups/", searchTable(2)];

$menuSub[] = [$text['menu-zlobody'], "/evilpoints.php", 0];
if (isset($user) && $user['aclUser'] > 0) {
    $menuSub[] = [$text['spravauzivatelu'], "/users", 0];
}
if (isset($user) && $user['aclUser'] > 0) {
    $menuSub[] = [$text['casovadostupnost'], "/doodle.php", 0];
} else {
    $doodle = mysqli_fetch_assoc(mysqli_query($database, "SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    $menuSub[] = [$text['casovadostupnost'], @$doodle['link'], 0];
}
if (isset($user) && $user['aclAudit'] > 0) {
    $menuSub[] = [$text['audit'], "/audit.php", 0];
}
$menuSub[] = [$text['nastaveni'], "/settings", 0];
if (isset($user) && $user['aclRoot'] > 0) {
    $menuSub[] = [$text['zalohovani'], '/backup', 0];
}
$menuSub[] = [$text['odhlasit'], "/logout", 0];


$menuLinks[] = [$text['forum'],"http://www.prazskahlidka.cz/forums/index.php",0];
$menuLinks[] = [$text['banka'],"http://banka.alembiq.net",0];
$menuLinks[] = [$text['prazskahlidka'],"http://prazskahlidka.cz",0];
