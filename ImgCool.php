<?php

$str = $_GET['str'];
$zoom = isset($_GET['t'])? 0.25 : 1 ;

$imgPath = "img/955602.png";

$bigImg = imagecreatetruecolor(960 * $zoom, 655 * $zoom);
$typeImg = imagecreatefromstring(file_get_contents($imgPath));
imagecopyresized ($bigImg, $typeImg , 0, 0, 0, 0, 960 * $zoom, 655 * $zoom,960 , 655 );

$color = imagecolorallocate($bigImg, 124, 128, 111);
$string = ["真","香","。"];
switch (mb_strlen($str)){
    case 3:
        $string = [
        mb_substr($str, 0,1,"UTF-8"),
        mb_substr($str, 1,1,"UTF-8"),
        mb_substr($str, 2,1,"UTF-8")];
        break;
    case 2:
        $string = [
            mb_substr($str, 0,1,"UTF-8"),
            mb_substr($str, 1,1,"UTF-8"),
            "。"];
        break;
    case 1:
        $string = [
            mb_substr($str, 0,1,"UTF-8"),
            "。",
            ""];
        break;
    case 0:


        break;
    default:
        $string = [
            mb_substr($str, 0,1,"UTF-8"),
            mb_substr($str, 1,1,"UTF-8"),
            "。"];
        break;
}


imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom, 420* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf', $string[0]);
imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom, 510* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf', $string[1]);
imagettftext($bigImg, 60 * $zoom, 0,  775 * $zoom , 590* $zoom, $color, 'AdobeFanHeitiStd-Bold.otf',$string[2] );


header('Access-Control-Allow-Origin: *');
header('Content-Type: image/jpeg');
imagejpeg($bigImg, NULL, 100);

imagedestroy($bigImg);
imagedestroy($typeImg);
