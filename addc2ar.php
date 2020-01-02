<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');;
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latteParameters['title'] = 'Úprava hlášení';
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	
	mainMenu (5);
        $custom_Filter = custom_Filter(16);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přidání hlášení</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
	    if ($rec = mysqli_fetch_assoc ($res)) {
	        ?>

<div id="obsah">
    <p>
        K případu můžete přiřadit hlášení, která se ho týkají.
    </p>

    <?php
		// zpracovani filtru
	if (!isset($custom_Filter['type'])) {
	    $f_cat = 0;
	} else {
	    $f_cat = $custom_Filter['type'];
	}
	        if (!isset($custom_Filter['sort'])) {
	            $f_sort = 6;
	        } else {
	            $f_sort = $custom_Filter['sort'];
	        }
	        if (!isset($custom_Filter['status'])) {
	            $f_stat = 0;
	        } else {
	            $f_stat = $custom_Filter['status'];
	        }
	        if (!isset($custom_Filter['archiv'])) {
	            $f_archiv = 0;
	        } else {
	            $f_archiv = 1;
	        }
	        switch ($f_cat) {
	  case 0: $fsql_cat = ''; break;
	  case 1: $fsql_cat = ' AND '.DB_PREFIX.'report.type=1 '; break;
	  case 2: $fsql_cat = ' AND '.DB_PREFIX.'report.type=2 '; break;
	  default: $fsql_cat = '';
	}
	        switch ($f_sort) {
	  case 1: $fsql_sort = ' '.DB_PREFIX.'report.datum DESC '; break;
	  case 2: $fsql_sort = ' '.DB_PREFIX.'report.datum ASC '; break;
	  case 3: $fsql_sort = ' '.DB_PREFIX.'user.login ASC '; break;
	  case 4: $fsql_sort = ' '.DB_PREFIX.'user.login DESC '; break;
	  case 5: $fsql_sort = ' '.DB_PREFIX.'report.adatum ASC '; break;
	  case 6: $fsql_sort = ' '.DB_PREFIX.'report.adatum DESC '; break;
	  default: $fsql_sort = ' '.DB_PREFIX.'report.adatum DESC ';
	}
	        switch ($f_stat) {
		case 0: $fsql_stat = ''; break;
		case 1: $fsql_stat = ' AND '.DB_PREFIX.'report.status=0 '; break;
		case 2: $fsql_stat = ' AND '.DB_PREFIX.'report.status=1 '; break;
		case 3: $fsql_stat = ' AND '.DB_PREFIX.'report.status=2 '; break;
		default: $fsql_stat = '';
	}
	        switch ($f_archiv) {
		case 0: $fsql_archiv = ' AND '.DB_PREFIX.'report.status<>3 '; break;
		case 1: $fsql_archiv = ''; break;
		default: $fsql_archiv = ' AND '.DB_PREFIX.'report.status<>3 ';
	}
	        // filtr samotny
	        function filter ()
	        {
	            global $f_cat,$f_sort,$f_stat, $f_archiv;
	            echo '<form action="addc2ar.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>

	  <p>Vypsat <select name="status">
	<option value="0"'.(($f_stat == 0) ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.(($f_stat == 1) ? ' selected="selected"' : '').'>rozpracovaná</option>
	<option value="2"'.(($f_stat == 2) ? ' selected="selected"' : '').'>dokončená</option>
	<option value="3"'.(($f_stat == 3) ? ' selected="selected"' : '').'>analyzovaná</option>
</select> hlášení <select name="type">
	<option value="0"'.(($f_cat == 0) ? ' selected="selected"' : '').'>všechna</option>
	<option value="1"'.(($f_cat == 1) ? ' selected="selected"' : '').'>z výjezdu</option>
	<option value="2"'.(($f_cat == 2) ? ' selected="selected"' : '').'>z výslechu</option>
</select> a 

seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort == 1) ? ' selected="selected"' : '').'>data hlášení sestupně</option>
	<option value="2"'.(($f_sort == 2) ? ' selected="selected"' : '').'>data hlášení vzestupně</option>
	<option value="3"'.(($f_sort == 3) ? ' selected="selected"' : '').'>jména autora vzestupně</option>
	<option value="4"'.(($f_sort == 4) ? ' selected="selected"' : '').'>jména autora sestupně</option>
	<option value="5"'.(($f_sort == 5) ? ' selected="selected"' : '').'>data výjezdu vzestupně</option>
	<option value="6"'.(($f_sort == 6) ? ' selected="selected"' : '').'>data výjezdu sestupně</option>
        </select>.<br />
        <input type="checkbox" name="archiv" value="archiv" class="checkbox"'.(($f_archiv == 1) ? ' checked="checked"' : '').' /> I archiv.
        </p>
	  <div id="filtersubmit">
	  <input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	  <input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addreports.php" method="post" class="otherform">';
	        }
	        filter();
	        // vypis hlášení
	        if ($usrinfo['right_power']) {
	            $sql = "SELECT
			".DB_PREFIX."report.id AS 'id',
	        ".DB_PREFIX."report.datum AS 'datum',
	        ".DB_PREFIX."report.label AS 'label',
	        ".DB_PREFIX."report.task AS 'task',
	        ".DB_PREFIX."user.login AS 'autor',
	        ".DB_PREFIX."report.type AS 'type',
	        ".DB_PREFIX."ar2c.iduser 
	        	FROM ".DB_PREFIX."user, ".DB_PREFIX."report LEFT JOIN ".DB_PREFIX."ar2c 
	        	ON ".DB_PREFIX."ar2c.idreport=".DB_PREFIX."report.id AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']."
				WHERE ".DB_PREFIX."report.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."report.deleted=0".$fsql_cat.$fsql_stat.$fsql_archiv."
				ORDER BY ".$fsql_sort;
	        } else {
	            $sql = "SELECT
			".DB_PREFIX."report.id AS 'id',
	        ".DB_PREFIX."report.datum AS 'datum',
	        ".DB_PREFIX."report.label AS 'label',
	        ".DB_PREFIX."report.task AS 'task',
	        ".DB_PREFIX."user.login AS 'autor',
	        ".DB_PREFIX."report.iduser AS 'iduser',
	        ".DB_PREFIX."report.type AS 'type',
	        ".DB_PREFIX."ar2c.iduser 
	        	FROM ".DB_PREFIX."user, ".DB_PREFIX."report LEFT JOIN ".DB_PREFIX."ar2c 
	        	ON ".DB_PREFIX."ar2c.idreport=".DB_PREFIX."report.id AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']."
				WHERE ".DB_PREFIX."report.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."report.deleted=0 AND ".DB_PREFIX."report.secret=0".$fsql_cat.$fsql_stat.$fsql_archiv."
				ORDER BY ".$fsql_sort;
	        }
	        $res = mysqli_query ($database,$sql); ?>
    <div style="padding-left: 0px; padding-right: 0px; padding-top: 0px; padding-bottom: 0px;" id="in-form-table">
        <?php
	while ($rec = mysqli_fetch_assoc ($res)) {
	    echo '<div class="news_div '.(($rec['type'] == 1) ? 'game_news' : 'system_news').'">
	<div class="news_head"><input type="checkbox" name="report[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser']) ? ' checked="checked"' : '').' /><strong><a href="readactrep.php?rid='.$rec['id'].'">'.StripSlashes($rec['label']).'</a></strong></span>';
	  
	    echo '<p><span>['.webdatetime($rec['datum']).']</span> '.$rec['autor'].'<br /> <strong>Úkol: </strong>'
	.StripSlashes($rec['task']).'</p></div>
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
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	?>
