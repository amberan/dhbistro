<?php

function mainMenu ()
{
    global $database,$usrinfo,$config,$text;
    $currentfile = $_SERVER["PHP_SELF"];
    $dlink = mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    echo '<div id="menu">
  <ul class="'.$config['barva'].'">
	  <li '.((searchTable(5)) ? ' class="unread"' : ((searchTable(6)) ? ' class="unread"' : '')).'><a href="/">Aktuality</a></li>
	  <li '.((searchTable(4)) ? ' class="unread"' : '').'><a href="reports.php">'.$text['hlaseniV'].' '.searchTable(4).'</a></li>	
	  <li '.((searchTable(1)) ? ' class="unread"' : ((searchTable(7)) ? ' class="unread"' : '')).'><a href="persons.php">Osoby '.searchTable(1).'</a></li>
	  <li '.((searchTable(3)) ? ' class="unread"' : '').'><a href="cases.php">Případy '.searchTable(3).'</a></li>
	  <li '.((searchTable(2)) ? ' class="unread"' : '').'><a href="groups.php">Skupiny '.searchTable(2).'</a></li>
	  '/*Docasne odstranena mapa agentu, stejne to nikdo nepouziva
	  .(($usrinfo['right_power'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'')*/.'
	  '.(($usrinfo['right_power'] > 0) ? '<li><a href="doodle.php">Časová dostupnost</a></li>' : '<li><a href="'.$dlink['link'].'" target="_blank">Časová dostupnost</a></li>').'
	  <li><a href="http://www.prazskahlidka.cz/forums/index.php" target="_blank">Fórum</a></li>
	  <li><a href="evilpoints.php">'.$text['menu-zlobody'].'</a></li>
	  <li><a href="/settings">Nastavení</a></li>
			  <li><a href="search.php">Vyhledávání</a></li>
	  '.(($usrinfo['right_power'] > 0) ? '<li><a href="/users">Uživatelé</a></li>' : '').'
	  '.(($usrinfo['right_power'] < 1 && $usrinfo['right_text']) ? '<li><a href="tasks.php">Úkoly</a></li>' : '').'
	  '.(($usrinfo['right_aud']) ? '<li><a href="audit.php">Audit</a></li>' : '').'
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

function sparklets ($path,$actions = '')
{
    echo '<div id="sparklets">Cesta: '.$path.(($actions != '') ? ' || Akce: '.$actions : '').'</div>';
}



//LATTE
$menu[] = array($text['aktuality'], "/", searchTable(5) + searchTable(6));
$menu[] = array($text['hlaseniV'], "/reports.php", searchTable(4));
$menu[] = array($text['osoby'], "/persons.php", searchTable(1) + searchTable(7));
$menu[] = array($text['pripady'], "/cases.php", searchTable(3));
$menu[] = array($text['skupiny'], "/groups.php", searchTable(2));
$menu2[] = array($text['forum'], "http://www.prazskahlidka.cz/forums/", 0);
$menu2[] = array($text['menu-zlobody'], "/evilpoints.php", 0);
if (isset($usrinfo) and (($usrinfo['right_power'] < 1 and $usrinfo['right_text'] > 0) or ($usrinfo['right_power'] > 0))) {
    $menu2[] = array($text['ukoly'], "/tasks.php", 0);
}
if (isset($usrinfo) and $usrinfo['right_power'] > 0) {
    $doodle = mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    $menu2[] = array($text['casovadostupnost'], $doodle['link'], 0);
    $menu2[] = array($text['spravauzivatelu'], "/users", 0);
}
if (isset($usrinfo) and $usrinfo['right_aud'] > 0) {
    $menu2[] = array($text['audit'], "/audit.php", 0);
}
$menu2[] = array($text['nastaveni'], "/settings", 0);
if (isset($usrinfo) and $usrinfo['right_super'] > 0) {
    $menu2[] = array($text['zalohovani'], '/backup', 0);
}
$menu2[] = array($text['odhlasit'], "/logout.php", 0);

  ?>
