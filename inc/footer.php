<?php
function pageEnd () {
        global $starttime,$time,$mem;
?>
                </div>
        </body>
</html>
<?php
   echo "\n<!-- Vygenerováno za ".(round(array_sum(explode(" ",microtime())),4) - round($starttime,4))." vteřin -->\n";

  debug(array(
	'memory kB' => (memory_get_usage() - $mem) / 1024,
	'seconds' => microtime(TRUE) - $time
  ),"timer");
} ?>