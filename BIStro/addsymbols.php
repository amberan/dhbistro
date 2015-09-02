<?php
	require_once ('./inc/func_main.php');
	
	if (isset($_POST['addsymbol2c'])) {
		auditTrail(7, 6, $_POST['symbolid']);
		if ($usrinfo['right_power']==1) {
			$sql="DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=3";
			MySQL_Query ($sql);
		} else {
			$sql="DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."cases as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=3";
			MySQL_Query ($sql);
		}
		if (isset($_POST['case'])) {
			$case=$_POST['case'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Symbol přiřazen k příslušným případům.</p></div>';
		if (isset($_POST['case'])) {
			for ($i=0;$i<Count($case);$i++) {
				$sql="INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$case[$i]."','".$usrinfo['id']."','3')";
				MySQL_Query ($sql);
			}
		}
		pageEnd ();
	}
	
	if (isset($_POST['addsymbol2ar'])) {
		auditTrail(7, 6, $_POST['symbolid']);
		if ($usrinfo['right_power']==1) {
			$sql="DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=4";
			MySQL_Query ($sql);
		} else {
			$sql="DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."cases as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=4";
			MySQL_Query ($sql);
		}
		if (isset($_POST['report'])) {
			$report=$_POST['report'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Symbol přiřazen k příslušným hlášení.</p></div>';
		if (isset($_POST['report'])) {
			for ($i=0;$i<Count($report);$i++) {
				MySQL_Query ("INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$report[$i]."','".$usrinfo['id']."','4')");
			}
		}
		pageEnd ();
	}
	
        if (isset ($_POST['addsymb2pers'])) {
            auditTrail(7, 6, $_POST['symbolid']);
            MySQL_Query("UPDATE ".DB_PREFIX."symbols SET assigned=1 WHERE id=".$_POST['symbolid']);
            MySQL_Query("UPDATE ".DB_PREFIX."persons SET symbol=".$_POST['symbolid']." WHERE id=".$_POST['person']);
            pageStart ('Uložení změn');
            mainMenu (5);
            sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./symbols.php">nepřiřazené symboly</a>');
            $sql_p="SELECT name, surname FROM ".DB_PREFIX."persons WHERE id=".$_POST['person'];
            $res_p=MySQL_Query($sql_p);
            $rec_p=MySQL_Fetch_Assoc($res_p);
            echo '<div id="obsah"><p>Symbol přiřazen osobě <a href="readperson.php?rid='.$_POST['person'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec_p['surname']),StripSlashes($rec_p['name']))).'</a></p>';
            echo '<p>Zkontroluj prosím poznámky, přiložené k osobě, a případně smaž duplicity.</div>';
            // Hlaseni - priradit osobu tam, kde neni
            $sql_ar="SELECT idrecord FROM `".DB_PREFIX."symbol2all` WHERE `idsymbol` =".$_POST['symbolid']." AND `table` =4 AND idrecord NOT IN (
                    SELECT idreport FROM `".DB_PREFIX."ar2p` WHERE `idperson` =".$_POST['person'].")";
            $res_ar=MySQL_Query($sql_ar);
            while ($rec_ar=MySQL_Fetch_Assoc($res_ar)) {
                $sql_i="INSERT INTO ".DB_PREFIX."ar2p VALUES (".$_POST['person'].",".$rec_ar['idrecord'].",".$usrinfo['id'].",0)";
                mysql_query($sql_i);
            }
            // Pripady - priradit osobu tam, kde neni
            $sql_c="SELECT idrecord FROM `".DB_PREFIX."symbol2all` WHERE `idsymbol` =".$_POST['symbolid']." AND `table` =3 AND idrecord NOT IN (
                    SELECT idcase FROM `".DB_PREFIX."c2p` WHERE `idperson` =".$_POST['person'].")";
            $res_c=MySQL_Query($sql_c);
            while ($rec_c=MySQL_Fetch_Assoc($res_c)) {
                $sql_i="INSERT INTO ".DB_PREFIX."c2p VALUES (".$_POST['person'].",".$rec_c['idrecord'].",".$usrinfo['id'].")";
                mysql_query($sql_i);
            }
            // Preneseni popisu symbolu do poznamky k osobe
            $sql_d="SELECT `desc` FROM ".DB_PREFIX."symbols WHERE id=".$_POST['symbolid'];
            $res_d=MySQL_Query($sql_d);
            $rec_d=MySQL_Fetch_Assoc($res_d);
            MySQL_Query ("INSERT INTO ".DB_PREFIX."notes VALUES('','".$rec_d['desc']."','Popis symbolu přiřazeného ".Date("j/m/Y H:i:s", Time())."','".Time()."','".$usrinfo['id']."','1','".$_POST['person']."','0','0')");
            
            // Kopie poznamek k symbolu priradit k osobe
            $sql_n="SELECT * FROM ".DB_PREFIX."notes WHERE iditem=".$_POST['symbolid']." AND idtable=7 AND deleted=0";
            $res_n=MySQL_Query($sql_n);
            while ($rec_n=MySQL_Fetch_Assoc($res_n)) {
                $note_text="Poznámka zkopírována při přiřazení symbolu ".Date("j/m/Y H:i:s", Time())." <br />".$rec_n['note'];
                $sql_ni="INSERT INTO ".DB_PREFIX."notes VALUES ('','".$note_text."','".$rec_n['title']."','".Time()."',".$rec_n['iduser'].",1,".$_POST['person'].",".$rec_n['secret'].",0)";
                mysql_query($sql_ni);
            }
            
            pageEnd ();
        }
?>