<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	pageStart ('Přihlášení do systému');
?>
<div id="logincontent">
	<div id="loginmiddle">
		<form action="index.php" method="post" id="<?php echo (($config['verze']==0 || $config['verze']==1 || $config['verze']==3)?'dh':'other'); ?>">
  		<div>
  			<label for="loginname">Jméno:</label>
				<input type="text" name="loginname" id="loginname" tabindex="1" />
			</div>
			<div>
				<label for="loginpwd">Heslo:</label>
				<input type="password" name="loginpwd" id="loginpwd" tabindex="2" />
			</div>
			<div id="logmein">
				<input type="submit" name="logmein" value="Přihlásit" tabindex="3" />
			</div>
		</form>
	</div>
</div>
<?php
	pageEnd ();
?>