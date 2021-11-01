<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Uložení změn';
    if (isset($_POST['addsymbol2c'])) {
        auditTrail(7, 6, $_POST['symbolid']);
        if ($user['aclSecret'] == 1) {
            $sql = "DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=3";
            mysqli_query($database, $sql);
        } else {
            $sql = "DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."case as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=3";
            mysqli_query($database, $sql);
        }
        if (isset($_POST['case'])) {
            $case = $_POST['case'];
        }
        mainMenu();
        sparklets('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
        echo '<div id="obsah"><p>Symbol přiřazen k příslušným případům.</p></div>';
        if (isset($_POST['case'])) {
            for ($i = 0;$i < Count($case);$i++) {
                $sql = "INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$case[$i]."','".$user['userId']."','3')";
                mysqli_query($database, $sql);
            }
        }
        latteDrawTemplate("footer");
    }

    if (isset($_POST['addsymbol2ar'])) {
        auditTrail(7, 6, $_POST['symbolid']);
        if ($user['aclSecret'] == 1) {
            $sql = "DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=4";
            mysqli_query($database, $sql);
        } else {
            $sql = "DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."case as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=4";
            mysqli_query($database, $sql);
        }
        if (isset($_POST['report'])) {
            $report = $_POST['report'];
        }

        mainMenu();
        sparklets('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
        echo '<div id="obsah"><p>Symbol přiřazen k příslušným hlášení.</p></div>';
        if (isset($_POST['report'])) {
            for ($i = 0;$i < Count($report);$i++) {
                mysqli_query($database, "INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$report[$i]."','".$user['userId']."','4')");
            }
        }
        latteDrawTemplate("footer");
    }

        if (isset($_POST['addsymb2pers'])) {
            auditTrail(7, 6, $_POST['symbolid']);
            mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET assigned=1 WHERE id=".$_POST['symbolid']);
            mysqli_query($database, "UPDATE ".DB_PREFIX."person SET symbol=".$_POST['symbolid']." WHERE id=".$_POST['person']);
            mainMenu();
            sparklets('<a href="./persons.php">osoby</a> &raquo; <a href="./symbols.php">nepřiřazené symboly</a>');
            $sql_p = "SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_POST['person'];
            $res_p = mysqli_query($database, $sql_p);
            $rec_p = mysqli_fetch_assoc($res_p);
            echo '<div id="obsah"><p>Symbol přiřazen osobě <a href="readperson.php?rid='.$_POST['person'].'&amp;hidenotes=0">'.implode(', ', [StripSlashes($rec_p['surname']), StripSlashes($rec_p['name'])]).'</a></p>';
            echo '<p>Zkontroluj prosím poznámky, přiložené k osobě, a případně smaž duplicity.</div>';
            // Hlaseni - priradit osobu tam, kde neni
            $sql_ar = "SELECT idrecord FROM `".DB_PREFIX."symbol2all` WHERE `idsymbol` =".$_POST['symbolid']." AND `table` =4 AND idrecord NOT IN (
                    SELECT idreport FROM `".DB_PREFIX."ar2p` WHERE `idperson` =".$_POST['person'].")";
            $res_ar = mysqli_query($database, $sql_ar);
            while ($rec_ar = mysqli_fetch_assoc($res_ar)) {
                $sql_i = "INSERT INTO ".DB_PREFIX."ar2p VALUES (".$_POST['person'].",".$rec_ar['idrecord'].",".$user['userId'].",0)";
                mysqli_query($database, $sql_i);
            }
            // Pripady - priradit osobu tam, kde neni
            $sql_c = "SELECT idrecord FROM `".DB_PREFIX."symbol2all` WHERE `idsymbol` =".$_POST['symbolid']." AND `table` =3 AND idrecord NOT IN (
                    SELECT idcase FROM `".DB_PREFIX."c2p` WHERE `idperson` =".$_POST['person'].")";
            $res_c = mysqli_query($database, $sql_c);
            while ($rec_c = mysqli_fetch_assoc($res_c)) {
                $sql_i = "INSERT INTO ".DB_PREFIX."c2p VALUES (".$_POST['person'].",".$rec_c['idrecord'].",".$user['userId'].")";
                mysqli_query($database, $sql_i);
            }
            // Preneseni popisu symbolu do poznamky k osobe
            $sql_d = "SELECT `desc` FROM ".DB_PREFIX."symbol WHERE id=".$_POST['symbolid'];
            $res_d = mysqli_query($database, $sql_d);
            $rec_d = mysqli_fetch_assoc($res_d);
            mysqli_query($database, "INSERT INTO ".DB_PREFIX."note ( note, title, datum, iduser, idtable, iditem, secret, deleted) VALUES(".$rec_d['desc']."','Popis symbolu přiřazeného ".Date("j/m/Y H:i:s", Time())."','".Time()."','".$user['userId']."','1','".$_POST['person']."','0','0')");

            // Kopie poznamek k symbolu priradit k osobe
            $sql_n = "SELECT * FROM ".DB_PREFIX."note WHERE iditem=".$_POST['symbolid']." AND idtable=7 AND deleted=0";
            $res_n = mysqli_query($database, $sql_n);
            while ($rec_n = mysqli_fetch_assoc($res_n)) {
                $note_text = "Poznámka zkopírována při přiřazení symbolu ".Date("j/m/Y H:i:s", Time())." <br />".$rec_n['note'];
                $sql_ni = "INSERT INTO ".DB_PREFIX."note  ( note, title, datum, iduser, idtable, iditem, secret, deleted) VALUES ('".$note_text."','".$rec_n['title']."','".Time()."',".$rec_n['iduser'].",1,".$_POST['person'].",".$rec_n['secret'].",0)";
                mysqli_query($database, $sql_ni);
            }

            latteDrawTemplate("footer");
        }
