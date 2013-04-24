<?
include_once('../../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="select * from $con->catalogo.catalogo_proveedor_foto where IDPROVEEDOR='$_GET[idproveedor]'";
//echo $sql;
$result =$con->query($sql);
while ($reg = $result->fetch_object()){
	$mime=$reg->MIME;
	$contenido = $reg->FOTO;
}
header("Content-type: ".$mime);
//header("Content-Disposition: attachment; filename=$nombrearchivo");
print $contenido;

?>
