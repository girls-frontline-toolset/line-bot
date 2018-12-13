<?php

$str = $_GET['str'];
$zoom = isset($_GET['t'])? 0.25 : 1 ;

if (strLen($str) < 3 ){
    exit();
}

//$str .=  "\n。";


$imgPath = "img/955602.png";

$bigImg = imagecreatetruecolor(960 * $zoom, 655 * $zoom);
$typeImg = imagecreatefromstring(file_get_contents($imgPath));
imagecopyresized ($bigImg, $typeImg , 0, 0, 0, 0, 960 * $zoom, 655 * $zoom,960 , 655 );

//imagefilledrectangle($bigImg, 0, 0, 245, 257, $text_color);

//$text_box = imagettfbbox(40,0,'AdobeFanHeitiStd-Bold.otf',$str);

//$text_width = $text_box[2]-$text_box[0];
//$x = (242/2) - ($text_width/2);

$color = imagecolorallocate($bigImg, 124, 128, 111);

imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom, 420* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf', mb_substr($str, 0,1,"UTF-8"));
imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom, 510* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf', mb_substr($str, 1,1,"UTF-8"));
imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom , 590* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf', "。");



header('Content-Type: image/jpeg');
imagejpeg($bigImg, NULL, 100);

imagedestroy($bigImg);
imagedestroy($typeImg);
