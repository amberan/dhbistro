<?php   
        filter();
		// vypis aktualit
		$sql="SELECT
		".DB_PREFIX."news.id AS 'id',
		".DB_PREFIX."news.datum AS 'datum',
		".DB_PREFIX."news.nadpis AS 'nadpis',
		".DB_PREFIX."news.obsah AS 'obsah',
		".DB_PREFIX."user.login AS 'autor',
		".DB_PREFIX."news.kategorie AS 'kategorie'
						FROM ".DB_PREFIX."news, ".DB_PREFIX."user
						WHERE ".DB_PREFIX."news.iduser=".DB_PREFIX."user.id ".$fsql_cat." AND ".DB_PREFIX."news.deleted = 0
						ORDER BY ".$fsql_sort."LIMIT 10";
        $res=mysqli_query ($database,$sql);
        while ($rec=mysqli_fetch_assoc ($res)) {
          echo '<div class="news_div '.(($rec['kategorie']==1)?'game_news':'system_news').'">
		<div class="news_head"><h2>'.StripSlashes($rec['nadpis']).'</h2>
		<p><span> ['.webdatetime($rec['datum']).']</span> <strong> '.$rec['autor'].' </strong>';
		if ($usrinfo['right_power']) {
			echo ' <a href="index.php?newsdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat aktualitu &quot;".StripSlashes($rec['nadpis'])."&quot;?');".'" title="smazat">smazat</a>';
			}
		echo '</p></div>
        <div>'.StripSlashes($rec['obsah']).'<br></div>
</div>';
        }
?>