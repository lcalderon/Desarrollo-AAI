<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql= "select MIME,NOMBREARCHIVO,CONTENIDOARCHIVO from $con->catalogo.catalogo_programa where IDPROGRAMA='$_GET[idprograma]'";
 
$result = $con->query($sql);
while ($reg = $result->fetch_object()) {
$mime = $reg->MIME;
$nombrearchivo = $reg->NOMBREARCHIVO;
$contenidoarchivo = $reg->CONTENIDOARCHIVO;	
}
header('Content-type: '.$mime);

print $contenidoarchivo;

?>