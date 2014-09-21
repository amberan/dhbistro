<?php
	require_once ('./inc/func_main.php');
	pageStart ('Aktuality');
	mainMenu (1);
	sparklets ('<a href="index.php">aktuality</a> &raquo; <a href="dashboard.php">nástěnka</a> &raquo; <strong>úprava nástěnky</strong>');

?>

<div id="obsah">
<fieldset><legend><h2>Obsah nástěnky</h2></legend>
	<form action="procother.php" method="post" id="inputform">
	<textarea cols="140" rows="50" name="contents" id="contents">
	<div class="field-text">
	<?php $res_d=MySQL_Query ("SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
	if ($rec_d=MySQL_Fetch_Assoc($res_d)) {
		if (isset($rec_d['content'])) {
			echo StripSlashes($rec_d['content']);
		} else { 
			echo ''; 
		}
	} else {
		echo '';
	}
	?>
	</div>
	</textarea>
	 <input type="submit" name="editdashboard" id="submitbutton" value="Vložit" />
	</form>
	</fieldset>
</div>

<?php
	pageEnd ();
?>