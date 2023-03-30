<?php

function notesType($noteType = null)
{
    global $text;
    $list = [];
    $list[0] = $text['notePublic'];
    $list[1] = $text['noteSecret'];
    $list[2] = $text['notePrivate'];
    $return = $list;
    if (isset($noteType) && is_numeric($noteType)) {
        $return = $list[$noteType];
    } elseif (isset($noteType) && is_string($noteType)) {
        $return = array_search($noteType, $list);
    }
    return $return;
}
