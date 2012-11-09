<?php
require_once ('./inc/func_main.php');

?>

<script type="text/javascript" language="JavaScript">
<!--
function NameChanger()
{
	if(document.addpersons.isthere.checked == true) {
		document.addpersons.role.name = 'role[]';
	}
	if(document.addpersons.isthere.checked == false) {
		document.addpersons.role.name = 'norole[]';
	}
	return true;
}
// -->
</script>


<form action="post.php" method="post" name="payment">
<input type=hidden id="methodinput" name="default_name" >
<input id="rsubmit_post" type="radio" name="method" onClick="return NameChanger();">Option One - Post Payment<br />
<input id="rsubmit_transfer" type="radio" name="method" onClick="return NameChanger();">Option Two - Wire Transfer<br />
<input id="rsubmit_cash" type="radio" name="method" onClick="return NameChanger();">Option Three - Cash<br />
<input type="submit" value="Send me money!">
</form>

