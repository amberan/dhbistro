<?php
	require_once ('./inc/func_main.php');
	auditTrail(7, 1, 0);
	pageStart ('Vyhledané symboly');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="newperson.php">přidat osobu</a>; <a href="symbols.php">nepřiřazené symboly</a>; <a href="symbol_search.php">vyhledat symbol</a>');
 
if (isset($_POST['searchit'])) {
	// ############################################## Určení vyhledavaneho znaku #
	// test input
	//		$input_liner=1;
	//		$input_curver=2;
	//		$input_pointer=3;
	//		$input_geometrical=1;
	//		$input_alphabeter=2;
	//		$input_specialchar=3;
	
	// real input
	$input_liner = mysqli_real_escape_string ($database,htmlspecialchars($_POST['l']));
	$input_curver = mysqli_real_escape_string ($database,htmlspecialchars($_POST['c']));
	$input_pointer = mysqli_real_escape_string ($database,htmlspecialchars($_POST['p']));
	$input_geometrical = mysqli_real_escape_string ($database,htmlspecialchars($_POST['g']));
	$input_alphabeter = mysqli_real_escape_string ($database,htmlspecialchars($_POST['a']));
	$input_specialchar = mysqli_real_escape_string ($database,htmlspecialchars($_POST['sch']));
	
			///////////ECHO TEST PROMENNE
			//$vypis='liner= '.$l.', curver= '.$c.', pointer= '.$p.', geometrical= '.$g.', alphabeter= '.$a.', specialchar= '.$sch.'';
			//echo $vypis;
			//////////
			
	// ############################################## Vyhledavani a rovnani znaku #
	//QUERY
	$symbol_query_sql=("
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
				FROM ".DB_PREFIX."symbols AS s
				LEFT JOIN ".DB_PREFIX."persons AS p
				ON s.id = p.symbol				
				) AS searchsymbol
				WHERE deleted=0
				ORDER BY averangepercent DESC
			");
			//RESULT
	mysqli_query ($database,'SET NAMES utf8');
	// Kontrola SQL dotazy ////////////////////////
	//echo $symbol_query_sql;
	///////////////////////////////////////////////
		$symbol_result=mysqli_query ($database,$symbol_query_sql) or die ("Vyhledávání a srovnání symbolů neprošlo! SQL: $symbol_query_sql");
	
///////////////////////// FUNCTIONS /////////////////////////////
	
	// funkce pro změnu barvy ve výsledku vyhledávání
		
	function colorSwitch($input_sql_column)
	{
		$segment_color;
	
		if($input_sql_column == '100'){$segment_color="grey";}
			elseif(('100'>$input_sql_column) && ($input_sql_column>='75'))$segment_color="blue";
			elseif(('75'>$input_sql_column) && ($input_sql_column>='50'))$segment_color="green";
			elseif(('50'>$input_sql_column) && ($input_sql_column>='25'))$segment_color="yellow";
			elseif(('25'>$input_sql_column) && ($input_sql_column>'0'))$segment_color="orange";
			elseif(('0'>$input_sql_column) && ($input_sql_column != ''))$segment_color="red";
			elseif($input_sql_column == '0')$segment_color="brown";
			elseif($input_sql_column == '')$segment_color="white";
	
			return $segment_color;
	}
	
	// funkce pro string dotazu na osobu
	
	function ownerString($person_id,$symbol_id)
	{
		$segment_output;
	
		if($person_id === ''){
			$segment_output="<a class=\"redirection\" href=\"readsymbol.php?rid=".$symbol_id."&hidenotes=0\">Zobrazit info k symbolu</a>";
		}
		else {
			$segment_output="<a class=\"redirection\" href=\"readperson.php?rid=".$person_id."&hidenotes=0\">Zobrazit info k vlastníkovi</a>";
		}
	
		return $segment_output;
	}
	
//////////////////////////////////////////////////////////////////
		
	?>
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
	              	<?php echo $input_liner ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_curver ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_pointer ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_geometrical ?>&nbsp;&nbsp;&nbsp;
	               	<?php echo $input_alphabeter ?>&nbsp;&nbsp;&nbsp;<?php echo $input_specialchar ?>
	            </p>
	        </div>
	    </div>
	    <?php
	    	//echo $symbol_record = mysqli_fetch_array ($symbol_result);
	    		    
			$result = '
	    <div class="central_result_frame">'; while($symbol_record = mysqli_fetch_assoc ($symbol_result)){
	    if($usrinfo['right_power']){
				
				$color_l = colorSwitch((string)$symbol_record['lining']);
				$color_c = colorSwitch((string)$symbol_record['curving']);
				$color_p = colorSwitch((string)$symbol_record['pointing']);
				$color_g = colorSwitch((string)$symbol_record['geometricaling']);
				$color_a = colorSwitch((string)$symbol_record['alphabeting']);
				$color_sch = colorSwitch((string)$symbol_record['specialing']);
				$ownerhttp = ownerString((string)$symbol_record['pid'],(string)$symbol_record['id']);
				
			$result.='
			<div class="result">
	        	<div class="result_symbol_image">
	            	<img src="getportrait.php?nrid='.$symbol_record['id'].'" height="75" width="75" />
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
			} else {
                           if ($symbol_record['ssecret']==0 && $symbol_record['psecret']==0) { 
                            
                            $color_l = colorSwitch((string)$symbol_record['lining']);
                            $color_c = colorSwitch((string)$symbol_record['curving']);
                            $color_p = colorSwitch((string)$symbol_record['pointing']);
                            $color_g = colorSwitch((string)$symbol_record['geometricaling']);
                            $color_a = colorSwitch((string)$symbol_record['alphabeting']);
                            $color_sch = colorSwitch((string)$symbol_record['specialing']);
                            $ownerhttp = ownerString((string)$symbol_record['pid'],(string)$symbol_record['id']);

			$result.='
			<div class="result">
	        	<div class="result_symbol_image">
	            	<img src="getportrait.php?nrid='.$symbol_record['id'].'" height="75" width="75" />
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
                           };
                    };
		}
	    $result.='</div>';
		
		echo $result;
} else {
	pageEnd();
}
?>