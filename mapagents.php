<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
		pageStart ('Mapa agentů');
		mainMenu (4);
		sparklets ('<a href="./mapagents.php">Mapa agentů</a>');
if ($usrinfo['right_power']) { ?>
<div id="obsah">
<?php 
	//Přidání nové mapy
	if (isset($_POST['newmap'])) {
		if (isset($_POST['link'])) {
			mysqli_query ($database,"INSERT INTO ".DB_PREFIX."map VALUES('','".Time()."','".$_POST['link']."')");
			echo '<div id=""><p>Nový link na mapu agentů uložen.</p></div>';
		} else {
			echo '<div id=""><p>Link na mapu agentů nesmí být prázdný.</p></div>';
		}
	}
	$rec=mysqli_fetch_assoc (mysqli_query ($database,"SELECT link FROM ".DB_PREFIX."map ORDER BY id desc LIMIT 0,1"));
	echo '<div id=""><a href="'.$rec['link'].'" target=_new>Aktuální mapa agentů</a><br/><br/></div>
	<div class="otherform-wrap">
		<fieldset>
			<form action="mapagents.php" method="post" class="otherform">
			<label for="label"><strong>Vložit&nbsp;nový&nbsp;link&nbsp;na&nbsp;mapu&nbsp;agentů:</strong></label>
			<input type="text" size="39" name="link" id="link" />
			<input type="submit" name="newmap" class="submitbutton" value="Vložit" />
			<div class="clear">&nbsp;</div>
			</form>
		</fieldset>
	</div>
	<!-- end of .otherform-wrap -->';
	
	// vypis starších linků
	$sql="SELECT * FROM ".DB_PREFIX."map ORDER BY id DESC";
	$res=mysqli_query ($database,$sql);
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
		$even=0;
		while ($rec=mysqli_fetch_assoc ($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
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
} ?>
</div>
<!-- end of #obsah -->
<?php	
pageEnd ();
?>