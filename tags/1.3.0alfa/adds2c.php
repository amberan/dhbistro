<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava případu');
	mainMenu (5);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přiřazení řešitelům</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."cases WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>

<div id="obsah">
<p>
K případu můžete přiřadit řešitele.
</p>

<form action="addpersons.php" method="post" class="otherform">
<?php

	// vypis osob
	$sql="SELECT ".DB_PREFIX."users.id AS 'id', ".DB_PREFIX."users.login AS 'login', ".DB_PREFIX."c2s.iduser FROM ".DB_PREFIX."users LEFT JOIN ".DB_PREFIX."c2s ON ".DB_PREFIX."c2s.idsolver=".DB_PREFIX."users.id AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']." WHERE ".DB_PREFIX."users.deleted=0 ORDER BY ".DB_PREFIX."users.login ASC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="">
<table>
<thead>
	<tr>
	<th>#</th>
	  <th>Uživatel</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td><input type="checkbox" name="solver[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' /></td>';
		  echo '<td>'.StripSlashes($rec['login']).'</td></tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
?>

<div>
<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="submit" value="Uložit změny" name="addsolver" class="submitbutton" />
</div>
</form>

</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Případ neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>