<?php
/**
* Draw Latte Template
* @param string template to draw
 */
function latteDrawTemplate($template)
{
    global $latte,$config,$latteParameters;
    $latte->render($config['folder_templates'].$template.'.latte', $latteParameters);
}



?>