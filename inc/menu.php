<?php

function mainMenu ()
{
    global $database,$user,$config,$text;
    $currentfile = $_SERVER["PHP_SELF"];
    $dlink = mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    echo '<div id="menu">
  <ul class="'.$config['barva'].'">
	  <li '.((searchTable(5)) ? ' class="unread"' : ((searchTable(6)) ? ' class="unread"' : '')).'><a href="/">Aktuality</a></li>
	  <li '.((searchTable(4)) ? ' class="unread"' : '').'><a href="reports.php">'.$text['hlaseniV'].' '.searchTable(4).'</a></li>
	  <li '.((searchTable(1)) ? ' class="unread"' : ((searchTable(7)) ? ' class="unread"' : '')).'><a href="persons.php">Osoby '.searchTable(1).'</a></li>
	  <li '.((searchTable(3)) ? ' class="unread"' : '').'><a href="/cases/">Případy '.searchTable(3).'</a></li>
	  <li '.((searchTable(2)) ? ' class="unread"' : '').'><a href="groups/">Skupiny '.searchTable(2).'</a></li>
	  '/*Docasne odstranena mapa agentu, stejne to nikdo nepouziva
	  .(($user['aclDirector'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'')*/.'
	  '.(($user['aclDirector'] > 0) ? '<li><a href="doodle.php">Časová dostupnost</a></li>' : '<li><a href="'.$dlink['link'].'" target="_blank">Časová dostupnost</a></li>').'
	  <li><a href="http://www.prazskahlidka.cz/forums/index.php" target="_blank">Fórum</a></li>
	  <li><a href="evilpoints.php">'.$text['menu-zlobody'].'</a></li>
	  <li><a href="/settings">Nastavení</a></li>
			  <li><a href="search.php">Vyhledávání</a></li>
	  '.(($user['aclDirector'] > 0) ? '<li><a href="/users">Uživatelé</a></li>' : '').'
	  '.(($user['aclAudit']) ? '<li><a href="audit.php">Audit</a></li>' : '').'
	  <li class="float-right"><a href="/logout">Odhlásit</a></li>
	  <li class="float-right"><a href="'.$currentfile.'?delallnew=true" onclick="'."return confirm('Opravdu označit vše jako přečtené?');".'">Přečíst vše</a></li>
  </ul>
  <!-- form id="search_menu">
	  <input type="text" name="query" />
	  <input type="submit" value="Hledat" />
  </form -->
</div>
<!-- end of #menu -->';
}

function sparklets ($path,$actions = '')
{
    echo '<div id="sparklets">Cesta: '.$path.(($actions != '') ? ' || Akce: '.$actions : '').'</div>';
}



  ?>
