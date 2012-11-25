<?php
	require_once ('./inc/func_main.php');
		pageStart ('Časová dostupnost');
		mainMenu (4);
		sparklets ('<a href="./doode.php">Časová dostupnost</a>');
if ($usrinfo['right_power']) { ?>
<div id="obsah">
<?php 
	//Přidání nového doodlu
	if (isset($_POST['newlink'])) {
		if (isset($_POST['link'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."doodle VALUES('','".Time()."','".mysql_real_escape_string(safeInput($_POST['link']))."')");
			echo '<div id=""><p>Nový link na doodle uložen.</p></div>';
		} else {
			echo '<div id=""><p>Link na doodle nesmí být prázdný.</p></div>';
		}
	}
	$rec=MySQL_Fetch_Assoc(MySQL_Query ("SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
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
	$sql="SELECT * FROM ".DB_PREFIX."doodle ORDER BY id DESC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
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
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
		<td>'.Date ('d. m. Y - H:i:s',$rec['datum']).'</td>
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
pageEnd ();
?>