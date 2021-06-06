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
    if ($template == 'footer' || $template == 'footerMD') {
        latteNotification();
    }
    $latte->render($config['folder_templates'].$template.'.latte', $latteParameters);
}

/**
 * Fill notification modal window if $_SESSION['message'] exists.
 */
function latteNotification(): void
{
    global $latteParameters,$_SESSION;
    if (isset($_SESSION['message'])) {
        $latteParameters['message'] = $_SESSION['message'];
        unset($_SESSION['message']);
    }
}
