<?php

function mainMenu()
{
    global $database,$user,$config,$text,$URL;
    $currentfile = $_SERVER["PHP_SELF"];
    $dlink = mysqli_fetch_assoc(mysqli_query($database, "SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
    echo '<div id="menu">
  <ul class="'.$config['themeColor'].'">
	  <li '.((unreadItems(5)) ? ' class="unread"' : '').'><a href="/">Aktuality</a></li>
	  <li '.((unreadItems(6)) ? ' class="unread"' : '').'><a href="/board/">Nástěnka</a></li>
      <li '.((unreadItems(4)) ? ' class="unread"' : '').'><a href="/reports/">Hlášení '.unreadItems(4).'</a></li>
	  <li '.((unreadItems(1)) ? ' class="unread"' : '').'><a href="/persons/">Osoby '.unreadItems(1).'</a></li>
	  <li '.((unreadItems(7)) ? ' class="unread"' : '').'><a href="/symbols/">Symboly '.unreadItems(7).'</a></li>
	  <li '.((unreadItems(3)) ? ' class="unread"' : '').'><a href="/cases/">Případy '.unreadItems(3).'</a></li>
	  <li '.((unreadItems(2)) ? ' class="unread"' : '').'><a href="groups/">Skupiny '.unreadItems(2).'</a></li>';
    if ($user['aclUser']) {
        echo '<li><a href="doodle.php">Časová dostupnost</a></li>';
    } else {
        echo '<li><a href="'.$dlink['link'].'" target="_blank">Časová dostupnost</a></li>';
    }
    echo '<li><a href="evilpoints.php">'.$text['menuPoints'].'</a></li>
	  <li><a href="/settings">Nastavení</a></li>
			  <li><a href="/search/">Vyhledávání</a></li>';
    if ($user['aclUser']) {
        echo '<li><a href="/users/">Uživatelé</a></li>';
    }
    if ($user['aclAudit']) {
        echo '<li><a href="audit.php">Audit</a></li>';
    }
    echo '<li class="float-right"><a href="/logout">Odhlásit</a></li>
	  <li class="float-right"><a href="'.$URL[1].'?delallnew=true" onclick="'."return confirm('Opravdu označit vše jako přečtené?');".'">Přečíst vše</a></li>
  </ul>
  <!-- form id="search_menu">
	  <input type="text" name="query" />
	  <input type="submit" value="Hledat" />
  </form -->
</div>
<!-- end of #menu -->';
}

function sparklets($path, $actions = '')
{
    echo '<div id="sparklets">Cesta: '.$path.(($actions != '') ? ' || Akce: '.$actions : '').'</div>';
}
