<?php

$menu[] = [$text['aktuality'], "/", searchTable(5) + searchTable(6)];
$menu[] = [$text['hlaseniV'], "/reports.php", searchTable(4)];
$menu[] = [$text['osoby'], "/persons.php", searchTable(1) + searchTable(7)];
$menu[] = [$text['pripady'], "/cases/", searchTable(3)];
$menu[] = [$text['skupiny'], "/groups.php", searchTable(2)];
$menu2[] = [$text['forum'], "http://www.prazskahlidka.cz/forums/", 0];
$menu2[] = [$text['menu-zlobody'], "/evilpoints.php", 0];
if (isset($user) and $user['aclTask'] > 0) {
    $menu2[] = [$text['ukoly'], "/tasks.php", 0];
}
if (isset($user) and $user['aclDirector'] > 0) {
    $menu2[] = [$text['spravauzivatelu'], "/users", 0];
}
if (isset($user) and ($user['aclDeputy'] > 0 or $user['aclDirector'] > 0)) {
    $menu2[] = [$text['casovadostupnost'], "/doodle.php", 0];
} elseif (isset($user)) {
    $doodle = mysqli_fetch_assoc(mysqli_query($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    $menu2[] = [$text['casovadostupnost'], $doodle['link'], 0];
}
if (isset($user) and $user['aclAudit'] > 0) {
    $menu2[] = [$text['audit'], "/audit.php", 0];
}
$menu2[] = [$text['nastaveni'], "/settings", 0];
if (isset($user) and $user['aclRoot'] > 0) {
    $menu2[] = [$text['zalohovani'], '/backup', 0];
}
$menu2[] = [$text['odhlasit'], "/logout", 0];
