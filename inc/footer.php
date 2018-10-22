<?php

	// vypis konce stranky
	function pageEnd () {
        echo '		<!-- Vygenerováno za '.(array_sum(explode(" ",microtime())) - $starttime).' vteřin -->';
?>
  </div>
  <!-- end of #wrapper -->
  </body>
  </html>
  <?php
      }
 ?>