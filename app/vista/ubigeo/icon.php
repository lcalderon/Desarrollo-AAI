<?

$score =$_GET[num];
$imgname='blank.png';
$im = @imagecreatefrompng($imgname)  or die("Error creando la imagen");

//color del texto
$rojo = imagecolorallocate($im, 255, 0, 0);
// calcula el centro
$px     = (imagesx($im) - 7.5 * strlen($score)) / 2;
// escribe el texto
imagestring($im, 3, $px, 1, $score, $rojo);

header("Content-type: image/png");
//salida conservando el canal alfa
imagesavealpha($im,true);
imagepng($im);

?>
