<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;


latteDrawTemplate("header");

$latteParameters['title'] = 'Vyhledané symboly';

authorizedAccess(7, 1, 0);

    mainMenu();
    sparklets('<a href="/persons/">osoby</a> &raquo; <a href="newperson.php">přidat osobu</a>; <a href="symbols.php">nepřiřazené symboly</a>; <a href="symbol_search.php">vyhledat symbol</a>');

if (isset($_POST['searchit'])) {
    $input_liner = htmlspecialchars($_POST['l']);
    $input_curver = htmlspecialchars($_POST['c']);
    $input_pointer = htmlspecialchars($_POST['p']);
    $input_geometrical = htmlspecialchars($_POST['g']);
    $input_alphabeter = htmlspecialchars($_POST['a']);
    $input_specialchar = htmlspecialchars($_POST['sch']);



    $symbol_query_sql = "
			SELECT id,
				   assigned,
				   symbol,
				   lining,
				   curving,
				   pointing,
				   geometricaling,
				   alphabeting,
				   specialing,
				   pid,
                                   ssecret,
                                   psecret,
				   CONCAT(name,' ',surname) AS title,
			(
				COALESCE(lining,0)+
				COALESCE(curving,0)+
				COALESCE(pointing,0)+
				COALESCE(geometricaling,0)+
				COALESCE(alphabeting,0)+
				COALESCE(specialing,0)
			)/(6 - (
						ISNULL(lining)+
						ISNULL(curving)+
						ISNULL(pointing)+
						ISNULL(geometricaling)+
						ISNULL(alphabeting)+
						ISNULL(specialing)
					)
			)AS averangepercent
			FROM (
				SELECT s.id AS id,
					   s.assigned AS assigned,
					   s.symbol AS symbol,
					   s.deleted AS deleted,
                                           s.secret AS ssecret,
                                           p.secret AS psecret,
					CASE WHEN (s.search_lines=0 AND $input_liner>0) THEN 0
						 WHEN (s.search_lines>0 AND $input_liner=0) THEN 0
						 WHEN (s.search_lines=0 AND $input_liner=0) THEN null
						 WHEN ($input_liner/s.search_lines)>1 THEN (s.search_lines/$input_liner)*100
						ELSE ($input_liner/s.search_lines)*100 END AS lining,
					CASE WHEN (s.search_curves=0 AND $input_curver>0) THEN 0
						 WHEN (s.search_curves>0 AND $input_curver=0) THEN 0
						 WHEN (s.search_curves=0 AND $input_curver=0) THEN null
						 WHEN ($input_curver/s.search_curves)>1 THEN (s.search_curves/$input_curver)*100
						ELSE ($input_curver/s.search_curves)*100 END AS curving,
					CASE WHEN (s.search_points=0 AND $input_pointer>0) THEN 0
						 WHEN (s.search_points>0 AND $input_pointer=0) THEN 0
						 WHEN (s.search_points=0 AND $input_pointer=0) THEN null
						 WHEN ($input_pointer/s.search_points)>1 THEN (s.search_points/$input_pointer)*100
						ELSE ($input_pointer/s.search_points)*100 END AS pointing,
					CASE WHEN (s.search_geometricals=0 AND $input_geometrical>0) THEN 0
						 WHEN (s.search_geometricals>0 AND $input_geometrical=0) THEN 0
						 WHEN (s.search_geometricals=0 AND $input_geometrical=0) THEN null
						 WHEN ($input_geometrical/s.search_geometricals)>1 THEN (s.search_geometricals/$input_geometrical)*100
						ELSE ($input_geometrical/s.search_geometricals)*100 END AS geometricaling,
					CASE WHEN (s.search_alphabets=0 AND $input_alphabeter>0) THEN 0
						 WHEN (s.search_alphabets>0 AND $input_alphabeter=0) THEN 0
						 WHEN (s.search_alphabets=0 AND $input_alphabeter=0) THEN null
						 WHEN ($input_alphabeter/s.search_alphabets)>1 THEN (s.search_alphabets/$input_alphabeter)*100
						ELSE ($input_alphabeter/s.search_alphabets)*100 END AS alphabeting,
					CASE WHEN (s.search_specialchars=0 AND $input_specialchar>0) THEN 0
						 WHEN (s.search_specialchars>0 AND $input_specialchar=0) THEN 0
						 WHEN (s.search_specialchars=0 AND $input_specialchar=0) THEN null
						 WHEN ($input_specialchar/s.search_specialchars)>1 THEN (s.search_specialchars/$input_specialchar)*100
						ELSE ($input_specialchar/s.search_specialchars)*100 END AS specialing,
					CASE WHEN ISNULL(p.id) THEN null
						ELSE p.id END AS pid,
					CASE WHEN ISNULL(p.name) THEN \"Nepřiřazený\"
	 					ELSE p.name END AS name,
					CASE WHEN ISNULL(p.surname) THEN \"symbol\"
     					ELSE p.surname END AS surname
				FROM ".DB_PREFIX."symbol AS s
				LEFT JOIN ".DB_PREFIX."person AS p
				ON s.id = p.symbol
				) AS searchsymbol
				WHERE deleted=0
				ORDER BY averangepercent DESC
			";
    $symbol_result = mysqli_query($database, $symbol_query_sql) or die("Vyhledávání a srovnání symbolů neprošlo! SQL: $symbol_query_sql");


    function colorSwitch($inputSqlColumn)
    {
        $segmentColor;

        if ($inputSqlColumn == '100') {
            $segmentColor = "grey";
        } elseif (('100' > $inputSqlColumn) && ($inputSqlColumn >= '75')) {
            $segmentColor = "blue";
        } elseif (('75' > $inputSqlColumn) && ($inputSqlColumn >= '50')) {
            $segmentColor = "green";
        } elseif (('50' > $inputSqlColumn) && ($inputSqlColumn >= '25')) {
            $segmentColor = "yellow";
        } elseif (('25' > $inputSqlColumn) && ($inputSqlColumn > '0')) {
            $segmentColor = "orange";
        } elseif (('0' > $inputSqlColumn) && ($inputSqlColumn != '')) {
            $segmentColor = "red";
        } elseif ($inputSqlColumn == '0') {
            $segmentColor = "brown";
        } elseif ($inputSqlColumn == '') {
            $segmentColor = "white";
        }

        return $segmentColor;
    }

    // funkce pro string dotazu na osobu

    function ownerString($personId, $symbolId)
    {
        $segmentOutput;

        if ($personId == '') {
            $segmentOutput = "<a class=\"redirection\" href=\"readsymbol.php?rid=".$symbolId."&hidenotes=0\">Zobrazit info k symbolu</a>";
        } else {
            $segmentOutput = "<a class=\"redirection\" href=\"readperson.php?rid=".$personId."&hidenotes=0\">Zobrazit info k vlastníkovi</a>";
        }

        return $segmentOutput;
    } ?>
		<link href="css/symbolstyle.css" rel="stylesheet" type="text/css" />
	    <div class="top_margin"></div>
	    <div class="message_frame">
	    	<p class="message_text">Výsledek vyhledávání</p>
	    </div>
	    <div class="message_frame">
	    	<div class="predis_symbols">
	        	<img class="predis_image" src="images/line.png" height="10" width="10"/>
	            <img class="predis_image" src="images/curve.png" height="10" width="10" />
	            <img class="predis_image" src="images/point.png" height="10" width="10" />
	            <img class="predis_image" src="images/geometrical.png" height="10" width="10" />
	            <img class="predis_image" src="images/alphabet.png" height="10" width="10" />
	            <img class="predis_image" src="images/special.png" height="10" width="10" />
	        </div>
	        <div class="predis_numbers">
	        	<p class="label_text">
	            	&nbsp;&nbsp;
	              	<?php echo $input_liner; ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_curver; ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_pointer; ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_geometrical; ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_alphabeter; ?>&nbsp;&nbsp;&nbsp;<?php echo $input_specialchar; ?>
	            </p>
	        </div>
	    </div>
	    <?php

            $result = '
	    <div class="central_result_frame">';
    while ($symbol_record = mysqli_fetch_assoc($symbol_result)) {
        if ($user['aclSecret']) {
            $color_l = colorSwitch((string) $symbol_record['lining']);
            $color_c = colorSwitch((string) $symbol_record['curving']);
            $color_p = colorSwitch((string) $symbol_record['pointing']);
            $color_g = colorSwitch((string) $symbol_record['geometricaling']);
            $color_a = colorSwitch((string) $symbol_record['alphabeting']);
            $color_sch = colorSwitch((string) $symbol_record['specialing']);
            $ownerhttp = ownerString((string) $symbol_record['pid'], (string) $symbol_record['id']);

            $result .= '
			<div class="result">
	        	<div class="result_symbol_image">
	            	<img src="file/symbol/'.$symbol_record['id'].'" height="75" width="75" />
	            </div>
	            <div class="result_stats">
	            	<div class="result_stats_singles">
	                    	<img class="predis_image" src="images/'.$color_l.'.png" height="5" />
	                    	<img class="predis_image" src="images/'.$color_c.'.png" height="5" />
	                    	<img class="predis_image" src="images/'.$color_p.'.png" height="5" />
	                    	<img class="predis_image" src="images/'.$color_g.'.png" height="5" />
	                    	<img class="predis_image" src="images/'.$color_a.'.png" height="5" />
	                    	<img class="predis_image" src="images/'.$color_sch.'.png" height="5" />
	                        <div class="result_stats_singles_parts">
	                        	<p class="result_stats_singles_parts_text"><b>Vlastník: '.$symbol_record['title'].'</b></p>
	                        </div>
	                        <div class="result_stats_singles_url">
	                        	'.$ownerhttp.'
	                        </div>
	                </div>
	                <div class="result_symbol_avgpercent">
	                	<div class="result_symbol_avgpercent_margin">
	                    	<p class="label_text">&nbsp;&nbsp;&nbsp;&nbsp;%</p>
	                    </div>
	                	<p class="avgpercent_text">'.number_format($symbol_record['averangepercent'], 1, '.', '').'</p>
	                </div>
	            </div>
	        </div>';
        }
    }
    $result .= '</div>';

    echo $result;
} else {
    latteDrawTemplate("footer");
}
?>
