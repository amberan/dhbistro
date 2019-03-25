<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Aktuality';
  
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

mainMenu (1);
	sparklets ('<a href="index.php">aktuality</a> &raquo; <a href="dashboard.php">nástěnka</a> &raquo; <strong>úprava nástěnky</strong>');

?>

<div id="obsah">
<fieldset><legend><strong>Obsah nástěnky</strong></legend>
	<form action="procother.php" method="post" id="inputform">
	<textarea cols="140" rows="50" name="contents" id="contents">
	<div class="field-text">
	<?php $res_d=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
	if ($rec_d=mysqli_fetch_assoc ($res_d)) {
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
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>