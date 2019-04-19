<?php

function mainMenu ($index) {
	global $database,$usrinfo,$config,$text;
	$currentfile = $_SERVER["PHP_SELF"];
	$dlink=mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
	echo '<div id="menu">
  <ul class="'.$config['barva'].'">
	  <li '.((searchTable(5))?' class="unread"':((searchTable(6))?' class="unread"':'')).'><a href="index.php">Aktuality</a></li>
	  <li '.((searchTable(4))?' class="unread"':'').'><a href="reports.php">'.$text['hlaseniV'].'</a></li>	
	  <li '.((searchTable(1))?' class="unread"':((searchTable(7))?' class="unread"':'')).'><a href="persons.php">Osoby</a></li>
	  <li '.((searchTable(3))?' class="unread"':'').'><a href="cases.php">Případy</a></li>
	  <li '.((searchTable(2))?' class="unread"':'').'><a href="groups.php">Skupiny</a></li>
	  '/*Docasne odstranena mapa agentu, stejne to nikdo nepouziva
	  .(($usrinfo['right_power'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'')*/.'
	  '.(($usrinfo['right_power']>0)?'<li><a href="doodle.php">Časová dostupnost</a></li>':'<li><a href="'.$dlink['link'].'" target="_blank">Časová dostupnost</a></li>').'
	  <li><a href="http://www.prazskahlidka.cz/forums/index.php" target="_blank">Fórum</a></li>
	  <li><a href="evilpoints.php">'.$text['menu-zlobody'].'</a></li>
	  <li><a href="settings.php">Nastavení</a></li>
			  <li><a href="search.php">Vyhledávání</a></li>
	  '.(($usrinfo['right_power']>0)?'<li><a href="users.php">Uživatelé</a></li>':'').'
			  '.(($usrinfo['right_power']<1 && $usrinfo['right_text'])?'<li><a href="tasks.php">Úkoly</a></li>':'').'
	  '.(($usrinfo['right_aud'])?'<li><a href="audit.php">Audit</a></li>':'').'
	  <li class="float-right"><a href="logout.php">Odhlásit</a></li>
	  <li class="float-right"><a href="'.$currentfile.'?delallnew=true" onclick="'."return confirm('Opravdu označit vše jako přečtené?');".'">Přečíst vše</a></li>
  </ul>
  <!-- form id="search_menu">
	  <input type="text" name="query" />
	  <input type="submit" value="Hledat" />
  </form -->
</div>
<!-- end of #menu -->';
  }

// vyhledani tabulky v neprectenych zaznamech
function searchTable ($tablenum) { 
	global $database,$unread;
	foreach ($unread as $record) {
            if ($record['idtable'] == $tablenum) {
            return true;
        }
    }
    	return false;
}

function sparklets ($path,$actions='') {
	echo '<div id="sparklets">Cesta: '.$path.(($actions!='')?' || Akce: '.$actions:'').'</div>';
}

  ?>