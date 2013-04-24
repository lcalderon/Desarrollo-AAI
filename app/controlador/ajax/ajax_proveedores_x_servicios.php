<?
include_once('../../modelo/clase_mysqli.inc.php');

$idservicio=$_GET[idservicio];

$con = new DB_mysqli();
$db =$con->catalogo;
$con->select_db($db);

$sql="
SELECT
CONCAT(
cp.IDPROVEEDOR,',',
cp.NOMBREFISCAL,',',
cs.DESCRIPCION,',',
cps.PRIORIDAD,',',
cu.LATITUD,',',
cu.LONGITUD,',',
ct.CODIGOAREA,',',
ctsp.DESCRIPCION,',',
ct.NUMEROTELEFONO,',',
cps.IDSERVICIO,'/'
) CADENA

FROM
catalogo_proveedor_servicio cps,
catalogo_servicio cs,
catalogo_proveedor cp,
catalogo_proveedor_telefono cpt,
catalogo_telefono ct,
catalogo_ubigeo cu,

catalogo_tsp ctsp

WHERE
cp.IDPROVEEDOR = cp.IDPROVEEDOR
AND cp.IDUBIGEO = cu.IDUBIGEO
AND cp.IDPROVEEDOR = cpt.IDPROVEEDOR
AND cpt.IDTELEFONO = ct.IDTELEFONO
AND cps.IDSERVICIO = cs.IDSERVICIO

AND ctsp.IDTSP=ct.IDTSP
AND cps.IDSERVICIO = '$idservicio'
GROUP BY cp.IDPROVEEDOR

";
$result = $con->query($sql);
while($reg = $result->fetch_object()){
		$xml.= $reg->CADENA;
}
echo $xml;
?>