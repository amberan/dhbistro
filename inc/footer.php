<?php
function pageEnd () {
        global $starttime,$time,$mem;
?>		</div>
		<script src="./js/mrFixit.js"></script>
		<script src="./js/tinymce5/tinymce.min.js"></script>
		<script src="./js/tinymce5/jquery.tinymce.min.js"></script>
		<script>
		tinymce.init({
			selector: "textarea",
			entity_encoding: "raw",
			plugins: [
				"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
				"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				"save table contextmenu directionality template paste textcolor"
			],
			toolbar: "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor table removeformat",
			menubar: false,
			toolbar_items_size: "small"
		});
		</script>
<?php
echo "\n<!-- Vygenerováno za ".(round(array_sum(explode(" ",microtime())),4) - round($starttime,4))." vteřin -->\n";
debug(array(
	'memory kB' => (memory_get_usage() - $mem) / 1024,
	'seconds' => microtime(TRUE) - $time
),"timer");
echo "</body></html>"; } ?>