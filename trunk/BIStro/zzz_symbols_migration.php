<?php
require_once ('./inc/func_main.php');
        
        // najde nejvyssi aktualni symbol id
        $sql_count="SELECT id as 'id' FROM ".DB_PREFIX."symbols ORDER BY id desc LIMIT 1";
        $res_count=MySQL_Query ($sql_count);
        $rec_count=MySQL_Fetch_Assoc($res_count);
        $highest_id=$rec_count['id'];
        
        // prida sloupec assigned to tabulky symbols
        MySQL_Query ("ALTER TABLE  `nw_symbols` ADD  `assigned` INT NOT NULL");
        // hodnoty symbolu z persons vlozi do symbols
	MySQL_Query ("INSERT INTO nw_symbols (symbol) SELECT symbol FROM nw_persons WHERE symbol <> ''");
        
        $time=time();
        
        $sql="SELECT id as 'id', symbol as 'symbol' FROM ".DB_PREFIX."symbols WHERE ".DB_PREFIX."symbols.id > ".$highest_id;
	$res=MySQL_Query ($sql);
	while ($rec_utc=MySQL_Fetch_Assoc($res)) {
                // prepise v persons puvodni hodnoty symbolu novymi symbol id
		MySQL_Query ("UPDATE ".DB_PREFIX."persons SET symbol = ".$rec_utc['id']." WHERE symbol = '".$rec_utc['symbol']."'");
                // do tabulky symbols prida k novym symbolum ostatni hodnoty
                MySQL_Query ("UPDATE ".DB_PREFIX."symbols SET created = '".$time."', created_by = 1, modified = '".$time."', modified_by = 1, assigned = 1 WHERE id = ".$rec_utc['id']);
		echo 'vysledek='.$rec_utc;
		print_r($rec_utc);
		debug_zval_dump($rec_utc);
		echo '<br />';
	}

?>