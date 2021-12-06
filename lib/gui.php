<?php
/**
 * Draw Latte Template.
 *
 * @param string template to draw
 * @param mixed $template
 */
function latteDrawTemplate($template): void
{
    global $latte,$config,$latteParameters,$text,$URL;
    if ($template == 'footer' || $template == 'footerMD') {
        latteNotification();
    }
    $latteParameters['text'] = $text;
    $latteParameters['URL'] = $URL;
    $latteParameters['config'] = $config;
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

/**
 * timestamp to date.
 *
 * @param int date timestamp
 * @param mixed $date
 *
 * @return string d. m. Y
 */
function webDate($date): string
{
    global $text;
    if ($date < '100') {
        $value = $text['nekdydavno'];
    } else {
        $value = date('d. m. Y', $date);
    }

    return $value;
}

/**
 * timestamp to date and time.
 *
 * @param int date timestamp
 * @param mixed $date
 *
 * @return string d. m. Y - H:i:s
 */
function webDateTime($date): string
{
    global $text;
    if ($date < '100') {
        $value = $text['nekdydavno'];
    } else {
        $value = date('d. m. Y - H:i:s', $date);
    }

    return $value;
}

/**
 * remove diacritics for search purposes.
 *
 * @param string string
 * @param mixed $string
 *
 * @return string string without diacritics
 */
function nocs($string): string
{
    $table = [
        'Š' => 'S',
        'š' => 's',
        'Đ' => 'Dj',
        'đ' => 'dj',
        'Ž' => 'Z',
        'ž' => 'z',
        'Č' => 'C',
        'č' => 'c',
        'Ć' => 'C',
        'ć' => 'c',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        'Ŕ' => 'R',
        'ŕ' => 'r',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'ť' => 't',
        'ů' => 'u',
        'ü' => 'u',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ę' => 'E',
        'Ó' => 'O',
        'Ą' => 'A',
        'Ś' => 'S',
        'Ł' => 'L',
        'Ż' => 'Z',
        'Ź' => 'Z',
        'Ć' => 'C',
        'Ń' => 'N',
        'ę' => 'e',
        'ó' => 'o',
        'ą' => 'a',
        'ś' => 's',
        'ł' => 'l',
        'ż' => 'z',
        'ź' => 'z',
        'ć' => 'c',
        'ń' => 'n'



    ];

    return strtr($string, $table);
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
