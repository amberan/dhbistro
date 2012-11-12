<?php
	require_once ('./inc/func_main.php');
		pageStart ('Mapa agentů');
		mainMenu (4);
		sparklets ('<a href="./mapagents.php">Mapa agentů</a>');
if ($usrinfo['right_power']) {
	//Přidání nové mapy
	if (isset($_POST['newmap'])) {
		if (isset($_POST['link'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."map VALUES('','".Time()."','".mysql_real_escape_string(safeInput($_POST['link']))."')");
			echo '<div id="obsah"><p>Nový link na mapu agentů uložen.</p></div>';
		} else {
			echo '<div id="obsah"><p>Link na mapu agentů nesmí být prázdný.</p></div>';
		}
	}
	$rec=MySQL_Fetch_Assoc(MySQL_Query ("SELECT link FROM ".DB_PREFIX."map ORDER BY id desc LIMIT 0,1"));
	echo '<div id="obsah"><a href="'.$rec['link'].'" target=_new>Aktuální mapa agentů</a></div>
	<form action="mapagents.php" method="post" id="inputform">
	<div>
	<label for="label">Vložit nový link na mapu agentů:</label>
	</div>
	<div>
	<input type="text" name="link" id="link" />
	<input type="submit" name="newmap" id="submitbutton" value="Vložit" />
	</div>
	</form>';
	
	// vypis starších linků
	$sql="SELECT * FROM ".DB_PREFIX."map ORDER BY id DESC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
		echo '<div id="obsah">
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
	  echo '<div id="obsah"><p>Žádné uložené odkazy.</p></div>';
	}
} else {
	echo '<div id="obsah"><p>Tady nemáte co pohledávat.</p></div>';
}		
pageEnd ();
?>