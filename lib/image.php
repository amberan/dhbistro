<?php

// pridat imagemagic manipulaci se symboly


function imageResize ($img,$maxWidth,$maxHeight)
{
    $size = GetImageSize($img);
    $width = $size[0];
    $height = $size[1];
    $ratioX = $maxWidth / $width;
    $ratioY = $maxHeight / $height;
    if (($width <= $maxWidth) && ($height <= $maxHeight)) {
        $widthTn = $width;
        $heightTn = $height;
    } else {
        if (($ratioX * $height) < $maxHeight) {
            $heightTn = ceil($ratioX * $height);
            $widthTn = $maxWidth;
        } else {
            $widthTn = ceil($ratioY * $width);
            $heightTn = $maxHeight;
        }
    }
    if ($size[2] == 1) {
        $src = ImageCreateFromGIF($img);
    }
    if ($size[2] == 2) {
        $src = ImageCreateFromJPEG($img);
    }
    if ($size[2] == 3) {
        $src = ImageCreateFromPNG($img);
    }
    $dst = ImageCreateTrueColor($widthTn,$heightTn);
    ImageCopyResampled ($dst,$src,0,0,0,0,$widthTn,$heightTn,$width,$height);
    Imageinterlace($dst, 1);
    ImageDestroy($src);

    return $dst;
}

?>