<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$sql="
SELECT 
   cvp.IDPOLIGONO, cvp.ORDEN, cvp.LATITUD, cvp.LONGITUD,cp.ZOOMMAP,
   ce.LATITUD CEN_LAT,ce.LONGITUD CEN_LNG 
FROM 
	$con->catalogo.catalogo_entidad ce,
	$con->catalogo.catalogo_vertices_poligono cvp,
	$con->catalogo.catalogo_poligono cp
	
WHERE 
ce.IDPOLIGONO = cp.IDPOLIGONO
AND cp.IDPOLIGONO = cvp.IDPOLIGONO 
AND cp.IDPOLIGONO = (SELECT IDPOLIGONO FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEENTIDAD1='$_POST[CVEENTIDAD1]' AND ce.CVEENTIDAD2 ='$_POST[CVEENTIDAD2]'  LIMIT 1)
ORDER BY ORDEN ASC";



$result = $con->query($sql);
header('content-type: text/xml');
$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";


if ($result->num_rows){
	
$output .= "<data>";

while ($row = $result->fetch_object()){
    $output .= "<vertice>";
    $output .= "<idpoligono>".$row->IDPOLIGONO."</idpoligono>";
    $output .= "<orden>".$row->ORDEN."</orden>";
    $output .= "<latitud>".$row->LATITUD."</latitud>";
    $output .= "<longitud>".$row->LONGITUD."</longitud>";
    $output .= "<zoom>".$row->ZOOMMAP."</zoom>";
    $output .= "<cen_lat>".$row->CEN_LAT."</cen_lat>";
    $output .= "<cen_lng>".$row->CEN_LNG."</cen_lng>";
    $output .= "</vertice>";
}


unset($con);
$output .= "</data>";
}


echo $output;
?>