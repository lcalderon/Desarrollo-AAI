<?
header ('Content-Type: image/png');
$texto = strtoupper($_GET['texto']);
$ancho    =  6 * (strlen($texto));

$im = @imagecreatetruecolor($ancho, 20)     or die('No se puede Iniciar el nuevo flujo a la imagen GD');
$color_texto = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 3, 5,  $texto, $color_texto);
imagepng($im);
imagedestroy($im);

?>
