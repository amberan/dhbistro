<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;

latteDrawTemplate("header");

$latteParameters['title'] = 'Prirazeni pripadu k hlaseni ';
mainMenu();
    $customFilter = custom_Filter(18);
    sparklets('<a href="/reports/">hlášení</a> &raquo; <strong>úprava hlášení</strong>');
    $autharray = mysqli_fetch_assoc(mysqli_query($database, "SELECT reportOwner FROM ".DB_PREFIX."report WHERE reportId=".$_REQUEST['rid']));
    $author = $autharray['reportOwner'];
    if (is_numeric($_REQUEST['rid']) && ($user['aclReport'] || $user['userId'] == $author)) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."report WHERE reportId=".$_REQUEST['rid']);
        if ($rec = mysqli_fetch_assoc($res)) {
            ?>

<div id="obsah">
    <p>
        Hlášení můžete přiřadit k případu (či případům), kterého se týká.
    </p>

    <?php
if (!isset($customFilter['sort'])) {
                $filterSort = 1;
            } else {
                $filterSort = $customFilter['sort'];
            }
            switch ($filterSort) {
    case 1: $filterSqlSort = ' '.DB_PREFIX.'case.title ASC '; break;
    case 2: $filterSqlSort = ' '.DB_PREFIX.'case.title DESC '; break;
    default: $filterSqlSort = ' '.DB_PREFIX.'case.title ASC ';
}
            function filter()
            {
                global $filterSort;
                echo '<form action="addar2c.php" method="post" id="filter">
	<fieldset>
	<legend>Filtr</legend>
	<p>Vypsat všechny případy a seřadit je podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>názvu vzestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>názvu sestupně</option>
	</select>.</p>
	<input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
	</form>';
            }
            filter(); ?>
    <form action="addreports.php" method="post" class="otherform">
        <?php // vypis pripadu
        $sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."case.status, ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."ar2c.iduser
            FROM ".DB_PREFIX."case
            LEFT JOIN ".DB_PREFIX."ar2c ON ".DB_PREFIX."ar2c.idcase=".DB_PREFIX."case.id AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']."
            WHERE $sqlFilter ORDER BY ".$filterSqlSort;
            $res = mysqli_query($database, $sql); ?>
        <div id="in-form-table">
            <?php
if (mysqli_num_rows($res)) {
                echo '	<table>
	<thead>
	<tr>
	<th>#</th>
	<th>Název</th>
	<th>Stav</th>
	</tr>
	</thead>
	<tbody>
	';
                $even = 0;
                while ($rec = mysqli_fetch_assoc($res)) {
                    echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').(($rec['status']) ? ' solved' : '').'">
		<td><input type="checkbox" name="case[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser']) ? ' checked="checked"' : '').' /></td><td>'.(($rec['secret']) ? '<span class="secret"><a href="readcase.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a></span>' : '<a href="readcase.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>').'</td>
		<td>'.(($rec['status']) ? 'uzavřen' : '&mdash;').'</td>
		</tr>';
                    $even++;
                }
                echo '</table>
	';
            } else {
                echo '<p>Žádné případy neodpovídají výběru.</p>';
            } ?>
            <input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
            <input id="button-floating-uloz" type="submit" value="Uložit změny" name="addtoareport" class="submitbutton" title="Uložit změny" />
        </div>
        <!-- end of #in-form-table -->
    </form>

</div>
<!-- end of #obsah -->
<?php
        } else {
            echo '<div id="obsah"><p>Hlášení neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
        }
    } else {
        echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
    }
    latteDrawTemplate("footer");

?>
