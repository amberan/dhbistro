<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');


latteDrawTemplate("header");

$latteParameters['title'] = 'Prirazeni hlaseni k pripadu';
mainMenu();

$customFilter = custom_Filter(16);
sparklets('<a href="/cases/">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přidání hlášení</strong>');
if (is_numeric($_REQUEST['rid']) && $user['aclCase']) {
    $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
    if ($rec = mysqli_fetch_assoc($res)) {
        ?>

<div id="obsah">
    <p>
        K případu můžete přiřadit hlášení, která se ho týkají.
    </p>

    <?php
        // zpracovani filtru
    if (!isset($customFilter['type'])) {
        $filterCat = 0;
    } else {
        $filterCat = $customFilter['type'];
    }
        if (!isset($customFilter['sort'])) {
            $filterSort = 6;
        } else {
            $filterSort = $customFilter['sort'];
        }
        if (!isset($customFilter['status'])) {
            $filterStat = 0;
        } else {
            $filterStat = $customFilter['status'];
        }
        if (!isset($customFilter['archiv'])) {
            $filterArchiv = 0;
        } else {
            $filterArchiv = 1;
        }
        switch ($filterCat) {
            case 0: $filterSqlCat = '';
                break;
            case 1: $filterSqlCat = ' AND '.DB_PREFIX.'report.reportType=1 ';
                break;
            case 2: $filterSqlCat = ' AND '.DB_PREFIX.'report.reportType=2 ';
                break;
            default: $filterSqlCat = '';
        }
        switch ($filterSort) {
            case 1: $filterSqlSort = ' '.DB_PREFIX.'report.reportCreated DESC ';
                break;
            case 2: $filterSqlSort = ' '.DB_PREFIX.'report.reportCreated ASC ';
                break;
            case 3: $filterSqlSort = ' '.DB_PREFIX.'user.login ASC ';
                break;
            case 4: $filterSqlSort = ' '.DB_PREFIX.'user.login DESC ';
                break;
            case 5: $filterSqlSort = ' '.DB_PREFIX.'report.reportModified ASC ';
                break;
            case 6: $filterSqlSort = ' '.DB_PREFIX.'report.reportModified DESC ';
                break;
            default: $filterSqlSort = ' '.DB_PREFIX.'report.reportModified DESC ';
        }
        switch ($filterStat) {
            case 0: $fsql_stat = '';
                break;
            case 1: $fsql_stat = ' AND '.DB_PREFIX.'report.reportStatus=0 ';
                break;
            case 2: $fsql_stat = ' AND '.DB_PREFIX.'report.reportStatus=1 ';
                break;
            case 3: $fsql_stat = ' AND '.DB_PREFIX.'report.reportStatus=2 ';
                break;
            default: $fsql_stat = '';
        }
        switch ($filterArchiv) {
            case 0: $fsql_archiv = ' AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1)) ';
                break;
            case 1: $fsql_archiv = '';
                break;
            default: $fsql_archiv = ' AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1)) ';
        }
        // filtr samotny
    echo '<form action="addc2ar.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>

	  <p>Vypsat <select name="status">
	<option value="0"'.(($filterStat == 0) ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.(($filterStat == 1) ? ' selected="selected"' : '').'>rozpracovaná</option>
	<option value="2"'.(($filterStat == 2) ? ' selected="selected"' : '').'>dokončená</option>
	<option value="3"'.(($filterStat == 3) ? ' selected="selected"' : '').'>analyzovaná</option>
</select> hlášení <select name="type">
	<option value="0"'.(($filterCat == 0) ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.(($filterCat == 1) ? ' selected="selected"' : '').'>z výjezdu</option>
	<option value="2"'.(($filterCat == 2) ? ' selected="selected"' : '').'>z výslechu</option>
</select> a

seřadit je podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>data hlášení sestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>data hlášení vzestupně</option>
	<option value="3"'.(($filterSort == 3) ? ' selected="selected"' : '').'>jména autora vzestupně</option>
	<option value="4"'.(($filterSort == 4) ? ' selected="selected"' : '').'>jména autora sestupně</option>
	<option value="5"'.(($filterSort == 5) ? ' selected="selected"' : '').'>data výjezdu vzestupně</option>
	<option value="6"'.(($filterSort == 6) ? ' selected="selected"' : '').'>data výjezdu sestupně</option>
        </select>.<br />
        <input type="checkbox" name="archiv" value="archiv" class="checkbox"'.(($filterArchiv == 1) ? ' checked="checked"' : '').' /> I archiv.
        </p>
	  <div id="filtersubmit">
	  <input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	  <input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addreports.php" method="post" class="otherform">';
        // vypis hlášení
        $sqlFilter = '';
        if ($user['aclRoot'] < 1) {
            $sqlFilter = ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
        }
        $sqlFilter .= ' AND '.DB_PREFIX."report.reportSecret<=".$user['aclSecret'];

        $sql = "SELECT
			".DB_PREFIX."report.reportId AS 'id',
	        ".DB_PREFIX."report.reportModified AS 'datum',
	        ".DB_PREFIX."report.reportName AS 'label',
	        ".DB_PREFIX."report.reportTask AS 'task',
	        ".DB_PREFIX."user.userName AS 'autor',
	        ".DB_PREFIX."report.reportOwner AS 'iduser',
	        ".DB_PREFIX."report.reportType AS 'type',
	        ".DB_PREFIX."ar2c.iduser
	        	FROM ".DB_PREFIX."user, ".DB_PREFIX."report
                LEFT JOIN ".DB_PREFIX."ar2c	ON ".DB_PREFIX."ar2c.idreport=".DB_PREFIX."report.reportId AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']."
				WHERE 1 $sqlFilter AND ".DB_PREFIX."report.reportOwner=".DB_PREFIX."user.userId ".$filterSqlCat.$fsql_stat.$fsql_archiv."
				ORDER BY ".$filterSqlSort;

        $res = mysqli_query($database, $sql); ?>
    <div style="padding-left: 0px; padding-right: 0px; padding-top: 0px; padding-bottom: 0px;" id="in-form-table">
        <?php
    while ($rec = mysqli_fetch_assoc($res)) {
        echo '<div class="news_div '.(($rec['type'] == 1) ? 'game_news' : 'system_news').'">
	<div class="news_head"><input type="checkbox" name="report[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser']) ? ' checked="checked"' : '').' /><strong><a href="/reports/'.$rec['id'].'">'.StripSlashes($rec['label'].' ').'</a></strong></span>';

        echo '<p><span>['.($rec['datum']).']</span> '.$rec['autor'].'<br /> <strong>Úkol: </strong>'
    .StripSlashes($rec['task'].' ').'</p></div>
        </div>';
    } ?>

        <div>
            <input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
            <input id="button-floating-uloz" type="submit" value="Uložit změny" name="addcasetoareport" class="submitbutton" title="Uložit změny" />
        </div>
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
