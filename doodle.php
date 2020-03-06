<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Časová dostupnost';
		mainMenu ();
		sparklets ('<a href="./doode.php">Časová dostupnost</a>');
if ($usrinfo['right_power']) { ?>
<div id="obsah">
<?php
	//Přidání nového doodlu
	if (isset($_POST['newlink'])) {
	    if (isset($_POST['link'])) {
	        mysqli_query ($database,"INSERT INTO ".DB_PREFIX."doodle VALUES('','".Time()."','".$_POST['link']."')");
	        echo '<div id=""><p>Nový link na doodle uložen.</p></div>';
	    } else {
	        echo '<div id=""><p>Link na doodle nesmí být prázdný.</p></div>';
	    }
	}
	$rec = mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
	echo '<div id=""><a href="'.$rec['link'].'" target=_new>Aktuální doodle s časovou dostupností</a><br/><br/></div>
	<div class="otherform-wrap">
		<fieldset>
			<form action="doodle.php" method="post" class="otherform">
			<label for="label"><strong>Vložit&nbsp;nový&nbsp;link&nbsp;na&nbsp;doodle&nbsp;s&nbsp;časovou&nbsp;dostupností:</strong></label>
			<input type="text" size="39" name="link" id="link" />
			<input type="submit" name="newlink" class="submitbutton" value="Vložit" />
			<div class="clear">&nbsp;</div>
			</form>
		</fieldset>
	</div>
	<!-- end of .otherform-wrap -->';
	
	// vypis starších linků
	$sql = "SELECT * FROM ".DB_PREFIX."doodle ORDER BY id DESC";
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="">
		<table>
		<thead>
		<tr>
	  	<th colspan=2>Předchozí odkazy</th>
		</tr>
		<tr>
	  	<th>Čas vložení</th>
	  	<th>Link</th>
		</tr>
		</thead>
		<tbody>
		';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
		<td>'.webdatetime($rec['datum']).'</td>
		<td><a href="'.($rec['link']).'">'.($rec['link']).'</a></td>
		</tr>';
	        $even++;
	    }
	    echo '</tbody>
	</table>
	</div>
	';
	} else {
	    echo '<div id=""><p>Žádné uložené odkazy.</p></div>';
	}
} else {
    echo '<div id=""><p>Tady nemáte co pohledávat.</p></div>';
}?>
</div>
<!-- end of #obsah -->
<?php
latteDrawTemplate("footer");
?>