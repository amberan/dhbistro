<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Přidán úkol';

    if ($usrinfo['right_text']) {
        if (isset($_POST['inserttask'])) {
            auditTrail(10, 2, 0);
        } else {
            auditTrail(10, 1, 0);
        }

        // vlozeni noveho ukolu
        if (isset($_POST['inserttask']) && !empty($_POST['task'])) {
            mainMenu();
            $customFilter = custom_Filter(10);
            $sql_t = "INSERT INTO ".DB_PREFIX."task (task,iduser,status,created,created_by) VALUES('".$_POST['task']."','".$_POST['target']."','0','".time()."','".$user['userId']."')";
            mysqli_query($database,$sql_t);
            // Ukládání do novinek zakomentováno, protože nevím, jestli se použije. Kdyžtak SMAZAT.
            //		$gidarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."group WHERE UCASE(title)=UCASE('".mysqli_real_escape_string ($database,$_POST['title'])."')"));
            //		$gid=$gidarray['id'];
            //		if (!isset($_POST['notnew'])) {
            //			unreadRecords (2,$gid);
            //		}
            sparklets('<a href="/users">uživatelé</a> &raquo; <strong>úkoly</strong>');
            echo '<div id="obsah"><p>Úkol přidán.</p></div>';
        } else {
            if (isset($_POST['inserttask'])) {
                $latteParameters['title'] = 'Přidání úkolu neúspěšné';

                mainMenu();
                $customFilter = custom_Filter(10);
                sparklets('<a href="/users">uživatelé</a> &raquo; <strong>úkoly</strong>');
                echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
                latteDrawTemplate("footer");
            } else {
                $latteParameters['title'] = 'Úkoly';

                mainMenu();
                $customFilter = custom_Filter(10);
                sparklets('<a href="/users">uživatelé</a> &raquo; <strong>úkoly</strong>');
            }
        }

        // stav
        function status($status)
        {
            switch ($status) {
            case 0:
                return 'zadaný';
            case 1:
                return 'dokončený';
            case 2:
                return 'uzavřený';
            case 3:
                return 'zrušený';
            default:
            ;
        }
        }

        // zpracovani filtru
        if (!isset($customFilter['kategorie'])) {
            $filterCat = 1;
        } else {
            $filterCat = $customFilter['kategorie'];
        }
        switch ($filterCat) {
      case 0: $filterSqlCat = ' WHERE '.DB_PREFIX.'task.status<3 '; break;
      case 1: $filterSqlCat = ' WHERE '.DB_PREFIX.'task.status<2 '; break;
      case 2: $filterSqlCat = ' WHERE '.DB_PREFIX.'task.status=1 '; break;
      case 3: $filterSqlCat = ' WHERE '.DB_PREFIX.'task.status<4 '; break;
      default: $filterSqlCat = ' WHERE '.DB_PREFIX.'task.status<2 ';
    } ?>
<!-- Přidání úkolu -->
<div id="filter-wrapper">
    <form action="tasks.php" method="post" id="filter">
        <fieldset>
            <legend>Přidej úkol</legend>
            <p><label for="task">Zadání:</label>
                <input type="text" name="task" id="task" />
                <?php
    $sql = "SELECT userId as id, userName as login FROM ".DB_PREFIX."user WHERE userDeleted=0 ORDER BY login ASC";
        $res_n = mysqli_query($database,$sql);
        echo '<label for="target">Uživatel:</label>
		<select name="target" id="target">';
        while ($rec_n = mysqli_fetch_assoc($res_n)) {
            echo '<option value="'.$rec_n['id'].'"'.($rec_n['id'] === $user['userId'] ? ' selected="selected"' : '').'>'.$rec_n['login'].'</option>';
        }
        echo '</select>'; ?>
            </p>
            <div id="filtersubmit"><input type="submit" name="inserttask" value="Zadat" /></div>
        </fieldset>
    </form>
</div><!-- end of #filter-wrapper -->

<?php
    // filtr
    function filter(): void
    {
        global $filterCat;
        echo '<div id="filter-wrapper"><form action="tasks.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.($filterCat === 0 ? ' selected="selected"' : '').'>všechny</option>
	<option value="1"'.($filterCat === 1 ? ' selected="selected"' : '').'>neuzavřené</option>
	<option value="2"'.($filterCat === 2 ? ' selected="selected"' : '').'>dokončené</option>
	<option value="3"'.($filterCat === 3 ? ' selected="selected"' : '').'>i zrušené</option>
</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
    }
        filter();
        if (isset($_GET['sort'])) {
            sortingSet('task',$_GET['sort'],'task');
        }
        // vypis uživatelů
        $sql = "SELECT * FROM ".DB_PREFIX."task".$filterSqlCat.sortingGet('task');
        $res = mysqli_query($database,$sql);
        if (mysqli_num_rows($res)) {
            echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>#</th>
	  <th>Úkol</th>
	  <th>Uživatel  <a href="tasks.php?sort=iduser">&#8661;</a></th>
	  <th>Stav  <a href="tasks.php?sort=status">&#8661;</a></th>
	  <th>Zadáno  <a href="tasks.php?sort=created">&#8661;</a></th>
	  <th>Zadavatel  <a href="tasks.php?sort=created_by">&#8661;</a></th>
	  <th>Upraveno  <a href="tasks.php?sort=modified">&#8661;</a></th>
	  <th>Upravil  <a href="tasks.php?sort=modified_by">&#8661;</a></th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
            $even = 0;
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<tr class="'.($even % 2 === 0 ? 'even' : 'odd').'">
	<td>'.$rec['id'].'</td>
	<td>'.stripslashes($rec['task']).'</td>
	<td>'.getAuthor($rec['iduser'],0).'</td>
	<td>'.status($rec['status']).'</td>
	<td>'.($rec['created'] ? webdate($rec['created']) : 'nikdy').'</td>
	<td>'.getAuthor($rec['created_by'],0).'</td>
	<td>'.($rec['modified'] ? webdatetime($rec['modified']) : 'nikdy').'</td>
	<td>'.($rec['modified_by'] ? getAuthor($rec['modified_by'],0) : 'nikdo').'</td>
	<td>'.($rec['status'] !== 2 ? '<a href="procother.php?acctask='.$rec['id'].'">uzavřít</a> ' : '').($rec['status'] !== 0 ? '| <a href="procother.php?rtrntask='.$rec['id'].'">vrátit</a> ' : '').($rec['status'] !== 3 ? '| <a href="procother.php?cncltask='.$rec['id'].'">zrušit</a>' : '').'</td>
</tr>';
                $even++;
            }
            echo '</tbody>
</table>
</div>
';
        } else {
            echo '<div id="obsah"><p>Žádné úkoly neodpovídají výběru.</p></div>';
        }
    } else {
        auditTrail(10, 1, 0);

        mainMenu();
        sparklets('<strong>uživatelé</strong> &raquo; <strong>úkoly</strong>');
        echo '<div id="obsah"><p>Jste si jistí, že máte správná oprávnění?</p></div>';
    }
?>
<?php
    latteDrawTemplate("footer");
?>
