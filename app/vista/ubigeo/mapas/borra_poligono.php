<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$sql="update $con->catalogo.catalogo_entidad set IDPOLIGONO=0 where CVEENTIDAD1= '$_POST[CVEENTIDAD1]' AND CVEENTIDAD2='$_POST[CVEENTIDAD2]' limit 1 ";
$con->query($sql);


echo "Se elimino el poligono";


?>