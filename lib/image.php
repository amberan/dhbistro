<?php

// pridat imagemagic manipulaci se symboly

function imageResize($img,$maxWidth,$maxHeight)
{
    $size = getimagesize($img);
    $width = $size[0];
    $height = $size[1];
    $ratioX = $maxWidth / $width;
    $ratioY = $maxHeight / $height;
    if (($width <= $maxWidth) && ($height <= $maxHeight)) {
        $widthTn = $width;
        $heightTn = $height;
    } else {
        if ($ratioX * $height < $maxHeight) {
            $heightTn = ceil($ratioX * $height);
            $widthTn = $maxWidth;
        } else {
            $widthTn = ceil($ratioY * $width);
            $heightTn = $maxHeight;
        }
    }
    if ($size[2] === 1) {
        $src = imagecreatefromgif($img);
    }
    if ($size[2] === 2) {
        $src = imagecreatefromjpeg($img);
    }
    if ($size[2] === 3) {
        $src = imagecreatefrompng($img);
    }
    $dst = imagecreatetruecolor($widthTn,$heightTn);
    imagecopyresampled($dst,$src,0,0,0,0,$widthTn,$heightTn,$width,$height);
    imageinterlace($dst, 1);
    imagedestroy($src);

    return $dst;
}
