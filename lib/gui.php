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


function date_picker($name, $startyear = null, $endyear = null, $preset = null)
{
    global $user;
    if ($startyear == null) {
        echo $startyear = date("Y")-10;
    }
    if ($endyear == null) {
        $endyear = date("Y") ; //+ 5;
    }
    if ($preset != null) {
        $presetDay = date('j', $preset);
        $presetMonth = date('n', $preset);
        $presetYear = date('Y', $preset);
    }

    $months = ['', 'Leden', 'Únor', 'Březen', 'Duben', 'Květen',
        'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec', ];

    // roletka dnů
    $html = "<select class=\"day\" name=\"".$name."day\">";
    for ($i = 1; $i <= 31; $i++) {
        $html .= "<option ".($i == $presetDay ? ' selected' : '')." value='$i'>$i</option>";
    }
    $html .= "</select> ";

    // roletka měsíců
    $html .= "<select class=\"month\" name=\"".$name."month\">";

    for ($i = 1; $i <= 12; $i++) {
        $html .= "<option ".($i == $presetMonth ? ' selected' : '')." value='$i'>$months[$i]</option>";
    }
    $html .= "</select> ";

    // roletka let
    $html .= "<select class=\"year\" name=\"".$name."year\">";

    for ($i = $startyear; $i <= $endyear; $i++) {
        $html .= "<option ".($i == $presetYear ? ' selected' : '')." value='$i'>$i</option>";
    }
    $html .= "</select> ";

    return $html;
}
