<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$entidades = $_POST['ENTIDADES'];
$cond_entidades ='';
if ($entidades!='') $cond_entidades=" AND ce.ID IN ($entidades) ";
else $cond_entidades=0;

$sql="
SELECT  
ce.IDPOLIGONO,ce.DESCRIPCION,ce.LATITUD,ce.LONGITUD,
cp.ZOOMMAP
FROM 
$con->catalogo.catalogo_entidad ce,
$con->catalogo.catalogo_poligono cp
WHERE 
ce.IDPOLIGONO = cp.IDPOLIGONO
AND ce.LATITUD!='0'
AND ce.LONGITUD!='0'
$cond_entidades
";

//echo $sql;

$result1 = $con->query($sql);

header('content-type: text/xml');
$output = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
$output .= "<data>";

while ($reg1 = $result1->fetch_object()){
    $output .= "<poligono>";
    $output .= "<nombre>".$reg1->DESCRIPCION."</nombre>";
    $output .= "<cen_lat>".$reg1->LATITUD."</cen_lat>";
    $output .= "<cen_lng>".$reg1->LONGITUD."</cen_lng>";
    $output .= "<zoom>".$reg1->ZOOMMAP."</zoom>";
    
   $sql="SELECT 
			cvp.ORDEN,
			cvp.LATITUD,
			cvp.LONGITUD
		  FROM
			$con->catalogo.catalogo_poligono cp,
			$con->catalogo.catalogo_vertices_poligono cvp
		  WHERE
			cp.IDPOLIGONO = cvp.IDPOLIGONO
			AND cp.IDPOLIGONO ='$reg1->IDPOLIGONO'";
			
    $result2 = $con->query($sql);
       
    while ($reg2 = $result2->fetch_object()){
    	$output .= "<vertices>";	
    	$output .= "<latitud>".$reg2->LATITUD."</latitud>";
    	$output .= "<longitud>".$reg2->LONGITUD."</longitud>";
    	$output .= "</vertices>";	
    }
    $output .= "</poligono>";
}

$output .= "</data>";

echo $output;
?>