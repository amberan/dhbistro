<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  auditTrail(8, 11, $_REQUEST['delete']);
	  MySQL_Query ("UPDATE ".DB_PREFIX."users SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: users.php');
	}
	if (isset($_POST['insertuser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	  pageStart ('Přidán uživatel');
		mainMenu (2);
		sparklets ('<a href="./users.php">uživatelé</a> &raquo; <a href="./newuser.php">nový uživatel</a> &raquo; <strong>přidán uživatel</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".mysql_real_escape_string(safeInput($_POST['login']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Uživatel již existuje, změňte jeho jméno.</p></div>';
	  } else {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."users VALUES('','".mysql_real_escape_string(safeInput($_POST['login']))."','".mysql_real_escape_string($_POST['heslo'])."','','','".$_POST['power']."','".$_POST['texty']."','','','','','600','')");
			$uidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".mysql_real_escape_string(safeInput($_POST['login']))."')"));
			$uid=$uidarray['id'];
			auditTrail(8, 3, $uid);
			MySQL_Query ("CREATE TABLE nw_unread_".$uid." (id int NOT NULL PRIMARY KEY AUTO_INCREMENT, idtable int, idrecord int)");
			echo '<div id="obsah"><p>Uživatel vytvořen.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertuser'])) {
		  pageStart ('Přidán uživatel');
			mainMenu (2);
			sparklets ('<a href="./users.php">uživatelé</a> &raquo; <a href="./newuser.php">nový uživatel</a> &raquo; <strong>přidán uživatel</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['userid']) && isset($_POST['edituser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	  auditTrail(8, 2, $_POST['userid']);
	  pageStart ('Uložení změn');
		mainMenu (2);
		sparklets ('<a href="./users.php">uživatelé</a> &raquo; <a href="./edituser.php">úprava uživatele</a> &raquo; <strong>uložení změn</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".mysql_real_escape_string(safeInput($_POST['login']))."') AND id<>".$_POST['userid']);
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Uživatel již existuje, změňte jeho jméno.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."users SET login='".mysql_real_escape_string(safeInput($_POST['login']))."', right_power='".$_POST['power']."', right_text='".$_POST['texty']."', idperson='".$_POST['idperson']."' WHERE id=".$_POST['userid']);
			echo '<div id="obsah"><p>Uživatel upraven.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['edituser'])) {
		  pageStart ('Uložení změn');
			mainMenu (2);
			sparklets ('<a href="./users.php">uživatelé</a> &raquo; <a href="./edituser.php">úprava uživatele</a> &raquo; <strong>uložení změn</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
?>
