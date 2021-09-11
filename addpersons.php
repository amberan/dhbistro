<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Úprava hlášení';
if (isset($_POST['addtocase'])) {
    auditTrail(3, 6, $_POST['caseid']);
    mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=0 AND c.idcase=".$_POST['caseid']);
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=0 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=0 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=1 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=0 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=1 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=1 AND c.idcase=".$_POST['caseid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=1 AND c.idcase=".$_POST['caseid']);
    }
    if (isset($_POST['person'])) {
        $person = $_POST['person'];
    }
    mainMenu ();
    sparklets ('<a href="/cases/">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="readcase.php?rid='.$_POST['caseid'].'&hidenotes=0">zobrazit upravené</a>');
    echo '<div id="obsah"><p>Osoby k případu uloženy.</p></div>';
    if (isset($_POST['person'])) {
        for ($i = 0;$i < Count($person);$i++) {
            mysqli_query ($database,"INSERT INTO ".DB_PREFIX."c2p VALUES('".$person[$i]."','".$_POST['caseid']."','".$user['userId']."')");
        }
    }
    latteDrawTemplate("footer");
}

if (isset($_POST['addtogroup'])) {
    auditTrail(2, 6, $_POST['groupid']);
    mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
    }
    if (isset($_POST['person'])) {
        $person = $_POST['person'];
    }
    mainMenu ();
    sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>','<a href="readgroup.php?rid='.$_POST['groupid'].'&hidenotes=0">zobrazit upravené</a>');
    echo '<div id="obsah"><p>Osoby příslušné ke skupině uloženy.</p></div>';
    if (isset($_POST['person'])) {
        for ($i = 0;$i < Count($person);$i++) {
            mysqli_query ($database,"INSERT INTO ".DB_PREFIX."g2p VALUES('".$person[$i]."','".$_POST['groupid']."','".$user['userId']."')");
        }
    }
    latteDrawTemplate("footer");
}

if (isset($_POST['addtoareport'])) {
    auditTrail(4, 6, $_POST['reportid']);
    mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=0 AND c.idreport=".$_POST['reportid']);
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=0 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=0 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is null AND p.dead=1 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 0) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=0 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 0 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=0 AND p.archived is not null AND p.dead=1 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 0 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is null AND p.dead=1 AND c.idreport=".$_POST['reportid']);
    }
    if ($user['aclDirector'] == 1 && $_POST['farchiv'] == 1 && $_POST['fdead'] == 1) {
        mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."person as p WHERE c.idperson=p.id AND p.secret=1 AND p.archived is not null AND p.dead=1 AND c.idreport=".$_POST['reportid']);
    }
    if (isset($_POST['person'])) {
        $person = $_POST['person'];
    }
    // podminka pro urceni ulohy osoby v hlaseni
    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    }
    // 0: osoba přítomná
    // 1: vyslýchaný
    // 2: vyslýchající
    // 3: zatčený
    // 4: velitel vyjezdu

    if (isset($_POST['person'])) {
        for ($i = 0;$i < Count($person);$i++) {
            mysqli_query ($database,"INSERT INTO ".DB_PREFIX."ar2p VALUES('".$person[$i]."','".$_POST['reportid']."','".$user['userId']."','".$role[$i]."')");
        }
    }
    // header('Location: ./editactrep.php?rid='.$_POST['reportid']); // přesměrování zpět na předchozí stránku
    mainMenu ();
    sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>','<a href="readactrep.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
    echo '<div id="obsah"><p>Osoby příslušné k hlášení uloženy.</p></div>';
    latteDrawTemplate("footer");
}

if (isset($_POST['addsolver'])) {
    auditTrail(3, 6, $_POST['caseid']);
    mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."c2s as c, ".DB_PREFIX."user as p WHERE c.iduser=p.id AND c.idcase=".$_POST['caseid']);
    mainMenu ();
    sparklets ('<a href="/cases/">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="readcase.php?rid='.$_POST['caseid'].'&hidenotes=0">zobrazit upravené</a>');
    if (isset($_POST['solver'])) {
        $solver = $_POST['solver'];
    }
    echo '<div id="obsah"><p>Případ přiřazen řešitelům.</p></div>';
    if (isset($_POST['solver'])) {
        for ($i = 0;$i < Count($solver);$i++) {
            mysqli_query ($database,"INSERT INTO ".DB_PREFIX."c2s VALUES('".$solver[$i]."','".$_POST['caseid']."','".$user['userId']."')");
        }
    }
    latteDrawTemplate("footer");
}

?>
