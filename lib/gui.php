<?php
/**
 * Draw Latte Template.
 *
 * @param string template to draw
 * @param mixed $template
 */
function latteDrawTemplate($template): void
{
    global $latte,$config,$latteParameters;
    $latte->render($config['folder_templates'].$template.'.latte', $latteParameters);
}
