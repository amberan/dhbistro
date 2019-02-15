<?php
function pageEnd () {
        global $starttime,$time,$mem;
?>		</div>
		<script src="./js/jquery-3.3.1.min.js"></script>
		<script src="./js/mrFixit.js"></script>
		<script src="./js/tinymce5/tinymce.min.js"></script>
		<script src="./js/tinymce.init.js"></script>
<?php
echo "\n<!-- Vygenerováno za ".(round(array_sum(explode(" ",microtime())),4) - round($starttime,4))." vteřin -->\n";
echo "</body></html>"; } ?>