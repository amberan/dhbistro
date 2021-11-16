<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = $text['point'].'y';

if (isset($_POST['addpoints'])) {
    auditTrail(9, 2, 0);
} else {
    auditTrail(9, 1, 0);
}
    mainMenu();
    sparklets('<strong>'.$text['point'].'y</strong>', (($user['aclUser']) ? 'aktuální stav' : ''));  //TODO permission points
    //Přidání zlobodů
    if (isset($_POST['addpoints'])) {
        if (is_numeric($_POST['plus'])) {
            $ep_result = $_POST['oldpoints'] + $_POST['plus'];
            mysqli_query($database, "UPDATE ".DB_PREFIX."user SET zlobod=".$ep_result." WHERE userId=".$_POST['usrid']."");
            echo '<div id="obsah"><p>Zlobody přidány.</p></div>';
        } else {
            echo '<div id="obsah"><p>Přidané '.$text['point'].'y musí být číselné.</p></div>';
        }
    }

    // vypis uživatelů
    if (isset($_GET['sort'])) {
        sortingSet('points', $_GET['sort'], 'user');
    }
    $sql = "SELECT * FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.userDeleted=0".sortingGet('points', 'user');
    $res = mysqli_query($database, $sql);
    if (!is_bool($res)) {
        echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Kódové označení <a href="evilpoints.php?sort=userName">&#8661;</a></th>
	  <th>Aktuální počet '.$text['point'].'ů <a href="evilpoints.php?sort=zlobod">&#8661;</a></th>
	  '.(($user['aclUser']) ? '	  <th>Akce</th>' : '').'
	</tr>
</thead>
<tbody>
'; //TODO permission points
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td>'.getAuthor($rec['userId'], 0).'</td>
	<td>'.($rec['zlobod']).'</td>
	'.(($user['aclUsers']) ? '<td>
			<form action="evilpoints.php" method="post" id="inputform" class="evilform">
			<input class="plus" type="text" name="plus" id="plus" />
			<input type="hidden" name="usrid" value="'.($rec['userId']).'" />
			<input type="hidden" name="oldpoints" value="'.($rec['zlobod']).'" />
			<input type="submit" name="addpoints" value="Přidat" />
			</form>
	</td>' : '').'
</tr>';
            $even++;
        }
        echo '</tbody>
</table>
</div>
';
    } else {
        echo '<div id="obsah"><p>Žádní uživatelé neodpovídají výběru.</p></div>';
    }
?>
<?php
    latteDrawTemplate("footer");
?>
