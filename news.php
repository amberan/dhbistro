<?php   
        filter();
        // vypis aktualit
        $sql="SELECT
                ".DB_PREFIX."news.datum AS 'datum',
                ".DB_PREFIX."news.nadpis AS 'nadpis',
                ".DB_PREFIX."news.obsah AS 'obsah',
                ".DB_PREFIX."users.login AS 'autor',
                ".DB_PREFIX."news.kategorie AS 'kategorie'
                                FROM ".DB_PREFIX."news, ".DB_PREFIX."users
                                WHERE ".DB_PREFIX."news.iduser=".DB_PREFIX."users.id ".$fsql_cat."
                                ORDER BY ".$fsql_sort."LIMIT 10";
        $res=mysqli_query ($database,$sql);
        while ($rec=mysqli_fetch_assoc ($res)) {
          echo '<div class="news_div '.(($rec['kategorie']==1)?'game_news':'system_news').'">
        <div class="news_head"><h2>'.StripSlashes($rec['nadpis']).'</h2>
        <p><span>['.Date ('d. m. Y - H:i:s',$rec['datum']).']</span> <strong>'.$rec['autor'].'</strong></p></div>
        <div>'.StripSlashes($rec['obsah']).'</div>
</div>';
        }
?>