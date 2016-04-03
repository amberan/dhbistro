<?php
	require_once ('./inc/func_main.php');
        pageStart ('Přiřazení k hlášení');
	mainMenu (5);
?>

<form action="" method="post">
    <input type="text" placeholder="Jméno" id="personAutocomplete" class="ui-autocomplete-input" autocomplete="off" />
</form>

<script language="JavaScript" type="text/javascript">
$(document).ready(function($){
	$('#personAutocomplete').autocomplete({
		source:'asearch_search.php', 
		minLength:2,
		select: function(event,ui){
			var code = ui.item.id;
			if(code != '') {
				location.href = '/BIStro/readperson.php?rid=' + code;
			}
		},
                // optional
		html: true, 
		// optional (if other layers overlap the autocomplete list)
		open: function(event, ui) {
			$(".ui-autocomplete").css("z-index", 1000);
		}
	});
});
</script>