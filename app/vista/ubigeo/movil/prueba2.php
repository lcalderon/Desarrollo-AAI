<?
include_once('../../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli('','pe');

$sql="
SELECT 
    cp.IDPROVEEDOR,
    cp.NOMBREFISCAL,
    cp.NOMBRECOMERCIAL,
    cp.ACTIVO,
    cp.INTERNO,
    cpt.NUMEROTELEFONO,
    cpt.IDTSP,
    MAX(pgps.fecha_gps),
    pgps.latitud LATITUD,
    pgps.longitud LONGITUD
  
FROM
    $con->catalogo.catalogo_proveedor cp
    LEFT JOIN $con->catalogo.catalogo_proveedor_telefono cpt ON cpt.IDPROVEEDOR = cp.IDPROVEEDOR 
    LEFT JOIN $con->temporal.posicionGPS pgps ON pgps.receptor_id = cpt.NUMEROTELEFONO 
WHERE
cp.ACTIVO = 1
AND cp.INTERNO = 1
AND cpt.IDTSP = 3
AND pgps.latitud IS NOT NULL
AND pgps.longitud IS NOT NULL
GROUP BY cpt.NUMEROTELEFONO

";

$con->query($sql);

$result = $con->query($sql);

header('content-type: text/xml');
$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
$output .= "<data>";
while ($row = $result->fetch_object())
{
	$output .= "<proveedor>";
	$output .= "<id>".$row->IDPROVEEDOR."</id>";
	$output .= "<nc>".$row->NOMBRECOMERCIAL."</nc>";
	$output .= "<telf>".$row->NUMEROTELEFONO."</telf>";
	$output .= "<lat>".$row->LATITUD."</lat>";
	$output .= "<lng>".$row->LONGITUD."</lng>";
	$output .= "</proveedor>";
}
$output .= "</data>";
echo $output;
?>