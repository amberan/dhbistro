<?php
function pageEnd () {
        global $starttime,$time,$mem;
?>
                </div>
        </body>
</html>
<?php
   echo "\n<!-- Vygenerováno za ".(round(array_sum(explode(" ",microtime())),4) - round($starttime,4))." vteřin -->\n";

  xmp(array(
	'memory' => (memory_get_usage() - $mem) / (1024 * 1024),
	'seconds' => microtime(TRUE) - $time
  ));
} ?>