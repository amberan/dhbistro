<?php
function pageEnd () {
        global $starttime;
        echo "\n                <!-- Vygenerováno za ".(round(array_sum(explode(" ",microtime())),4) - round($starttime,4))." vteřin -->\n";
?>
                </div>
        </body>
</html>
<?php } ?>