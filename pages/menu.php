<?php

$menu[] = array($text['aktuality'], "/", searchTable(5) + searchTable(6));
$menu[] = array($text['hlaseniV'], "/reports.php", searchTable(4));
$menu[] = array($text['osoby'], "/persons.php", searchTable(1) + searchTable(7));
$menu[] = array($text['pripady'], "/cases/", searchTable(3));
$menu[] = array($text['skupiny'], "/groups.php", searchTable(2));
$menu2[] = array($text['forum'], "http://www.prazskahlidka.cz/forums/", 0);
$menu2[] = array($text['menu-zlobody'], "/evilpoints.php", 0);
if (isset ($user) AND $user['aclTask'] > 0) {
    $menu2[] = array($text['ukoly'], "/tasks.php", 0);
}
if (isset ($user) AND $user['aclDirector'] > 0) {
    $menu2[] = array($text['spravauzivatelu'], "/users", 0);
}
if (isset ($user) AND ($user['aclDeputy'] > 0 OR $user['aclDirector'] > 0)) {
    $menu2[] = array($text['casovadostupnost'], "/doodle.php", 0);
} elseif (isset ($user)) {
    $doodle = mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    $menu2[] = array($text['casovadostupnost'], $doodle['link'], 0);
}
if (isset ($user) AND $user['aclAudit'] > 0) {
    $menu2[] = array($text['audit'], "/audit.php", 0);
}
$menu2[] = array($text['nastaveni'], "/settings", 0);
if (isset ($user) AND $user['aclRoot'] > 0) {
    $menu2[] = array($text['zalohovani'], '/backup', 0);
}
$menu2[] = array($text['odhlasit'], "/logout", 0);
