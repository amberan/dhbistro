<h1 class="center">Vytvořit nového uživatele</h1>
<div class="table" id="user">
	<form action="users.php" method="post">
	<div>	
		<span>Login:</span>
		<input type="text" name="login" id="login" />
	</div>
	<div>
		<span>Heslo:</span>
		<input type="text" name="heslo" id="heslo" />
	</div>
	<div>
		<span>Číslo osoby:</span>
		<input type="text" name="idperson" id="idperson"/>
	</div>
	<div><b>Práva</b></div>
		<div>
			<span class="button">POWER USER</span>
			<select name="power" id="power">
				<option value="0">ne</option>
				<option value="1">ano</option>
			</select>
		</div>
		<div>
			<span class="button">EDITOR</span>
			<select name="texty" id="texty">
				<option value="0">ne</option>
				<option value="1">ano</option>
			</select>
		</div>
	<?php if ($usrinfo['right_aud']) { //pokud je uzivatel auditorem?>	
		<div>
			<span class="button">AUDITOR</span>
			<select name="auditor" id="auditor">
				<option value="0">ne</option>
				<option value="1">ano</option>
			</select>
		</div>
	<?php }
		if ($usrinfo['right_org']) { //pokud je uzivatel organizatorem ?>	
		<div>
			<span class="button">ORGANIZATOR</span>
			<select name="organizator" id="organizator">
				<option value="0">ne</option>
				<option value="1">ano</option>
			</select>
		</div>
	<?php } ?>
		<div>
			<input type="submit" name="insertuser" id="submitbutton" value="Uložit" />
		</div>
	</form>
</div>