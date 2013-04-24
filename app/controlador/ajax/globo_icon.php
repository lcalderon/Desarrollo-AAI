<?
$iconColor= $_GET[color];
$iconNumber=$_GET[num];
header("Content-type: image/png");  

$arch_globo ='/imagenes/icono_maps/'.$iconColor.'.PNG';
$im=imagecreatefrompng($arch_globo);


$fuente=5;
$color_txt = imagecolorallocate($im, 0, 0, 1);

$px     = ceil(imagesx($im)-3 * strlen($iconNumber)) / 2  -1  ;
imagestring($im, $fuente, $px, 2, $iconNumber,$color_txt);
  
$bg_color = imagecolorat($im,1,1);
imagecolortransparent($im, $bg_color);

imagepng($im);  
imagedestroy($im);  
?>