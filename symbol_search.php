<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	auditTrail(7, 14, 0);
	pageStart ('Osoby');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="newperson.php">přidat osobu</a>; <a href="symbols.php">nepřiřazené symboly</a>; <a href="symbol_search.php">vyhledat symbol</a>');
	?>
		<link href="css/symbolstyle.css" rel="stylesheet" type="text/css" />
	    <div class="message_frame">
	    	<p class="message_text">Zadejte specifikaci vyhledávaného symbolu</p>
	    </div>
	    <form action="symbol_search_result.php" method="post" enctype="multipart/form-data">
	    	<datalist id=hodnoty>
				<option>0</option>
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
				<option>6</option>
				<option>7</option>
				<option>8</option>
				<option>9</option>
				<option>10</option>
			</datalist>		
	    <div class="central_frame">
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Čáry</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="l" id="liner" list=hodnoty />
	            </div>
	        </div>
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Křivky</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="c" id="curver" list=hodnoty />
	            </div>
	        </div>
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Body</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="p" id="pointer" list=hodnoty />
	            </div>
	        </div>
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Geom. tvary</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="g" id="geometrical" list=hodnoty />
	            </div>
	        </div>
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Písma</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="a" id="alphabeter" list=hodnoty />
	            </div>
	        </div>
	        <div class="input_frame">
	        	<div class="input_text">
	            	<div class="input_text_margin"></div>
	            	<p class="label_text">Unikátní znaky</p>
	            </div>
	            <div class="input_slider">
	            	<div class="input_slider_numbers">
	                	<p class="input_slider_numbers_text">
	                    	0&nbsp;&nbsp;&nbsp;
	                    	1&nbsp;&nbsp;&nbsp;
	                    	2&nbsp;&nbsp;&nbsp;
	                    	3&nbsp;&nbsp;&nbsp;
	                    	4&nbsp;&nbsp;&nbsp;
	                    	5&nbsp;&nbsp;&nbsp;
	                    	6&nbsp;&nbsp;&nbsp;
	                    	7&nbsp;&nbsp;&nbsp;
	                    	8&nbsp;&nbsp;&nbsp;
	                    	9&nbsp;&nbsp;&nbsp;10
	                	</p>
	                </div>
	        		<input type="range" min="0" max="10" step="1" value="0" name="sch" id="specialchar" list=hodnoty />
	            </div>
	        </div>
	    </div>
	    <div class="button_frame">
	    	<input type="submit" name="searchit"  value="Vyhledat symbol" title="Vyhledat"/>
	    </div>
	    </form>
	    <?php
		pageEnd();
		?>