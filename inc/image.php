<?php

// pridat imagemagic manipulaci se symboly


function resize_Image ($img,$max_width,$max_height) {
	$size=GetImageSize($img);
	$width=$size[0];
	$height=$size[1];
	$x_ratio=$max_width/$width;
	$y_ratio=$max_height/$height;
	if (($width<=$max_width) && ($height<=$max_height)) {
		$tn_width=$width;
		$tn_height=$height;
	} else if (($x_ratio * $height) < $max_height) {
		$tn_height=ceil($x_ratio * $height);
		$tn_width=$max_width;
	} else {
		$tn_width=ceil($y_ratio * $width);
		$tn_height=$max_height;
	}
	if ($size[2]==1) {
		$src=ImageCreateFromGIF($img);
	}
	if ($size[2]==2) {
		$src=ImageCreateFromJPEG($img);
	}
	if ($size[2]==3) {
		$src=ImageCreateFromPNG($img);
	}
	$dst=ImageCreateTrueColor($tn_width,$tn_height);
	ImageCopyResampled ($dst,$src,0,0,0,0,$tn_width,$tn_height,$width,$height);
	Imageinterlace($dst, 1);
	ImageDestroy($src);
	return $dst;
}

?>